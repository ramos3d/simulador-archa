/**
 * Pagamento via cartão de crédito - total.
 */
import Api from '../../utils/api.js';
import Validate from '../../utils/validate.js';
import Format from '../../utils/format.js';
import Analytics from '../analytics.js';
import Storage from '../../utils/storage.js';

function collectCard() {
    const get = (id) => document.getElementById(id);
    return {
        name:    (get('cardName')?.value     || '').trim(),
        number:  (get('cardNumber')?.value   || '').replace(/\s/g, ''),
        cvv:     (get('cardCvv')?.value      || ''),
        expiry:  (get('cardExpiry')?.value   || ''),
        inst:    Number(get('installments')?.value || 0),
    };
}

function collectHolder() {
    const get = (id) => document.getElementById(id);
    return {
        name:        (get('cardName')?.value   || get('nome')?.value || '').trim(),
        nome:        (get('nome')?.value       || '').trim(),
        cpfCnpj:     (get('documento')?.value || '').replace(/\D/g, ''),
        documentType:(get('documento_tipo')?.value || '').toUpperCase(),
        postalCode:  (get('cep')?.value        || '').replace(/\D/g, ''),
        address:     (get('endereco_obra')?.value || ''),
        addressNumber:(get('numero')?.value    || ''),
        complemento: (get('complemento')?.value || ''),
        bairro:      (get('bairro')?.value     || ''),
        estado:      (get('estado')?.value     || ''),
        email:       (get('email')?.value      || '').trim(),
        countryCode: (get('codigo_pais')?.value|| 'BR').trim().toUpperCase(),
        telefone:    (get('fone')?.value       || '').replace(/\D/g, ''),
    };
}

function validate(card, holder, form) {
    let valid = true;
    const err = (id, msg) => { Validate.setError(document.getElementById(id), msg); valid = false; };

    if (!card.name)                                      err('cardName',    'Informe o nome impresso no cartão.');
    if (!Validate.luhn(card.number))                     err('cardNumber',  'Número de cartão inválido.');
    if (!/^\d{3,4}$/.test(card.cvv))                    err('cardCvv',     'CVV deve ter 3 ou 4 dígitos.');
    const { m, y } = Validate.parseExpiry(card.expiry);
    if (!Validate.cardExpiry(m, y))                     err('cardExpiry',  'Validade inválida ou expirada (MM/AAAA).');
    if (!(card.inst >= 1))                               err('installments','Selecione as parcelas.');
    if (!['CPF','CNPJ'].includes(holder.documentType))  err('documento_tipo','Selecione CPF ou CNPJ.');
    if (!holder.cpfCnpj)                                err('documento',   'Informe o CPF/CNPJ do titular.');

    if (!valid) Validate.scrollToFirstError(form);
    return valid;
}

export async function pay(slug, totalParcelado) {
    const form    = document.getElementById('formPersona') || document.body;
    form.classList.add('was-validated');

    const card   = collectCard();
    const holder = collectHolder();

    if (!validate(card, holder, form)) return false;

    const { m: expiryMonth, y: expiryYear } = Validate.parseExpiry(card.expiry);

    Analytics.trackPaymentIntent(holder.nome, holder.email, holder.telefone, totalParcelado, 'card', String(card.inst));

    const { ok, data } = await Api.post('checkout/card', {
        slug,
        installments: card.inst,
        externalRef: `Checkout-${slug}`,
        card: { number: card.number, expiryMonth, expiryYear, ccv: card.cvv },
        holder,
    });

    if (!ok || data.ok === false) {
        Analytics.trackPaymentStatus(holder.nome, holder.email, holder.telefone, totalParcelado, 'card', String(card.inst), 'error');
        return { ok: false, data };
    }

    Analytics.trackPaymentStatus(holder.nome, holder.email, holder.telefone, totalParcelado, 'card', String(card.inst), 'success');

    Storage.thankYouData = {
        nome:      holder.nome,
        ambientes: document.getElementById('sumAmb')?.textContent || '',
        tipo:      document.getElementById('sumTipo')?.textContent || '',
        metragem:  document.getElementById('sumM2')?.textContent || '',
    };

    return { ok: true, data };
}
