<script>
  document.addEventListener('DOMContentLoaded', () => {
    const banner = document.getElementById('banner-preenchimento');
    const cardPreco = document.getElementById('card-preco');
    const btnPreco = document.getElementById('btnVerPreco');

    btnPreco?.addEventListener('click', () => {
      banner?.classList.add('d-none');
      cardPreco?.classList.remove('d-none');
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  });
</script>
<?php include('components/modal-persona.php'); ?>
<div id="mobile-backdrop" aria-hidden="true"></div>

<!-- folha de estilo do card -->
<link rel="stylesheet"
  href="css/mobile-card-orcamento.css?v=<?= filemtime('css/mobile-card-orcamento.css'); ?>">

<!-- faixa lilás – aparece primeiro -->
<div id="banner-preenchimento"
  class=" bg-purple text-white d-flex align-items-center gap-2 px-3 py-2">
  <img src="images/ico-dollar.png" alt="icone monetário">
  <span class="subtitle-mobile">Preencha para liberar o orçamento</span>
</div>

<!-- cabeçalho verde + acordeão – começa oculto -->
<div id="card-preco" class="fixed-top w-100  text-blue shadow-sm d-none">
  <div class="d-flex bg-green">

    <!-- ícone do cifrão, tamanho original -->
    <span class="ico-dollar px-3">
      <img src="images/ico-dollar-mobile.png" alt="icone monetário">
    </span>
    <div class="flex-grow-1 px-1 py-2 f-exo f-500 valor">
      A partir de: 10x de <span id="precoMin" class="fw-bold">R$
        <div class="spinner-grow spinner-grow-sm text-dark ms-2" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </span>
    </div>

    <!-- caret quadrado -->
    <button type="button"
      class="btn btn-caret"
      data-bs-toggle="collapse"
      data-bs-target="#orcAccordion"
      aria-expanded="false">
      <img src="images/arrow-down-lg.png" alt="expandir">
    </button>
  </div>

  <!-- acordeão de planos (mesmo markup do desktop) -->
  <div id="orcAccordion" class="collapse bg-white">
    <?php include 'includes/plan-accordion-mobile.php'; ?>
  </div>
</div>


<h1 class="azul text-start px-3 mt-4 f-bold f-anek">
  Quanto custa seu projeto de arquitetura e decoração?
</h1>

<main class="container-xl px-lg-5 my-4">
  <div class="row g-lg-5">
    <div class="col-lg-8 col-xl-9">
      <form id="form-inteligente">
        <?php
        include SEC_DIR . 'section1.php';
        include SEC_DIR . 'section2.php';
        include SEC_DIR . 'section3.php';
        ?>
      </form>
    </div>
  </div>
</main>



<script>
  document.addEventListener('DOMContentLoaded', () => {
    const card = document.getElementById('card-preco');
    const acc = document.getElementById('orcAccordion');

    if (!card || !acc) return;

    // função que sincroniza a classe com o estado do collapse
    const sync = () => {
      card.classList.toggle('is-open', acc.classList.contains('show'));
    };

    // quando abrir/fechar o collapse, atualiza a classe
    acc.addEventListener('shown.bs.collapse', sync);
    acc.addEventListener('hidden.bs.collapse', sync);

    // estado inicial (fechado)
    sync();
  });
</script>

