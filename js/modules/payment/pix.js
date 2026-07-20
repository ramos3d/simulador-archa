/**
 * Pagamento via PIX - total.
 * Gera QR code e faz polling até confirmação.
 */
import Api from '../../utils/api.js';
import Validate from '../../utils/validate.js';
import Analytics from '../analytics.js';
import Storage from '../../utils/storage.js';

const POLL_INTERVAL = 3000;
let   _pollTimer    = null;

function validateDoc(form) {
    const elTipo = document.getElementById('documento_tipo');
    const elNum  = document.getElementById('documento');
    Validate.clearError(elTipo);
    Validate.clearError(elNum);

    const tipo   = (elTipo?.value || '').toUpperCase();
    const numero = (elNum?.value  || '').replace(/\D/g, '');

    if (tipo !== 'CPF') { Validate.setError(elTipo, 'Para PIX, selecione CPF.'); }
    if (!numero)        { Validate.setError(elNum,  'Informe o número do CPF.'); }

    if (form.querySelector('.is-invalid')) {
        Validate.scrollToFirstError(form);
        return null;
    }
    return { tipo, numero };
}

function renderQr(pixJson) {
    const qr          = pixJson.qr || {};
    const container   = document.getElementById('pix-qr-container');
    const payloadEl   = document.getElementById('pix-payload');
    const expiryEl    = document.getElementById('pix-expiration');
    const copyBtn     = document.getElementById('btnCopyPix');
    const pixSection  = document.getElementById('pix-section');

    if (pixSection) pixSection.style.display = 'block';

    if (container) {
        container.innerHTML = qr.encodedImage
            ? `<img src="data:image/png;base64,${qr.encodedImage}" alt="QR Code PIX" class="img-fluid pix-qr">`
            : '<p class="text-muted">QR Code gerado. Abra seu banco para pagar.</p>';
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
    stopPolling();
    _pollTimer = setInterval(async () => {
        const { ok, data } = await Api.get(`checkout/${slug}/status`);
        if (ok && data.status === 'confirmed') {
            stopPolling();
            onConfirmed();
        }
    }, POLL_INTERVAL);
}

function stopPolling() {
    if (_pollTimer) { clearInterval(_pollTimer); _pollTimer = null; }
}

export async function pay(slug, totalAvista) {
    const form = document.getElementById('formPersona') || document.body;
    form.classList.add('was-validated');

    const doc = validateDoc(form);
    if (!doc) return false;

    const nome     = document.getElementById('nome')?.value?.trim() || '';
    const email    = document.getElementById('email')?.value?.trim() || '';
    const telefone = document.getElementById('fone')?.value?.replace(/\D/g, '') || '';

    // 1. Sincroniza CPF no checkout
    const docResp = await Api.post('checkout/doc', { slug, documento_tipo: doc.tipo, documento_numero: doc.numero });
    if (!docResp.ok || docResp.data.ok === false) {
        alert(docResp.data.message || 'Não foi possível validar o documento.');
        return false;
    }

    Analytics.trackPaymentIntent(nome, email, telefone, totalAvista, 'pix', '');

    // 2. Gera QR Code
    const pixResp = await Api.post('checkout/pix', { slug, externalRef: `Checkout-${slug}` });
    if (!pixResp.ok || pixResp.data.ok === false) {
        Analytics.trackPaymentStatus(nome, email, telefone, totalAvista, 'pix', '', 'error');
        alert(pixResp.data.message || pixResp.data.error || 'Não foi possível gerar o PIX.');
        return false;
    }

    Analytics.trackPaymentStatus(nome, email, telefone, totalAvista, 'pix', '', 'success');
    renderQr(pixResp.data);

    // 3. Polling até confirmação
    Storage.thankYouData = {
        nome,
        ambientes: document.getElementById('sumAmb')?.textContent || '',
        tipo:      document.getElementById('sumTipo')?.textContent || '',
        metragem:  document.getElementById('sumM2')?.textContent || '',
    };

    startPolling(slug, () => { window.location.href = 'thank-you.php'; });

    return { ok: true, polling: true };
}

export { stopPolling };
