<?php
$secDir = LEGACY_FS . '/etapas/' . (IS_MOBILE ? 'mobile/' : '');
?>
<div class="row justify-content-center">
  <div class="col-lg-7 col-xl-8">
    <div class="title-badge-card mb-2">
      <h1 class="bg-azul text-green text-start f-anek fw-bold">
        <?= t('Quanto custa seu projeto de arquitetura e decoração?') ?>
      </h1>
    </div>
    <form id="form-inteligente">
      <?php
        if (file_exists($secDir . 'section1.php')) include $secDir . 'section1.php';
        if (file_exists($secDir . 'section2.php')) include $secDir . 'section2.php';
        if (file_exists($secDir . 'section3.php')) include $secDir . 'section3.php';
      ?>
    </form>
  </div>

  <aside class="col-lg-4 col-xl-3">
    <?php include __DIR__ . '/plan-card.php'; ?>
  </aside>
</div>
