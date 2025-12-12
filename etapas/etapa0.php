<!-- etapa0_novo.php – simulador completo com seções independentes -->
<link rel="stylesheet" href="css/simulador-layout.css?v=<?php echo filemtime('css/simulador-layout.css'); ?>">
<link rel="stylesheet" href="css/card-orcamento.css?v=<?php echo filemtime('css/card-orcamento.css'); ?>">
<?php include('components/modal-persona.php'); ?>

<div class="my-4" style="margin-left: -115px;">
  <div class="row  justify-content-center">


    <!-- COLUNA PRINCIPAL ------------------------------------------- -->
    <div class="col-lg-7 col-xl-8">
      <div class="title-badge-card mb-2">
        <h1 class="bg-azul text-green text-start  f-anek fw-bold">Quanto custa seu projeto de arquitetura e decoração?</h1>
      </div>
      <form id="form-inteligente">

        <!-- etapa0.php (única versão, funciona p/ desk + mobile) -->
        <?php

        include SEC_DIR . 'section1.php';
        include SEC_DIR . 'section2.php';
        include SEC_DIR . 'section3.php';
        ?>

      </form>
    </div><!-- /formulário -->

    <!-- COLUNA LATERAL (card de orçamento) ------------------------- -->



    <!-- COLUNA LATERAL (card de orçamento) ------------------------- -->
    <aside class="col-lg-4 col-xl-3">
      <!-- Pilha sticky única -->
      <div id="aside-stick">
        <div id="card-preco" class="card shadow-sm p-4" style="min-width:420px">
          <?php if (defined('PROMO_CLIENTE') && PROMO_CLIENTE): ?>
            <div class="promo-header-blackfriday f-exo">
              <!--<span class="promo-text">Semana do Cliente <strong>15% OFF</strong></span>-->
              <span class="promo-text"><b>Black Friday Archa </b><strong class="text-rosa">15% OFF</strong></span>

            </div>

          <?php endif; ?>

          <h5 class="fw-bold mb-1 text-start f-exo card-m9">Orçamento do seu projeto de arquitetura</h5>
          <p id="orcamento-resumo" class="card-m9 small text-secondary text-start itens-margem-b f-anek f-14 ">Nenhuma informação preenchida</p>
          <div id="vant-card" class="vant-card text-start f-exo lista-desktop-margem " style="margin-bottom: -15px;">
            <ul id="vantagensExtras" class="vant-list benef-list mb-2 d-none"></ul>
            <ul id="vantItensMobile" class="vant-list benef-list mb-2"></ul>
          </div>

          <hr class="my-3 card-m9">
          <p class="text-start f-14 f-anek card-m9" style="margin-bottom: -1px;">Opções de contratação</p>

          <!-- ... mantém TODO o conteúdo do accordion exatamente como está ... -->
          <div class="accordion accordion-flush" id="planAccordion">

            <!-- ================= SOLO ================= -->
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

                      <!-- Nome do plano ACIMA do rádio -->
                      <div class="plan-title text-uppercase f-anek">Solo</div>

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

                    <!-- tarja azul 15% OFF à direita -->
                    <span class="badge-off">15% OFF à vista</span>
                  </div>
                </button>
              </h2>

              <div id="planSolo" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
                <div class="accordion-body py-2">
                  <ul class="benef-list mb-0 text-start mb-2">
                    <li><strong>1 projeto completo:</strong> Moodboard e Referências; Imagens 3D,
                      Planta de Layout; Planta de Obra Civil; Memorial Descritivo
                    </li>
                    <li><strong>2</strong> revisões</li>
                    <li><strong>Assistente pessoal</strong> do início ao fim</li>
                    <li><strong>Desconto</strong> com marcas parceiras</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- ================= DUO ================== -->
             
            <div class="accordion-item plan plan-duo selected" data-produto="duo">
              <h2 class="accordion-header">
                <button type="button"
                  class="accordion-button collapsed plan-header-btn"
                  data-bs-toggle="collapse"
                  data-bs-target="#planDuo"
                  aria-expanded="false"
                  aria-controls="planDuo">

                 
                  <div class="plan-header w-100 d-flex align-items-center">
                    <div class="plan-main flex-grow-1">

                      <!-- Nome + badge “Recomendado” -->
                      <div class="d-flex align-items-center mb-1">
                        <div class="plan-title text-uppercase f-anek mb-0">Duo</div>
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

              <div id="planDuo" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
                <div class="accordion-body py-2">
                  <ul class="benef-list mb-0 text-start mb-2">
                    <li><strong>Tudo do Solo +</strong></li>
                    <li><strong>2</strong> projetos completos</li>
                    <li><strong>Vídeos explicativos</strong> dos projetos</li>
                    <li>
                      <strong>Desconto de 10%</strong> na contratação de
                      <a href="<?= BASE_URL ?>/arquivos/tabela_de_serviços_adicionais.pdf"
                        target="_blank"
                        rel="noopener noreferrer">serviços adicionais</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- ================= TRIO ================= -->
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

                      <div class="plan-title text-uppercase f-anek">Trio</div>

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
                  <ul class="benef-list mb-0 text-start mb-2">
                    <li><strong>Tudo do Duo +</strong></li>
                    <li><strong>3</strong> propostas de projetos</li>
                    <li>Planilha de <strong>orçamento</strong> de produtos</li>
                    <li><strong>+1</strong> reunião de revisão</li>
                    <li>
                      <strong>Desconto de 20%</strong> na contratação de
                      <a href="<?= BASE_URL ?>/arquivos/tabela_de_serviços_adicionais.pdf"
                        target="_blank"
                        rel="noopener noreferrer">serviços adicionais</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

          </div><!-- /accordion -->
          <!-- /accordion -->

          <!--<button type="button"
            id="btnIrPagamento"
            class="text-secondary btn-outline-grey mt-4 w-100 btnIrParaPagamento btnCustomizado"
            data-bs-toggle="modal"
            data-bs-target="#modalPersona"
            disabled style="margin-top: -1px; padding:3%;" style="width: 105% !important;
    margin-left: -9px !important;">
            Avançar
          </button> -->
          <button
            type="button"
            id="btnIrPagamento"
            class="text-secondary btn-outline-grey mt-4 w-100 btnIrParaPagamento btnCustomizado f-exo"
            style="margin-top:-1px; padding:3%; width:105%; margin-left:-9px;">
            Avançar
          </button>

        </div>

        <!-- Âncora para detectar quando a lista encosta no card -->
        <div id="vant-anchor" aria-hidden="true"></div>

        <!-- Lista de vantagens em fluxo normal -->
        <div class="card-preco" style="min-width:340px">
          <div id="vant-card" class="vant-card text-start mt-4 lista-beneficios-customizada">
            <ul class="vant-list mb-2 js-vantagens-list space-line"></ul>
            <!-- <a id="vant-link" href="#" class="vant-link">Todas as vantagens</a> -->
          </div>
        </div>
      </div>
    </aside>

  </div><!-- /row -->
</div>


<style>
  /* Desktop */
  .lista-desktop-margem {
    margin-left: -6%;
  }

  /* Desktop: trata apenas a lista dentro do card do topo */
  @media (min-width: 992px) {

    /* remove o ícone verde de cada <li> da lista do topo */
    .lista-desktop-margem .vant-list li::before {
      content: none !important;
      background: none !important;
    }

    /* garante alinhamento à esquerda e não deixa “sobra” de espaço do bullet */
    .lista-desktop-margem .vant-list li {
      padding-left: 0 !important;
      justify-content: space-between;
      /* mantém o lápis à direita */
      align-items: baseline;
    }

    .lista-desktop-margem .vant-list li .text-part {
      margin-left: 0 !important;
      /* texto realmente colado à esquerda */
    }

    /* opcional: ajuste fino do lápis */
    .lista-desktop-margem .vant-list li .edit {
      margin-left: .5rem;
    }

    /* ====== Lista FINAL (carregada do JSON) – volta ao fluxo normal ====== */
    .vant-card .js-vantagens-list li {
      display: block !important;
      justify-content: initial !important;
      gap: 0 !important;
      padding-left: 22px;
      position: relative;
    }
  }
</style>