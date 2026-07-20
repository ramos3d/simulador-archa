<footer class="mt-auto pt-5 pb-5" style="background-color:#262942;color:#fff">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-12 col-md-4 mb-3 mb-md-0">
        <img src="<?= ASSETS_BASE ?>/images/logo-branca.png" alt="Archa" style="height:30px">
      </div>
    </div>
    <hr class="border-light my-3" style="opacity:.3">
    <div class="d-flex align-items-center justify-content-between">
      <div class="small" style="font-family:'Roboto','Arial Narrow',Arial,sans-serif;font-size:14px">&copy; <?= date('Y') ?> Archa</div>
      <div class="d-flex align-items-center gap-3">
        <a href="https://www.youtube.com/channel/UCObA-uzVeGRphE9L4Yv7-jw" target="_blank" rel="noopener">
          <img src="<?= ASSETS_BASE ?>/images/youtube.png" alt="YouTube" style="height:20px">
        </a>
        <a href="https://www.instagram.com/archa.company/" target="_blank" rel="noopener">
          <img src="<?= ASSETS_BASE ?>/images/instagram.png" alt="Instagram" style="height:20px">
        </a>
        <a href="https://www.linkedin.com/company/archacompany/posts/?feedView=all" target="_blank" rel="noopener">
          <img src="<?= ASSETS_BASE ?>/images/linkedin.png" alt="LinkedIn" style="height:20px">
        </a>
      </div>
    </div>
  </div>
</footer>

<div id="footer-sentinel" aria-hidden="true"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php
/* Carrega os scripts de interação do archa-form via caminhos absolutos */
$af = LEGACY_FS . '/js';
$v  = fn(string $f): string => @filemtime("{$af}/{$f}") ?: time();
?>

<script>
  /* Endpoint de precificação apontado para o calculador local */
  window.CALC_ENDPOINT = '/archa-checkout/pricing/calculator.php';
</script>

<script src="<?= LEGACY_URL ?>/js/config.js?v=<?= $v('config.js') ?>"></script>
<script src="<?= LEGACY_URL ?>/js/site.js?v=<?= $v('site.js') ?>"></script>
<script src="<?= LEGACY_URL ?>/js/calculator.js?v=<?= $v('calculator.js') ?>"></script>
<script src="<?= LEGACY_URL ?>/js/simulador.js?v=<?= $v('simulador.js') ?>"></script>
<script src="<?= LEGACY_URL ?>/js/inputs.js?v=<?= $v('inputs.js') ?>"></script>
<script src="<?= LEGACY_URL ?>/js/getSeenPrices.js?v=<?= $v('getSeenPrices.js') ?>"></script>
<script src="<?= LEGACY_URL ?>/js/prices-decoration.js?v=<?= $v('prices-decoration.js') ?>"></script>

<?php if ((strtolower($_GET['mode'] ?? '') === 'edit') && !empty($_GET['projeto'])): ?>
<!-- Edit mode: carrega snapshot e prefilla o formulario -->
<script src="<?= LEGACY_URL ?>/js/editar-simulacao.js?v=<?= $v('editar-simulacao.js') ?>"></script>
<?php endif; ?>

<!-- Comportamento sticky do card de preços -->
<script>
(function () {
  const root   = document.documentElement;
  const body   = document.body;
  const card   = document.getElementById('card-preco');
  const anchor = document.getElementById('vant-anchor');
  const sentinel = document.getElementById('footer-sentinel');
  if (!card) return;

  function measure() {
    const h = card.offsetHeight;
    const w = Math.max(340, Math.round(card.getBoundingClientRect().width));
    root.style.setProperty('--cardH', h + 'px');
    root.style.setProperty('--cardW', w + 'px');
    checkAnchor();
  }

  function checkAnchor() {
    if (!anchor) return;
    const cardBottom = card.getBoundingClientRect().bottom;
    const anchorTop  = anchor.getBoundingClientRect().top;
    if (anchorTop <= cardBottom + 8) body.classList.add('release-card');
    else body.classList.remove('release-card');
  }

  if (sentinel) {
    new IntersectionObserver((entries) => {
      for (const e of entries)
        body.classList.toggle('near-footer', e.isIntersecting);
    }, { rootMargin: '0px 0px -20% 0px' }).observe(sentinel);
  }

  window.addEventListener('load',   () => { measure(); checkAnchor(); });
  window.addEventListener('resize', () => { measure(); checkAnchor(); });
  window.addEventListener('scroll', () => requestAnimationFrame(checkAnchor), { passive: true });
  document.addEventListener('shown.bs.collapse',  measure, true);
  document.addEventListener('hidden.bs.collapse', measure, true);
  measure();
})();
</script>

<!-- Módulo do archa-checkout: fluxo de checkout -->
<script type="module" src="./js/pages/simulator.js?v=<?= time() ?>"></script>
</body>
</html>
