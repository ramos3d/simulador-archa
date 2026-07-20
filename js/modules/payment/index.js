/**
 * Orquestrador de pagamento.
 * Decide qual módulo usar com base na modalidade selecionada.
 */
import State from '../checkout-state.js';
import Format from '../../utils/format.js';
import { pay as payCard }   from './card.js';
import { pay as payPix }    from './pix.js';
import { pay as payEntrada, renderEntradaTotals } from './entrada.js';

function showOverlay(msg = 'Processando...') {
    let el = document.getElementById('payOverlay');
    if (!el) {
        el = document.createElement('div');
        el.id = 'payOverlay';
        el.innerHTML = `<div class="pay-overlay-inner"><div class="spinner-border text-light mb-3"></div><p>${msg}</p></div>`;
        document.body.appendChild(el);
    } else {
        el.querySelector('p').textContent = msg;
    }
    el.style.display = 'flex';
}

function hideOverlay() {
    const el = document.getElementById('payOverlay');
    if (el) el.style.display = 'none';
}

function setButtonState(loading) {
    const btn = document.getElementById('btnFinish');
    if (!btn) return;
    btn.disabled = loading;
    btn.textContent = loading ? 'Processando...' : 'Finalizar pagamento';
}

function showInlineError(msg) {
    document.getElementById('formError')?.remove();
    const el = document.createElement('div');
    el.id = 'formError';
    el.className = 'alert alert-danger mt-3';
    el.textContent = msg;
    document.getElementById('btnFinish')?.closest('.ck-body')?.appendChild(el);
}

// ─── Inicialização da UI ──────────────────────────────────────────────────────

export function initPaymentUI() {
    const { parcelado, avista } = State.getValores();

    // Constrói parcelas do seletor cartão total (1-10x)
    buildInstallmentsSelect('installments', parcelado, 1, 10);

    // Listeners dos botões de método (total)
    document.getElementById('btnPayCard')?.addEventListener('click', () => activateMethod('card'));
    document.getElementById('btnPayPix')?.addEventListener('click',  () => activateMethod('pix'));

    // Listeners da modalidade
    document.querySelectorAll('input[name="modalidade_pagamento"]').forEach(r => {
        r.addEventListener('change', () => activateModalidade(r.value));
    });

    // Seletor de parcelas do saldo (entrada) — escondido no novo fluxo
    document.getElementById('saldoParcelasSelect')?.addEventListener('change', (e) => {
        const n = parseInt(e.target.value) || 10;
        State.setSaldoParcelas(n);
        const forma = window.__entradaForma || 'cartao';
        const parcs = parseInt(document.getElementById('entradaParcelasSelect')?.value || '2');
        renderEntradaTotals(avista, parcelado, n, forma, parcs);
    });

    // Método de pagamento da entrada (PIX/cartão)
    document.querySelectorAll('input[name="entrada_forma"]').forEach(r => {
        r.addEventListener('change', () => {
            State.setEntradaForma(r.value);
            toggleEntradaMetodoUI(r.value);
        });
    });

    // Estado inicial: respeita qual radio veio marcado no HTML
    const initialMode = document.querySelector('input[name="modalidade_pagamento"]:checked')?.value || 'entrada_parcelas';
    activateMethod('card');                 // default interno
    activateModalidade(initialMode);         // aplica a modalidade real (e ajusta totais)
}

function buildInstallmentsSelect(id, total, min, max) {
    const sel = document.getElementById(id);
    if (!sel || !total || sel.dataset.built === '1') return;
    sel.innerHTML = '';
    for (let n = min; n <= max; n++) {
        const opt = document.createElement('option');
        opt.value = String(n);
        opt.textContent = `${n}x de ${Format.currency(total / n)}`;
        sel.appendChild(opt);
    }
    sel.value = String(max);
    sel.dataset.built = '1';
}

function activateModalidade(mode) {
    State.setModalidade(mode);
    document.getElementById('boxPaymentTotal')?.classList.toggle('d-none',   mode !== 'total');
    document.getElementById('boxPaymentEntrada')?.classList.toggle('d-none', mode !== 'entrada_parcelas');

    const { avista, parcelado } = State.getValores();
    if (mode === 'entrada_parcelas') {
        const forma = window.__entradaForma || 'cartao';
        const parcs = parseInt(document.getElementById('entradaParcelasSelect')?.value || '2');
        renderEntradaTotals(avista, parcelado, State.saldoParcelas, forma, parcs);
    }
    updateBigTotalFromState();
}

function activateMethod(method) {
    document.getElementById('btnPayCard')?.classList.toggle('active', method === 'card');
    document.getElementById('btnPayPix')?.classList.toggle('active',  method === 'pix');
    document.getElementById('boxCard')?.classList.toggle('d-none', method !== 'card');
    document.getElementById('boxPix')?.classList.toggle('d-none',  method !== 'pix');
    document.body.dataset.payMethod = method;
    if (State.modalidade === 'total') updateBigTotal(method);
}

function toggleEntradaMetodoUI(forma) {
    document.getElementById('boxEntradaCard')?.classList.toggle('d-none', forma !== 'cartao');
    document.getElementById('boxEntradaPix')?.classList.toggle('d-none',  forma !== 'pix');
    document.getElementById('entrada-pix-section')?.style && (document.getElementById('entrada-pix-section').style.display = 'none');
}

function updateBigTotal(method) {
    const el = document.getElementById('sumTotal');
    if (!el) return;
    const { avista, parcelado } = State.getValores();
    el.textContent = Format.currency(method === 'pix' ? avista : parcelado);
}

function updateBigTotalFromState() {
    const { modalidade } = State;
    if (modalidade === 'entrada_parcelas') {
        const el = document.getElementById('sumTotal');
        if (el) el.textContent = Format.currency(State.getValores().parcelado);
    } else {
        updateBigTotal(document.body.dataset.payMethod || 'card');
    }
}

// ─── Submit ───────────────────────────────────────────────────────────────────

export async function submit() {
    const slug       = State.slug;
    const modalidade = State.modalidade;
    const { avista, parcelado } = State.getValores();

    if (!slug) { alert('Projeto não identificado. Recarregue e tente novamente.'); return; }

    setButtonState(true);
    showOverlay();

    try {
        let result;

        if (modalidade === 'entrada_parcelas') {
            showOverlay(State.entradaForma === 'pix' ? 'Gerando PIX da entrada...' : 'Processando entrada...');
            result = await payEntrada(slug, avista, parcelado, State.saldoParcelas, State.entradaForma, State.entradaParcelas);
        } else if (document.body.dataset.payMethod === 'pix') {
            showOverlay('Gerando PIX, aguarde...');
            result = await payPix(slug, avista);
        } else {
            showOverlay('Processando pagamento, aguarde...');
            result = await payCard(slug, parcelado);
        }

        if (!result || result === false) return;

        if (result.polling) {
            hideOverlay();
            // Mantém o botão desabilitado enquanto aguarda polling
            return;
        }

        if (result.ok) {
            window.location.href = 'thank-you.php';
        } else {
            const msg = result.data?.message || result.data?.error || 'Não foi possível processar o pagamento.';
            showInlineError(msg);
        }
    } catch (err) {
        showInlineError(err?.message || 'Falha de rede. Tente novamente.');
    } finally {
        if (!window.location.href.includes('thank-you')) {
            hideOverlay();
            setButtonState(false);
        }
    }
}
