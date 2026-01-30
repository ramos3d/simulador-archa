<?php include 'components/widget-whatsapp.php'; ?>
<footer style="background-color: #262942; color: #fff;" class="pt-5 pb-5 mt-auto">

  <div class="container">
    <div class="row align-items-center">
      <div class="col-12 col-md-4 mb-3 mb-md-0">
        <img src="images/logo-branca.png" alt="Archa Logo" style="height: 30px;">
      </div>
    </div>

    <hr class="border-light my-3" style="opacity: .3;">

    <!-- Barra inferior: texto à esquerda, ícones à direita -->
    <div class="d-flex align-items-center justify-content-between">
      <div class="small" style="font-family: 'Roboto', 'Arial Narrow', Arial, sans-serif; font-size:14px;">&copy; 2025 Archa</div>

      <div class="d-flex align-items-center gap-3">
        <a href="https://www.youtube.com/channel/UCObA-uzVeGRphE9L4Yv7-jw" target="_blank" rel="noopener">
          <img src="images/youtube.png" alt="YouTube" style="height:20px;">
        </a>
        <a href="https://www.instagram.com/archa.company/" target="_blank" rel="noopener">
          <img src="images/instagram.png" alt="Instagram" style="height:20px;">
        </a>
        <a href="https://www.linkedin.com/company/archacompany/posts/?feedView=all" target="_blank" rel="noopener">
          <img src="images/linkedin.png" alt="LinkedIn" style="height:20px;">
        </a>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <script src="js/config.js?v=<?= filemtime('js/config.js'); ?>"></script>

  <script>
    window.CALC_ENDPOINT = 'calculo-precificacao.php';
  </script>
  <script src="js/calculator.js?v=<?= filemtime('js/calculator.js'); ?>"></script>
  <script src="js/site.js?v=<?= filemtime('js/site.js'); ?>"></script>
  <script src="js/getSeenPrices.js?v=<?= filemtime('js/getSeenPrices.js'); ?>"></script>
  <script src="js/simulador.js?v=<?= filemtime('js/simulador.js'); ?>"></script>
  <script src="js/inputs.js?v=<?= filemtime('js/inputs.js'); ?>"></script>

  <!-- footer (depois de simulador.js) -->
  <script src="js/lead-sync.js?v=<?= filemtime('js/lead-sync.js'); ?>"></script>

  <!--<script src="js/lead-cache.js?v=<?= filemtime('js/lead-cache.js'); ?>"></script>-->
  <script src="js/prices-decoration.js?v=<?= filemtime('js/prices-decoration.js'); ?>"></script>

  <script>
    (function() {
      const root = document.documentElement;
      const body = document.body;
      const card = document.getElementById('card-preco');
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

      // Solta o card quando a lista encostar no fundo do card
      function checkAnchor() {
        if (!anchor) return;
        const cardBottom = card.getBoundingClientRect().bottom;
        const anchorTop = anchor.getBoundingClientRect().top;
        const safety = 8; // margem de segurança
        if (anchorTop <= cardBottom + safety) body.classList.add('release-card');
        else body.classList.remove('release-card');
      }

      // Perto do footer, soltar a pilha inteira
      if (sentinel) {
        const io = new IntersectionObserver((entries) => {
          for (const e of entries) {
            if (e.isIntersecting) body.classList.add('near-footer');
            else body.classList.remove('near-footer');
          }
        }, {
          root: null,
          threshold: 0,
          rootMargin: '0px 0px -20% 0px'
        });
        io.observe(sentinel);
      }

      // Eventos
      window.addEventListener('load', () => {
        measure();
        checkAnchor();
      });
      window.addEventListener('resize', () => {
        measure();
        checkAnchor();
      });
      window.addEventListener('scroll', () => {
        requestAnimationFrame(checkAnchor);
      }, {
        passive: true
      });

      // Recalcular quando o accordion mudar a altura do card
      document.addEventListener('shown.bs.collapse', measure, true);
      document.addEventListener('hidden.bs.collapse', measure, true);

      // Primeira medição
      measure();
    })();
  </script>

  <script src="js/editar-simulacao.js?v=<?= filemtime('js/editar-simulacao.js'); ?>"></script>
  <!--<script src="js/checkout-cache.js?v=<?= filemtime('js/checkout-cache.js'); ?>"></script>-->
  <script src="js/checkout-functions.js?v=<?= filemtime('js/checkout-functions.js'); ?>"></script>
  <script src="js/checkout-start.js?v=<?= filemtime('js/checkout-start.js'); ?>"></script>
</footer>
</body>

</html>