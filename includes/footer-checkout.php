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
  <script src="js/simulador.js?v=<?= filemtime('js/simulador.js'); ?>"></script>

  <script src="js/editar-simulacao.js?v=<?= filemtime('js/editar-simulacao.js'); ?>"></script>
  <!--<script src="js/checkout-cache.js?v=<?= filemtime('js/checkout-cache.js'); ?>"></script>-->
  <script src="js/checkout-functions.js?v=<?= filemtime('js/checkout-functions.js'); ?>"></script>
  <script src="js/inputs.js?v=<?= filemtime('js/inputs.js'); ?>"></script>
  <script src="js/checkout-review.js?v=<?= filemtime('js/checkout-review.js'); ?>"></script>
  <!--<script src="js/checkout-pix.js?v=<?= filemtime('js/checkout-pix.js') ?>" defer></script>-->
</footer>
</body>


</html>