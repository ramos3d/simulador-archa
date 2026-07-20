/**
 * Modalidade: Entrada 25% + Parcelas do saldo (75%).
 */
import Api from '../../utils/api.js';
import Validate from '../../utils/validate.js';
import Format from '../../utils/format.js';
import Analytics from '../analytics.js';
import Storage from '../../utils/storage.js';
import State from '../checkout-state.js';

const POLL_INTERVAL = 3000;
let _pollTimer = null;

// ─── Cálculo ─────────────────────────────────────────────────────────────────

/**
 * Calcula entrada (25%) e saldo (75%).
 *
 * Regras:
 *  - PIX          → base = avista   (sem taxa)
 *  - Cartão Nx    → base = parcelado (inclui taxa de parcelamento)
 *  - Saldo 75%    → sempre sobre parcelado
 */
export function calcEntrada(avista, totalParcelado, saldoParcelas, entradaForma = 'cartao', entradaParcelas = 2) {
    const usaAvista = entradaForma === 'pix';
    const base = usaAvista ? (avista || totalParcelado) : totalParcelado;

    const entrada       = Math.round(base * 0.25);
    const saldo         = Math.round(totalParcelado * 0.75);
    const parcelaSaldo  = Math.round(saldo / (saldoParcelas || 10));
    const parcelaEntrada = Math.round(entrada / (entradaParcelas || 2));

    return { entrada, saldo, parcela: parcelaSaldo, parcelaEntrada, entradaParcelas, usaAvista };
}

// ─── Renderização dos totais ──────────────────────────────────────────────────

export function renderEntradaTotals(avista, totalParcelado, saldoParcelas, entradaForma = 'cartao', entradaParcelas = 2) {
    const r = calcEntrada(avista, totalParcelado, saldoParcelas, entradaForma, entradaParcelas);

    const set    = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt; };
    const show   = (id, vis) => { const el = document.getElementById(id); if (el) el.style.display = vis ? '' : 'none'; };

    // Compat legado
    set('entradaValor',      Format.currency(r.entrada));
    set('saldoTotal',        Format.currency(r.saldo));
    set('saldoParcelas',     String(saldoParcelas));
    set('saldoParcelaValor', Format.currency(r.parcela));
    set('entradaParcelasN',  String(saldoParcelas));

    // Subtotal / saldo
    set('totalParceladoBruto', Format.currency(totalParcelado));
    set('totalSaldoBruto',     Format.currency(r.saldo));

    // ── Hero: "Nx de R$ X" quando cartão, total quando PIX ───────────────────
    const prefixEl    = document.getElementById('totalEntradaParcPrefix');
    const totalLineEl = document.getElementById('totalEntradaValorFullLine');
    const lblEl       = document.getElementById('entradaParcelaLabel');
    const unitEl      = document.getElementById('totalEntradaParcelaUnit');

    if (entradaForma === 'pix') {
        set('totalEntradaValor', Format.currency(r.entrada));
        if (prefixEl)    { prefixEl.style.display = 'none'; }
        if (totalLineEl) { totalLineEl.style.display = 'none'; }
        if (lblEl)       { lblEl.textContent = 'À vista · PIX'; }
        if (unitEl)      { unitEl.textContent = ''; }
    } else {
        // Cartão Nx: hero = por parcela, total abaixo
        set('totalEntradaValor', Format.currency(r.parcelaEntrada));
        if (prefixEl) {
            prefixEl.style.display = '';
            prefixEl.textContent   = `${entradaParcelas}x de`;
        }
        if (totalLineEl) { totalLineEl.style.display = ''; }
        set('totalEntradaValorFull', Format.currency(r.entrada));
        if (lblEl)  { lblEl.textContent = `${entradaParcelas}x no cartão`; }
        if (unitEl) { unitEl.textContent = ''; }
    }

    // Botão CTA
    const btnAmt = document.getElementById('btnFinishAmount');
    if (btnAmt) {
        btnAmt.textContent = entradaForma === 'pix'
            ? `de ${Format.currency(r.entrada)}`
            : `${entradaParcelas}x de ${Format.currency(r.parcelaEntrada)}`;
    }

    // Hidden helpers
    const h1 = document.getElementById('totalParcelado'); if (h1) h1.value = String(totalParcelado);
    const h2 = document.getElementById('totalSaldoParc'); if (h2) h2.value = String(r.parcela);

    return r;
}

