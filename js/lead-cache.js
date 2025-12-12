/* lead-cache.js */
(function () {
    const API_LEAD_CACHE = CONFIG.API_LEAD_CACHE;
    const MIN_INTERVAL_MS = 5000;
    const INPUT_BURST_MS = 800;

    let lastSend = 0;
    let dirty = false;
    let typingTm = null;

    // 🔴 Novo: checa se o envio incremental está liberado
    function isActive() {
        return window.LEAD_ACTIVE === true || localStorage.getItem('leadActive') === '1';
    }

    function getSelectedPlanCode() {
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

    window.getSelectedPlanCode = getSelectedPlanCode;
    function buildPayload() {
        return {
            token: localStorage.getItem('leadUuid') || null,
            produto_code: getSelectedPlanCode(),
            channel: 'archa',
            precos: getSeenPrices(),
            origem_cliente: (typeof detectarOrigem === 'function') ? detectarOrigem() : null,
            cliente: {
                nome: document.getElementById('nome')?.value.trim() || '',
                email: document.getElementById('email')?.value.trim() || '',
                telefone: document.getElementById('fone')?.value.trim() || '',
            },
            projeto: {
                metragem: +document.getElementById('area')?.value || 0,
                ambientes: +document.getElementById('qtd_amb')?.value || 0,
                prazo_dias: (typeof getPrazo === 'function') ? getPrazo() : null,
                categoria: (typeof getCat === 'function') ? getCat() : null,
                regiao_arquiteto: (typeof getReg === 'function') ? getReg() : null,
                adicionais: (typeof getAdicionaisForLead === 'function') ? getAdicionaisForLead() : [],
                tipo_projeto: (typeof getTipo === 'function') ? getTipo()
                    : (document.querySelector('#tipo_projeto')?.value?.trim() || null),
            }
        };
    }

    async function sendLeadCache(force = false) {
        // 🔴 Gate: só envia se leadActive estiver liberado
        if (!isActive()) return;

        const now = Date.now();
        if (!force && (now - lastSend < MIN_INTERVAL_MS || !dirty)) return;

        const payload = buildPayload();
        if (!payload.token) return;

        lastSend = now;
        dirty = false;

        try {
            await fetch(API_LEAD_CACHE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-API-TOKEN': API_TOKEN
                },
                body: JSON.stringify(payload),
                keepalive: true
            });
        } catch (_) { }
    }

    function markDirtySoon() {
        dirty = true;
        clearTimeout(typingTm);
        typingTm = setTimeout(() => sendLeadCache(true), INPUT_BURST_MS);
    }

    function wireInputs() {
        const selectors = [
            '#nome', '#email', '#fone',
            '#area', '#qtd_amb', '#tipo_projeto',
            '#planAccordion',
        ];
        selectors.forEach(sel => {
            document.querySelectorAll(sel).forEach(el => {
                ['input', 'change', 'blur'].forEach(evt =>
                    el.addEventListener(evt, markDirtySoon, { passive: true })
                );
            });
        });

        const acc = document.getElementById('planAccordion');
        if (acc) new MutationObserver(markDirtySoon)
            .observe(acc, { attributes: true, subtree: true, childList: true });
    }

    // 🔴 Intervalo sempre ativo; o gate é checado dentro do sendLeadCache
    setInterval(() => sendLeadCache(false), 5000);

    // 🔴 Quando o leadActive for ligado (após “Ver preço”), force 1º envio
    window.addEventListener('storage', (e) => {
        if (e.key === 'leadActive' && e.newValue === '1') {
            dirty = true;
            sendLeadCache(true);
        }
        if (e.key === 'leadUuid' && e.newValue) {
            dirty = true;
            sendLeadCache(true);
        }
    });

    // 🔴 Se a página já abriu com leadActive=1 (ex.: volta de navegação), dispara
    if (isActive()) { dirty = true; sendLeadCache(true); }

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && isActive()) { dirty = true; sendLeadCache(true); }
    });

    wireInputs();

    // expõe manual
    window.sendLeadCache = () => { dirty = true; sendLeadCache(true); };
})();
