<!DOCTYPE html>
<html lang="<?= SIM_LANG === 'en' ? 'en' : 'pt-BR' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= t('Finalizar contratação - Archa') ?></title>
  <?php emit_robots(); emit_app_meta(null, false); emit_gtm_head(); emit_jt_bootstrap(); ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anek+Latin:wght@300;400;500;600;700;800&family=Exo+2:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <?php $cssDir = LEGACY_FS . '/css'; $cv = fn(string $f) => @filemtime("{$cssDir}/{$f}") ?: time(); ?>
  <link rel="stylesheet" href="<?= ASSETS_BASE ?>/css/styles.css?v=<?= $cv('styles.css') ?>">
  <link rel="stylesheet" href="<?= ASSETS_BASE ?>/css/fontes.css?v=<?= $cv('fontes.css') ?>">
  <link rel="stylesheet" href="<?= ASSETS_BASE ?>/css/styles-checkout.css?v=<?= $cv('styles-checkout.css') ?>">
  <link rel="stylesheet" href="<?= ASSETS_BASE ?>/css/checkout.css?v=<?= $cv('checkout.css') ?>">
  <link rel="icon" href="<?= ASSETS_BASE ?>/images/favicon.png">
  <style>
    /* Restaura bordas dos inputs dentro do checkout (styles.css remove input.form-control border) */
    .ck-card .form-control,
    .ck-card .form-select,
    .ck-card input.form-control,
    .ck-card textarea.form-control{
      border:1px solid #D1CCDE !important;
      border-radius:10px !important;
      background:#fff !important;
      box-shadow:none;
      padding:.55rem .8rem;
      font-family:'Exo 2','Exo2',sans-serif;
    }
    .ck-card .form-control:focus,
    .ck-card .form-select:focus{
      border-color:#7BD550 !important;
      box-shadow:0 0 0 .15rem rgba(123,213,80,.18) !important;
      outline:0;
    }
    .ck-card .form-control.is-invalid,
    .ck-card .form-select.is-invalid{
      border-color:#dc3545 !important;
    }
    .ck-card .form-label{
      color:#262942;
      font-weight:500;
      margin-bottom:.35rem;
    }

    /* === Split background: metade branco (form) / metade cinza claro (sidebar) === */
    body.checkout-page{background:#fff}
    @media(min-width:992px){
      body.checkout-page{background:linear-gradient(to right,#ffffff 0 58%,#f9fafa 58% 100%)}
    }

    /* === UX: layout compacto com sticky === */
    .checkout-sticky{position:sticky;top:1rem;display:flex;flex-direction:column;gap:0}
    @media(max-width:991.98px){.checkout-sticky{position:static}}

    /* Card de preço destacado */
    .ck-card-price{
      background:linear-gradient(180deg,#f7fcf2 0%,#fff 60%);
      border:1px solid #e0eed1 !important;
      border-radius:14px;
    }
    .price-hero{
      font-family:'Anek Latin','Anek',sans-serif;
      font-size:2.25rem;
      font-weight:700;
      color:#2e7d32;
      line-height:1.1;
      margin:.25rem 0 .15rem;
    }
    .entrada-info-box{
      background:#eef4ff;
      border-left:3px solid #1254cc;
      border-radius:6px;
      padding:.55rem .7rem;
      font-size:.74rem;
      color:#262942;
      margin-top:.6rem;
      line-height:1.4;
    }
    .entrada-info-box.guarantee{
      background:#fdf6e8;
      border-left-color:#f5a623;
      font-size:.82rem;
    }

    /* Revisão compacta */
    .ck-resume-icon-sm{
      width:46px !important;
      height:46px !important;
    }
    .ck-resume-icon-sm img{max-width:65%;max-height:65%}
    .review-grid > div{padding:.15rem 0;color:#262942;line-height:1.4}

    /* Métodos de pagamento (entrada) */
    .entrada-metodo{display:flex;gap:.5rem}
    .entrada-metodo .pay-btn{flex:1;justify-content:center;padding:.6rem .75rem}
    .entrada-metodo .pay-btn img{height:18px;width:auto}

    /* Parcelas da entrada (1-10x pills) */
    .parcelas-entrada-selector{display:flex;flex-wrap:wrap;gap:.3rem}
    .parcelas-entrada-selector .parcela-btn{
      flex:1;min-width:44px;padding:.4rem .35rem;
      border:1.5px solid #e0e0e0;border-radius:8px;background:#fff;
      font-size:.8rem;font-weight:600;color:#262942;cursor:pointer;
      transition:all .15s;
    }
    .parcelas-entrada-selector .parcela-btn.active{
      border-color:#262942;background:#262942;color:#fff;
    }
    .parcelas-entrada-selector .parcela-btn small{display:block;font-size:.6rem;font-weight:500;opacity:.85}

    /* Cards mais compactos */
    .ck-card{margin-bottom:.75rem}
    .ck-head{border-bottom:1px solid #f1f1f4 !important}
    .ck-title{font-size:1rem !important}

    /* Container do checkout um pouco menor pra centralizar */
    main.flex-grow-1 .container{max-width:1140px}

    .pay-overlay{position:fixed;inset:0;background:rgba(38,41,66,.7);display:none;align-items:center;justify-content:center;z-index:9999;flex-direction:column;color:#fff;text-align:center}
    .pay-overlay-inner{display:flex;flex-direction:column;align-items:center;gap:12px}
    .pix-qr{max-width:220px;border-radius:8px}
    .payment-mode-selector{display:flex;flex-direction:column;gap:12px;margin-bottom:24px}
    .payment-mode-btn{display:flex;align-items:flex-start;gap:12px;padding:16px;border:2px solid #e0e0e0;border-radius:12px;background:#fff;cursor:pointer;transition:border-color .2s,background .2s;text-align:left}
    .payment-mode-btn:has(input:checked){border-color:#262942;background:#f5f6ff}
    .payment-mode-btn input[type=radio]{margin-top:2px;accent-color:#262942;flex-shrink:0}
    .payment-mode-btn .mode-label{font-weight:600;color:#262942;font-size:.95rem}
    .payment-mode-btn .mode-desc{font-size:.82rem;color:#666;margin-top:2px}
    .badge-recomendado-pay{background:#D4FFAD;color:#262942;font-size:.7rem;font-weight:700;padding:2px 8px;border-radius:20px;margin-left:8px;vertical-align:middle}
    .entrada-breakdown{background:#f8f9ff;border:1px solid #e0e3ff;border-radius:12px;padding:16px;margin:16px 0}
    .entrada-breakdown .eb-row{display:flex;justify-content:space-between;align-items:center;padding:4px 0}
    .entrada-breakdown .eb-label{color:#555;font-size:.88rem}
    .entrada-breakdown .eb-value{font-weight:700;color:#262942}
    .entrada-breakdown .eb-row.eb-entrada .eb-value{color:#2e7d32;font-size:1.1rem}
    .entrada-breakdown hr{border-color:#ccd}
    .parcelas-selector{display:flex;flex-wrap:wrap;gap:8px;margin:12px 0}
    .parcelas-selector .parcela-btn{padding:6px 14px;border:2px solid #e0e0e0;border-radius:20px;background:#fff;cursor:pointer;font-size:.85rem;font-weight:600;color:#333;transition:all .15s}
    .parcelas-selector .parcela-btn.active{border-color:#262942;background:#262942;color:#fff}
    .entrada-metodo{display:flex;gap:10px;margin:12px 0}
    .pay-btn{padding:8px 16px;border:2px solid #e0e0e0;border-radius:10px;background:#fff;cursor:pointer;display:flex;align-items:center;gap:8px;font-size:.9rem;font-weight:500;transition:border-color .15s}
    .pay-btn.active,.pay-btn:focus{border-color:#262942;outline:none}
    .pay-btn img{height:22px;width:auto}
  </style>
</head>
<body class="d-flex flex-column min-vh-100 f-anek checkout-page">
<?php emit_gtm_body(); ?>
<nav class="navbar navbar-light bg-white border-bottom py-3">
  <div class="container">
    <a class="navbar-brand" href="https://archa.com.br">
      <img src="<?= ASSETS_BASE ?>/images/logo-azul.png" alt="Archa" height="36">
    </a>
    <a href="https://api.whatsapp.com/send/?phone=5511942892984" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary f-exo">
      <img src="<?= ASSETS_BASE ?>/images/icon-whatsapp.png" alt="WhatsApp" width="18" class="me-1"> <?= t('Ajuda') ?>
    </a>
  </div>
</nav>
<div id="payOverlay" class="pay-overlay"><div class="pay-overlay-inner"><div class="spinner-border text-light mb-2"></div><p><?= t('Processando...') ?></p></div></div>
