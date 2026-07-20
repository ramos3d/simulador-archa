<?php include 'includes/header.php'; ?>

<main class="container my-5 bg-purple py-5 px-3 rounded-4">
  <h1 class="f-anek fw-bold text-center font-blue">
    Escolha a <span class="text-wine">melhor opção</span> para você!
  </h1>
  <p class="text-center text-muted mb-5">
    Obrigado por compartilhar suas informações.<br class="d-none d-md-block">
    Agora é só escolher o investimento que mais combina com seu momento
    e dar o próximo passo rumo ao seu novo espaço.
  </p>
  <div id="spinner-loading" class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Carregando...</span>
    </div>
  </div>

  <div class="row justify-content-center g-4 col-md-10 mx-auto" id="planos-wrapper"></div>

  <div class="text-center mt-5">
    <h5 class="fw-bold f-anek mb-3 text-blue">Deseja revisar o seu orçamento?</h5>
    <p class="text-secondary mb-3">
      Basta clicar no botão e alterar as informações que preencheu no seu formulário.
    </p>
    <a href="index.php" class="btn btn-outline-dark px-4 py-2 rounded text-secondary">Quero fazer a revisão</a>
  </div>

</main>


<?php include 'includes/footer.php'; ?>

<!-- Config + lógica -->
<script src="js/apiConfig.js"></script>
<script src="js/telaOrcamento.js"></script>

<style>
  .btn {
    padding: 3%;
  }

  :root {
    --archa-green: #D4FFAD;
    --archa-pink: #E893C7;
    --archa-blue: #131935;
  }

  .plan-card {
    border-radius: 1rem;
  }

  .plan-featured {
    background: var(--archa-blue) !important;
    color: #fff;
  }

  .plan-featured .btn {
    background: var(--archa-green);
    color: #000;
  }

  .plan-icon {
    width: 40px;
    height: 40px;
  }

  .popular-tag {
    background: var(--archa-pink);
  }

  .btn-choose {
    border: none;
    transition: background-color 0.2s ease;
  }

  .btn-flex,
  .btn-duo {
    background-color: var(--archa-blue);
    color: var(--archa-green);
  }

  .btn-trio {
    background-color: var(--archa-green);
    color: var(--archa-blue);
  }

  .btn-flex:hover,
  .btn-duo:hover {
    background-color: #0f152d;
    /* leve escurecimento do azul */
  }

  .btn-trio:hover {
    background-color: #c4ec9b;
    /* leve escurecimento do verde */
  }

  .btn-outline-dark:hover {
    background-color: transparent;
    color: #0f152d!important;
  }


  body {
    background-color: #DEBDF2;
  }
</style>
<script>
  const navbar = $(`#navbar`);
  navbar.addClass('bg-purple');
</script>