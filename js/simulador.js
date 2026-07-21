/* =========================================================
 * Simulador — simples e robusto
 * ======================================================= */
window.onReady = window.onReady || (fn =>
    (document.readyState !== 'loading') ? fn() : document.addEventListener('DOMContentLoaded', fn)
);

// Detecta se é mobile 
const isMobileViewport = (maxWidth = 1024) =>
    window.matchMedia(`(max-width: ${maxWidth}px)`).matches;

const DEBUG = true;
const log = (...a) => DEBUG && console.log('[SIM]', ...a);

/* ENDPOINTS */
//const API_FORM = CONFIG.API_FORM;
const API_CACHE = CONFIG.API_CACHE;
const CACHE_TTL = 30 * 60 * 1e3;

/* ESTADO */
const state = { ready: false, paramTab: null, cacheBorn: 0, planCode: null };
const cacheLive = () => state.paramTab && (Date.now() - state.cacheBorn) < CACHE_TTL;




// ===== GA4: fila de update para disparar após o recálculo =====
const GAUpdate = { pending: null };

function queueUpdate(field, value, context) {
    GAUpdate.pending = {
        field_changed: String(field || ''),
        new_value: (value ?? '').toString(),
        step_context: String(context || '')
    };
}

function fireUpdateIfAny(planos) {
    const p = GAUpdate.pending;
    if (!p) return;

    const planCode = (state.planCode || planoSelecionado || 'duo').toLowerCase();
    const packs = {
        solo: planos?.flex_pack || {},
        duo: planos?.duo_pack || {},
        trio: planos?.trio_pack || {}
    };
    const sel = packs[planCode] || {};
    const newPrice = +(
        sel.parcelado_com_taxa_archa ??
        sel.parcelado ??
        sel.parcelado_total ?? 0
    );

    const payload = {
        field_changed: p.field_changed,
        new_value: p.new_value,
        new_price: newPrice,
        step_context: p.step_context
    };

    const priceChanged = Number.isFinite(newPrice) && newPrice > 0 &&
        Math.abs(newPrice - (fireUpdateIfAny._lastPrice ?? 0)) >= 1;

    const gate = shouldSendUpdate(payload); // { ok, wait }

    if (priceChanged && gate.ok) {
        try {
            ArchaAnalytics.trackSimulationUpdate(
                payload.field_changed,
                payload.new_value,
                payload.new_price,
                payload.step_context
            );
        } catch (_) { }
        fireUpdateIfAny._lastPrice = newPrice;
        GAUpdate.pending = null;                    // <- só limpa quando enviar
        clearTimeout(fireUpdateIfAny._retryTO);
        fireUpdateIfAny._retryTO = null;
    } else {
        // mantém pendente e agenda retry se estiver no intervalo mínimo
        GAUpdate.pending = p;                       // <- mantém o último estado
        if (gate.wait) {
            clearTimeout(fireUpdateIfAny._retryTO);
            fireUpdateIfAny._retryTO = setTimeout(() => {
                try { fireUpdateIfAny(planosLocal()); } catch (_) { }
            }, gate.wait + 10);
        }
    }
}


// =============================================================


// ===== GA4: dedupe + rate limit para simulation_update =====
const GAUpdateGuard = {
    sent: 0,
    maxPerSession: 40,       // não envia mais que 40 updates na sessão
    minIntervalMs: 3000,      // intervalo mínimo entre envios
    lastTs: 0,
    lastHash: null           // usado pra deduplicar payloads idênticos
};

function _hash(obj) {
    // hash simples estável
    try { return JSON.stringify(obj); } catch { return String(Math.random()); }
}

function shouldSendUpdate(payload) {
    const now = Date.now();
    if (GAUpdateGuard.sent >= GAUpdateGuard.maxPerSession) return { ok: false };

    const since = now - GAUpdateGuard.lastTs;
    const wait = GAUpdateGuard.minIntervalMs - since;
    if (wait > 0) return { ok: false, wait };

    const h = _hash(payload);
    if (h === GAUpdateGuard.lastHash) return { ok: false }; // dedupe do último ENVIADO

    // reservar o slot (só quando formos enviar)
    GAUpdateGuard.lastHash = h;
    GAUpdateGuard.lastTs = now;
    GAUpdateGuard.sent += 1;
    return { ok: true };
}

// =============================================================




/* ===== Plano selecionado (compat com legado) ===================== */
let planoSelecionado = null; // alias legado

function initSelectedPlan() {
    const openBtn = document.querySelector('#planAccordion .accordion-button:not(.collapsed)');
    if (openBtn) {
        const item = openBtn.closest('.plan');
        item?.classList.add('selected');
        const code = item?.dataset.produto || null;
        state.planCode = code;
        planoSelecionado = code;
    }
}

/* mantém sincronizado quando troca o plano no acordeon */
/*document.addEventListener('DOMContentLoaded', () => {
    const acc = document.getElementById('planAccordion');
    if (!acc) return;
    acc.addEventListener('show.bs.collapse', (e) => {
        document.querySelectorAll('#planAccordion .plan.selected').forEach(p => p.classList.remove('selected'));
        const plan = e.target.closest('.plan');
        plan?.classList.add('selected');
        const code = plan?.dataset.produto || null;
        state.planCode = code;
        planoSelecionado = code;
    });
});

*/

