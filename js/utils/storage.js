/**
 * Abstração de localStorage com namespace e fallback silencioso.
 */
const Storage = {
    get(key, fallback = null) {
        try {
            const v = localStorage.getItem(key);
            if (v === null) return fallback;
            try { return JSON.parse(v); } catch { return v; }
        } catch { return fallback; }
    },

    set(key, value) {
        try {
            localStorage.setItem(key, typeof value === 'string' ? value : JSON.stringify(value));
        } catch { /* storage cheio ou bloqueado */ }
    },

    remove(key) {
        try { localStorage.removeItem(key); } catch { }
    },

    clear() {
        try { localStorage.clear(); } catch { }
    },

    // Atalhos para as chaves principais do simulador
    get leadUuid()      { return this.get('leadUuid') || this.get('uuidLaravel') || ''; },
    set leadUuid(v)     { this.set('leadUuid', v); this.set('uuidLaravel', v); },

    get checkoutSlug()  { return this.get('checkoutSlug') || ''; },
    set checkoutSlug(v) { this.set('checkoutSlug', v); },

    get precosVistos()  { return this.get('precos_vistos') || null; },
    set precosVistos(v) { this.set('precos_vistos', v); },

    get thankYouData()  { return this.get('archa_thankyou') || {}; },
    set thankYouData(v) { this.set('archa_thankyou', v); },
    clearThankYou()     { this.remove('archa_thankyou'); },
};

export default Storage;
