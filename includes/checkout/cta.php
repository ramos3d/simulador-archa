<?php /** CTA: aceite + botão (dentro da coluna sticky) */ ?>
<div class="ck-card mb-0">
  <div class="ck-body p-3">

    <button id="btnFinish" type="button"
            class="btn btn-archa-primary w-100 f-exo fw-bold"
            disabled
            style="font-size:1.05rem;padding:.9rem;border-radius:10px">
      Pagar entrada <span id="btnFinishAmount"></span>
    </button>

    <div class="form-check mt-2">
      <input class="form-check-input" type="checkbox" id="agree" required>
      <label class="form-check-label f-exo" for="agree" style="font-size:.78rem">
        Concordo com os
        <a href="https://archa.com.br/temos-de-uso" target="_blank" class="text-decoration-underline">termos</a> e
        <a href="https://archa.com.br/temos-de-uso#conduta_esperada" target="_blank" class="text-decoration-underline">políticas de privacidade</a>.
      </label>
    </div>

    <div class="d-flex align-items-center justify-content-center gap-2 mt-2 ck-muted" style="font-size:.72rem">
      <img src="<?= ASSETS_BASE ?>/images/icon-lock.png" alt="" width="12">
      <span>Pagamento seguro · 100% criptografado</span>
    </div>

  </div>
</div>

<script>
(function () {
  const agree     = document.getElementById('agree');
  const btnFinish = document.getElementById('btnFinish');
  if (!agree || !btnFinish) return;

  function sync() { btnFinish.disabled = !agree.checked; }
  agree.addEventListener('change', sync);
  sync();

  btnFinish.addEventListener('click', (e) => {
    e.preventDefault();
    if (!agree.checked) { alert('Aceite os termos para continuar.'); return; }
    document.dispatchEvent(new CustomEvent('checkout:submit'));
  });
})();
</script>
