const Validate = {
    luhn(num) {
        const s = String(num || '').replace(/\D/g, '');
        let sum = 0, dbl = false;
        for (let i = s.length - 1; i >= 0; i--) {
            let d = +s[i];
            if (dbl) { d *= 2; if (d > 9) d -= 9; }
            sum += d;
            dbl = !dbl;
        }
        return s.length >= 12 && sum % 10 === 0;
    },

    cardExpiry(mm, yyyy) {
        const M = +mm, Y = +yyyy;
        if (!(M >= 1 && M <= 12) || String(yyyy).length !== 4) return false;
        return new Date(Y, M, 0, 23, 59, 59) >= new Date();
    },

    parseExpiry(str) {
        const s = String(str || '').replace(/\s/g, '');
        return { m: s.slice(0, 2).replace(/\D/g, ''), y: s.slice(-4).replace(/\D/g, '') };
    },

    cpf(value) {
        const d = String(value || '').replace(/\D/g, '');
        if (d.length !== 11 || /^(\d)\1+$/.test(d)) return false;
        let sum = 0;
        for (let i = 0; i < 9; i++) sum += +d[i] * (10 - i);
        let r = (sum * 10) % 11; if (r === 10 || r === 11) r = 0;
        if (r !== +d[9]) return false;
        sum = 0;
        for (let i = 0; i < 10; i++) sum += +d[i] * (11 - i);
        r = (sum * 10) % 11; if (r === 10 || r === 11) r = 0;
        return r === +d[10];
    },

    cnpj(value) {
        const d = String(value || '').replace(/\D/g, '');
        if (d.length !== 14 || /^(\d)\1+$/.test(d)) return false;
        const calc = (len) => {
            const weights = len === 12 ? [5,4,3,2,9,8,7,6,5,4,3,2] : [6,5,4,3,2,9,8,7,6,5,4,3,2];
            const sum = weights.reduce((acc, w, i) => acc + +d[i] * w, 0);
            const r = sum % 11;
            return r < 2 ? 0 : 11 - r;
        };
        return calc(12) === +d[12] && calc(13) === +d[13];
    },

    setError(el, msg) {
        if (!el) return;
        el.classList.add('is-invalid');
        if (el.setCustomValidity) el.setCustomValidity(msg);
        const fb = el.parentElement?.querySelector('.invalid-feedback')
            || el.insertAdjacentElement('afterend', Object.assign(document.createElement('div'), { className: 'invalid-feedback' }));
        if (fb) fb.textContent = msg;
    },

    clearError(el) {
        if (!el) return;
        el.classList.remove('is-invalid');
        if (el.setCustomValidity) el.setCustomValidity('');
        const fb = el.parentElement?.querySelector('.invalid-feedback');
        if (fb && !fb.dataset.static) fb.textContent = '';
    },

    scrollToFirstError(container = document.body) {
        const el = container.querySelector(':invalid, .is-invalid');
        if (!el) return false;
        const top = el.getBoundingClientRect().top + window.pageYOffset - 16;
        window.scrollTo({ top, behavior: 'smooth' });
        setTimeout(() => el.focus({ preventScroll: true }), 220);
        return true;
    },
};

export default Validate;
