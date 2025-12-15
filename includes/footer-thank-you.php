<?php if (IS_MOBILE): ?>
  <footer  class="bg-azul text-white mt-auto py-4">
    <div class="container text-center px-4">

      <!-- logo -->
      <img src="images/logo-branca.png" alt="Archa" style="height:25px" class="mb-5 mx-auto">
      <hr class="my-3 opacity-25 mx-auto custom-line mb-5">

      <!-- ícones sociais -->
      <div class="d-flex justify-content-center gap-4 mb-5">
        <a href="https://www.youtube.com/channel/UCObA-uzVeGRphE9L4Yv7-jw" target="_blank"><img src="images/youtube.png" alt="YouTube" height="22"></a>
        <a href="https://www.instagram.com/archa.company/" target="_blank"><img src="images/instagram.png" alt="Instagram" height="22"></a>
        <a href="https://www.linkedin.com/company/archacompany/posts/?feedView=all" target="_blank"><img src="images/linkedin.png" alt="LinkedIn" height="22"></a>
      </div>
      <small>&copy; 2025 Archa</small>
    </div>
  </footer>

<?php else: ?>


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

  </footer>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</body>

</html>