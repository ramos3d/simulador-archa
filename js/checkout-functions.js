/**
 * 
 * Funções JS para Checkout
 * checkout-functions.js
 */
// Alternância de método
const btnPayCard = document.getElementById('btnPayCard');
const btnPayPix = document.getElementById('btnPayPix');
const boxCard = document.getElementById('boxCard');
const boxPix = document.getElementById('boxPix');
const agree = document.getElementById('agree');
const btnFinish = document.getElementById('btnFinish');

/* Para controlar quais arquivos importar */
function isCheckoutPage() {
    return location.pathname.split('/').pop() === 'checkout.php';
}


function setPay(method) {
    const card = method === 'card';
    btnPayCard.classList.toggle('active', card);
    btnPayPix.classList.toggle('active', !card);
    boxCard.classList.toggle('d-none', !card);
    boxPix.classList.toggle('d-none', card);
}
btnPayCard?.addEventListener('click', () => setPay('card'));
btnPayPix?.addEventListener('click', () => {
    // Só deixa trocar para PIX se tiver CPF válido
    if (!ensureCpfBeforePix()) return;
    setPay('pix');
});


agree?.addEventListener('change', () => {
    btnFinish.disabled = !agree.checked;
});

// Integra com seus IDs já usados em scripts anteriores (se existirem no storage / DOM)
(function hydrateResumeFromUI() {
    const m2 = +document.getElementById('area')?.value || null;
    const amb = +document.getElementById('qtd_amb')?.value || null;
    const tipo = (document.getElementById('tipo_projeto')?.value || '').split(':').pop()?.trim() || null;
    const ad = document.querySelectorAll('#workItems .multi-option.selected').length;

    if (m2) document.getElementById('sumM2').textContent = `${m2} m²`;
    if (amb) document.getElementById('sumAmb').textContent = amb;
    if (tipo) document.getElementById('sumTipo').textContent = tipo;
    if (ad) document.getElementById('sumAdic').textContent = ad;
})();

// Exemplo de submit 
document.getElementById('formPersona')?.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!agree.checked) return;
    // aqui entra sua função de pagarCartao/pagarPix, etc.
    // window.pagarCartao(...) ou window.pagarPix(...);
});


const cupomInp = document.getElementById('cupom');
const btnAplicar = document.getElementById('btnAplicar');

function updateCupomUI() {
    const raw = cupomInp.value || '';
    cupomInp.value = cupomInp.value.toUpperCase(); // converte para maiúsculas

    const compact = raw.replace(/\s+/g, '');
    cupomInp.classList.toggle('has-value', raw.trim().length > 0);
    btnAplicar.disabled = compact.length < 3;
}

if (cupomInp && btnAplicar) {
    ['input', 'blur'].forEach(ev => cupomInp.addEventListener(ev, updateCupomUI));
    cupomInp.addEventListener('paste', () => setTimeout(updateCupomUI));
    cupomInp.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !btnAplicar.disabled) btnAplicar.click();
    });
    updateCupomUI(); // estado inicial
}



/* ===== Bandeira do cartão (detecta no número e aplica no NOME) ===== */
/* ===== Bandeira do cartão (detecta no número e aplica no PRÓPRIO input #cardNumber) ===== */
const ccNumber = document.getElementById('cardNumber');

function detectCardBrand(digits) {
    const n = (digits || '').replace(/\D/g, '');
    if (/^4\d{6,}$/.test(n)) return 'visa';
    if (/^(5[1-5]\d{4,}|2(2[2-9]\d|[3-6]\d{2}|7[01]\d|720)\d{2,})$/.test(n)) return 'mastercard';
    if (/^3[47]\d{5,}$/.test(n)) return 'amex';
    if (/^(3(0[0-5]|09|6|[89])\d{3,})$/.test(n)) return 'diners';
    return null;
}

function applyBrandToNumberField() {
    if (!ccNumber) return;
    const brand = detectCardBrand(ccNumber.value);

    if (brand) {
        ccNumber.classList.add('cc-brand');
        ccNumber.style.backgroundImage = `url("images/cards/${brand}.svg")`;
    } else {
        ccNumber.style.backgroundImage = 'none';
        ccNumber.classList.remove('cc-brand');
    }
}

// liga nos eventos relevantes
ccNumber?.addEventListener('input', applyBrandToNumberField);
ccNumber?.addEventListener('paste', () => setTimeout(applyBrandToNumberField));
applyBrandToNumberField(); // estado inicial







// === Máscara automática CPF/CNPJ/RG no blur ===========================
const docInput = document.getElementById('documento');
const docSelect = document.getElementById('documento_tipo'); // opcional

const onlyDigitsX = s => (s || '').toUpperCase().replace(/[^0-9X]/g, '');

function detectDocType(raw) {
    const v = onlyDigitsX(raw);
    if (/^\d{14}$/.test(v)) return 'CNPJ';
    if (/^\d{11}$/.test(v)) return 'CPF';
    // RG genérico: 9 posições (permite X como dígito verificador)
    if (/^[0-9]{8}[0-9X]$/.test(v)) return 'RG';
    return null;
}