/* mantém sincronizado quando troca o plano no acordeon + GA select_package */
document.addEventListener('DOMContentLoaded', () => {
    const acc = document.getElementById('planAccordion');
    if (!acc) return;

    acc.addEventListener('show.bs.collapse', (e) => {
        // marca visualmente
        document.querySelectorAll('#planAccordion .plan.selected')
            .forEach(p => p.classList.remove('selected'));
        const plan = e.target.closest('.plan');
        plan?.classList.add('selected');

        // atualiza estado
        const code = plan?.dataset.produto || null;
        state.planCode = code;
        planoSelecionado = code;

        // envia imediatamente pro /api/lead/cache (sem depender de recálculo)
        //window.sendLeadCache();

        // snapshot direto para /api/lead (sem Redis)
        if (typeof window.scheduleSync === 'function') window.scheduleSync(200);


        // === GA: select_package (só após 1º cálculo) ===
        try {
            if (state.ready && cacheLive()) {
                const planos = planosLocal();
                const selCode = (state.planCode || 'duo').toLowerCase();
                const packs = {
                    solo: planos?.flex_pack || {},
                    duo: planos?.duo_pack || {},
                    trio: planos?.trio_pack || {}
                };
                const sel = packs[selCode] || {};
                const finalPrice = +(
                    sel.parcelado_com_taxa_archa ??
                    sel.parcelado ??
                    sel.parcelado_total ?? 0
                );

                // analytics.js: assinatura atual (sem architect_type)
                ArchaAnalytics.trackSelectPackage(
                    selCode,                                        // package_selected
                    finalPrice,                                     // final_price
                    getTipo(),                                      // project_type
                    +document.getElementById('area')?.value || 0,   // space_size_m2
                    +document.getElementById('qtd_amb')?.value || 0,   // rooms_count
                    getAdic(),                                      // extras - adicionais
                    getPrazo()                                      // prazo (dias)
                );
            }
        } catch (_) { /* silencia erros de GA */ }
    });
});


window.planoSelecionado = planoSelecionado;

/* HELPERS */
// --- helpers para normalizar valores vindos do Calculator ---
function normalizePlanVals(plan) {
    // aceita vários nomes que podem vir do motor/calculadora
    const avista = +(
        plan?.a_vista ??
        plan?.a_vista_bruto ??
        plan?.valor_avista ??
        0
    );

    const totalParcelado = +(
        plan?.parcelado ??
        plan?.parcelado_com_taxa_archa ??
        plan?.valor_parcelado_total ??
        0
    );

    const parcela = totalParcelado > 0 ? totalParcelado / 10 : 0;
    return { avista, totalParcelado, parcela };
}

function paintPlan(code, planObj) {
    const { avista, parcela } = normalizePlanVals(planObj);

    const ids = {
        solo: { hdr: 'priceSolo', parc: 'priceSoloParcela', cash: 'priceSoloAvista' },
        duo: { hdr: 'priceDuo', parc: 'priceDuoParcela', cash: 'priceDuoAvista' },
        trio: { hdr: 'priceTrio', parc: 'priceTrioParcela', cash: 'priceTrioAvista' }
    }[code];

    // Cabeçalho do acordeão: mostra a PARCELA (10x)
    const elHdr = document.getElementById(ids.hdr);
    if (elHdr) elHdr.textContent = money(parcela);

    // Dentro do corpo: 10x de <parcela> no cartão
    const elParc = document.getElementById(ids.parc);
    if (elParc) elParc.textContent = money(parcela);

    // Dentro do corpo: ou R$ <à vista> (15% OFF)
    const elCash = document.getElementById(ids.cash);
    if (elCash) elCash.textContent = money(avista);
}


const qs = s => document.querySelector(s);
const qsa = s => [...document.querySelectorAll(s)];
const money = v => (+v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

/* COLETA */
const getPrazo = () =>
    +qs('.prazo-box.selected')?.dataset.prazo || +document.getElementById('prazo_dias')?.value || 21;

window.getPrazo = getPrazo;

const getNova = () =>
    qs('#novaAreaWrap .option-box.selected')?.dataset.reg || document.getElementById('nova_area')?.value || 'NAO';
const getAdic = () => qsa('.multi-option.selected').map(el => el.dataset.key);
const getTipo = () => qs('#tipo_projeto')?.value || '';
const getCat = () => qs('#categoria')?.value || 'ESTREANTES';
const getReg = () => qs('#regiao_key')?.value || 'BRASIL_TODO';
// Lê a região com base no box selecionado
window.getReg = function () {
    const sel = document.querySelector('#regwrap .option-box.selected');
    return (sel?.dataset.reg || 'BRASIL_TODO').toUpperCase();
};

window.getCat = getCat;

/* UUID */
async function ensureUuid() {
    let uuid = localStorage.getItem('uuidLaravel');
    if (uuid) {
        //const test = await fetch(CONFIG.apiParametros(uuid), { method: 'HEAD' });
        const test = await fetch(CONFIG.apiParametros(uuid), {
            method: 'GET',
            headers: { 'Accept': 'application/json', 'X-API-TOKEN': API_TOKEN }
        });

        const r = await fetch(API_CACHE, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-API-TOKEN': API_TOKEN }
        });
        if (test.ok) return uuid;
    }
    const r = await fetch(API_CACHE, { method: 'POST' });
    const { uuid: novo } = await r.json();
    localStorage.setItem('uuidLaravel', novo);
    return novo;
}

