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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<?php $af = LEGACY_FS . '/js'; $v  = fn(string $f): string => @filemtime("{$af}/{$f}") ?: time(); ?>
<!-- Máscaras de input (telefone, CEP, etc.) -->
<script src="<?= LEGACY_URL ?>/js/inputs.js?v=<?= $v('inputs.js') ?>"></script>

<script type="module" src="./js/pages/checkout.js?v=<?= time() ?>"></script>
</body>
</html>
