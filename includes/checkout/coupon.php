<?php /** Cupom compacto */ ?>
<div class="ck-card mb-3">
  <div class="ck-body p-3">
    <div class="d-flex align-items-center gap-2">
      <input id="cupom" type="text" class="form-control form-control-sm f-exo flex-grow-1" placeholder="Cupom de desconto" style="font-size:.85rem">
      <button id="btnAplicarCupom" type="button" class="btn btn-sm btn-aplicar f-exo flex-shrink-0" disabled>Aplicar</button>
    </div>
  </div>
</div>

<script>
(function () {
  const input = document.getElementById('cupom');
  const btn   = document.getElementById('btnAplicarCupom');
  if (!input || !btn) return;
  input.addEventListener('input', () => { btn.disabled = input.value.trim().length < 3; });
  btn.addEventListener('click', () => {
    const code = input.value.trim();
    if (!code) return;
    if (window.ArchaAnalytics?.trackCheckoutCupom) ArchaAnalytics.trackCheckoutCupom(code, false);
  });
})();
</script>