// ─── PIX: geração única do QR ─────────────────────────────────────────────────

function renderPixEntrada(pixJson) {
    const qr        = pixJson.qr || {};
    const container = document.getElementById('entrada-pix-qr');
    const payloadEl = document.getElementById('entrada-pix-payload');
    const expiryEl  = document.getElementById('entrada-pix-expiry');
    const copyBtn   = document.getElementById('btnCopyEntradaPix');
    const section   = document.getElementById('entrada-pix-section');

    if (section) section.style.display = 'block';

    if (container) {
        container.innerHTML = qr.encodedImage
            ? `<img src="data:image/png;base64,${qr.encodedImage}" alt="QR Code PIX da entrada" class="img-fluid pix-qr">`
            : '<p class="text-muted small">QR Code gerado. Abra seu banco para pagar.</p>';
    }
    if (payloadEl && qr.payload) {
        payloadEl.textContent = qr.payload;
        if (copyBtn) {
            copyBtn.style.display = 'inline-block';
            copyBtn.onclick = () => {
                navigator.clipboard.writeText(qr.payload).catch(() => {});
                copyBtn.textContent = 'Copiado!';
                setTimeout(() => { copyBtn.textContent = 'Copiar código'; }, 2000);
            };
        }
    }
    if (expiryEl && qr.expirationDate) {
        expiryEl.textContent = 'Válido até: ' + qr.expirationDate;
    }
}

function startPolling(slug, onConfirmed) {
    if (_pollTimer) clearInterval(_pollTimer);
    _pollTimer = setInterval(async () => {
        const { ok, data } = await Api.get(`checkout/${slug}/status`);
        if (ok && (data.status === 'entrada_confirmed' || data.status === 'confirmed')) {
            clearInterval(_pollTimer);
            onConfirmed();
        }
    }, POLL_INTERVAL);
}

function collectCardData() {
    const get = (id) => document.getElementById(id);
    return {
        name:   (get('entradaCardName')?.value   || get('cardName')?.value || '').trim(),
        number: (get('entradaCardNumber')?.value || get('cardNumber')?.value || '').replace(/\s/g, ''),
        cvv:    (get('entradaCardCvv')?.value    || get('cardCvv')?.value || ''),
        expiry: (get('entradaCardExpiry')?.value || get('cardExpiry')?.value || ''),
    };
}

function collectHolder() {
    const get = (id) => document.getElementById(id);
    return {
        nome:         (get('nome')?.value         || '').trim(),
        cpfCnpj:      (get('documento')?.value    || '').replace(/\D/g, ''),
        documentType: (get('documento_tipo')?.value || '').toUpperCase(),
        postalCode:   (get('cep')?.value          || '').replace(/\D/g, ''),
        address:      (get('endereco_obra')?.value || ''),
        addressNumber:(get('numero')?.value        || ''),
        complemento:  (get('complemento')?.value   || ''),
        bairro:       (get('bairro')?.value        || ''),
        estado:       (get('estado')?.value        || ''),
        email:        (get('email')?.value         || '').trim(),
        countryCode:  (get('codigo_pais')?.value   || 'BR').trim().toUpperCase(),
        telefone:     (get('fone')?.value          || '').replace(/\D/g, ''),
    };
}

