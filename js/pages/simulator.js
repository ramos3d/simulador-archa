/**
 * Inicialização da página do simulador.
 * Gerencia o fluxo do formulário e o envio para o checkout.
 */
import Storage  from '../utils/storage.js';
import { calcularDoFormulario } from '../modules/calculator.js';

const ENDPOINT_START = './api/checkout-start.php';

const onlyDigits = (s) => String(s || '').replace(/\D+/g, '');

function getCookie(name) {
    return document.cookie.split('; ').find(r => r.startsWith(name + '='))?.split('=')[1] || '';
}

/* UTMs: array de "k=v" strings (formato esperado pelo backend) */
function collectUTMs() {
    const qs = new URLSearchParams(location.search);
    const keys = ['utm_source','utm_medium','utm_campaign','utm_content','utm_term'];
    const out = [];
    for (const k of keys) {
        const v = qs.get(k) || getCookie(k) || '';
        if (v) out.push(`${k}=${v}`);
    }
    return out;
}

function ensureLeadUuid() {
    if (!Storage.leadUuid) {
        const uuid = crypto.randomUUID?.() || generateUUID();
        Storage.leadUuid = uuid;
    }
    return Storage.leadUuid;
}

function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
        const r = Math.random() * 16 | 0;
        return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
    });
}

/* Categoria: UI usa maiúsculas (ESTREANTES); backend espera capitalizado (Estreantes) */
function categoriaApiFromUI(ui) {
    const map = { ESTREANTES: 'Estreantes', VERIFICADOS: 'Verificados', PREFERIDOS: 'Preferidos' };
    return map[String(ui || '').toUpperCase()] || 'Estreantes';
}

/* Lê adicionais do DOM (mesma lógica do legado: #workItems .multi-option.selected) */
function readAdicionaisFromDom() {
    const cont = document.getElementById('workItems');
    const keys = Array.from(cont ? cont.querySelectorAll('.multi-option.selected') : [])
        .map(el => el.getAttribute('data-key'))
        .filter(Boolean);

    if (!keys.length) {
        const hid = document.getElementById('propostas');
        if (hid && hid.value) {
            try {
                const parsed = JSON.parse(hid.value);
                if (Array.isArray(parsed)) keys.push(...parsed.filter(Boolean));
                else keys.push(...String(hid.value).split(',').map(s => s.trim()).filter(Boolean));
            } catch {
                keys.push(...String(hid.value).split(',').map(s => s.trim()).filter(Boolean));
            }
        }
    }

    const novaAreaEl = document.getElementById('nova_area');
    const novaArea = (novaAreaEl?.value || 'NAO').toString().toUpperCase();
    return [...keys, `nova_area=${novaArea}`];
}

function collectFormData() {
    const get = (id) => document.getElementById(id)?.value?.trim() || '';

    return {
        nome:        get('nome'),
        email:       get('email'),
        telefone:    get('fone'),
        codigo_pais: get('codigo_pais') || '55',

        metragem:           parseFloat(get('area'))     || 0,
        qtd_ambientes:      parseInt(get('qtd_amb'))    || 0,
        prazo_dias:         parseInt(get('prazo_dias')) || 21,
        categoria_ui:       get('categoria') || 'ESTREANTES',
        regiao_arquiteto:   get('regiao_key') || 'BRASIL_TODO',
        tipo_projeto:       get('tipo_projeto') || 'RESIDENCIAL: Apartamento',
        adicionais:         JSON.stringify(readAdicionaisFromDom()),

        documento_tipo:     get('documento_tipo') || 'CPF',
        documento_numero:   onlyDigits(get('documento')),
        endereco_obra:      get('endereco_obra') || null,
        cep:                onlyDigits(get('cep')) || null,
        cidade:             get('cidade') || null,
        estado:             get('estado') || null,
        pais:               get('pais') || 'BR',
    };
}

