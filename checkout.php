<?php
include 'config.php';
include 'includes/header-checkout.php';

// slug vindo por query string
$slug = isset($_GET['projeto']) ? trim($_GET['projeto']) : null;
?>

<script>
  window.CHECKOUT_SLUG = "<?= htmlspecialchars($slug ?? '', ENT_QUOTES) ?>";
</script>

<main class="flex-grow-1 checkout-wrap f-anek">
  <form id="formPersona" novalidate>
    <div class="container margem-customizada">
      <?php if (IS_MOBILE): ?>
        <!-- ====== MOBILE (ordem “limpa”) ======
             1) Review → 2) Dados → 3) Cupom → 4) Pagamento(CTA) → 5) Totais -->
        <div class="row g-4 checkout-split">
          <section class="col-12">
            <?php include 'includes/checkout/_review.php'; ?>
          </section>
          <section class="col-12">
            <?php include 'includes/checkout/_dados.php'; ?>
          </section>
          <section class="col-12">
            <?php include 'includes/checkout/_pagamento.php'; ?>
          </section>
          <section class="col-12 topo-cartao">
            <?php include 'includes/checkout/_cupom.php'; ?>
          </section>
          <section class="col-12 mobile-totais-section">
            <?php include 'includes/checkout/_totais.php'; ?>
          </section>
          <section class="col-12 mt-27">
            <?php include 'includes/checkout/_aceite_cta.php'; ?>
          </section>
        </div>

      <?php else: ?>
        <!-- ====== DESKTOP (mantém exatamente o que já funciona) ====== -->
        <div class="row g-4 g-lg-5 checkout-split">
          <!-- COLUNA DIREITA NO DESKTOP (revisão/cupom/totais) -->
          <section class="col-12 col-lg-6 order-1 order-lg-2">
            <?php include 'includes/checkout/_review.php'; ?>
            <?php include 'includes/checkout/_cupom.php'; ?>
            <div class="desktop-subtotal-37">
              <?php include 'includes/checkout/_totais.php'; ?>
            </div>
          </section>

          <!-- COLUNA ESQUERDA NO DESKTOP (dados/pagamento dentro do form) -->
          <section class="col-12 col-lg-6 order-2 order-lg-1">
            <?php include 'includes/checkout/_dados.php'; ?>
            <?php include 'includes/checkout/_pagamento.php'; ?>
            <div class="desktop-cta">
              <?php include 'includes/checkout/_aceite_cta.php'; ?>
            </div>

          </section>
        </div>
      <?php endif; ?>
    </div>
  </form>
</main>

<?php include 'includes/footer-checkout.php'; ?>

<script>
  // Bootstrap: expõe config para os módulos JS
  (function() {
    const qs = new URLSearchParams(location.search);
    let slug = (window.CHECKOUT_SLUG || '').trim() || qs.get('projeto') || localStorage.getItem('checkoutSlug') || '';

    // Normaliza a URL (não quebra se não houver slug)
    if (slug && !qs.get('projeto')) {
      qs.set('projeto', slug);
      history.replaceState(null, '', `${location.pathname}?${qs.toString()}`);
    }

    window.CHECKOUT = {
      slug,
      bridgeUrl: './bridge.php',
      endpoints: {
        editorShow: 'op=editor_show&slug=',
        cardPay: 'op=checkout_card', // POST via bridge
        pixPay: 'op=checkout_pix' // POST via bridge
      }
    };
  })();
</script>


<script>
  // Evento de abertura do checkout
  document.addEventListener("DOMContentLoaded", function() {
    if (window.ArchaAnalytics && typeof window.ArchaAnalytics.trackCheckoutView === "function") {
      ArchaAnalytics.trackCheckoutView(window.location.href);
    }
  });
</script>