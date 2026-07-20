/**
 * Estado centralizado do checkout.
 * Fonte única de verdade para slug, produto selecionado e preços.
 */
import Storage from '../utils/storage.js';
import Format from '../utils/format.js';

const State = (() => {
    let _slug    = '';
    let _produto = 'duo';   // solo | duo | trio
    let _precos  = null;    // { solo, duo, trio } - cada um com { avista, parcelado }
    let _modalidade = 'entrada_parcelas';  // total | entrada_parcelas (default agora é entrada)
    let _saldoParcelas = 10;       // 75% será parcelado em até 10x na próxima etapa
    let _entradaForma = 'cartao';  // pix | cartao  (cartão é o default)
    let _entradaParcelas = 2;      // parcelas da ENTRADA (cartão começa em 2x)

    function resolveSlug() {
        const qs = new URLSearchParams(location.search);
        return (window.CHECKOUT_SLUG || '').trim()
            || (qs.get('projeto') || '').trim()
            || Storage.checkoutSlug;
    }

    function init() {
        _slug   = resolveSlug();
        _precos = Storage.precosVistos;
        _produto = Storage.get('selectedPlan') || 'duo';
        return { slug: _slug, precos: _precos, produto: _produto };
    }

    function setSlug(slug) {
        _slug = slug;
        Storage.checkoutSlug = slug;
        if (slug && !new URLSearchParams(location.search).get('projeto')) {
            const qs = new URLSearchParams(location.search);
            qs.set('projeto', slug);
            history.replaceState(null, '', `${location.pathname}?${qs}`);
        }
    }

    function setProduto(code) {
        _produto = code;
        Storage.set('selectedPlan', code);
    }

    function setModalidade(mode) { _modalidade = mode; }
    function setSaldoParcelas(n) { _saldoParcelas = Math.min(10, Math.max(2, n)); }
    function setEntradaForma(forma) { _entradaForma = forma; }
    function setEntradaParcelas(n) { _entradaParcelas = Math.min(10, Math.max(1, n)); }

    // Valores calculados do produto selecionado
    function getValores() {
        if (!_precos || !_precos[_produto]) return { avista: 0, parcelado: 0 };
        const p = _precos[_produto];
        return { avista: p.avista || 0, parcelado: p.parcelado || p.parcelado_total || 0 };
    }

    // Valores para modalidade entrada
    function getValoresEntrada() {
        const { parcelado } = getValores();
        const entrada = Math.round(parcelado * 0.25);
        const saldo   = parcelado - entrada;
        const parcela = Math.round(saldo / _saldoParcelas);
        return { entrada, saldo, parcela, saldoParcelas: _saldoParcelas };
    }

    return {
        init,
        setSlug, setPrecos: (p) => { _precos = p; Storage.precosVistos = p; },
        setProduto, setModalidade, setSaldoParcelas, setEntradaForma, setEntradaParcelas,
        get slug()           { return _slug; },
        get produto()        { return _produto; },
        get modalidade()     { return _modalidade; },
        get saldoParcelas()  { return _saldoParcelas; },
        get entradaForma()   { return _entradaForma; },
        get entradaParcelas(){ return _entradaParcelas; },
        get precos()        { return _precos; },
        getValores,
        getValoresEntrada,
    };
})();

export default State;
