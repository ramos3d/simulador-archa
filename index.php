<?php
include 'config.php';
include 'includes/header.php';
include 'config-promo.php';
?>

<script>
  // Sem essa linha, u chekout confirmado pode ser sobreposto por outro e com isso perde-se o fluxo de pagamento
  //localStorage.clear(); sessionStorage.clear();
</script>

<script>
  (function() {
    const qs = new URLSearchParams(location.search);
    const mode = (qs.get('mode') || '').toLowerCase();
    const slug = qs.get('projeto') || '';
    const qTok = qs.get('token') || qs.get('lead') || '';
    const isEdit = (mode === 'edit') && (slug || qTok);
    const forceReset = qs.get('reset') === '1';

    function clearAll() {
      try {
        ['leadUuid', 'uuidLaravel', 'leadActive'].forEach(k => localStorage.removeItem(k));
        // limpe aqui outros caches seus, se houver
        localStorage.clear();
        sessionStorage.clear();
      } catch (e) {}
    }

    // reset forçado via ?reset=1
    if (forceReset) {
      clearAll();
      return;
    }

    if (isEdit) {
      if (qTok) {
        try {
          localStorage.setItem('leadUuid', qTok);
          localStorage.setItem('uuidLaravel', qTok); // compat legado
          localStorage.setItem('leadActive', '1'); // habilita envio incremental
        } catch (e) {}
      }
      // não limpar storage em modo edição
      window.__EDIT_MODE__ = true;
      window.__EDIT_SLUG__ = slug || null;
    } else {
      // abertura "do zero": limpa para novo lead/uuid
      clearAll();
    }
  })();
</script>



<main class="flex-grow-1">
  <div class="container text-center  mb-5"><!-- px-lg mantém respiro só no desktop -->
    <div id="form-slider-wrapper"><!-- sem d-flex p/ mobile -->
      <div id="form-slider">

        <div id="form-inteligente" action="#" class="needs-validation" novalidate>
          <?php include SEC_DIR . 'etapa0.php'; ?>
        </div>

      </div>
    </div>
  </div>
</main>


<!-- Sentinel p/ detectar aproximação do footer -->
<div id="footer-sentinel" aria-hidden="true"></div>

<?php if (IS_MOBILE): ?>
  <?php include 'includes/footer-mobile.php'; ?>
<?php else: ?>
  <?php include 'includes/footer.php'; ?>
<?php endif; ?>

<style>
  /* Botão outline azul Archa */
  .btn-outline-azull {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 12px;

    padding: 8px 14px;
    border-radius: 12px;
    border: 2px solid #262942;
    background-color: #ffffff;

    color: #262942;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    line-height: 1;

    transition: background-color 0.2s ease,
      color 0.2s ease,
      box-shadow 0.2s ease,
      transform 0.1s ease;
    text-align: center;
  }

  /* Ícone dentro do botão */
  .btn-outline-azull .btn-icon-whatsapp {
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .btn-outline-azull .btn-icon-whatsapp img {
    display: block;
    width: 32px;
    height: 32px;
  }

  /* Hover / foco */
  .btn-outline-azull:hover,
  .btn-outline-azull:focus {
    background-color: #262942;
    color: #ffffff;
    text-decoration: none;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    transform: translateY(-1px);
  }

  /* Para manter o ícone colorido mesmo com hover escuro */
  .btn-outline-azull:hover .btn-icon-whatsapp img,
  .btn-outline-azull:focus .btn-icon-whatsapp img {
    filter: none;
  }
</style>