function categoriaApiFromUI(ui) {
    const t = String(ui || '').trim().toUpperCase();
    if (t === 'VERIFICADOS') return 'Verificados';
    if (t === 'PREFERIDOS') return 'Preferidos';
    return 'Estreantes';
}

/* 1º CÁLCULO (POST ÚNICO) */

async function primeiroCalculo() {
    const uuid = await ensureUuid();

    const res = await fetch(CONFIG.apiParametros(uuid), { cache: 'no-store' });
    const json = await res.json();
    if (!res.ok || json.status !== 'success') throw Error(window.jt('Falha ao obter parâmetros'));

    state.paramTab = json.parametros;
    state.cacheBorn = Date.now();

    // ✅ entrada no formato que o Calculator espera
    const input = {
        metragem: +document.getElementById('area')?.value || 0,
        ambientes: +document.getElementById('qtd_amb')?.value || 0,
        prazoDias: getPrazo(),
        adicionaisKeys: getAdic(),
        novaAreaSN: getNova(),
        tipoProjeto: getTipo(),
        regiaoKey: getReg(),
        categoriaUI: getCat(),
        categoria: categoriaApiFromUI(getCat())
    };

    // ✅ assinatura correta: (input, tabela)
    const planos = Calculator.calcularPlanosLocais(input, state.paramTab);

    render(planos);
    fireUpdateIfAny(planos); // dispara GA4 se houver update pendente
    log('primeiroCalculo OK', planos);
}



/* ======== CÁLCULO LOCAL (ponte para o motor Calculator) ========= */

// Wrapper que mantém o nome antigo planosLocal(), mas usa o novo motor
function planosLocal() {
    const input = {
        metragem: +document.getElementById('area')?.value || 0,
        ambientes: +document.getElementById('qtd_amb')?.value || 0,
        prazoDias: getPrazo(),
        adicionaisKeys: getAdic(),
        novaAreaSN: getNova(),
        tipoProjeto: getTipo(),
        regiaoKey: getReg(),
        categoriaUI: getCat(),  // 🔸 passa categoria para afetar o preço
        categoria: categoriaApiFromUI(getCat())
    };
    return Calculator.calcularPlanosLocais(input, state.paramTab);
}

// Atualiza o cartão lateral (vant-card)
function updateVantFromCurrentUI() {
    //if (!isMobileViewport() || typeof window.updateVantCard !== 'function') return;

    const adicionaisCount = document.querySelectorAll('#workItems .multi-option.selected').length;

    window.updateVantCard({
        tipo: getTipo(),
        ambientes: +document.getElementById('qtd_amb')?.value || 0,
        metragem: +document.getElementById('area')?.value || 0,
        categoria: getCat(),
        regiao: getReg() === 'BRASIL_TODO' ? window.jt('todo Brasil') : window.jt('minha região'),
        prazo: getPrazo(),
        novaArea: getNova() === 'SIM' ? window.jt('SIM') : window.jt('NÃO'),
        adicionalCount: adicionaisCount
    });
}


/* PINTA */

