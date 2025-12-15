/* =====================================================================
 * Calculator — motor de cálculo (compat) usando endpoint PHP
 *  - expõe: window.Calculator (mesmas assinaturas do legado)
 *  - POST síncrono para /calculo-precificacao.php (retorno imediato)
 *  - mantém compat: window.catMap e window.mapUI
 * =================================================================== */
(function (global) {
    "use strict";

    /* ─────────────────────── Compat UI (globais) ─────────────────────── */
    const mapUI = {
        marcenaria: 'USO DE MARCENARIA',
        marmore: 'USO DE MÁRMORE OU GRANITO',
        pinturas: 'PINTURAS DE PAREDES E/OU PISOS',
        altura_teto: 'ALTERAÇÃO DA ALTURA DO TETO',
        novos_pisos: 'NOVOS PISOS DE MADEIRA, PORCELANATO OU OUTRO TIPO',
        eletro: 'ELETRODOMÉSTICOS, LUMINÁRIAS E/OU LÂMPADAS',
        tomadas: 'TOMADAS E/OU INTERRUPTORES',
        chuveiro: 'CHUVEIROS, TORNEIRAS E/OU VASO SANITÁRIO',
        paredes: 'PAREDES DE ALVENARIA OU DRYWALL'
    };


    global.mapUI = global.mapUI || mapUI;
    // global.catMap = global.catMap || catMap;

    /* ───────────────────── Config ─────────────────────
     * Você pode forçar via window.CALC_ENDPOINT = '/meu/path.php'
     */
    const ENDPOINTS = [
        (typeof global.CALC_ENDPOINT === 'string' && global.CALC_ENDPOINT) || '/calculo-precificacao.php',
        'calculo-precificacao.php' // fallback relativo
    ];

    /* ───────────────────── Helpers ───────────────────── */
    function normalizeCategoriaNome(s) {
        const t = String(s ?? '').trim().toLowerCase();
        if (t === 'estreantes') return 'Estreantes';
        if (t === 'verificados') return 'Verificados';
        if (t === 'preferidos') return 'Preferidos';
        return 'Estreantes'; // default
    }
    function u(v) { return v == null ? '' : String(v); }

    /* Mapa: chave da UI -> código aceito pelo PHP */
    const ADDS_CODE_MAP = {
        marcenaria: 'MARCENARIA',
        marmore: 'MARMORE_GRANITO',
        pinturas: 'PINTURA_PAREDES_PISOS',
        altura_teto: 'ALTERACAO_TETO',
        novos_pisos: 'NOVOS_PISOS',
        eletro: 'ELETRODOMESTICOS',
        tomadas: 'TOMADAS_INTERRUPTORES',
        chuveiro: 'CHUVEIROS_SANITARIO',
        paredes: 'PAREDES_ALVENARIA_DRYWALL',
    };

    /* tenta normalizar o que vier (key da UI, label, ou já-código) */
    function normalizeAdicionais(list) {
        const arr = Array.isArray(list) ? list : [];
        return arr.map((item) => {
            if (!item) return null;
            const s = String(item);

            // 1) já é código? (ex.: MARCENARIA)
            if (/^[A-Z0-9_]+$/.test(s)) return s;

            // 2) key da UI? (ex.: marcenaria)
            const k = s.toLowerCase().trim();
            if (ADDS_CODE_MAP[k]) return ADDS_CODE_MAP[k];

            // 3) label da UI? (ex.: "USO DE MARCENARIA")
            for (const uiKey in mapUI) {
                if (mapUI[uiKey] && mapUI[uiKey].toLowerCase() === k) {
                    return ADDS_CODE_MAP[uiKey] || null;
                }
            }
            return null;
        }).filter(Boolean);
    }

    /* Constrói o payload aceito pelo endpoint a partir do "input" legado */
    function buildPayload(input) {

        const categoria = normalizeCategoriaNome(input?.categoria ?? input?.categoriaUI ?? 'Estreantes');

        const metragem = Math.max(20, +(input?.metragem ?? input?.m2 ?? 20));
        const ambientes = +(input?.ambientes ?? input?.amb ?? 0);
        const prazo = +(input?.prazoDias ?? input?.prazo ?? 21);
        const regiao = u(input?.regiaoKey || 'BRASIL_TODO');

        // tipos → endpoint aceita múltiplos; se vier 1, viramos array
        let tipos = input?.tipos;
        if (!Array.isArray(tipos) || !tipos.length) {
            tipos = [u(input?.tipoProjeto || input?.tipo || 'RESIDENCIAL: Apartamento')];
        }

        // adicionais (pode vir como adicionaisKeys do front)
        const addsRaw = Array.isArray(input?.adicionaisKeys) ? input.adicionaisKeys
            : (Array.isArray(input?.adicionais) ? input.adicionais : []);
        const adicionaisCodes = normalizeAdicionais(addsRaw);

        const nova_area = (u(input?.novaAreaSN).toUpperCase() === 'SIM' || !!input?.novaArea) ? 1 : 0;

        // Formato x-www-form-urlencoded (inclui arrays tipos[] / adicionais[])
        const params = new URLSearchParams();
        params.append('_', String(Date.now())); // anti-cache
        params.append('metragem', String(metragem));
        params.append('ambientes', String(ambientes));
        params.append('categoria', categoria);
        params.append('prazo', String(prazo));
        params.append('regiao', regiao);
        params.append('nova_area', String(nova_area));

        tipos.forEach(t => params.append('tipos[]', String(t)));

        if (adicionaisCodes.length) {
            adicionaisCodes.forEach(a => params.append('adicionais[]', a));
        } else {
            // mantém compat com o endpoint do teste (quando nenhum adicional)
            params.append('sem_adicionais', '1');
        }

        return params;
    }


    // POST síncrono (compat com fluxo antigo que espera retorno imediato)
    function postSync(url, urlSearchParams) {
        try {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', url, false); // síncrono
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.send(urlSearchParams.toString());
            if (xhr.status >= 200 && xhr.status < 300) {
                try { return JSON.parse(xhr.responseText); } catch (e) { console.warn('[Calculator] JSON inválido do endpoint', e); }
            } else {
                console.warn('[Calculator] HTTP ' + xhr.status + ' em ' + url);
            }
        } catch (err) {
            console.warn('[Calculator] Falha ao POSTar para ' + url, err);
        }
        return null;
    }

    // Tenta em múltiplos caminhos (absoluto depois relativo)
    function callEndpoint(params) {
        for (let i = 0; i < ENDPOINTS.length; i++) {
            const resp = postSync(ENDPOINTS[i], params);
            if (resp) return resp;
        }
        return null;
    }

    // Mapeia a resposta do endpoint para o formato antigo do Calculator
    function mapToLegacy(resp) {
        return {
            valor_base_projeto: +(resp?.intermediarios?.BASE || 0),

            // packs no formato antigo (mantemos nomes esperados)
            flex_pack: {
                a_vista_bruto: +(resp?.solo?.a_vista || 0),
                a_vista_com_taxa_archa: +(resp?.solo?.a_vista || 0),
                parcelado_com_taxa_archa: +(resp?.solo?.parcelado || 0),
                parcelado_base: +(resp?.solo?.parcelado_base || 0)
            },
            duo_pack: {
                a_vista_bruto: +(resp?.duo?.a_vista || 0),
                a_vista_com_taxa_archa: +(resp?.duo?.a_vista || 0),
                parcelado_com_taxa_archa: +(resp?.duo?.parcelado || 0),

                parcelado_base: +(resp?.duo?.parcelado_base || 0)

            },
            trio_pack: {
                a_vista_bruto: +(resp?.trio?.a_vista || 0),
                a_vista_com_taxa_archa: +(resp?.trio?.a_vista || 0),
                parcelado_com_taxa_archa: +(resp?.trio?.parcelado || 0),

                parcelado_base: +(resp?.trio?.parcelado_base || 0)

            },

            // aliases numéricos usados pela UI (money(flex/duo/trio))
            flex: +(resp?.solo?.a_vista || 0),
            duo: +(resp?.duo?.a_vista || 0),
            trio: +(resp?.trio?.a_vista || 0),
        };
    }

    /* ─────────────────── Implementação compat ─────────────────── */

    // guarda última resposta para funções auxiliares
    let _lastLegacy = null;

    /** Chave: esta função agora chama o endpoint e retorna o objeto legado */
    function calcularPlanosLocais(input, tabela) {
        const payload = buildPayload(input);
        const resp = callEndpoint(payload) || {};
        const legacy = mapToLegacy(resp);
        _lastLegacy = legacy;
        return legacy;
    }

    // Mantidos só por compat; se alguém chamar, devolvemos algo útil
    function calcularValorBaseProjeto(input, tabela) {
        const legacy = _lastLegacy || calcularPlanosLocais(input, tabela);
        return legacy.valor_base_projeto || 0;
    }

    function precoSoloFromBase(valorBase, tabela) {
        return _lastLegacy?.flex ?? 0;
    }

    /** (legado) payload formatado em strings */
    function montarPayloadCompat(planos) {
        const fmt = (v) => (+v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const cada = fmt(planos?.valor_base_projeto || 0);
        const packFmt = (p = {}) => ({
            a_vista_bruto: fmt(p.a_vista_bruto || 0),
            a_vista_com_taxa_archa: fmt(p.a_vista_com_taxa_archa || 0),
            parcelado_com_taxa_archa: fmt(p.parcelado_com_taxa_archa || 0),
            cada_puro: cada
        });
        return {
            cada_puro: cada,
            flex: packFmt(planos?.flex_pack),
            duo: packFmt(planos?.duo_pack),
            trio: packFmt(planos?.trio_pack),
            parametros_token: (typeof localStorage !== 'undefined')
                ? (localStorage.getItem('uuidLaravel') || null)
                : null
        };
    }

    /* ─────────────────────── Exposição pública ───────────────────────── */
    global.Calculator = {
        calcularValorBaseProjeto,
        precoSoloFromBase,
        calcularPlanosLocais,
        montarPayloadCompat
    };

})(window);
