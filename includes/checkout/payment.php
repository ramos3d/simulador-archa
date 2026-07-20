<?php /** Pagamento da entrada (25%) — único fluxo */ ?>
<div class="ck-card mb-4">
  <div class="ck-head py-2 px-3">
    <h6 class="ck-title mb-0">Como pagar a entrada (25%)</h6>
  </div>
  <div class="ck-body p-3">

    <!-- Modalidade fixa (compat) -->
    <input type="radio" name="modalidade_pagamento" value="entrada_parcelas" checked hidden>

    <!-- Métodos: Cartão (primeiro) | PIX -->
    <div class="entrada-metodo mb-3">
      <button type="button" class="pay-btn active" id="btnEntradaCard" data-forma="cartao">
        <img src="<?= ASSETS_BASE ?>/images/icon-card.png" alt="Cartão">
        <span class="f-exo">Cartão</span>
      </button>
      <button type="button" class="pay-btn" id="btnEntradaPix" data-forma="pix">
        <img src="<?= ASSETS_BASE ?>/images/icon-pix.png" alt="PIX">
        <span class="f-exo">PIX</span>
      </button>
    </div>

    <!-- Cartão (padrão, visível) -->
    <div id="boxEntradaCard">
      <div class="mb-3">
        <label class="form-label f-anek mb-1" style="font-size:.85rem">Parcele a entrada em</label>
        <div class="parcelas-entrada-selector">
          <?php foreach (range(2, 10) as $n): ?>
          <button type="button" class="parcela-btn <?= $n === 2 ? 'active' : '' ?>"
                  data-entrada-parcelas="<?= $n ?>">
            <?= $n ?>x
          </button>
          <?php endforeach; ?>
        </div>
        <input type="hidden" id="entradaParcelasSelect" value="2">
      </div>

      <!-- Campos do cartão -->
      <div class="row g-2 mb-2">
        <div class="col-12">
          <label for="entradaCardName" class="form-label f-anek mb-1">Nome impresso no cartão</label>
          <input id="entradaCardName" type="text" class="form-control f-exo" placeholder="Como está no cartão" autocomplete="cc-name">
        </div>
        <div class="col-12">
          <label for="entradaCardNumber" class="form-label f-anek mb-1">Número do cartão</label>
          <input id="entradaCardNumber" type="text" class="form-control f-exo" inputmode="numeric" autocomplete="cc-number" placeholder="0000 0000 0000 0000">
        </div>
        <div class="col-6">
          <label for="entradaCardExpiry" class="form-label f-anek mb-1">Validade</label>
          <input id="entradaCardExpiry" type="text" class="form-control f-exo" inputmode="numeric" placeholder="MM/AAAA" maxlength="7">
        </div>
        <div class="col-6">
          <label for="entradaCardCvv" class="form-label f-anek mb-1">CVV</label>
          <input id="entradaCardCvv" type="text" class="form-control f-exo" inputmode="numeric" placeholder="999" maxlength="4">
        </div>
      </div>
    </div>

    <!-- PIX (oculto até clicar) -->
    <div id="boxEntradaPix" class="d-none">
      <div id="entrada-pix-section" class="text-center mt-2">
        <div id="entrada-pix-qr" class="my-3">
          <div class="pix-loading text-muted small py-3">
            <div class="spinner-border spinner-border-sm me-2"></div>Gerando QR Code...
          </div>
        </div>
        <div id="entrada-pix-payload" class="text-muted small font-monospace mb-2" style="word-break:break-all"></div>
        <button id="btnCopyEntradaPix" type="button" class="btn btn-sm btn-outline-secondary f-exo mb-2" style="display:none">
          Copiar código
        </button>
        <div id="entrada-pix-expiry" class="text-muted small"></div>
      </div>
    </div>

    <!-- Mantidos por compat de IDs do JS legado -->
    <div id="boxPaymentTotal" class="d-none">
      <select id="installments"><option value="1">1x</option></select>
      <div id="boxCard"></div>
      <div id="boxPix" class="d-none"></div>
    </div>
    <div id="boxPaymentEntrada"></div>
    <div id="entradaValor" hidden></div>
    <div id="saldoTotal" hidden></div>
    <div id="saldoParcelas" hidden></div>
    <div id="saldoParcelaValor" hidden></div>
    <div id="entradaParcelasN" hidden></div>
    <input type="hidden" id="saldoParcelasSelect" value="10">

  </div>
</div>

<script>
(function () {
  const btnPix  = document.getElementById('btnEntradaPix');
  const btnCard = document.getElementById('btnEntradaCard');
  const boxPix  = document.getElementById('boxEntradaPix');
  const boxCard = document.getElementById('boxEntradaCard');

  /* Estado inicial: cartão */
  window.__entradaForma = 'cartao';

  function setMetodo(forma) {
    window.__entradaForma = forma;
    btnPix?.classList.toggle('active',  forma === 'pix');
    btnCard?.classList.toggle('active', forma === 'cartao');
    boxPix?.classList.toggle('d-none',  forma !== 'pix');
    boxCard?.classList.toggle('d-none', forma !== 'cartao');
    document.dispatchEvent(new CustomEvent('entrada:metodo', { detail: { forma } }));

    /* Ao selecionar PIX: gera QR uma única vez */
    if (forma === 'pix' && !window._pixQrGenerated) {
      document.dispatchEvent(new CustomEvent('pix:generate'));
    }
  }

  btnCard?.addEventListener('click', () => setMetodo('cartao'));
  btnPix?.addEventListener('click',  () => setMetodo('pix'));

  /* Parcelas da entrada (2-10) */
  document.querySelectorAll('.parcelas-entrada-selector .parcela-btn').forEach(b => {
    b.addEventListener('click', function () {
      document.querySelectorAll('.parcelas-entrada-selector .parcela-btn').forEach(x => x.classList.remove('active'));
      this.classList.add('active');
      const n = parseInt(this.dataset.entradaParcelas) || 2;
      const hidden = document.getElementById('entradaParcelasSelect');
      if (hidden) hidden.value = String(n);
      document.dispatchEvent(new CustomEvent('entrada:parcelas', { detail: { parcelas: n } }));
    });
  });

  /* Init: dispara evento para que o checkout.js renderize os totais com cartão/2x */
  document.dispatchEvent(new CustomEvent('entrada:parcelas', { detail: { parcelas: 2 } }));
})();
</script>
