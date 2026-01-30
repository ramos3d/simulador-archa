<!-- ACCORDION DE PLANOS – agora com markup igual ao desktop ----------->
<div id="planAccordion" class="accordion accordion-flush">

  <!-- SOLO ------------------------------------------------------------ -->
  <div class="accordion-item plan plan-solo" data-produto="solo">
    <h2 class="accordion-header">
      <button type="button"
        class="accordion-button collapsed plan-header-btn"
        data-bs-toggle="collapse"
        data-bs-target="#planSolo"
        aria-expanded="false"
        aria-controls="planSolo">

        <div class="plan-header w-100 d-flex align-items-center">
          <div class="plan-main flex-grow-1">

            <!-- nome do plano acima do rádio -->
            <div class="plan-title text-uppercase">Solo</div>

            <div class="d-flex align-items-center">
              <span class="plan-radio me-2">
                <span class="radio-indicator"></span>
              </span>

              <div class="plan-price-block">
                <div class="install-line">
                  <span class="install-prefix">10x de</span>
                  <span id="priceSolo" class="plan-price">R$ 0,00</span>
                </div>

                <div class="cash-line">
                  <?php if (defined('PROMO_CLIENTE') && PROMO_CLIENTE): ?>
                    De <span class="old-price">
                      <span id="priceSoloBase">R$ 0,00</span>
                    </span>
                  <?php endif; ?>
                  ou à vista <strong id="priceSoloAvista" class="cash">R$ 0,00</strong>
                </div>
              </div>
            </div>

          </div>

          <!-- tarja “15% OFF à vista” à direita -->
          <span class="badge-off">15% OFF à vista</span>
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
  <div class="accordion-item plan plan-duo is-selected" data-produto="duo">
    <h2 class="accordion-header">
      <button type="button"
        class="accordion-button plan-header-btn"
        data-bs-toggle="collapse"
        data-bs-target="#planDuo"
        aria-expanded="true"
        aria-controls="planDuo">

        <div class="plan-header w-100 d-flex align-items-center">
          <div class="plan-main flex-grow-1">

            <div class="d-flex align-items-center mb-1">
              <div class="plan-title text-uppercase mb-0">Duo</div>
              <span class="badge-recomendado ms-2">Recomendado</span>
            </div>

            <div class="d-flex align-items-center">
              <span class="plan-radio me-2">
                <span class="radio-indicator"></span>
              </span>

              <div class="plan-price-block">
                <div class="install-line">
                  <span class="install-prefix">10x de</span>
                  <span id="priceDuo" class="plan-price">R$ 0,00</span>
                </div>

                <div class="cash-line">
                  <?php if (defined('PROMO_CLIENTE') && PROMO_CLIENTE): ?>
                    De <span class="old-price">
                      <span id="priceDuoBase">R$ 0,00</span>
                    </span>
                  <?php endif; ?>
                  ou à vista <strong id="priceDuoAvista" class="cash">R$ 0,00</strong>
                </div>
              </div>
            </div>

          </div>

          <span class="badge-off">15% OFF à vista</span>
        </div>

      </button>
    </h2>

    <div id="planDuo" class="accordion-collapse collapse show" data-bs-parent="#planAccordion">
      <div class="accordion-body py-2">
        <ul class="benef-list mb-0 text-start linha-custom">
          <li><strong>Tudo do Solo +</strong></li>
          <li><strong>2</strong> projetos completos</li>
          <li><strong>Vídeos explicativos</strong> dos projetos</li>
          <li>
            <strong>Desconto de 10%</strong> na contratação de
            <a href="<?= BASE_URL ?>/arquivos/tabela_de_serviços_adicionais.pdf"
              target="_blank" rel="noopener noreferrer">serviços adicionais</a>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- TRIO ------------------------------------------------------------- -->
  <div class="accordion-item plan plan-trio" data-produto="trio">
    <h2 class="accordion-header">
      <button type="button"
        class="accordion-button collapsed plan-header-btn"
        data-bs-toggle="collapse"
        data-bs-target="#planTrio"
        aria-expanded="false"
        aria-controls="planTrio">

        <div class="plan-header w-100 d-flex align-items-center">
          <div class="plan-main flex-grow-1">

            <div class="plan-title text-uppercase">Trio</div>

            <div class="d-flex align-items-center">
              <span class="plan-radio me-2">
                <span class="radio-indicator"></span>
              </span>

              <div class="plan-price-block">
                <div class="install-line">
                  <span class="install-prefix">10x de</span>
                  <span id="priceTrio" class="plan-price">R$ 0,00</span>
                </div>

                <div class="cash-line">
                  <?php if (defined('PROMO_CLIENTE') && PROMO_CLIENTE): ?>
                    De <span class="old-price">
                      <span id="priceTrioBase">R$ 0,00</span>
                    </span>
                  <?php endif; ?>
                  ou à vista <strong id="priceTrioAvista" class="cash">R$ 0,00</strong>
                </div>
              </div>
            </div>

          </div>

          <span class="badge-off">15% OFF à vista</span>
        </div>

      </button>
    </h2>

    <div id="planTrio" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
      <div class="accordion-body py-2">
        <ul class="benef-list mb-0 text-start linha-custom">
          <li><strong>Tudo do Duo +</strong></li>
          <li><strong>3</strong> propostas de projetos</li>
          <li>Planilha de <strong>orçamento</strong> de produtos</li>
          <li><strong>+1</strong> reunião de revisão</li>
          <li>
            <strong>Desconto de 20%</strong> na contratação de
            <a href="<?= BASE_URL ?>/arquivos/tabela_de_serviços_adicionais.pdf"
              target="_blank" rel="noopener noreferrer">serviços adicionais</a>
          </li>
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

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const acc = document.getElementById('planAccordion');

    // garante que sempre exista um selecionado (por padrão DUO)
    if (!acc.querySelector('.plan.is-selected')) {
      const def = acc.querySelector('.plan-duo') || acc.querySelector('.plan');
      def?.classList.add('is-selected');
    }

    // clique no header marca o plano
    acc.addEventListener('click', (e) => {
      const btn = e.target.closest('.accordion-button');
      if (!btn) return;
      const plan = btn.closest('.plan');
      if (!plan) return;

      acc.querySelectorAll('.plan.is-selected').forEach(p => {
        if (p !== plan) p.classList.remove('is-selected');
      });
      plan.classList.add('is-selected');
    });

    // abrir o collapse também marca o plano
    acc.querySelectorAll('.accordion-collapse').forEach(col => {
      col.addEventListener('show.bs.collapse', () => {
        const plan = col.closest('.plan');
        acc.querySelectorAll('.plan.is-selected').forEach(p => p.classList.remove('is-selected'));
        plan.classList.add('is-selected');
      });
    });
  });
</script>