function ensureCpfBeforePix() {
    const form = document.getElementById('formPersona');
    if (!form || !docInput) return true; // fallback: não trava nada

    // limpa erros anteriores
    docInput.classList.remove('is-invalid');
    docSelect?.classList.remove('is-invalid');

    const raw = docInput.value || '';
    const digits = onlyDigitsX(raw);
    let tipo = (docSelect?.value || '').toUpperCase();

    // se o select não estiver preenchido, tenta detectar pelo número
    if (!tipo && digits) {
        const detected = detectDocType(digits);
        if (detected) tipo = detected.toUpperCase();
    }

    let hasError = false;

    // precisa ter número
    if (!digits) {
        hasError = true;
        docInput.classList.add('is-invalid');
    } else if (digits.length !== 11) {
        // ASAAS: precisa ser CPF, 11 dígitos
        hasError = true;
        docInput.classList.add('is-invalid');
    }

    // e o tipo precisa ser CPF
    if (tipo !== 'CPF') {
        hasError = true;
        if (docSelect) docSelect.classList.add('is-invalid');
    }

    if (hasError) {
        form.classList.add('was-validated'); // faz o Bootstrap exibir .invalid-feedback

        const top = docInput.getBoundingClientRect().top + window.pageYOffset - 20;
        window.scrollTo({ top, behavior: 'smooth' });
        setTimeout(() => docInput.focus({ preventScroll: true }), 250);

        return false;
    }

    // normaliza select para CPF se passou na validação
    if (docSelect) docSelect.value = 'CPF';

    return true;
}

const fmtCPF = n => n.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
const fmtCNPJ = n => n.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
function fmtRG(n) {
    // 9 caracteres (8 dígitos + DV que pode ser número ou X)
    const body = n.slice(0, 8);
    const dv = n.slice(8);
    return body.replace(/(\d{2})(\d{3})(\d{3})/, '$1.$2.$3') + '-' + dv;
}

function maskDocumentoOnBlur() {
    const raw = docInput.value;
    const clean = onlyDigitsX(raw);
    const type = detectDocType(clean);

    if (type === 'CPF') docInput.value = fmtCPF(clean);
    else if (type === 'CNPJ') docInput.value = fmtCNPJ(clean);
    else if (type === 'RG') docInput.value = fmtRG(clean);
    // se não reconheceu, mantém como está

    if (docSelect && type) docSelect.value = type; // atualiza select, se existir
}

docInput?.addEventListener('blur', maskDocumentoOnBlur);

// Fixa a barra "Revisão do projeto" no topo (somente mobile)
(function () {
    if (!window.matchMedia('(max-width: 991.98px)').matches) return;

    const header = document.getElementById('reviewHeader');
    if (!header) return;

    // cria um espaçador (antes do header) para evitar jump de layout
    let spacer = document.getElementById('reviewSpacer');
    if (!spacer) {
        spacer = document.createElement('div');
        spacer.id = 'reviewSpacer';
        header.parentNode.insertBefore(spacer, header);
    }

    // posição original do header no documento
    let anchorY = header.getBoundingClientRect().top + window.scrollY;

    function updateFixed() {
        const shouldFix = window.scrollY >= anchorY;
        if (shouldFix) {
            if (!header.classList.contains('is-fixed')) {
                spacer.style.height = header.offsetHeight + 'px'; // reserva o espaço
                header.classList.add('is-fixed');
            }
        } else {
            if (header.classList.contains('is-fixed')) {
                header.classList.remove('is-fixed');
                spacer.style.height = '0px';
            }
        }
    }

    // recalcula âncora e altura em resize/orientação
    function recalc() {
        // limpa estado para medir corretamente
        header.classList.remove('is-fixed');
        spacer.style.height = '0px';
        anchorY = header.getBoundingClientRect().top + window.scrollY;
        updateFixed();
    }

    window.addEventListener('scroll', updateFixed, { passive: true });
    window.addEventListener('resize', recalc);
    window.addEventListener('orientationchange', recalc);
    updateFixed();
})();

/* Scroll para o primeiro valor invalid */

(function () {
    const form = document.getElementById('formPersona');
    if (!form) return;

    // altura do header/sticky no mobile (se houver)
    function stickyOffset() {
        const el = document.getElementById('reviewHeader');
        return el ? el.offsetHeight : 0;
    }

    form.addEventListener('submit', function (e) {
        // força estilos de validação do BS
        form.classList.add('was-validated');

        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();

            // primeiro inválido dentro do form
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                // garante que qualquer colapse/aba esteja aberta (se usar)
                // firstInvalid.closest('.collapse')?.classList.add('show');

                // rola até o campo com folga para o header sticky
                const top = firstInvalid.getBoundingClientRect().top + window.pageYOffset - (stickyOffset() + 12);
                window.scrollTo({ top, behavior: 'smooth' });

                // foca depois do scroll para não “puxar” de volta
                setTimeout(() => firstInvalid.focus({ preventScroll: true }), 350);

                // opcional: força estilo de inválido, caso não venha automático
                firstInvalid.classList.add('is-invalid');
            }
        }
    }, false);
})();