function render(planos) {
    const {
        // aliases numéricos (à vista)
        flex = 0, duo = 0, trio = 0,
        // packs com os totais (onde vem o parcelado)
        flex_pack = {}, duo_pack = {}, trio_pack = {}
    } = planos || {};

    // SOLO
    const soloAvista = +(flex_pack.a_vista_bruto ?? flex ?? 0);
    const soloTotalParcelado = +(flex_pack.parcelado_com_taxa_archa ?? 0);
    const soloParcela = soloTotalParcelado > 0 ? (soloTotalParcelado / 10) : 0;

    // DUO
    const duoAvista = +(duo_pack.a_vista_bruto ?? duo ?? 0);
    const duoTotalParcelado = +(duo_pack.parcelado_com_taxa_archa ?? 0);
    const duoParcela = duoTotalParcelado > 0 ? (duoTotalParcelado / 10) : 0;

    // TRIO
    const trioAvista = +(trio_pack.a_vista_bruto ?? trio ?? 0);
    const trioTotalParcelado = +(trio_pack.parcelado_com_taxa_archa ?? 0);
    const trioParcela = trioTotalParcelado > 0 ? (trioTotalParcelado / 10) : 0;

    paintVerPrecoButton(soloParcela);

    // Preços base sem descontos promocionais

    const soloBaseTotal = +(flex_pack.parcelado_base ?? 0);
    const duoBaseTotal = +(duo_pack.parcelado_base ?? 0);
    const trioBaseTotal = +(trio_pack.parcelado_base ?? 0);

    document.getElementById('priceSoloBase')?.replaceChildren(
        document.createTextNode(money(soloBaseTotal / 10))
    );
    document.getElementById('priceDuoBase')?.replaceChildren(
        document.createTextNode(money(duoBaseTotal / 10))
    );
    document.getElementById('priceTrioBase')?.replaceChildren(
        document.createTextNode(money(trioBaseTotal / 10))
    );

    // fim Preços base sem descontos promocionais



    // ---- Cabeçalho: "10x de R$ ..." (parcela) ----
    const priceSolo = document.getElementById('priceSolo');
    const priceDuo = document.getElementById('priceDuo');
    const priceTrio = document.getElementById('priceTrio');

    if (priceSolo) priceSolo.textContent = money(soloParcela);
    if (priceDuo) priceDuo.textContent = money(duoParcela);
    if (priceTrio) priceTrio.textContent = money(trioParcela);


    // ---- Corpo do acordeão ----
    // parcelas
    document.getElementById('priceSoloParcela')?.replaceChildren(document.createTextNode(money(soloParcela)));
    document.getElementById('priceDuoParcela')?.replaceChildren(document.createTextNode(money(duoParcela)));
    document.getElementById('priceTrioParcela')?.replaceChildren(document.createTextNode(money(trioParcela)));

    // à vista
    document.getElementById('priceSoloAvista')?.replaceChildren(document.createTextNode(money(soloAvista)));
    document.getElementById('priceDuoAvista')?.replaceChildren(document.createTextNode(money(duoAvista)));
    document.getElementById('priceTrioAvista')?.replaceChildren(document.createTextNode(money(trioAvista)));

    // Atualiza a lista de itens (vant-card)

    /* updateVantFromCurrentUI();
     window.sendLeadCache();*/

    updateVantFromCurrentUI();
    if (typeof window.scheduleSync === 'function') window.scheduleSync(900);

}

function paintVerPrecoButton(soloParcela) {
    const btn = document.getElementById('btnVerPreco');
    if (!btn) return;

    // Sem cálculo válido → volta ao padrão
    if (!Number.isFinite(soloParcela) || soloParcela <= 0) {
        btn.classList.remove('btn-ver-preco-active');
        btn.textContent = window.jt('Ver preço');
        return;
    }

    // Estado com preço: fundo branco + borda + texto azul
    btn.classList.add('btn-ver-preco-active');

    btn.innerHTML = `
        <span style="display:flex; align-items:center; gap:8px; width:100%; justify-content:center;color:#262942!important;">
            ${window.jt('A partir de 10x de')} <strong>${money(soloParcela)}</strong>
        </span>
    `;
}


/* RECALC (debounce + fila durante o 1º cálculo) */
let tDebounce;
let pendingFrom = null;


function scheduleRecalc(from = '?') {
    if (!state.ready) {
        pendingFrom = from;
        log('change guardada (aguardando 1º cálculo):', from);
        return;
    }
    clearTimeout(tDebounce);
    tDebounce = setTimeout(async () => {
        try {
            if (!cacheLive()) {
                log('cache expirado → refaz 1º cálculo');
                await ensureUuid();
                await primeiroCalculo();
            } else {
                const planos = planosLocal();
                render(planos);
                fireUpdateIfAny(planos); // dispara GA4 se houver update pendente
                log('recalc OK:', from, planos);
            }
        } catch (e) {
            console.warn('[SIM] recalc erro:', e);
        }
    }, 200);
}




