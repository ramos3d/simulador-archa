import Storage from '../utils/storage.js';

document.addEventListener('DOMContentLoaded', () => {
    const data = Storage.thankYouData || {};

    const set = (id, txt) => { const el = document.getElementById(id); if (el && txt) el.textContent = txt; };

    set('tkNome',  data.nome      || (window.jt ? window.jt('Cliente') : 'Cliente'));
    set('tkAmb',   data.ambientes || '');
    set('tkTipo',  data.tipo      || '');
    set('tkM2',    data.metragem  || '');

    Storage.clearThankYou();
});
