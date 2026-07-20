<?php /** Painel de preço sticky — destaca o valor de hoje (25%) */ ?>
<div class="ck-card ck-card-price mb-3">
  <div class="ck-body p-3">

    <div class="text-muted f-exo text-uppercase" style="font-size:.72rem;letter-spacing:.5px">
      Você paga hoje (25%)
    </div>

    <!-- Hero: "Nx de" prefix + valor -->
    <div class="d-flex align-items-baseline gap-2 mt-1">
      <span id="totalEntradaParcPrefix" class="f-anek ck-muted" style="font-size:1rem;font-weight:500;display:none"></span>
      <div id="totalEntradaValor" class="price-hero">R$ 0,00</div>
    </div>

    <!-- Total quando parcelado -->
    <div id="totalEntradaValorFullLine" class="ck-muted mt-0" style="font-size:.82rem;display:none">
      Total <strong id="totalEntradaValorFull">R$ 0,00</strong>
    </div>

    <div class="d-flex justify-content-between align-items-center small mt-2">
      <span class="ck-muted" id="entradaParcelaLabel">2x no cartão</span>
      <span id="totalEntradaParcelaUnit" class="ck-muted"></span>
    </div>

    <hr class="my-3" style="opacity:.15">

    <div class="d-flex justify-content-between small mb-1">
      <span class="ck-muted">Subtotal do projeto</span>
      <span id="totalParceladoBruto" class="ck-muted">R$ 0,00</span>
    </div>
    <div class="d-flex justify-content-between small mb-2">
      <span class="ck-muted">Restante (75%)</span>
      <span id="totalSaldoBruto" class="ck-muted">R$ 0,00</span>
    </div>

    <div class="entrada-info-box" role="note">
      <strong>📌 Importante:</strong> o restante de 75% só será cobrado quando você
      <strong>escolher o escritório</strong> que tocará seu projeto - parcelado em até <strong>10x</strong>.
    </div>

    <div class="entrada-info-box guarantee" role="note">
      <strong>🛡️ Garantia de satisfação:</strong> se a experiência não atender suas expectativas, <strong>devolvemos seu dinheiro</strong>.
    </div>

  </div>
  <!-- Hidden helpers (mantidos por compatibilidade com JS legado) -->
  <input type="hidden" id="totalParcelado" value="0">
  <input type="hidden" id="totalSaldoParc" value="0">
  <input type="hidden" id="totalSaldoN" value="10">
  <!-- Bloco antigo "total" mantido escondido para não quebrar selectors -->
  <span id="sumSubtotal" hidden>R$ 0,00</span>
  <span id="sumDesc" hidden>R$ 0,00</span>
  <span id="sumTotal" hidden data-avista="0" data-parcelado-total="0">R$ 0,00</span>
  <div id="totalsTotal" class="d-none"></div>
  <div id="totalsEntrada"></div>
</div>
