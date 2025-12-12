
(function () {
    const $ = (s) => document.querySelector(s);
    const set = (sel, v) => { const el = $(sel); if (el) el.textContent = (v ?? '—'); };
    const setBRL = (sel, v) => {
        const el = $(sel); if (!el) return;
        const n = Number(v || 0);
        el.textContent = n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    };
    const fmtBR = (n) => (Number(n) || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

    const PLANOS = { solo: 'Archa Solo', duo: 'Archa Duo', trio: 'Archa Trio' };

    function normalizeAllCaps(str) {
        if (!str) return '';
        return String(str).replace(/\p{L}+/gu, w => {
            if (/^[\p{Lu}]{4,}$/.test(w)) { const lw = w.toLowerCase(); return lw[0].toUpperCase() + lw.slice(1); }
            return w;
        });
    }
    function formatTipoProjetoLabel(raw) {
        if (!raw) return '—';

        // tudo em minúsculas para normalizar
        const parts = String(raw).toLowerCase().split(':');

        const toTitle = (s) =>
            s.trim()
                .split(/\s+/)
                .map(w => w.charAt(0).toUpperCase() + w.slice(1))
                .join(' ');

        const left = toTitle(parts[0] || '');
        const right = toTitle(parts[1] || '');

        return right ? `${left} - ${right}` : left;
    }

    function getSlug() {
        const qs = new URLSearchParams(location.search);
        let slug = (qs.get('projeto') || '').trim();
        if (!slug) slug = localStorage.getItem('checkoutSlug') || '';
        if (slug && !qs.get('projeto')) {
            qs.set('projeto', slug);
            history.replaceState(null, '', `${location.pathname}?${qs.toString()}`);
        }
        return slug;
    }

    function parseAdicionais(val) {
        if (!val) return [];
        if (Array.isArray(val)) return val;
        if (typeof val === 'string') {
            try { const j = JSON.parse(val); if (Array.isArray(j)) return j; } catch { }
            if (val.includes('","') || val.includes(',')) {
                return val.replace(/^\[|\]$/g, '').replace(/(^"|"$)/g, '').split(/","|",\s*'|,\s*/).filter(Boolean);
            }
        }
        return [];
    }

    // ---- estado local para totais / método de pagamento ----
    let AVISTA = 0;
    let PARC_TOTAL_10X = 0;
    let payMethod = 'card';

    // elementos de totais/botões/parcelas (presentes no checkout)
    const elSubtotal = () => $('#sumSubtotal');
    const elDesc = () => $('#sumDesc');
    const elTotal = () => $('#sumTotal');
    const selInst = () => $('#installments');
    const btnCard = () => $('#btnPayCard');
    const btnPix = () => $('#btnPayPix');

    // monta 1x..10x a partir do total de 10x
    function buildInstallments() {
        const sel = selInst(); if (!sel) return;
        sel.innerHTML = '';
        const frag = document.createDocumentFragment();
        for (let n = 1; n <= 10; n++) {
            const per = PARC_TOTAL_10X ? (PARC_TOTAL_10X / n) : 0;
            const opt = document.createElement('option');
            opt.value = String(n);
            opt.dataset.amount = per.toFixed(2);
            opt.textContent = `${n}x de ${fmtBR(per)}`;
            frag.appendChild(opt);
        }
        sel.appendChild(frag);
        sel.value = '10';
    }

    // aplica totais conforme método
    function applyTotalsFor(method) {
        payMethod = method;

        // Subtotal sempre baseado no total parcelado de 10x (referência)
        if (elSubtotal()) elSubtotal().textContent = fmtBR(PARC_TOTAL_10X);

        if (method === 'pix') {
            const desc = Math.max(0, PARC_TOTAL_10X - AVISTA);
            if (elTotal()) elTotal().textContent = fmtBR(AVISTA);
            if (elDesc()) elDesc().textContent = fmtBR(desc);
            if (selInst()) selInst().disabled = true;
            btnPix()?.classList.add('active'); btnCard()?.classList.remove('active');
        } else {
            if (elTotal()) elTotal().textContent = fmtBR(PARC_TOTAL_10X);
            if (elDesc()) elDesc().textContent = fmtBR(0);
            if (selInst()) selInst().disabled = false;
            btnCard()?.classList.add('active'); btnPix()?.classList.remove('active');
        }

        // expor nos data-* se outras rotinas quiserem ler
        if (elTotal()) {
            elTotal().dataset.avista = String(AVISTA);
            elTotal().dataset.parceladoTotal = String(PARC_TOTAL_10X);
        }
    }

    async function carregarResumo() {
        const slug = getSlug();
        if (!slug) return;

        const url = `./bridge.php?op=editor_show&slug=${encodeURIComponent(slug)}`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const data = await res.json().catch(() => ({}));

        const root = data.checkout || data || {};
        const snap = root.snapshot || {};
        const pick = (k, def = null) => (root[k] ?? snap[k] ?? def);

        const produto_code = String(pick('produto_code', '')).toLowerCase();
        const planoLabel = PLANOS[produto_code] || '—';

        const m2 = Number(pick('metragem', 0));
        const amb = Number(pick('qtd_ambientes', 0));
        const prazo = Number(pick('prazo_dias', 14));

        const regCode = String(pick('regiao_arquiteto', 'BRASIL_TODO')).toUpperCase();
        const regiao = (regCode === 'MINHA_REGIAO') ? 'minha região' : 'todo Brasil';

        const catUi = normalizeAllCaps(pick('categoria', 'verificados'));

        const tipoFull = String(pick('tipo_projeto', '')).trim();
        const tipoLabel = formatTipoProjetoLabel(tipoFull);
        const adicionaisArr = parseAdicionais(pick('adicionais', []));
        const adicCount = adicionaisArr.filter(x => x && !String(x).startsWith('nova_area=')).length;
        const novaFlag = (adicionaisArr.find(x => String(x).startsWith('nova_area=')) || '').split('=')[1] || '';
        const novaAreaTxt = (String(novaFlag).toUpperCase() === 'SIM') ? 'novo ambiente' : 'sem nova área';

        const n = Number(amb) || 0;
        if (n === 1) { set('#sumAmb', `${n} ambiente`); set('#qtd_label', 'a ser projetado'); }
        else { set('#sumAmb', `${n} ambientes`); set('#qtd_label', 'a serem projetados'); }

        // Review (lado direito)
        set('#sumPlano', planoLabel);
        set('#sumRegiao', regiao);
        set('#sumNovaArea', novaAreaTxt);
        set('#sumPrazo', String(prazo || 14));
        set('#sumCategoria', catUi || 'verificados');
        set('#sumM2', `${m2} m²`);
        set('#sumTipo', tipoLabel);
        set('#sumAdic', String(adicCount));

        // Ícone do tipo de projeto
        const iconEl = document.querySelector('#sumTipoIcon');
        if (iconEl) {
            const icon = resolveTipoIcon(tipoFull);
            iconEl.src = icon.src;
            iconEl.alt = icon.alt;
        }


        // ----- Totais
        // 1) tenta direto da API
        let avista = Number(pick('valor_avista', 0));
        let total10 = Number(pick('valor_parcelado_total', 0));

        // 2) fallback em precos_vistos[produto_code]
        if ((!avista || !total10) && (root.precos_vistos || snap.precos_vistos) && produto_code) {
            const seen = (root.precos_vistos && root.precos_vistos[produto_code])
                || (snap.precos_vistos && snap.precos_vistos[produto_code])
                || null;
            if (seen) {
                if (!avista) avista = Number(seen.avista || 0);
                if (!total10) total10 = Number(seen.parcelado_total || 0);
            }
        }

        // 3) atualiza estado e DOM
        AVISTA = avista || 0;
        PARC_TOTAL_10X = total10 || 0;
        // Subtotal sempre é o total de 10x (base)
        setBRL('#sumSubtotal', PARC_TOTAL_10X);
        // Inicialmente em Cartão (Total = total de 10x, Desconto = 0)
        buildInstallments();
        applyTotalsFor(payMethod);
        // guarda slug final
        window.CHECKOUT_SLUG = root.slug || slug;
    }

    // toggles
    document.addEventListener('DOMContentLoaded', () => {
        carregarResumo();
        btnCard()?.addEventListener('click', () => applyTotalsFor('card'));
        btnPix()?.addEventListener('click', () => applyTotalsFor('pix'));
        selInst()?.addEventListener('change', () => {
            // apenas informativo: manter Total como soma (já cuidado em applyTotalsFor)
            // const parcela = Number(selInst().selectedOptions[0].dataset.amount || 0);
            // console.log('Parcela escolhida:', parcela);
        });
    });


    /**
     * Resolve o ícone correto a partir do tipo_projeto
     */
    function resolveTipoIcon(tipoFull) {
        const t = String(tipoFull || '').toUpperCase();

        if (t.includes('APARTAMENTO')) {
            return {
                src: 'images/icons/questao_4/Active/apartamento.png',
                alt: 'Apartamento'
            };
        }
        if (t.includes('CASA')) {
            return {
                src: 'images/icons/questao_4/Active/casa.png',
                alt: 'Casa'
            };
        }
        if (t.includes('HOTELARIA')) {
            return {
                src: 'images/icons/questao_4/Active/hotelaria.png',
                alt: 'Hotelaria'
            };
        }
        if (t.includes('BARES, RESTAURANTES') || t.includes('CASAS NOTURNAS')) {
            return {
                src: 'images/icons/questao_4/Active/bares.png',
                alt: 'Bares, restaurantes e casas noturnas'
            };
        }
        if (t.includes('LOJAS VAREJO')) {
            return {
                src: 'images/icons/questao_4/Active/lojas.png',
                alt: 'Lojas varejo'
            };
        }
        if (t.includes('CLÍNICAS') || t.includes('CLINICAS')) {
            return {
                src: 'images/icons/questao_4/Active/clinicas.png',
                alt: 'Clínicas e espaços estéticos'
            };
        }
        if (t.includes('ESCRITÓRIO') || t.includes('ESCRITORIO')) {
            return {
                src: 'images/icons/questao_4/Active/escritorio.png',
                alt: 'Escritório'
            };
        }
        if (t.includes('ESTANDES')) {
            return {
                src: 'images/icons/questao_4/Active/estandes.png',
                alt: 'Estandes'
            };
        }
        if (t.includes('ESPAÇOS PARA EVENTOS') || t.includes('MASTERPLAN') || t.includes('PALCO')) {
            return {
                src: 'images/icons/questao_4/Active/eventos.png',
                alt: 'Espaços de eventos / masterplan / palco'
            };
        }

        // fallback
        return {
            src: 'images/icon-opcoes-contratacao.png',
            alt: 'Opção de contratação'
        };
    }
})();