/* FAILSAFE + HANDLERS (Passo 2 e 3) — versão delegada e única */
onReady(() => {
    // Inputs numéricos (área / ambientes)
    /*['#area', '#qtd_amb'].forEach((sel) => {
        const el = document.querySelector(sel);
        if (el) el.addEventListener('input', () => scheduleRecalc(sel));
    });*/

    ['#area', '#qtd_amb'].forEach((sel) => {
        const el = document.querySelector(sel);
        if (!el) return;
        el.addEventListener('input', () => {
            if (sel === '#area') queueUpdate('space_size_m2', el.value, 'bloco1');
            else if (sel === '#qtd_amb') queueUpdate('rooms_count', el.value, 'bloco1');
            scheduleRecalc(sel);
            if (typeof window.scheduleSync === 'function') window.scheduleSync(900);
        });
    });


    // Hiddens que disparam recálculo quando alterados por script
    /*
    ['#tipo_projeto', '#categoria', '#regiao_key', '#prazo_dias', '#nova_area', '#propostas']
        .forEach((sel) => {
            const el = document.querySelector(sel);
            if (!el) return;
            el.addEventListener('input', () => scheduleRecalc(el.id));
            el.addEventListener('change', () => scheduleRecalc(el.id));
        });
        */

    ['#tipo_projeto', '#categoria', '#regiao_key', '#prazo_dias', '#nova_area', '#propostas']
        .forEach((sel) => {
            const el = document.querySelector(sel);
            if (!el) return;
            el.addEventListener('input', () => {
                if (sel === '#tipo_projeto') queueUpdate('project_type', el.value, 'bloco1');
                scheduleRecalc(el.id);
                if (typeof window.scheduleSync === 'function') window.scheduleSync(900);
            });
            el.addEventListener('change', () => {
                if (sel === '#tipo_projeto') queueUpdate('project_type', el.value, 'bloco1');
                scheduleRecalc(el.id);
                if (typeof window.scheduleSync === 'function') window.scheduleSync(900);
            });
        });


    // ===== Delegação ÚNICA de cliques (resiste a re-render) =====
    document.addEventListener('click', (e) => {
        // --- Passo 2: Categoria ---
        const catBox = e.target.closest('#proCatWrap .option-box');
        if (catBox) {
            const wrap = document.getElementById('proCatWrap');
            wrap?.querySelectorAll('.option-box').forEach(x => x.classList.remove('selected'));
            catBox.classList.add('selected');

            const hCat = document.getElementById('categoria');
            if (hCat) hCat.value = catBox.dataset.cat || 'ESTREANTES';

            queueUpdate('category', hCat.value, 'bloco2');

            // agenda recálculo após aplicar seleção
            setTimeout(() => scheduleRecalc('#categoria:body'), 0);
            if (typeof window.scheduleSync === 'function') window.scheduleSync(900);

            return;
        }

        // --- Passo 2: Região ---
        const regBox = e.target.closest('#regWrap .option-box');
        if (regBox) {
            const wrap = document.getElementById('regWrap');
            wrap?.querySelectorAll('.option-box').forEach(x => x.classList.remove('selected'));
            regBox.classList.add('selected');

            const hReg = document.getElementById('regiao_key');
            if (hReg) hReg.value = regBox.dataset.reg || 'BRASIL_TODO';

            queueUpdate('region', hReg.value, 'bloco2');

            setTimeout(() => scheduleRecalc('#regiao_key:body'), 0);
            if (typeof window.scheduleSync === 'function') window.scheduleSync(900);

            return;
        }

        // --- Passo 3: Itens de obra (multi) ---
        const workBox = e.target.closest('#workItems .multi-option');
        if (workBox) {
            workBox.classList.toggle('selected');
            const on = workBox.classList.contains('selected');

            const chk = workBox.querySelector('.check-indicator img');
            if (chk) chk.style.display = on ? 'block' : 'none';

            const hid = document.getElementById('propostas');
            if (hid) {
                const keys = [...document.querySelectorAll('#workItems .multi-option.selected')]
                    .map(x => x.dataset.key).filter(Boolean);
                hid.value = Array.from(new Set(keys)).join(',');

                // marca o item específico adicionado/removido (ex.: "+pintura" / "-pintura")
                const changedKey = workBox.dataset.key || '';
                queueUpdate('extras', (on ? `+${changedKey}` : `-${changedKey}`), 'bloco3');
            }

            setTimeout(() => scheduleRecalc('#propostas:body'), 0);
            if (typeof window.scheduleSync === 'function') window.scheduleSync(900);

            return;
        }

        // --- Passo 3: Nova área? (rádio) ---
        const novaBox = e.target.closest('#novaAreaWrap .option-box');
        if (novaBox) {
            const wrap = document.getElementById('novaAreaWrap');
            wrap?.querySelectorAll('.option-box').forEach(x => x.classList.remove('selected'));
            novaBox.classList.add('selected');

            const hid = document.getElementById('nova_area');
            if (hid) {
                hid.value = novaBox.dataset.reg || 'NAO';
                queueUpdate('new_area', hid.value, 'bloco3');
            }

            setTimeout(() => scheduleRecalc('#nova_area:body'), 0);
            if (typeof window.scheduleSync === 'function') window.scheduleSync(900);

            return;
        }

        // --- Passo 3: Prazo (rádio) ---
        const prazoBox = e.target.closest('#prazoWrap .option-box');
        if (prazoBox) {
            const wrap = document.getElementById('prazoWrap');
            wrap?.querySelectorAll('.option-box').forEach(x => x.classList.remove('selected'));
            prazoBox.classList.add('selected');

            const hid = document.getElementById('prazo_dias');
            if (hid) {
                hid.value = prazoBox.dataset.prazo || '21';
                queueUpdate('deadline_days', hid.value, 'bloco3');
            }

            setTimeout(() => scheduleRecalc('#prazo_dias:body'), 0);
            if (typeof window.scheduleSync === 'function') window.scheduleSync(900);

            return;
        }
    }, { passive: true });
});


// FLUXO (inalterado)
function getAdicionaisForLead() {
    const picked = getAdic();
    let base = Array.isArray(picked) && picked.length ? picked.slice() : [];
    if (!base.length) {
        const h = document.getElementById('propostas');
        if (h && h.value) {
            try {
                const parsed = JSON.parse(h.value);
                if (Array.isArray(parsed)) base = parsed.slice();
            } catch { }
        }
    }
    const novaPair = `nova_area=${String(getNova() || 'NAO').toUpperCase()}`;
    const merged = [
        ...base.filter(v => !/^nova_area(?:=|$)/i.test(String(v || ''))),
        novaPair
    ];
    return Array.from(new Set(
        merged.filter(v => v != null).map(v => String(v).trim()).filter(Boolean)
    ));
}


// --- UTM helpers -----------------------------------------------------------
const UTM_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'];

