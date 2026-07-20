<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Simulador de Projetos - Archa</title>
  <?php emit_robots(); emit_app_meta(); emit_gtm_head(); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anek+Latin:wght@300;400;500;600;700;800&family=Exo+2:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <?php $cssDir = LEGACY_FS . '/css'; $cv = fn(string $f) => @filemtime("{$cssDir}/{$f}") ?: time(); ?>
  <link rel="stylesheet" href="<?= ASSETS_BASE ?>/css/styles.css?v=<?= $cv('styles.css') ?>">
  <link rel="stylesheet" href="<?= ASSETS_BASE ?>/css/fontes.css?v=<?= $cv('fontes.css') ?>">
  <link rel="stylesheet" href="<?= ASSETS_BASE ?>/css/form-layouts.css?v=<?= $cv('form-layouts.css') ?>">
  <link rel="stylesheet" href="<?= ASSETS_BASE ?>/css/simulador-layout.css?v=<?= $cv('simulador-layout.css') ?>">
  <link rel="stylesheet" href="<?= ASSETS_BASE ?>/css/card-orcamento.css?v=<?= $cv('card-orcamento.css') ?>">
  <link rel="icon" href="<?= ASSETS_BASE ?>/images/favicon.png">
  <style>
    /* WhatsApp outline button (originalmente inline em archa-form/index.php) */
    .btn-outline-azull{display:inline-flex;align-items:center;justify-content:center;gap:12px;padding:8px 14px;border-radius:12px;border:2px solid #262942;background:#fff;color:#262942;font-size:1rem;font-weight:500;text-decoration:none;line-height:1;transition:background-color .2s,color .2s,box-shadow .2s,transform .1s;text-align:center}
    .btn-outline-azull .btn-icon-whatsapp{display:inline-flex;align-items:center;justify-content:center}
    .btn-outline-azull .btn-icon-whatsapp img{display:block;width:32px;height:32px}
    .btn-outline-azull:hover,.btn-outline-azull:focus{background:#262942;color:#fff;text-decoration:none;box-shadow:0 4px 10px rgba(0,0,0,.15);transform:translateY(-1px)}
    .btn-outline-azull:hover .btn-icon-whatsapp img,.btn-outline-azull:focus .btn-icon-whatsapp img{filter:none}
  </style>
</head>
<body class="d-flex flex-column min-vh-100">
<?php emit_gtm_body(); ?>
<nav class="navbar navbar-light bg-white border-bottom py-3">
  <div class="container">
    <a class="navbar-brand" href="https://archa.com.br">
      <img src="<?= ASSETS_BASE ?>/images/logo-azul.png" alt="Archa" height="36">
    </a>
    <a href="https://api.whatsapp.com/send/?phone=5511942892984&text=Ol%C3%A1%21+Gostaria+de+ajuda+com+o+simulador+Archa." target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary f-exo">
      <img src="<?= ASSETS_BASE ?>/images/icon-whatsapp.png" alt="WhatsApp" width="18" class="me-1"> Ajuda
    </a>
  </div>
</nav>