function validateDocForPix(form) {
    const elTipo = document.getElementById('documento_tipo');
    const elNum  = document.getElementById('documento');
    Validate.clearError(elTipo);
    Validate.clearError(elNum);
    const tipo   = (elTipo?.value || '').toUpperCase();
    const numero = (elNum?.value  || '').replace(/\D/g, '');
    if (tipo !== 'CPF') Validate.setError(elTipo, 'Para PIX, selecione CPF.');
    if (!numero)        Validate.setError(elNum, 'Informe o número do CPF.');
    if (form.querySelector('.is-invalid')) { Validate.scrollToFirstError(form); return null; }
    return { tipo, numero };
}

/**
 * Gera o QR Code PIX da entrada uma única vez.
 * Chamada automática quando o usuário clica no botão PIX.
 */
export async function generatePixQR(slug, avista, totalParcelado, saldoParcelas) {
    const qrEl = document.getElementById('entrada-pix-qr');

    // CPF obrigatório
    const form = document.getElementById('formPersona') || document.body;
    const doc  = validateDocForPix(form);
    if (!doc) {
        if (qrEl) qrEl.innerHTML = '<p class="text-warning small py-2">Preencha seu CPF acima para gerar o QR Code.</p>';
        return { ok: false };
    }

    if (qrEl) qrEl.innerHTML = '<div class="text-muted small py-3"><div class="spinner-border spinner-border-sm me-2"></div>Gerando QR Code...</div>';

    // Sincroniza CPF
    const docResp = await Api.post('checkout/doc', { slug, documento_tipo: doc.tipo, documento_numero: doc.numero });
    if (!docResp.ok || docResp.data.ok === false) {
        if (qrEl) qrEl.innerHTML = `<p class="text-danger small py-2">${docResp.data?.message || 'Erro ao validar CPF.'}</p>`;
        return { ok: false };
    }

    const holder  = collectHolder();
    const { entrada } = calcEntrada(avista, totalParcelado, saldoParcelas, 'pix', 1);

    const resp = await fetch('./api/checkout-entrada.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
            slug,
            modalidade_pagamento: 'entrada_parcelas',
            entrada_percentual:   25,
            entrada_valor:        entrada,
            entrada_parcelas:     1,
            saldo_parcelas:       saldoParcelas,
            entrada_forma:        'pix',
            externalRef:          `Checkout-${slug}`,
            holder,
        }),
    });
    const data = await resp.json().catch(() => ({}));

    if (!resp.ok || data.ok === false) {
        if (qrEl) qrEl.innerHTML = `<p class="text-danger small py-2">${data.message || data.error || 'Não foi possível gerar o PIX.'}</p>`;
        return { ok: false };
    }

    renderPixEntrada(data);
    window._pixQrGenerated = true;

    Storage.thankYouData = {
        nome:      holder.nome,
        ambientes: document.getElementById('sumAmb')?.textContent || '',
        tipo:      document.getElementById('sumTipo')?.textContent || '',
        metragem:  document.getElementById('sumM2')?.textContent || '',
    };

    startPolling(slug, () => { window.location.href = 'thank-you.php'; });
    return { ok: true, polling: true };
}

// ─── Submit principal ─────────────────────────────────────────────────────────