/* Botao Checkout */
function buildCheckoutStartPayload() {
    const v = id => document.getElementById(id)?.value?.trim() || '';

    const toNum = s => {
        const n = Number(String(s || '').replace(',', '.'));
        return Number.isFinite(n) ? n : 0;
    };

    const produto_code = getProdutoCode();        // já existente
    const forma_pagamento = 'cartao';                // padrão nesta tela
    const pais = 'BR';                    // padrão
    const utms = getUTMs();               // <-- usa a sua função

    const precos_vistos =
        (typeof getSeenPrices === 'function') ? getSeenPrices() : undefined;

    const adicionaisArr =
        (typeof readAdicionaisFromDom === 'function') ? readAdicionaisFromDom() : [];
    const adicionais = JSON.stringify(adicionaisArr); // back espera string JSON

    return {
        // tracking
        token: localStorage.getItem('uuidLaravel') || undefined,
        utms,
        channel: 'archa',

        // contato
        nome: v('nome'),
        email: v('email'),
        telefone: v('fone'),

        // documentos (opc. aqui)
        documento_tipo: v('documento_tipo') || null,
        documento_numero: v('documento') || null,
        razao_social: null,

        // obra (opc. aqui)
        endereco_obra: v('endereco_obra') || null,
        cep: v('cep') || null,
        cidade: v('cidade') || null,
        estado: v('estado') || null,
        pais,

        // simulador (obrigatórios)
        tipo_projeto: v('tipo_projeto') || null,
        metragem: toNum(v('area')),
        qtd_ambientes: toNum(v('qtd_amb')),
        prazo_dias: toNum(v('prazo_dias')) || null,
        adicionais,

        // seleção
        forma_pagamento,
        produto_code,

        // analytics
        precos_vistos
    };
}



/* ====== Máscaras em tempo real: CVV e Validade (MM/AAAA) ====== */
(function initCardMasks(cvvId = 'cardCvv', expiryId = 'cardExpiry') {
    const cvvEl = document.getElementById(cvvId);
    const expEl = document.getElementById(expiryId);

    // --- Helpers
    const onlyDigits = (s) => String(s || '').replace(/\D+/g, '');

    // --- CVV (3–4 dígitos)
    if (cvvEl) {
        cvvEl.setAttribute('inputmode', 'numeric');
        cvvEl.setAttribute('autocomplete', 'cc-csc');
        cvvEl.addEventListener('input', () => {
            cvvEl.value = onlyDigits(cvvEl.value).slice(0, 4);
        });
    }

    // --- Validade (MM/AAAA)
    if (expEl) {
        expEl.setAttribute('placeholder', 'MM/AAAA');
        expEl.setAttribute('inputmode', 'numeric');
        expEl.setAttribute('autocomplete', 'cc-exp');

        // Formata enquanto digita/cola
        expEl.addEventListener('input', () => {
            const digits = onlyDigits(expEl.value).slice(0, 6); // 2 (MM) + 4 (AAAA)
            let out;
            if (digits.length <= 2) {
                out = digits; // ainda digitando o mês
            } else {
                out = digits.slice(0, 2) + '/' + digits.slice(2);
            }

            // Normaliza mês em tempo real (01–12)
            if (out.length >= 2) {
                let mm = out.slice(0, 2).replace(/\D/g, '');
                if (mm.length === 2) {
                    if (mm === '00') mm = '01';
                    if (+mm > 12) mm = '12';
                    out = mm + out.slice(2);
                }
            }

            expEl.value = out;
        });

        // Normaliza ao sair do campo (preenche zero à esquerda no mês, limita ano a 4 dígitos)
        expEl.addEventListener('blur', () => {
            const m = expEl.value.match(/^(\d{1,2})(?:\/(\d{1,4}))?$/);
            if (!m) return;

            let mm = m[1] || '';
            let yyyy = m[2] || '';

            mm = String(Math.max(1, Math.min(12, parseInt(mm || '1', 10)))).padStart(2, '0');
            yyyy = onlyDigits(yyyy).slice(0, 4);

            expEl.value = yyyy ? `${mm}/${yyyy}` : mm; // mantém só mês se o ano não foi informado
        });

        // Impede caracteres não numéricos (permite navegação/edição)
        expEl.addEventListener('keydown', (e) => {
            const allowed =
                ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(e.key) ||
                (e.ctrlKey || e.metaKey); // copiar/colar/selecionar
            if (allowed) return;
            if (!/^\d$/.test(e.key)) e.preventDefault();
        });
    }


    window.getCardExpiryParts = function () {
        if (!expEl) return null;
        const m = expEl.value.match(/^(\d{2})\/(\d{4})$/);
        return m ? { month: +m[1], year: +m[2] } : null;
    };
})();

/* Chame automaticamente ao carregar a página */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {

    });
} else {

}
