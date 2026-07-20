const Format = {
    currency(value) {
        return Number(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    },

    parseBRL(str) {
        const s = String(str || '0').replace(/[^\d,.-]/g, '').replace('.', '').replace(',', '.');
        const n = Number(s);
        return Number.isFinite(n) ? n : 0;
    },

    phone(value) {
        const d = String(value || '').replace(/\D/g, '');
        if (d.length === 11) return d.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        if (d.length === 10) return d.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
        return value;
    },

    cpf(value) {
        return String(value || '').replace(/\D/g, '').replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    },

    cnpj(value) {
        return String(value || '').replace(/\D/g, '').replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5');
    },

    cardNumber(value) {
        return String(value || '').replace(/\D/g, '').replace(/(.{4})/g, '$1 ').trim();
    },

    cardExpiry(value) {
        return String(value || '').replace(/\D/g, '').replace(/(\d{2})(\d{0,4})/, '$1/$2');
    },
};

export default Format;