export async function pay(slug, avista, totalParcelado, saldoParcelas, entradaForma, entradaParcelas = 2) {
    const form = document.getElementById('formPersona') || document.body;
    form.classList.add('was-validated');

    const holder  = collectHolder();
    const { entrada } = calcEntrada(avista, totalParcelado, saldoParcelas, entradaForma, entradaParcelas);

    const basePayload = {
        slug,
        modalidade_pagamento: 'entrada_parcelas',
        entrada_percentual:   25,
        entrada_valor:        entrada,
        entrada_parcelas:     entradaForma === 'pix' ? 1 : entradaParcelas,
        saldo_parcelas:       saldoParcelas,
        entrada_forma:        entradaForma,
        externalRef:          `Checkout-${slug}`,
    };

    Analytics.trackPaymentIntent(holder.nome, holder.email, holder.telefone, totalParcelado, `entrada_${entradaForma}`, String(saldoParcelas));

    // ── PIX: se QR já foi gerado, só aguarda polling ──────────────────────────
    if (entradaForma === 'pix') {
        if (window._pixQrGenerated) {
            // QR já exibido, apenas confirma polling
            Storage.thankYouData = {
                nome:      holder.nome,
                ambientes: document.getElementById('sumAmb')?.textContent || '',
                tipo:      document.getElementById('sumTipo')?.textContent || '',
                metragem:  document.getElementById('sumM2')?.textContent || '',
            };
            startPolling(slug, () => { window.location.href = 'thank-you.php'; });
            return { ok: true, polling: true };
        }

        // Gera QR agora (fallback: usuário clicou submit sem clicar PIX antes)
        const doc = validateDocForPix(form);
        if (!doc) return false;

        const docResp = await Api.post('checkout/doc', { slug, documento_tipo: doc.tipo, documento_numero: doc.numero });
        if (!docResp.ok || docResp.data.ok === false) {
            alert(docResp.data.message || 'Não foi possível validar o CPF.');
            return false;
        }

        const resp = await fetch('./api/checkout-entrada.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ ...basePayload, holder }),
        });
        const data = await resp.json().catch(() => ({}));

        if (!resp.ok || data.ok === false) {
            Analytics.trackPaymentStatus(holder.nome, holder.email, holder.telefone, totalParcelado, 'entrada_pix', String(saldoParcelas), 'error');
            alert(data.message || data.error || 'Não foi possível gerar o PIX da entrada.');
            return false;
        }

        renderPixEntrada(data);
        window._pixQrGenerated = true;

        Storage.thankYouData = {
            nome:      holder.nome,
            ambientes: document.getElementById('sumAmb')?.textContent || '',
            tipo:      document.getElementById('sumTipo')?.textContent || '',
            metragem:  document.getElementById('sumM2')?.textContent || '',
        };

        startPolling(slug, () => { window.location.href = 'thank-you.php'; });
        return { ok: true, polling: true };
    }

    // ── CARTÃO ────────────────────────────────────────────────────────────────
    const card = collectCardData();
    const { m: expiryMonth, y: expiryYear } = Validate.parseExpiry(card.expiry);

    const cardNumEl   = document.getElementById('entradaCardNumber') || document.getElementById('cardNumber');
    const cardCvvEl   = document.getElementById('entradaCardCvv')    || document.getElementById('cardCvv');
    const cardExpEl   = document.getElementById('entradaCardExpiry') || document.getElementById('cardExpiry');

    if (!Validate.luhn(card.number))                        { Validate.setError(cardNumEl, 'Número de cartão inválido.'); Validate.scrollToFirstError(form); return false; }
    if (!/^\d{3,4}$/.test(card.cvv))                       { Validate.setError(cardCvvEl,  'CVV inválido.');              Validate.scrollToFirstError(form); return false; }
    if (!Validate.cardExpiry(expiryMonth, expiryYear))      { Validate.setError(cardExpEl,  'Validade inválida.');         Validate.scrollToFirstError(form); return false; }

    const resp = await fetch('./api/checkout-entrada.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify({
            ...basePayload,
            card: { number: card.number, expiryMonth, expiryYear, ccv: card.cvv },
            holder: { ...holder, name: card.name || holder.nome },
        }),
    });
    const data = await resp.json().catch(() => ({}));

    if (!resp.ok || data.ok === false) {
        Analytics.trackPaymentStatus(holder.nome, holder.email, holder.telefone, totalParcelado, 'entrada_card', String(saldoParcelas), 'error');
        return { ok: false, data };
    }

    Analytics.trackPaymentStatus(holder.nome, holder.email, holder.telefone, totalParcelado, 'entrada_card', String(saldoParcelas), 'success');

    Storage.thankYouData = {
        nome:      holder.nome,
        ambientes: document.getElementById('sumAmb')?.textContent || '',
        tipo:      document.getElementById('sumTipo')?.textContent || '',
        metragem:  document.getElementById('sumM2')?.textContent || '',
    };

    return { ok: true, data };
}
