/**
 * Inicialização da página de checkout.
 * Orquestra: carregamento dos dados, UI de pagamento e submit.
 */
import State    from '../modules/checkout-state.js';
import Format   from '../utils/format.js';
import Storage  from '../utils/storage.js';
import Api      from '../utils/api.js';
import Analytics from '../modules/analytics.js';
import { initPaymentUI, submit } from '../modules/payment/index.js';
import { renderEntradaTotals, generatePixQR } from '../modules/payment/entrada.js';

async function loadCheckoutData(slug) {
    /* Usa bridge.php para preservar o token X-API-TOKEN server-side */
    const url = `./bridge.php?op=checkout_show&slug=${encodeURIComponent(slug)}`;
    try {
        const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const data = await resp.json().catch(() => null);
        if (!resp.ok || !data) {
            console.warn('[checkout] loadCheckoutData falhou', resp.status, data);
            return null;
        }
        return data;
    } catch (e) {
        console.error('[checkout] loadCheckoutData erro', e);
        return null;
    }
}

function populateClientForm(data) {
    const setVal = (id, v) => {
        const el = document.getElementById(id);
        if (el && v != null && v !== '') el.value = v;
    };

    setVal('nome',        data.nome);
    setVal('email',       data.email);
    setVal('fone',        data.telefone);
    setVal('codigo_pais', data.codigo_pais || '55');

    setVal('endereco_obra', data.endereco_obra);
    setVal('cep',           data.cep);
    setVal('cidade',        data.cidade);
    setVal('bairro',        data.bairro);
    setVal('complemento',   data.complemento);

    if (data.estado) {
        const sel = document.getElementById('estado');
        if (sel) sel.value = data.estado;
    }

    setVal('documento',      data.documento_numero);
    if (data.documento_tipo) {
        const sel = document.getElementById('documento_tipo');
        if (sel) sel.value = data.documento_tipo;
    }
}

function populateReview(data) {
    const set = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt; };

    const prod = (data.produto_code || 'duo').toLowerCase();
    set('sumPlano',     `Archa ${prod.charAt(0).toUpperCase() + prod.slice(1)}`);
    set('sumRegiao',    data.regiao_arquiteto === 'MINHA_REGIAO' ? window.jt('da minha região') : window.jt('todo Brasil'));
    set('sumPrazo',     data.prazo_dias ?? '21');
    set('sumCategoria', window.jt(data.categoria_arquiteto ?? 'Estreantes'));
    set('sumM2',        `${data.metragem ?? '?'} m²`);
    set('sumAmb',       data.qtd_ambientes ?? '?');
    set('sumTipo',      window.jt(data.tipo_projeto ?? ''));

    const adics = Array.isArray(data.adicionais) ? data.adicionais.length : 0;
    set('sumAdic', String(adics));
}

function populatePrecos(data) {
    const pv = data.precos_vistos || Storage.precosVistos;
    if (!pv) {
        /* Sem preços calculados: ainda tenta usar valor_avista/valor_parcelado_total do checkout */
        const av = Number(data.valor_avista) || 0;
        const pa = Number(data.valor_parcelado_total) || 0;
        if (av || pa) {
            State.setPrecos({ [(data.produto_code||'duo').toLowerCase()]: { avista: av, parcelado: pa } });
            State.setProduto((data.produto_code || 'duo').toLowerCase());
            renderEntradaTotals(av, pa, State.saldoParcelas, window.__entradaForma || 'cartao', 2);
        }
        return;
    }

    State.setPrecos(pv);
    State.setProduto((data.produto_code || 'duo').toLowerCase());

    const { avista, parcelado } = State.getValores();

    const elTotal = document.getElementById('sumTotal');
    if (elTotal) {
        elTotal.dataset.avista         = String(avista);
        elTotal.dataset.parceladoTotal = String(parcelado);
    }

    const forma = window.__entradaForma || 'cartao';
    const parcs = parseInt(document.getElementById('entradaParcelasSelect')?.value || '2');
    renderEntradaTotals(avista, parcelado, State.saldoParcelas, forma, parcs);
}

/* Re-renderiza ao mudar método (PIX/cartão) ou parcelas da entrada */
function bindEntradaUpdates() {
    document.addEventListener('entrada:metodo', (e) => {
        State.setEntradaForma(e.detail?.forma || window.__entradaForma || 'cartao');
        const { avista, parcelado } = State.getValores();
        const parcs = parseInt(document.getElementById('entradaParcelasSelect')?.value || '2');
        renderEntradaTotals(avista, parcelado, State.saldoParcelas, State.entradaForma, parcs);
    });
    document.addEventListener('entrada:parcelas', (e) => {
        const parcs = e.detail?.parcelas || parseInt(document.getElementById('entradaParcelasSelect')?.value || '2');
        State.setEntradaParcelas(parcs);
        const { avista, parcelado } = State.getValores();
        renderEntradaTotals(avista, parcelado, State.saldoParcelas, State.entradaForma, parcs);
    });

    /* Gera QR PIX uma única vez quando solicitado */
    document.addEventListener('pix:generate', async () => {
        if (window._pixQrGenerated) return;
        const { avista, parcelado } = State.getValores();
        await generatePixQR(State.slug, avista, parcelado, State.saldoParcelas);
    });
}

async function init() {
    const { slug } = State.init();

    if (!slug) {
        document.querySelector('main')?.insertAdjacentHTML('afterbegin',
            `<div class="alert alert-warning m-4">${window.jt('Projeto não encontrado.')} <a href="index.php">${window.jt('Voltar ao simulador')}</a>.</div>`
        );
        return;
    }

    // Carrega dados do checkout do backend
    const data = await loadCheckoutData(slug);
    if (data) {
        populateClientForm(data);
        populateReview(data);
        populatePrecos(data);
    }

    initPaymentUI();
    bindEntradaUpdates();

    // Listener de submit global
    document.addEventListener('checkout:submit', async () => {
        const entradaForma     = window.__entradaForma || 'cartao';
        const entradaParcelas  = parseInt(document.getElementById('entradaParcelasSelect')?.value || '2');
        const saldoN           = parseInt(document.getElementById('saldoParcelasSelect')?.value || '10');

        State.setSaldoParcelas(saldoN);
        State.setEntradaForma(entradaForma);
        State.setEntradaParcelas(entradaParcelas);

        await submit();
    });

    Analytics.trackCheckoutView(window.location.href);
}

document.addEventListener('DOMContentLoaded', init);
