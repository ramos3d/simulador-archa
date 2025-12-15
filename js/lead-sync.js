/* =========================================================
 * lead-sync.js — Upsert direto em /api/lead (sem Redis)
 * Requisitos globais: CONFIG.API_LEAD, API_TOKEN,
 * ensureUuid(), getSeenPrices(), getPrazo(), getCat(),
 * getReg(), getAdicionaisForLead(), getTipo(),
 * state.planCode ou planoSelecionado
 * ======================================================= */
/* lead-sync.js — Upsert direto em /api/leads (sem Redis) */
(function () {
    let tSync = null;

    function resolvePlanCode() {
        const s = (window.state && window.state.planCode) || window.planoSelecionado || null;
        if (s) return String(s).toLowerCase();
        const sel = document.querySelector('#planAccordion .plan.selected');
        if (sel?.dataset?.produto) return String(sel.dataset.produto).toLowerCase();
        const opened = document.querySelector('#planAccordion .accordion-collapse.show');
        const plan = opened?.closest('.plan');
        if (plan?.dataset?.produto) return String(plan.dataset.produto).toLowerCase();
        if (plan?.classList?.contains('plan-solo')) return 'solo';
        if (plan?.classList?.contains('plan-trio')) return 'trio';
        return 'duo';
    }

    const safe = (fn, dflt = null) => { try { return (typeof fn === 'function') ? fn() : dflt; } catch { return dflt; } };

    async function buildPayload(silent) {
        const token = (typeof ensureUuid === 'function')
            ? await ensureUuid()
            : (localStorage.getItem('leadUuid') || localStorage.getItem('uuidLaravel'));
        if (token) { try { localStorage.setItem('leadUuid', token); localStorage.setItem('uuidLaravel', token); } catch (_) { } }

        const body = {
            token,
            produto_code: resolvePlanCode(),
            channel: 'archa',
            silent: !!silent,
            origem_cliente: safe(window.detectarOrigem, null),
            utms: (() => {
                try {
                    const qs = new URLSearchParams(location.search);
                    const out = {};
                    ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid']
                        .forEach(k => { const v = qs.get(k); if (v) out[k] = v; });
                    return out;
                } catch { return {}; }
            })(),
            precos: (typeof window.getSeenPrices === 'function') ? window.getSeenPrices() : {},
            cliente: {
                nome: (document.getElementById('nome')?.value || '').trim(),
                email: (document.getElementById('email')?.value || '').trim(),
                telefone: (document.getElementById('fone')?.value || '').trim(),
            },
            projeto: {
                metragem: +document.getElementById('area')?.value || 0,
                ambientes: +document.getElementById('qtd_amb')?.value || 0,
                prazo_dias: safe(window.getPrazo, null),
                categoria: (() => {
                    const ui = (typeof window.getCat === 'function') ? window.getCat() : 'ESTREANTES';
                    const t = String(ui || '').trim().toUpperCase();
                    if (t === 'VERIFICADOS') return 'Verificados';
                    if (t === 'PREFERIDOS') return 'Preferidos';
                    return 'Estreantes';
                })(),
                regiao_arquiteto: safe(window.getReg, 'BRASIL_TODO'),
                adicionais: safe(window.getAdicionaisForLead, []),
                tipo_projeto: (typeof window.getTipo === 'function')
                    ? window.getTipo()
                    : (document.querySelector('#tipo_projeto')?.value?.trim() || null),
            }
        };
        return body;
    }

    async function syncLeadNow(silent = true) {
        try {
            if (!window.CONFIG || !CONFIG.API_LEAD) return;
            const body = await buildPayload(silent);
            if (!body.token || !body.produto_code) {
                clearTimeout(tSync);
                tSync = setTimeout(() => syncLeadNow(silent), 300);
                return;
            }
            if (window.DEBUG) console.debug('[LeadSync] POST', CONFIG.API_LEAD, body);
            await fetch(CONFIG.API_LEAD, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-API-TOKEN': (typeof window.API_TOKEN !== 'undefined') ? window.API_TOKEN : ''
                },
                body: JSON.stringify(body),
                keepalive: true
            }).catch(() => { });
        } catch (_) { }
    }

    function scheduleSync(delayMs = 900) {
        clearTimeout(tSync);
        tSync = setTimeout(() => syncLeadNow(true), delayMs);
    }

    window.syncLeadNow = syncLeadNow;
    window.scheduleSync = scheduleSync;
})();
