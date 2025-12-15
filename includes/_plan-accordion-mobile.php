<!-- ACCORDION DE PLANOS – markup idêntico ao desktop ------------------->
<div id="planAccordion" class="accordion accordion-flush">

  <!-- SOLO ------------------------------------------------------------ -->
  <div class="accordion-item plan plan-solo" data-produto="solo">
    <h2 class="accordion-header">
      <button type="button" class="accordion-button d-flex align-items-center collapsed"
        data-bs-toggle="collapse" data-bs-target="#planSolo"
        aria-expanded="false" aria-controls="planSolo">

        <span class="radio-indicator me-2"></span>
        <span class="flex-grow-1 f-anek mt-1">Solo</span>

        <!-- PARCELADO -->
        <!-- SOLO -->
        <div class="text-end mobile-price-stack">
          <?php if (defined('PROMO_CLIENTE') && PROMO_CLIENTE): ?>
            <span class="old-price-mobile"><strike id="priceSoloBase" class="text-muted">R$ 0,00</strike></span>
          <?php endif; ?>
          <div class="parcelado-line">
            <span class="label-parcelado">10x de</span>
            <span id="priceSolo" class="fw-bold me-3">R$ 0,00</span>
          </div>
          <div class="small text-muted cash-line me-3 f-w-400 f-exo text-secondary">
            ou à vista <strong id="priceSoloAvista">R$ 0,00</strong>
          </div>
        </div>
      </button>
    </h2>

    <div id="planSolo" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
      <div class="accordion-body py-2">
        <ul class="benef-list mb-0 text-start linha-custom">
          <li><strong>1 projeto</strong> Moodboard e referências; planta de layout; planta de obra civil; memorial descritivo</li>
          <li><strong>2</strong> revisões</li>
          <li><strong>Assistente pessoal</strong> do início ao fim</li>
          <li><strong>Desconto</strong> com marcas parceiras</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- DUO -------------------------------------------------------------- -->
  <div class="accordion-item plan plan-duo" data-produto="duo">
    <h2 class="accordion-header">
      <button type="button" class="accordion-button d-flex align-items-center"
        data-bs-toggle="collapse" data-bs-target="#planDuo"
        aria-expanded="true" aria-controls="planDuo">

        <span class="radio-indicator me-2"></span>
        <span class="flex-grow-1 f-anek mt-1">
          Duo <span class="badge bg-success-subtle text-success ms-2">Recomendado</span>
        </span>

        <!-- DUO -->
        <div class="text-end mobile-price-stack">
          <?php if (defined('PROMO_CLIENTE') && PROMO_CLIENTE): ?>
            <span class="old-price-mobile"><strike id="priceDuoBase" class="text-muted">R$ 0,00</strike></span>
          <?php endif; ?>
          <div class="parcelado-line">
            <span class="label-parcelado">10x de</span>
            <span id="priceDuo" class="fw-bold me-3">R$ 0,00</span>
          </div>
          <div class="text-muted cash-line text-end f-14 me-3 f-w-400 f-exo text-secondary">
            ou à vista <strong id="priceDuoAvista">R$ 0,00</strong>
          </div>
        </div>

      </button>
    </h2>

    <div id="planDuo" class="accordion-collapse collapse show" data-bs-parent="#planAccordion">
      <div class="accordion-body py-2">


        <ul class="benef-list mb-0 text-start linha-custom">
          <li><strong>Tudo do Solo +</strong></li>
          <li><strong>2</strong> projetos completos</li>
          <li><strong>Vídeos explicativos</strong> dos projetos</li>
          <li> <strong>Desconto de 10%</strong> na contratação de <a href="<?= BASE_URL ?>/arquivos/tabela_de_serviços_adicionais.pdf" target="_blank" rel="noopener noreferrer">serviços adicionais</a> </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- TRIO ------------------------------------------------------------- -->
  <div class="accordion-item plan plan-trio" data-produto="trio">
    <h2 class="accordion-header">
      <button type="button" class="accordion-button d-flex align-items-center collapsed"
        data-bs-toggle="collapse" data-bs-target="#planTrio"
        aria-expanded="false" aria-controls="planTrio">

        <span class="radio-indicator me-2"></span>
        <span class="flex-grow-1 f-anek mt-1">Trio</span>

        <!-- TRIO -->
        <div class="text-end mobile-price-stack">
          <?php if (defined('PROMO_CLIENTE') && PROMO_CLIENTE): ?>
            <span class="old-price-mobile"><strike id="priceTrioBase" class="text-muted">R$ 0,00</strike></span>
          <?php endif; ?>
          <div class="parcelado-line">
            <span class="label-parcelado">10x de</span>
            <span id="priceTrio" class="fw-bold me-3">R$ 0,00</span>
          </div>
          <div class="small text-muted cash-line me-3 f-w-400 f-exo text-secondary">
            ou à vista <strong id="priceTrioAvista">R$ 0,00</strong>
          </div>
        </div>

      </button>
    </h2>

    <div id="planTrio" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
      <div class="accordion-body py-2">
        <ul class="benef-list mb-0 text-start linha-custom ">
          <li><strong>Tudo do Duo +</strong></li>
          <li><strong>3</strong> propostas de projetos</li>
          <li>Planilha de <strong>orçamento</strong> de produtos</li>
          <li><strong>+1</strong> reunião de revisão</li>
          <li><strong>Desconto de 20%</strong> na contratação de <a href="<?= BASE_URL ?>/arquivos/tabela_de_serviços_adicionais.pdf" target="_blank" rel="noopener noreferrer">serviços adicionais</a></li>
        </ul>
      </div>
    </div>
  </div>

  <!-- BOTÃO AVANÇAR ---------------------------------------------------- -->
  <button type="button"
    id="btnIrPagamento"
    class="text-secondary btn-outline-grey mt-4 w-100 f-exo"
    disabled>
    Avançar
  </button>
  

  <!-- CARTÃO DE VANTAGENS --------------------------------------------- -->
  <div id="vant-card" class="vant-card text-start mt-4 f-exo">
    <hr class="my-3">
    <h6 class="mb-2 fw-semibold">Itens no orçamento</h6>
    <ul id="vantagensExtras" class="vant-list benef-list mb-2 d-none"></ul>
    <ul id="vantItensMobile" class="vant-list benef-list mb-2"></ul>
  </div>

</div><!-- /planAccordion -->

<style>

</style>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const acc = document.getElementById('planAccordion');

    // 1) seleção inicial (se nada marcado, marca o DUO; ajuste se quiser outro)
    if (!acc.querySelector('.plan.is-selected')) {
      const def = acc.querySelector('.plan-duo') || acc.querySelector('.plan');
      def?.classList.add('is-selected');
    }

    // 2) clicar no cabeçalho SEMPRE mantém/define a seleção naquele plano
    acc.addEventListener('click', (e) => {
      const btn = e.target.closest('.accordion-button');
      if (!btn) return;
      const plan = btn.closest('.plan');
      if (!plan) return;

      // remove dos irmãos, aplica no clicado
      acc.querySelectorAll('.plan.is-selected').forEach(p => {
        if (p !== plan) p.classList.remove('is-selected');
      });
      plan.classList.add('is-selected');
    });

    // 3) se abrir via script/teclado, também ajusta seleção
    acc.querySelectorAll('.accordion-collapse').forEach(col => {
      col.addEventListener('show.bs.collapse', () => {
        const plan = col.closest('.plan');
        acc.querySelectorAll('.plan.is-selected').forEach(p => p.classList.remove('is-selected'));
        plan.classList.add('is-selected');
      });
    });
  });
</script>