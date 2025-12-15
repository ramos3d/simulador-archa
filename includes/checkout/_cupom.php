<?php

/** includes/checkout/_cupom.php
 * Bloco: Cupom de desconto (input + botão aplicar)
 * Obs.: JS que habilita o botão com 3+ caracteres já está no checkout-functions.js
 */
?>
<hr class="margem-custom-bottom mobile-only" style="margin-bottom: 21px;">
<div class="ck-body">
  <hr class="margem-custom-bottom cupom-top-custom desktop-only">


  <div class="d-flex align-items-center gap-2 margem-custom-bottom">
    <input id="cupom" type="text" class="form-control f-exo flex-grow-1"
      placeholder="Cupom de desconto">
    <button id="btnAplicar" type="button" class="btn btn-aplicar f-exo flex-shrink-0" disabled>
      Aplicar
    </button>
  </div>
  <hr class="margem-custom-bottom desktop-only">
</div>
<hr class="margem-custom-bottom mobile-only" style="margin-top: -10px;">

<script>
  document.addEventListener("DOMContentLoaded", function() {
    const inputCupom = document.getElementById("cupom");
    const btnAplicar = document.getElementById("btnAplicar");

    if (!inputCupom || !btnAplicar) return;

    btnAplicar.addEventListener("click", function() {
      const code = (inputCupom.value || "").trim();
      if (!code) return;

      if (window.ArchaAnalytics && typeof ArchaAnalytics.trackCheckoutCupom === "function") {
        const isTest = location.hostname === "localhost" || location.hostname.includes("homolog");
        const sentCode = isTest ? "[TESTE] " + code : code;

        ArchaAnalytics.trackCheckoutCupom(sentCode, false);

        console.log("GA checkout_cupom enviado:", {
          cupom_name: sentCode,
          valid: false,
          test: isTest
        });
      }
    });
  });
</script>