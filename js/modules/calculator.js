/**
 * Motor de cálculo do simulador.
 * POST assíncrono para /pricing/calculator.php.
 */
import Storage from '../utils/storage.js';

const ENDPOINT = './pricing/calculator.php';

function collectFormData() {
    const val = (id) => (document.getElementById(id)?.value ?? '').trim();

    /* Categoria: UI usa ESTREANTES/VERIFICADOS/PREFERIDOS; endpoint espera capitalizado */
    const catMap = { ESTREANTES: 'Estreantes', VERIFICADOS: 'Verificados', PREFERIDOS: 'Preferidos' };
    const catUI  = val('categoria') || 'ESTREANTES';
    const categoria = catMap[catUI.toUpperCase()] || 'Estreantes';

    /* Adicionais vem como JSON string (hidden input #propostas com name=adicionais) */
    let adicionais = [];
    try {
        const raw = val('propostas') || '[]';
        const parsed = JSON.parse(raw);
        if (Array.isArray(parsed)) adicionais = parsed;
    } catch (_) {}

    return {
        metragem:   parseFloat(val('area'))      || 0,
        ambientes:  parseInt(val('qtd_amb'))     || 0,
        prazo:      parseInt(val('prazo_dias'))  || 21,
        regiao:     val('regiao_key')            || 'BRASIL_TODO',
        categoria,
        adicionais,
        tipos:      [val('tipo_projeto') || 'RESIDENCIAL: Apartamento'],
        nova_area:  (val('nova_area') || 'NAO').toUpperCase() === 'SIM' ? 1 : 0,
        origem:     new URLSearchParams(location.search).get('utm_source') || '',
    };
}

async function calcular(params) {
    const body = new URLSearchParams();
    Object.entries(params).forEach(([k, v]) => {
        if (Array.isArray(v)) v.forEach(item => body.append(`${k}[]`, item));
        else body.append(k, String(v));
    });

    const resp = await fetch(ENDPOINT, { method: 'POST', body });
    if (!resp.ok) throw new Error('Erro no cálculo');
    const data = await resp.json();
    if (data.error) throw new Error(data.error);
    return data;
}

async function calcularDoFormulario() {
    const params = collectFormData();
    const result = await calcular(params);
    Storage.precosVistos = result;
    return result;
}

export { calcular, calcularDoFormulario, collectFormData };