function _readCookie(name) {
    const hit = document.cookie.split('; ').find(s => s.startsWith(name + '='));
    return hit ? decodeURIComponent(hit.split('=')[1]) : null;
}
function _readLS(name) {
    try { return localStorage.getItem(name); } catch { return null; }
}
function _getPersisted(key) {
    const fromUrl = new URLSearchParams(location.search).get(key);
    if (fromUrl) return fromUrl;
    const fromCookie = _readCookie(key);
    if (fromCookie) return fromCookie;
    const fromLS = _readLS(key);
    if (fromLS) return fromLS;
    return null;
}

// Coleta só o que vier na URL desta página
function getAllUTMs() {
    const qs = new URLSearchParams(location.search);
    const out = {};
    UTM_KEYS.forEach(k => {
        const v = qs.get(k);
        if (v && v.trim() !== '') out[k] = v;
    });
    return out;
}


// normalizações de nomes de origem utms
const ORIGIN_MAP = {
    casavogue: 'vogue',
    casa_vogue: 'vogue',
    'living-wellness': 'living_wellness',
    lifebylufe: 'lufe',
    revista_haus: 'revista_haus'
};

// Origem: 1) URL atual; 2) se vazio, referrer do MESMO domínio; 3) null
function detectarOrigem() {
    const fromUrl = (new URLSearchParams(location.search).get('utm_source') || '').trim();
    if (fromUrl) return fromUrl;

    try {
        const ref = new URL(document.referrer || '');
        const sameHost =
            ref.hostname.replace(/^www\./, '') === location.hostname.replace(/^www\./, '');
        if (sameHost) {
            const fromRef = (new URLSearchParams(ref.search).get('utm_source') || '').trim();
            if (fromRef) return fromRef;
        }
    } catch (_) { }

    return null;
}





const CHANNEL = 'archa';


async function enviarLead() {

    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('email').value.trim();
    const telefone = document.getElementById('fone').value.trim();
    const metragem = +document.getElementById('area').value;
    const ambientes = +document.getElementById('qtd_amb').value;

    // bloqueia tudo se faltar algo
    if (!nome || !email || !telefone || metragem <= 0 || ambientes <= 0) {
        // só recarrega a tela e NÃO chama a API
        location.reload();
        return;
    }



    // usa SEMPRE o mesmo token do incremental
    const token = await ensureUuid();   // garante/pega leadUuid

    const planCode = state.planCode || planoSelecionado || null;
    const utms = getAllUTMs();
    const origem = detectarOrigem();

    const body = {
        api_token: API_TOKEN,
        token,                      // <- mesmo token (leadUuid)
        produto_code: planCode,
        channel: CHANNEL,
        origem_cliente: origem,
        utms,
        precos: getSeenPrices(),   // ← preços vistos (array de strings)
        silent: false,               // ← primeiro envio é explícito (notifica)
        cliente: {
            nome: document.getElementById('nome').value.trim(),
            email: document.getElementById('email').value.trim(),
            telefone: document.getElementById('fone').value.trim(),
        },
        projeto: {
            metragem: +document.getElementById('area').value,
            ambientes: +document.getElementById('qtd_amb').value,
            prazo_dias: getPrazo(),
            categoria: categoriaApiFromUI(getCat()),
            regiao_arquiteto: getReg(),
            adicionais: getAdicionaisForLead(),
            tipo_projeto: getTipo()
        }
    };

    const res = await fetch(CONFIG.API_LEAD, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-API-TOKEN': API_TOKEN
        },
        body: JSON.stringify(body)
    });

    // mantém consistência de chave no storage
    const js = await res.json().catch(() => ({}));
    if (js?.uuid) {
        localStorage.setItem('leadUuid', js.uuid);
        localStorage.setItem('uuidLaravel', js.uuid); // compat, se algo legado usar
    }
}


/*
async function enviarLead(uuidLaravel) {
    const planCode = state.planCode || planoSelecionado || null;
    const utms = getAllUTMs();             // ← coleta tudo (se houver)
    const origem = detectarOrigem();       // ← mesma origem em todo lugar

    const body = {
        api_token: API_TOKEN,
        token: uuidLaravel,
        produto_code: planCode,
        channel: CHANNEL,
        origem_cliente: origem,
        utms: utms,
        cliente: {
            nome: document.getElementById('nome').value.trim(),
            email: document.getElementById('email').value.trim(),
            telefone: document.getElementById('fone').value.trim(),
        },
        projeto: {
            metragem: +document.getElementById('area').value,
            ambientes: +document.getElementById('qtd_amb').value,
            prazo_dias: getPrazo(),
            categoria: categoriaApiFromUI(getCat()),
            regiao_arquiteto: getReg(),
            adicionais: getAdicionaisForLead(),
            tipo_projeto: getTipo()
        }
    };

    const res = await fetch(CONFIG.API_LEAD, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-API-TOKEN': API_TOKEN
        },
        body: JSON.stringify(body)
    });
    const { uuid } = await res.json().catch(() => ({}));
    if (uuid) localStorage.setItem('leadUuid', uuid);
}*/

