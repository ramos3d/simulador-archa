<?php
require_once __DIR__ . '/config.php';
include __DIR__ . '/includes/layout/head-checkout.php';

$slug = isset($_GET['projeto']) ? trim($_GET['projeto']) : '';
?>
<script>window.CHECKOUT_SLUG = "<?= htmlspecialchars($slug, ENT_QUOTES) ?>";</script>

<main class="flex-grow-1 py-4 py-md-5">
  <form id="formPersona" novalidate>
    <div class="container" style="max-width:1100px">

      <?php if (IS_MOBILE): ?>
        <!-- MOBILE: fluxo linear -->
        <div class="row g-4">
          <section class="col-12"><?php include __DIR__ . '/includes/checkout/review-accordion.php'; ?></section>
          <section class="col-12"><?php include __DIR__ . '/includes/checkout/totals.php'; ?></section>
          <section class="col-12"><?php include __DIR__ . '/includes/checkout/client-form.php'; ?></section>
          <section class="col-12"><?php include __DIR__ . '/includes/checkout/payment.php'; ?></section>
          <section class="col-12"><?php include __DIR__ . '/includes/checkout/cta.php'; ?></section>
        </div>

      <?php else: ?>
        <!-- DESKTOP: 2 colunas — direita sticky com preço sempre visível -->
        <div class="row g-4">
          <!-- Coluna esquerda: dados + pagamento -->
          <section class="col-12 col-lg-7 order-2 order-lg-1">
            <?php include __DIR__ . '/includes/checkout/client-form.php'; ?>
            <?php include __DIR__ . '/includes/checkout/payment.php'; ?>
          </section>
          <!-- Coluna direita: STICKY com revisão + totais + CTA -->
          <aside class="col-12 col-lg-5 order-1 order-lg-2">
            <div class="checkout-sticky">
              <?php include __DIR__ . '/includes/checkout/review-accordion.php'; ?>
              <?php include __DIR__ . '/includes/checkout/totals.php'; ?>
              <?php include __DIR__ . '/includes/checkout/cta.php'; ?>
            </div>
          </aside>
        </div>
      <?php endif; ?>

    </div>
  </form>
</main>

<?php include __DIR__ . '/includes/layout/footer-checkout.php'; ?>
