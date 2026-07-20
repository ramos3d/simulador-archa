<?php
require_once __DIR__ . '/config.php';
$slug = isset($_GET['checkout']) ? trim($_GET['checkout']) : '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Projeto confirmado - Archa</title>
  <?php emit_robots(); emit_gtm_head(); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Anek+Latin:wght@300;400;700;800&family=Exo+2:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="icon" href="<?= ASSETS_BASE ?>/images/favicon.png">
  <style>
    body { background: #fff; }
    .ty-hero { text-align:center; padding: 3rem 0 4rem; }
    .ty-img { max-width:420px; width:100%; }
    .ty-title { font-size: clamp(2rem,6vw,3.2rem); font-weight:800; color:#262942; }
    .ty-name { color:#E893C7; }
    .ty-sub { font-size:1.1rem; font-weight:700; color:#262942; }
    .btn-blue { background:#262942; color:#D4FFAD; border:none; }
    .btn-blue:hover { background:#1e2035; color:#c2ea9e; }
    .btn-whats { border:2px solid #262942; color:#262942; background:#fff; }
    .btn-whats:hover { background:#f2f4ff; color:#262942; }
    @media(max-width:576px){ .ty-img{ max-width:280px; } }
  </style>
</head>
<body class="d-flex flex-column min-vh-100 f-anek">
<?php emit_gtm_body(); ?>

<nav class="navbar bg-white border-bottom py-3">
  <div class="container">
    <a class="navbar-brand" href="https://archa.com.br">
      <img src="<?= ASSETS_BASE ?>/images/logo-archa.png" alt="Archa" height="36">
    </a>
  </div>
</nav>

<main class="flex-grow-1">
  <div class="container ty-hero">
    <img src="<?= ASSETS_BASE ?>/images/arte-thankyou.png" alt="Projeto confirmado" class="ty-img mb-4">

    <h1 class="ty-title mb-3">
      Obrigado, <span class="ty-name" id="tkNome">Cliente</span>!
    </h1>
    <h2 class="ty-sub mb-4" style="font-size:1.4rem">
      Seu projeto com a Archa está pronto para começar.
    </h2>
    <p class="lead mb-2" style="font-size:1rem">
      O <strong id="tkPlano">Plano Archa</strong> para
      <strong id="tkTipo">seu imóvel</strong> começou!
      <strong id="tkAmb"></strong> em <strong id="tkM2"></strong>, feitos para refletir o seu estilo.
    </p>
    <p class="text-muted mb-5" style="font-size:.95rem">
      Nosso time entrará em contato em breve. Confira seu e-mail para os próximos passos.
    </p>

    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
      <a href="https://api.whatsapp.com/send/?phone=5511942892984&text=Olá!+Acabei+de+contratar+um+projeto+com+a+Archa."
         class="btn btn-lg btn-whats f-exo d-inline-flex align-items-center justify-content-center gap-2"
         target="_blank" rel="noopener">
        <img src="<?= ASSETS_BASE ?>/images/icon-whatsapp.png" alt="WhatsApp" width="20">
        Falar com a Archa
      </a>
    </div>
  </div>
</main>

<footer class="py-4 border-top bg-white mt-auto">
  <div class="container text-center">
    <p class="text-muted small mb-0">© <?= date('Y') ?> Archa. Todos os direitos reservados.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="./js/pages/thank-you.js"></script>
</body>
</html>