/* VER PREÇO (one-shot) — inalterado no fluxo */
const btnVerPreco = document.getElementById('btnVerPreco');
btnVerPreco?.addEventListener('click', async () => {
    if (btnVerPreco.disabled) return;

    const original = btnVerPreco.textContent.trim();
    try {
        btnVerPreco.disabled = true;
        btnVerPreco.innerHTML = `Calculando...
      <span id="spinnerVerPreco" class="spinner-grow spinner-grow-sm ms-2" role="status" aria-hidden="true"></span>`;

        initSelectedPlan();
        await enviarLead();
        const planos = await primeiroCalculo();

        // Libera o cache incremental (front passa a enviar para /api/lead/cache)
        try {
            localStorage.setItem('leadActive', '1');  // chave que o lead-cache.js lê
            window.LEAD_ACTIVE = true;
        } catch (_) { }

        // Obs.: o lead-cache só envia se state.ready === true,
        state.ready = true;
        if (window.sendLeadCache) setTimeout(() => window.sendLeadCache(), 0);

        // Analytics - evento  Start
        try {
            ArchaAnalytics.trackSimulationStart(
                getTipo(),
                +document.getElementById('area')?.value || 0,
                +document.getElementById('qtd_amb')?.value || 0,
                Math.round(+planos?.duo_pack?.a_vista_com_taxa_archa || +planos?.duo || 0),
                true
            );
        } catch (gaErr) { console.warn('[GA] simulation_start falhou:', gaErr); }


        const btnIrPagamento = document.getElementById('btnIrPagamento');
        if (btnIrPagamento) {
            btnIrPagamento.disabled = false;
            btnIrPagamento.classList.remove('btn-disabled', 'text-secondary', 'btn-outline-grey');
            btnIrPagamento.classList.add('btn-azul');
            btnIrPagamento.dataset.flow = '1';
        }
        const btnAvancar3 = document.getElementById('btnAvancar3');
        if (btnAvancar3) {
            btnAvancar3.disabled = false;
            btnAvancar3.classList.remove('btn-disabled');
            btnAvancar3.classList.add('btn-azul');
            btnAvancar3.dataset.flow = '1';
        }

        // flush
        if (pendingFrom) { const f = pendingFrom; pendingFrom = null; scheduleRecalc('flush:' + f); }
        else { scheduleRecalc('post-primeiroCalculo'); }

    } catch (e) {
        alert(e.message || e);
        btnVerPreco.textContent = original;
        btnVerPreco.disabled = false;
        return;
    } finally {
        document.getElementById('spinnerVerPreco')?.remove();
        btnVerPreco.textContent = window.jt('Ver preço');
        btnVerPreco.disabled = true;
        btnVerPreco.classList.add('btn-disabled');
        btnVerPreco.dataset.locked = '1';
    }
});

/* Debug */
window.simulator = { state, cacheLive, primeiroCalculo, scheduleRecalc };







/* ========================================================
 * ENVIANDO CHAMADA A API (SUBMIT MODAL)
 *  - FormData (sem Content-Type manual)
 *  - Accept: application/json (evita redirect HTML/302)
 *  - api_token no body (compat com middleware novo)
 * ====================================================== */
