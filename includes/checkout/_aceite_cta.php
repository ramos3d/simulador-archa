<div class="ck-body">
  <div class="form-check my-3 mt-5">
    <input class="form-check-input" type="checkbox" id="agree" required>
    <label class="form-check-label f-14 pad-5" for="agree">
      Ao realizar o pagamento você concorda com os nossos <br>
      <a href="https://archa.com.br/temos-de-uso" class="text-decoration-underline link" target="_blank" >termos de uso</a> e
      nossas <a href="https://archa.com.br/temos-de-uso#conduta_esperada" class="text-decoration-underline link" target="_blank">políticas de privacidade</a>.
    </label>
  </div>

  <button id="btnFinish" class="btn btn-archa-primary w-100 f-22 mt-2" type="button" disabled>
    Finalizar pagamento
  </button>
</div>

<script>
(function () {
  // Helpers
  const $ = (s) => document.querySelector(s);
  const fmtBR = (n) => Number(n || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  const parseBR = (s) =>
    Number(String(s || '0').replace(/[^\d,.-]/g, '').replace('.', '').replace(',', '.')) || 0;

  // Elementos
  const elAgree   = $('#agree');
  const btnFinish = $('#btnFinish');
  const selInst   = $('#installments');
  const elTotal   = $('#sumTotal');     // deve conter data-avista e data-parcelado-total
  const btnCard   = $('#btnPayCard');
  const btnPix    = $('#btnPayPix');

  // Leitura segura de data-attributes
  const readDataNum = (el, name, fallbackSelText = false) => {
    const v = Number(el?.dataset?.[name] || 0);
    if (Number.isFinite(v) && v > 0) return v;
    return fallbackSelText ? parseBR(el?.textContent) : 0;
  };

  // Totais expostos no resumo
  const AVISTA         = readDataNum(elTotal, 'avista', true);          // PIX (à vista)
  const PARC_TOTAL_10X = readDataNum(elTotal, 'parceladoTotal', true);  // Cartão (total em 10x)

  if (!PARC_TOTAL_10X && !AVISTA) {
    console.warn('[checkout] Totais não encontrados. Verifique data-avista e data-parcelado-total no #sumTotal.');
  }

  // Constrói opções de parcelas (1..10) a partir do total em 10x (uma única vez)
  function buildInstallments() {
    if (!selInst || selInst.dataset.built === '1' || !PARC_TOTAL_10X) return;
    const frag = document.createDocumentFragment();
    selInst.innerHTML = '';
    for (let n = 1; n <= 10; n++) {
      const per = PARC_TOTAL_10X / n;
      const opt = document.createElement('option');
      opt.value = String(n);
      opt.dataset.amount = per.toFixed(2);
      opt.textContent = `${n}x de ${fmtBR(per)}`;
      frag.appendChild(opt);
    }
    selInst.appendChild(frag);
    selInst.value = '10';        // default visual
    selInst.dataset.built = '1'; // marca como construído
  }

  // Atualiza o totalão exibido (cartão vs pix)
  function updateBigTotal(method) {
    if (!elTotal) return;
    elTotal.textContent = (method === 'pix') ? fmtBR(AVISTA) : fmtBR(PARC_TOTAL_10X);
  }

  // Estado visual + acessibilidade do seletor de parcelas
  function activateCard() {
    btnCard?.classList.add('active');
    btnPix?.classList.remove('active');
    selInst?.removeAttribute('disabled');
    document.body.dataset.payMethod = 'card';
    updateBigTotal('card');
  }

  function activatePix() {
    btnPix?.classList.add('active');
    btnCard?.classList.remove('active');
    selInst?.setAttribute('disabled', 'disabled');
    document.body.dataset.payMethod = 'pix';
    updateBigTotal('pix');

    // Exibe desconto, se existir #sumDesc
    const elDesc = document.querySelector('#sumDesc');
    if (elDesc && AVISTA && PARC_TOTAL_10X) {
      elDesc.textContent = (PARC_TOTAL_10X - AVISTA).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }
  }

  // Listeners de UI (sem lógica de pagamento aqui)
  btnCard?.addEventListener('click', activateCard);
  btnPix?.addEventListener('click', activatePix);

  // Habilita/desabilita o botão conforme aceite
  function syncFinishState() { btnFinish.disabled = !!(elAgree && !elAgree.checked); }
  elAgree?.addEventListener('change', syncFinishState);

  // Clique do botão → dispara evento global para o _pagamento.php enviar o POST
  btnFinish?.addEventListener('click', (e) => {
    e.preventDefault();
    if (elAgree && !elAgree.checked) {
      alert('Você precisa aceitar os termos para continuar.');
      return;
    }
    document.dispatchEvent(new CustomEvent('checkout:submit'));
  });

  // Boot
  buildInstallments();
  activateCard();         // padrão = cartão
  syncFinishState();      // estado inicial do botão
})();
</script>
