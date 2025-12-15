(function () {
    function moneyBR(v) {
        return (+v || 0).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });
    }

    // Normaliza os nomes vindos do simulador (ajuste se houver outros)
    function normalize(vals) {
        return {
            avista: +(
                vals?.a_vista ??
                vals?.a_vista_bruto ??
                vals?.valor_avista ??
                0
            ),
            parcelado: +(
                vals?.parcelado ??
                vals?.parcelado_com_taxa_archa ??
                vals?.valor_parcelado_total ??
                0
            )
        };
    }

    function setText(id, num) {
        const el = document.getElementById(id);
        if (el) el.textContent = moneyBR(num);
    }

    function setPlan(code, vals) {
        const {
            avista,
            parcelado
        } = normalize(vals);
        const parcela = parcelado > 0 ? parcelado / 10 : 0;

        // IDs por plano
        const ids = {
            solo: {
                hdr: 'priceSolo',
                parc: 'priceSoloParcela',
                cash: 'priceSoloAvista'
            },
            duo: {
                hdr: 'priceDuo',
                parc: 'priceDuoParcela',
                cash: 'priceDuoAvista'
            },
            trio: {
                hdr: 'priceTrio',
                parc: 'priceTrioParcela',
                cash: 'priceTrioAvista'
            }
        }[code];

        // Cabeçalho = parcela (10x de …)
        setText(ids.hdr, parcela);
        // Corpo
        setText(ids.parc, parcela);
        setText(ids.cash, avista);
    }

    // Exponho uma função para o simulador chamar quando calcular
    window.renderPrecos = function (result) {
        const solo = result?.solo ?? result?.flex ?? {};
        const duo = result?.duo ?? {};
        const trio = result?.trio ?? {};
        setPlan('solo', solo);
        setPlan('duo', duo);
        setPlan('trio', trio);
    };

    // Retrocompatibilidade: se algum script continuar setando #priceSolo/Duo/Trio,
    // espelhar para os <strong> da parcela dentro do corpo.
    ['solo', 'duo', 'trio'].forEach(code => {
        const map = {
            solo: 'priceSolo',
            duo: 'priceDuo',
            trio: 'priceTrio'
        };
        const src = document.getElementById(map[code]);
        const dst = document.getElementById(
            code === 'solo' ? 'priceSoloParcela' :
                code === 'duo' ? 'priceDuoParcela' : 'priceTrioParcela'
        );
        if (!src || !dst) return;
        const obs = new MutationObserver(() => {
            // tenta ler número do texto e reformatar
            const raw = (src.textContent || '').replace(/[^\d,.-]/g, '').replace(/\./g, '').replace(',', '.');
            const n = parseFloat(raw);
            if (!isNaN(n)) dst.textContent = moneyBR(n);
        });
        obs.observe(src, {
            childList: true,
            characterData: true,
            subtree: true
        });
    });
})();