/**
 * checkout-cache.js
 * Cache incremental do Checkout (quase tempo real) sem sobrescrever contato com vazio
 * - Reusa window.getSelectedPlanCode() (lead-cache) com fallback local
 * - Envia produto_code, precos (preços vistos) e adicionais (array) de forma robusta
 * - NÃO zera nome/email/telefone (filtra vazios do patch)
 * - Dispara em bursts e impõe intervalo mínimo para não sobrecarregar o servidor
 * - Aceita semeadura via evento window.dispatchEvent(new CustomEvent('checkout:prime', { detail }))
 */
(function checkoutCacheIIFE() {
    'use strict';

    const API = (typeof CONFIG !== 'undefined' && CONFIG.API_CHECKOUT_CACHE) ? CONFIG.API_CHECKOUT_CACHE : null;
    const MIN_INTERVAL_MS = 5000;
    const INPUT_BURST_MS = 800;

    if (!API) {
        // Falha defensiva: evita ruído no console caso CONFIG não esteja carregado ainda
        console.warn('[checkout-cache] CONFIG.API_CHECKOUT_CACHE ausente; cache desativado.');
        return;
    }

    // Habilita o cache incremental caso não esteja ligado
    if (window.CHECKOUT_ACTIVE !== true && localStorage.getItem('checkoutActive') !== '1') {
        window.CHECKOUT_ACTIVE = true;
        localStorage.setItem('checkoutActive', '1');
    }

    let lastSend = 0;
    let dirty = false;
    let typingTm = null;
    let lastSnapshot = null;
    let lastHash = '';

    // ---------- Gates ----------
    function isActive() {
        if (window.CHECKOUT_SUSPENDED === true) return false;
        return window.CHECKOUT_ACTIVE === true || localStorage.getItem('checkoutActive') === '1';
    }
    function getSlug() {
        return window.CHECKOUT_SLUG || new URLSearchParams(location.search).get('projeto') || null;
    }
    function getToken() {
        return window.CHECKOUT_TOKEN || localStorage.getItem('uuidLaravel') || null;
    }

    // ---------- Util ----------
    function stableStringify(obj) {
        try {
            if (obj === null || typeof obj !== 'object') return JSON.stringify(obj);
            const keys = [];
            JSON.stringify(obj, (k, v) => { keys.push(k); return v; });
            keys.sort();
            return JSON.stringify(obj, keys);
        } catch {
            return '';
        }
    }

    // Detecta plano selecionado, priorizando a função única da verdade do lead-cache
    function detectPlanCode() {
        if (typeof window.getSelectedPlanCode === 'function') {
            try { return window.getSelectedPlanCode(); } catch { }
        }
        // Fallback local idêntico à lógica já usada
        if (window.state?.planCode) return String(window.state.planCode).toLowerCase();
        if (window.planoSelecionado) return String(window.planoSelecionado).toLowerCase();

        const sel = document.querySelector('#planAccordion .plan.selected');
        if (sel?.dataset?.produto) return String(sel.dataset.produto).toLowerCase();

        const opened = document.querySelector('#planAccordion .accordion-collapse.show');
        const plan = opened?.closest('.plan');
        if (plan?.dataset?.produto) return String(plan.dataset.produto).toLowerCase();

        if (plan?.classList?.contains('plan-solo')) return 'solo';
        if (plan?.classList?.contains('plan-trio')) return 'trio';
        return 'duo';
    }

    // Lê adicionais diretamente do DOM (fonte da verdade visual)
    function readAdicionaisFromDom() {
        const cont = document.getElementById('workItems');
        const keys = Array.from(cont ? cont.querySelectorAll('.multi-option.selected') : [])
            .map(el => el.getAttribute('data-key'))
            .filter(Boolean);



        // Fallback: hidden "propostas" se nada marcado visualmente
          if (!keys.length) {
                const hid = document.getElementById('propostas');
                if (hid && hid.value) {
                      try {
                            const parsed = JSON.parse(hid.value);
                            if (Array.isArray(parsed)) keys.push(...parsed.filter(Boolean));
                            else keys.push(...String(hid.value).split(',').map(s => s.trim()).filter(Boolean));
                          } catch {
                                keys.push(...String(hid.value).split(',').map(s => s.trim()).filter(Boolean));
                              }
                    }
             }



        const novaAreaValue = (document.getElementById('nova_area')?.value || 'NAO').toString().toUpperCase();
        keys.push(`nova_area=${novaAreaValue}`);

        return keys;
    }

    // Leitura atual da UI
    function readState() {
        const byId = (id) => {
            const el = document.getElementById(id);
            return (el && typeof el.value === 'string') ? el.value.trim() : '';
        };
        const int = (id) => {
            const v = +byId(id);
            return Number.isFinite(v) ? v : 0;
        };

        let precosVistos = null;
        try {
            // getSeenPrices() deve retornar { solo:{...}, duo:{...}, trio:{...} }
            if (typeof getSeenPrices === 'function') precosVistos = getSeenPrices();
        } catch { }

        return {
            slug: getSlug(),
            token: getToken(),

            // contato (não enviar vazio no patch)
            nome: byId('nome'),
            email: byId('email'),
            telefone: byId('fone'),

            // simulador
            tipo_projeto: byId('tipo_projeto'),
            metragem: int('area'),
            ambientes: int('qtd_amb'),
            categoria: byId('categoria'),        // ESTREANTES | VERIFICADOS | PREFERIDOS (UI)
            regiao: byId('regiao_key'),          // BRASIL_TODO, etc.
            nova_area: (byId('nova_area') || 'NAO').toUpperCase(),
            prazo_dias: int('prazo_dias'),

            // adicionais do DOM
            adicionais: readAdicionaisFromDom(),

            // plano selecionado
            produto_code: detectPlanCode(),

            // preços vistos (última visão do usuário)
            precos: precosVistos
        };
    }

    function buildPatch(curr, prev) {
        if (!prev) return { ...curr }; // primeiro envio é full
        const patch = {};
        for (const k of Object.keys(curr)) {
            if (stableStringify(curr[k]) !== stableStringify(prev[k])) {
                patch[k] = curr[k];
            }
        }
        return patch;
    }

    // ---------- Envio ----------
    async function sendCheckoutCache(force = false) {
        if (window.CHECKOUT_SUSPENDED === true) return;
        if (!isActive()) return;

        const now = Date.now();
        if (!force && (now - lastSend < MIN_INTERVAL_MS || !dirty)) return;

        const curr = readState();
        if (!curr.slug) return; // slug é obrigatório

        const rev = Number(localStorage.getItem('checkoutRev') || '0') + 1;
        localStorage.setItem('checkoutRev', String(rev));

        // gera patch
        const patch = buildPatch(curr, lastSnapshot);

        // NÃO sobrescrever contato com vazio
        ['nome', 'email', 'telefone'].forEach((k) => {
            if (Object.prototype.hasOwnProperty.call(patch, k)) {
                const v = patch[k];
                if (v === '' || v === null || typeof v === 'undefined') delete patch[k];
            }
        });


        // NÃO sobrescrever métricas/tipo/adicionais com "vazio" se já havia valor
        const protectIfEmpty = (key, isEmptyFn) => {
            if (!Object.prototype.hasOwnProperty.call(patch, key)) return;
            const becameEmpty = isEmptyFn(patch[key]);
            const hadValue = lastSnapshot && !isEmptyFn(lastSnapshot[key]);
            if (becameEmpty && hadValue) delete patch[key];
        };
        protectIfEmpty('metragem', v => !(Number(v) > 0));
        protectIfEmpty('ambientes', v => !(Number(v) > 0));
        protectIfEmpty('tipo_projeto', v => !v || String(v).trim() === '');
        protectIfEmpty('adicionais', v => !Array.isArray(v) || v.length === 0);

        // Garantir adicionais como array
        if (Object.prototype.hasOwnProperty.call(patch, 'adicionais')) {
            if (!Array.isArray(patch.adicionais)) {
                if (typeof patch.adicionais === 'string') {
                    patch.adicionais = patch.adicionais.split(',').map(s => s.trim()).filter(Boolean);
                } else {
                    patch.adicionais = [];
                }
            }
        }

        // Enviar SEMPRE os preços vistos, se houver (server pode decidir salvar o último)
        if (curr.precos && typeof curr.precos === 'object') {
            patch.precos = curr.precos;
        }

        const body = { slug: curr.slug, token: curr.token || null, rev, patch };
        const bodyStr = stableStringify(body);
        if (bodyStr === lastHash && !force) return;

        lastHash = bodyStr;
        lastSend = now;
        dirty = false;
        lastSnapshot = curr;

        try {
            await fetch(API, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-API-TOKEN': (typeof window.API_TOKEN !== 'undefined') ? API_TOKEN : ''
                },
                body: JSON.stringify(body),
                keepalive: true
            });
        } catch {
            // silencioso
        }
    }

    function markDirtySoon() {
        dirty = true;
        clearTimeout(typingTm);
        typingTm = setTimeout(() => sendCheckoutCache(true), INPUT_BURST_MS);
    }

    // ---------- Listeners ----------
    function wireInputs() {
        const selectors = [
            '#nome', '#email', '#fone',
            '#area', '#qtd_amb', '#tipo_projeto',
            '#categoria', '#regiao_key', '#nova_area', '#prazo_dias',
            '#planAccordion'
        ];

        selectors.forEach((sel) => {
            document.querySelectorAll(sel).forEach((el) => {
                ['input', 'change', 'blur'].forEach((evt) => {
                    el.addEventListener(evt, markDirtySoon, { passive: true });
                });
            });
        });

        // Cards de plano
        document.querySelectorAll('#planAccordion .plan').forEach((el) => {
            el.addEventListener('click', markDirtySoon, { passive: true });
        });

        // Adicionais (multi-option)
        const work = document.getElementById('workItems');
        if (work) {
            work.querySelectorAll('.multi-option').forEach((el) => {
                el.addEventListener('click', markDirtySoon, { passive: true });
            });
            new MutationObserver(() => markDirtySoon())
                .observe(work, { attributes: true, subtree: true, childList: true, attributeFilter: ['class'] });
        }

        // Fallback para mudanças no accordion
        const acc = document.getElementById('planAccordion');
        if (acc) {
            new MutationObserver(() => markDirtySoon())
                .observe(acc, { attributes: true, subtree: true, childList: true });
        }

        // Semeadura inicial vinda do editor (evita primeiro patch apagar contato)
        window.addEventListener('checkout:prime', (ev) => {
            const primed = ev?.detail || null;
            if (!primed) return;
            // só aceita se for do mesmo slug
            const s = getSlug();
            if (!s || primed.slug !== s) return;

            // define o snapshot base e força 1 envio para alinhar revisões
            lastSnapshot = {
                slug: s,
                token: getToken() || primed.token || null,
                nome: primed.nome || '',
                email: primed.email || '',
                telefone: primed.telefone || '',
                tipo_projeto: primed.tipo_projeto || '',
                metragem: Number(primed.metragem) || 0,
                ambientes: Number(primed.ambientes) || 0,
                categoria: primed.categoria || '',
                regiao: primed.regiao || primed.regiao_arquiteto || 'BRASIL_TODO',
                nova_area: (primed.nova_area || 'NAO').toString().toUpperCase(),
                prazo_dias: Number(primed.prazo_dias) || 21,
                produto_code: primed.produto_code || detectPlanCode(),
                adicionais: Array.isArray(primed.adicionais) ? primed.adicionais.slice() : readAdicionaisFromDom(),
                precos: primed.precos || (typeof getSeenPrices === 'function' ? getSeenPrices() : null)
            };

            // ativa se necessário e dispara envio
            if (!isActive()) {
                window.CHECKOUT_ACTIVE = true;
                localStorage.setItem('checkoutActive', '1');
            }
            dirty = true;
            sendCheckoutCache(true);
        });

        // Reage a alterações de storage (ex.: outra aba ligou o checkoutActive)
        window.addEventListener('storage', (e) => {
            if (e.key === 'checkoutActive' && e.newValue === '1') {
                dirty = true;
                sendCheckoutCache(true);
            }
        });
    }

    // ---------- Heartbeat / Visibilidade / Unload ----------
    setInterval(() => sendCheckoutCache(false), 5000);

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && isActive()) {
            dirty = true;
            sendCheckoutCache(true);
        }
    });

    window.addEventListener('beforeunload', () => {
        if (!isActive()) return;
        try {
            const curr = readState();
            const rev = Number(localStorage.getItem('checkoutRev') || '0') + 1;
            localStorage.setItem('checkoutRev', String(rev));

            const patch = buildPatch(curr, lastSnapshot);

            // não enviar contato vazio no unload
            ['nome', 'email', 'telefone'].forEach((k) => {
                if (Object.prototype.hasOwnProperty.call(patch, k)) {
                    const v = patch[k];
                    if (v === '' || v === null || typeof v === 'undefined') delete patch[k];
                }
            });

            // garantir adicionais array
            if (Object.prototype.hasOwnProperty.call(patch, 'adicionais')) {
                if (!Array.isArray(patch.adicionais)) {
                    if (typeof patch.adicionais === 'string') {
                        patch.adicionais = patch.adicionais.split(',').map(s => s.trim()).filter(Boolean);
                    } else {
                        patch.adicionais = [];
                    }
                }
            }

            // incluir preços vistos
            if (curr.precos && typeof curr.precos === 'object') {
                patch.precos = curr.precos;
            }

            const body = { slug: curr.slug, token: curr.token || null, rev, patch };
            const blob = new Blob([JSON.stringify(body)], { type: 'application/json' });
            navigator.sendBeacon(API, blob);
        } catch {
            // silencioso
        }
    });

    // ---------- Boot ----------
    wireInputs();
    if (isActive()) {
        dirty = true;
        sendCheckoutCache(true);
    }

    // Exposição manual (debug/dev)
    window.sendCheckoutCache = function () {
        dirty = true;
        sendCheckoutCache(true);
    };
})();