(window.onReady || (fn => (
    document.readyState !== 'loading'
        ? fn()
        : document.addEventListener('DOMContentLoaded', fn)
)))(() => {
    const formPersona = document.getElementById('formPersona');
    if (!formPersona) return;

    const onlyDigits = (s) => (s || '').replace(/\D+/g, '');
    function cpfIsValid(raw) {
        const cpf = onlyDigits(raw);
        if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
        for (let t = 9; t < 11; t++) {
            let d = 0; for (let c = 0; c < t; c++) d += parseInt(cpf[c], 10) * ((t + 1) - c);
            d = ((10 * d) % 11) % 10; if (parseInt(cpf[t], 10) !== d) return false;
        }
        return true;
    }

    const tipoSel = formPersona.querySelector('[name="documento_tipo"]');
    const wrapRazao = document.getElementById('wrapRazao');
    const wrapRep = document.getElementById('wrapRepresentante');
    const razaoInp = formPersona.querySelector('[name="razao_social"]');
    const repCpfInp = formPersona.querySelector('[name="rep_cpf"]');

    function applyTipoPessoa() {
        const isPJ = (tipoSel?.value === 'CNPJ');
        wrapRazao?.classList.toggle('d-none', !isPJ);
        wrapRep?.classList.toggle('d-none', !isPJ);
        if (razaoInp) razaoInp.required = isPJ;
        if (repCpfInp) repCpfInp.required = isPJ;
    }
    applyTipoPessoa();
    tipoSel?.addEventListener('change', applyTipoPessoa);

    formPersona.addEventListener('submit', async (e) => {
        e.preventDefault();
        e.stopImmediatePropagation();

        if (!formPersona.checkValidity()) {
            formPersona.classList.add('was-validated');
            return;
        }

        // garante leadUuid
        if (!localStorage.getItem('leadUuid')) {

            if (!window.state?.planCode && !planoSelecionado) initSelectedPlan();
            await enviarLead();
        }
        const token = localStorage.getItem('leadUuid');
        if (!token) {
            alert(window.jt('Lead não localizado. Clique em "Ver preço" e tente novamente.'));
            return;
        }

        const ov = document.createElement('div');
        ov.id = 'processingOverlay';
        ov.textContent = window.jt('Gerando contrato…');
        document.body.appendChild(ov);

        try {
            const get = (sel) => formPersona.querySelector(sel)?.value?.trim() || null;

            const tipo = get('[name="documento_tipo"]') || 'CPF';
            const docRaw = get('[name="documento"]') || '';
            const docNum = onlyDigits(docRaw);          // CPF ou CNPJ no mesmo campo

            // CPF que iremos enviar SEMPRE (PF usa o próprio documento; PJ usa o do representante)
            let cpfNum = onlyDigits(
                tipo === 'CNPJ' ? (get('[name="rep_cpf"]') || '') : docRaw
            );

            // valida CPF quando for exigido
            if (cpfNum.length) {
                if (!cpfIsValid(cpfNum)) {
                    if (tipo === 'CNPJ') {
                        repCpfInp?.classList.add('is-invalid'); repCpfInp?.focus();
                        throw new Error(window.jt('Informe um CPF válido do representante.'));
                    } else {
                        throw new Error(window.jt('Informe um CPF válido.'));
                    }
                }
            }
            repCpfInp?.classList.remove('is-invalid');

            // --- FormData (sem alterar os demais campos) ---

            const fd = new FormData();
            fd.append('api_token', window.API_TOKEN);
            fd.append('token', token);
            fd.append('produto_code', state.planCode || planoSelecionado || '');
            fd.append('forma_pagamento', 'cartao');
            const origem = detectarOrigem();
            fd.append('origem_cliente', origem);
            fd.append('channel', CHANNEL);



            // cliente.*
            fd.append('cliente[nome]', document.getElementById('nome')?.value.trim() || '');
            fd.append('cliente[email]', document.getElementById('email')?.value.trim() || '');
            fd.append('cliente[telefone]', document.getElementById('fone')?.value.trim() || '');

            fd.append('cliente[documento_tipo]', tipo);   // 'CPF' ou 'CNPJ'
            fd.append('cliente[documento]', docNum); // apenas dígitos (CPF ou CNPJ)

            // se for PJ, envie também o CPF do representante e a razão social
            if (tipo === 'CNPJ') {
                fd.append('rep_cpf', cpfNum);                           // ← só no PJ
                fd.append('cliente[razao_social]', get('[name="razao_social"]') || '');
            }

            // projeto.*
            fd.append('projeto[regiao_arquiteto]', getReg());
            //fd.append('projeto[categoria]', (window.catMap?.[getCat()] || 'Estreantes'));
            fd.append('projeto[categoria]', categoriaApiFromUI(getCat()));
            fd.append('projeto[tipo_projeto]', getTipo());
            fd.append('projeto[metragem]', String(+document.getElementById('area')?.value || 0));
            fd.append('projeto[ambientes]', String(+document.getElementById('qtd_amb')?.value || 0));
            fd.append('projeto[prazo_dias]', String(getPrazo()));
            fd.append('projeto[endereco_obra]', get('#endereco_obra') || '');
            fd.append('projeto[cep]', onlyDigits(get('#cep') || ''));
            fd.append('projeto[estado]', get('#estado') || '');
            fd.append('projeto[pais]', (get('#pais') || 'BR'));          // opcional, mas já envia
            //(getAdic() || []).forEach(v => fd.append('projeto[adicionais][]', v));
            (getAdic() || []).forEach(v => fd.append('projeto[adicionais][]', v));
            fd.append('projeto[adicionais][]', `nova_area=${String(getNova() || 'NAO').toUpperCase()}`);

            const res = await fetch(CONFIG.API_CONTRATAR, {
                method: 'POST',
                body: fd, // mantém FormData
                headers: {
                    'Accept': 'application/json',
                    'X-API-TOKEN': API_TOKEN          // ← obrigatório para passar no middleware
                }
            });

            const json = await res.json().catch(() => ({}));
            if (!res.ok) throw new Error(json.message || 'Erro ao gerar contrato');

            if (json.viewer_url) location.href = json.viewer_url;
            else if (json.embed_url) location.href = json.embed_url;
            else location.href = `/contrato/${json.form_id}/visualizar`;
        } catch (err) {
            console.error(err);
            alert(err.message || 'Falha inesperada');
        } finally {
            ov.remove();
        }
    });
});

// no final do simulador.js OU em regiao.js (carregado após simulador.js)
(function bindRegiao() {
    const boxes = document.querySelectorAll('#regwrap .option-box[data-reg]');
    if (!boxes.length) return;

    const fire = () => {
        if (typeof scheduleRecalc === 'function') scheduleRecalc('#regiao:body');
        if (typeof window.scheduleSync === 'function') window.scheduleSync(900);
    };

    boxes.forEach(el => {
        el.addEventListener('click', () => {
            document.querySelectorAll('#regwrap .option-box').forEach(b => b.classList.remove('selected'));
            el.classList.add('selected');

            const hid = document.getElementById('regiao_key'); // opcional, se existir
            if (hid) hid.value = el.dataset.reg;

            fire();
        });
    });

    // dispara uma vez para refletir o estado inicial
    fire();
})();