async function startCheckout(produto) {
    const token  = ensureLeadUuid();
    const data   = collectFormData();
    const precos = Storage.precosVistos || {};

    const sel = (precos && precos[produto]) ? precos[produto] : null;
    const n2 = (x) => { const n = Number(x); return Number.isFinite(n) ? Math.round(n * 100) / 100 : null; };

    const payload = {
        token,
        channel: 'archa',
        produto_code: produto,

        nome:         data.nome,
        email:        data.email,
        telefone:     data.telefone,
        codigo_pais:  data.codigo_pais,

        metragem:            data.metragem,
        qtd_ambientes:       data.qtd_ambientes,
        prazo_dias:          data.prazo_dias,
        categoria_arquiteto: categoriaApiFromUI(data.categoria_ui),
        regiao_arquiteto:    data.regiao_arquiteto,
        tipo_projeto:        data.tipo_projeto,
        adicionais:          data.adicionais,

        endereco_obra:       data.endereco_obra,
        cep:                 data.cep,
        cidade:              data.cidade,
        estado:              data.estado,
        pais:                data.pais,

        utms: collectUTMs(),

        precos_vistos: precos && Object.keys(precos).length ? precos : null,
        valor_avista:          sel ? n2(sel.avista ?? sel.a_vista) : null,
        valor_parcelado_total: sel ? n2(sel.parcelado ?? sel.parcelado_total) : null,
        valor_parcela:         sel ? n2(sel.parcela ?? ((sel.parcelado || sel.parcelado_total) / 10)) : null,
    };

    /* Reaproveita slug existente, se houver */
    const existingSlug = new URLSearchParams(location.search).get('projeto') || Storage.checkoutSlug;
    if (existingSlug) payload.slug = existingSlug;

    let resp, json;
    try {
        resp = await fetch(ENDPOINT_START, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
        json = await resp.json().catch(() => ({}));
    } catch (e) {
        console.error('[simulator] fetch error', e);
        alert('Erro de rede. Tente novamente.');
        return;
    }

    const slug = json?.checkout?.slug || json?.slug || existingSlug;

    if (!resp.ok || !slug) {
        console.error('[simulator] checkout/start falhou', { status: resp.status, json, payload });
        if (json?.errors) {
            const msgs = Object.entries(json.errors).map(([k, v]) => `${k}: ${Array.isArray(v) ? v.join(', ') : v}`).join('\n');
            alert(`Erros de validação:\n\n${msgs}`);
        } else {
            alert(json?.message || json?.error || 'Não foi possível iniciar o checkout.');
        }
        return;
    }

    Storage.checkoutSlug = slug;
    window.location.href = `checkout.php?projeto=${slug}`;
}

function init() {
    /* "Avançar" do simulador */
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('#btnIrPagamento, .btnIrParaPagamento');
        if (!btn) return;
        if (btn.disabled) return;

        /* Bloqueia se faltar dado básico */
        const get = (id) => document.getElementById(id)?.value?.trim() || '';
        if (!get('nome') || !get('email') || !get('fone') || (+get('area') <= 0) || (+get('qtd_amb') <= 0)) {
            alert('Preencha nome, e-mail, WhatsApp, área e ambientes antes de avançar.');
            return;
        }

        const old = btn.textContent;
        try {
            btn.disabled = true;
            btn.textContent = 'Iniciando…';

            try { await calcularDoFormulario(); }
            catch (err) { console.warn('[simulator] cálculo falhou, continuando com cache', err); }

            const planEl = document.querySelector('.plan.selected, .accordion-item.plan.selected');
            const produto = planEl?.dataset?.produto || Storage.get('selectedPlan') || 'duo';

            await startCheckout(produto);
        } finally {
            btn.textContent = old;
            btn.disabled = false;
        }
    });

    /* Sincroniza Storage quando produto é selecionado */
    document.addEventListener('click', (e) => {
        const plan = e.target.closest('.plan');
        if (!plan) return;
        document.querySelectorAll('.plan').forEach(p => p.classList.remove('selected'));
        plan.classList.add('selected');
        Storage.set('selectedPlan', plan.dataset.produto || 'duo');
    });
}

document.addEventListener('DOMContentLoaded', init);
