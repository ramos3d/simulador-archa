function getSeenPrices() {
    // tenta calcular pelos dados locais do simulador
    try {
        if (typeof window.planosLocal === 'function') {
            const planos = window.planosLocal() || {};
            const packs = {
                solo: planos.flex_pack || {},
                duo: planos.duo_pack || {},
                trio: planos.trio_pack || {}
            };

            function p(pack) {
                const avista = +(pack.a_vista_bruto ?? pack.a_vista ?? 0);
                const total = +(pack.parcelado_com_taxa_archa ?? pack.parcelado_total ?? pack.parcelado ?? 0);
                const parcela = total > 0 ? total / 10 : 0;
                return { avista, parcelado_total: total, parcela };
            }

            return {
                solo: p(packs.solo),
                duo: p(packs.duo),
                trio: p(packs.trio)
            };
        }
    } catch (_) { }

    // fallback: tenta ler do DOM; se não achar, devolve zeros
    const parseBRL = (sel) => {
        const t = document.querySelector(sel)?.textContent || '0';
        const n = t.replace(/\./g, '').replace(',', '.').replace(/[^\d.]/g, '');
        return +(n || 0);
    };
    return {
        solo: { avista: parseBRL('#priceSoloAvista'), parcelado_total: parseBRL('#priceSoloParcela') * 10, parcela: parseBRL('#priceSoloParcela') },
        duo: { avista: parseBRL('#priceDuoAvista'), parcelado_total: parseBRL('#priceDuoParcela') * 10, parcela: parseBRL('#priceDuoParcela') },
        trio: { avista: parseBRL('#priceTrioAvista'), parcelado_total: parseBRL('#priceTrioParcela') * 10, parcela: parseBRL('#priceTrioParcela') }
    };
}