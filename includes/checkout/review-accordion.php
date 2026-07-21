<?php /** Resumo do projeto — accordion colapsível, começa recolhido */ ?>
<div class="ck-card mb-3" id="reviewAccordionCard">
  <!-- Cabeçalho clicável -->
  <div class="ck-head py-2 px-3 d-flex justify-content-between align-items-center review-acc-toggle"
       role="button" aria-expanded="false" aria-controls="reviewAccordionBody"
       style="cursor:pointer;user-select:none">
    <div class="d-flex align-items-center gap-2">
      <div class="ck-resume-icon ck-resume-icon-sm">
        <img src="<?= ASSETS_BASE ?>/images/icon-opcoes-contratacao.png" alt="">
      </div>
      <div>
        <div class="ck-muted" style="font-size:.7rem;line-height:1"><?= t('Opção de contratação') ?></div>
        <div id="sumPlano" class="fw-bold f-anek" style="font-size:1.05rem;line-height:1.2">Archa Duo</div>
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <!-- Botão Editar - estilo archa-form: azul escuro com ícone -->
      <button id="btnEditarProjeto" type="button"
              class="btn-editar-archa f-exo"
              title="<?= t('Editar projeto') ?>"
              onclick="event.stopPropagation()">
        <img src="<?= ASSETS_BASE ?>/images/pencil-green.png" alt="<?= t('Editar') ?>" width="13" style="filter:brightness(0) invert(1)">
        <?= t('Editar') ?>
      </button>
      <!-- Chevron indicador -->
      <span class="review-chevron" aria-hidden="true">&#8964;</span>
    </div>
  </div>

  <!-- Corpo colapsível (começa escondido) -->
  <div id="reviewAccordionBody" class="review-acc-body" style="display:none">
    <div class="ck-body py-2 px-3">
      <div class="review-grid small">
        <div><strong id="sumM2">–</strong> · <strong><span id="sumAmb">–</span></strong> <?= t('ambientes') ?> · <strong id="sumTipo">–</strong></div>
        <div><?= t('Profissionais {cat} de {reg}', ['cat' => '<strong id="sumCategoria">–</strong>', 'reg' => '<strong id="sumRegiao">–</strong>']) ?></div>
        <div><?= t('Entrega em {prazo} dias · {adic} itens adicionais', [
            'prazo' => '<strong><span id="sumPrazo">–</span></strong>',
            'adic'  => '<strong><span id="sumAdic">0</span></strong>',
        ]) ?></div>
      </div>
    </div>
  </div>
</div>

<style>
  /* === Botão Editar estilo archa-form === */
  .btn-editar-archa {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: #262942;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: .3rem .65rem;
    font-size: .75rem;
    font-weight: 600;
    cursor: pointer;
    transition: background .15s;
    white-space: nowrap;
  }
  .btn-editar-archa:hover { background: #1a1d30; }

  /* Chevron giratório */
  .review-chevron {
    font-size: 1.1rem;
    color: #888;
    transition: transform .25s;
    display: inline-block;
    line-height: 1;
  }
  .review-acc-toggle[aria-expanded="true"] .review-chevron {
    transform: rotate(180deg);
  }

  /* Animação suave */
  .review-acc-body {
    overflow: hidden;
    transition: height .25s ease;
  }
</style>

<script>
(function () {
  /* === Accordion toggle === */
  const toggle = document.querySelector('.review-acc-toggle');
  const body   = document.getElementById('reviewAccordionBody');
  if (toggle && body) {
    toggle.addEventListener('click', () => {
      const open = body.style.display !== 'none';
      body.style.display = open ? 'none' : 'block';
      toggle.setAttribute('aria-expanded', String(!open));
    });
  }

  /* === Botão Editar — redireciona para formulário preenchido === */
  const btnEditar = document.getElementById('btnEditarProjeto');
  if (!btnEditar) return;
  btnEditar.addEventListener('click', () => {
    const qs   = new URLSearchParams(location.search);
    const slug = (window.CHECKOUT_SLUG || qs.get('projeto') || localStorage.getItem('checkoutSlug') || '').trim();
    if (!slug) { alert("<?= addslashes(t('Projeto não identificado. Recarregue e tente novamente.')) ?>"); return; }
    localStorage.setItem('checkoutSlug', slug);
    location.href = `index.php?projeto=${slug}&mode=edit`;
  });
})();
</script>
