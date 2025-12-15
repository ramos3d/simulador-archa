<!doctype html>
<html lang="pt-br">

<head>

  <meta charset="utf-8">
  <?php emit_gtm_head(); ?>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php emit_app_meta(APP_BASE /*, false */); ?>
  <?php emit_seo_indexing('https://www.archa.com.br/projeto-de-arquitetura-online'); ?>
  <title>Checkout</title>
  <!-- Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-0B713MY059"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());
    gtag('config', 'G-0B713MY059', {
      'linker': {
        'domains': ['archa.com.br', 'archa.pro', 'dev.archa.pro']
      }
    });
  </script>


  <link rel="icon" type="image/png" href="https://archa.com.br/projeto-de-arquitetura-online/images/favicon.png">
  <!-- Bootstrap 5 (CSS) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- CSS exclusivo do checkout -->
  <link href="<?= BASE_URL ?>/css/styles-checkout.css?v=<?= filemtime(ROOT_PATH . '/css/styles-checkout.css'); ?>" rel="stylesheet">
  <link href="<?= BASE_URL ?>/css/fontes.css?v=<?= filemtime(ROOT_PATH . '/css/fontes.css'); ?>" rel="stylesheet">
  <link href="<?= BASE_URL ?>/css/checkout.css?v=<?= filemtime(ROOT_PATH . '/css/checkout.css'); ?>" rel="stylesheet">
  <script src="js/analytics.js?v=<?= filemtime('js/analytics.js'); ?>"></script>
</head>

<body class="checkout-page">
  <?php emit_gtm_body(); ?>
  <!-- Header mínimo e neutro só com logo -->
  <header class="checkout-header container py-4 mt-custom">
    <div class="d-flex align-items-center justify-content-between">
      <a href="/" class="d-inline-flex align-items-center text-decoration-none">
        <img src="images/logo-azul.png" alt="Archa" height="26" class="d-block logo-alignment-desktop">
      </a>
      <a href="https://api.whatsapp.com/send/?phone=5511942892984&text=Para+iniciar+seu+atendimento%2C+envie+uma+mensagem+como+esta%3A+Ol%C3%A1%21+Gostaria+de+contratar+um+projeto+de+arquitetura+com+a+Archa.&type=phone_number&app_absent=0"
        target="_blank"
        class="btn-outline-azull none">
        <span class="btn-icon-whatsapp">
          <img src="images/icon-colored-whatsapp.svg" alt="Whatsapp Archa">
        </span>
        <span>Falar com a Archa</span>
      </a>


    </div>
  </header>