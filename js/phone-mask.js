
async function loadPhoneCodes() {
    const res = await fetch('/json/phone_codes.json');
    return await res.json();
}

function applyMask(value) {
    // remove tudo que não for número
    let digits = value.replace(/\D/g, '');

    // se começar com 55 (Brasil), formata como +55 (XX) XXXXX-XXXX
    if (digits.startsWith('55')) {
        return digits
            .replace(/^(\d{2})(\d{2})(\d{5})(\d{4}).*/, '+$1 ($2) $3-$4');
    }

    // se começar com 1 (EUA/Canadá), formata como +1 (XXX) XXX-XXXX
    if (digits.startsWith('1')) {
        return digits
            .replace(/^(\d)(\d{3})(\d{3})(\d{4}).*/, '+$1 ($2) $3-$4');
    }

    // se começar com 33 (França), formata como +33 X XX XX XX XX
    if (digits.startsWith('33')) {
        return digits
            .replace(/^(\d{2})(\d)(\d{2})(\d{2})(\d{2})(\d{2}).*/, '+$1 $2 $3 $4 $5 $6');
    }

    // se começar com 351 (Portugal), formata como +351 XXXX-XXXXXX
    if (digits.startsWith('351')) {
        return digits
            .replace(/^(\d{3})(\d{4})(\d{5}).*/, '+$1 $2-$3');
    }

    // fallback → só retorna com +
    return '+' + digits;
}

document.addEventListener("DOMContentLoaded", () => {
    const foneInput = document.getElementById("fone");

    foneInput.addEventListener("input", e => {
        const cursor = foneInput.selectionStart; // posição do cursor
        const formatted = applyMask(foneInput.value);
        foneInput.value = formatted;
        foneInput.setSelectionRange(cursor, cursor);
    });
});

