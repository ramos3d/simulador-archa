<!-- ========== PASSO 3 ===================================== -->
<div class="accordion step-card qa-accordion" id="accordionStep3">
  <div class="accordion-item">
    <h2 class="accordion-header" id="head3">
      <button class="accordion-button collapsed gap-3 " type="button"
        data-bs-toggle="collapse" data-bs-target="#step3"
        aria-expanded="false" aria-controls="step3">
        <span class="step-badge d-inline-flex align-items-center
                     justify-content-center flex-shrink-0">3</span>
        <span class="fw-bold fs-5 text-blue f-anek"><?= t('O que você deseja para o novo espaço?') ?></span>
      </button>
    </h2>

    <div id="step3" class="accordion-collapse collapse show">
      <div class="accordion-body">

        <p class=" text-blue mb-3 intro-indent text-start f-14 f-exo">
          <?= t('Selecione os itens que farão parte da sua reforma ou construção.') ?>
        </p>
        <!-- contêiner: só “row g-2” -->
        <div id="workItems" class="row g-2 mb-4">

          <div class="col-12 col-md-6 mb-2">
            <div class="multi-option option-box p-3 h-100 d-flex align-items-center gap-2" data-key="marcenaria">
              <span class="check-indicator"><img src="images/checked.png" alt=""></span>
              <img class="flex-shrink-0" height="42" src="images/icons/questao_8/Default/marcenaria.png" alt="">
              <p class=" m-0 flex-grow-1 text-start f-exo"><?= t('Uso de marcenaria') ?></p>
            </div>
          </div>

          <div class="col-12 col-md-6 mb-2">
            <div class="multi-option option-box p-3 h-100 d-flex align-items-center gap-2" data-key="pinturas">
              <span class="check-indicator"><img src="images/checked.png" alt=""></span>
              <img class="flex-shrink-0" height="42" src="images/icons/questao_8/Default/pinturas.png" alt="">
              <p class=" m-0 flex-grow-1 text-start f-exo"><?= t('Pinturas de paredes e/ou pisos') ?></p>
            </div>
          </div>

          <div class="col-12 col-md-6 mb-2">
            <div class="multi-option option-box p-3 h-100 d-flex align-items-center gap-2" data-key="novos_pisos">
              <span class="check-indicator"><img src="images/checked.png" alt=""></span>
              <img class="flex-shrink-0" height="42" src="images/icons/questao_8/Default/novos_pisos.png" alt="">
              <p class=" m-0 flex-grow-1 text-start f-exo"><?= t('Novos pisos (madeira, porcelanato ou outro tipo)') ?></p>
            </div>
          </div>

          <div class="col-12 col-md-6 mb-2">
            <div class="multi-option option-box p-3 h-100 d-flex align-items-center gap-2" data-key="marmore">
              <span class="check-indicator"><img src="images/checked.png" alt=""></span>
              <img class="flex-shrink-0" height="42" src="images/icons/questao_8/Default/marmore.png" alt="">
              <p class=" m-0 flex-grow-1 text-start f-exo"><?= t('Uso de mármore ou granito') ?></p>
            </div>
          </div>

          <div class="col-12 col-md-6 mb-2">
            <div class="multi-option option-box p-3 h-100 d-flex align-items-center gap-2" data-key="altura_teto">
              <span class="check-indicator"><img src="images/checked.png" alt=""></span>
              <img class="flex-shrink-0" height="42" src="images/icons/questao_8/Default/altura_teto.png" alt="">
              <p class=" m-0 flex-grow-1 text-start f-exo"><?= t('Alteração da altura do teto') ?></p>
            </div>
          </div>

          <div class="col-12 col-md-6 mb-2">
            <div class="multi-option option-box p-3 h-100 d-flex align-items-center gap-2" data-key="paredes">
              <span class="check-indicator"><img src="images/checked.png" alt=""></span>
              <!-- ⚠️ paredes usa pasta questao_9 -->
              <img class="flex-shrink-0" height="42" src="images/icons/questao_9/Default/paredes.png" alt="">
              <p class=" m-0 flex-grow-1 text-start f-exo"><?= t('Paredes de alvenaria ou drywall') ?></p>
            </div>
          </div>

          <div class="col-12 col-md-6 mb-2">
            <div class="multi-option option-box p-3 h-100 d-flex align-items-center gap-2" data-key="eletro">
              <span class="check-indicator"><img src="images/checked.png" alt=""></span>
              <img class="flex-shrink-0" height="42" src="images/icons/questao_9/Default/eletro.png" alt="">
              <p class=" m-0 flex-grow-1 text-start f-exo"><?= t('Novos eletrodomésticos, luminárias e/ou lâmpadas') ?></p>
            </div>
          </div>

          <div class="col-12 col-md-6 mb-2">
            <div class="multi-option option-box p-3 h-100 d-flex align-items-center gap-2" data-key="chuveiro">
              <span class="check-indicator"><img src="images/checked.png" alt=""></span>
              <img class="flex-shrink-0" height="42" src="images/icons/questao_9/Default/chuveiro.png" alt="">
              <p class=" m-0 flex-grow-1 text-start f-exo"><?= t('Trocar/adicionar chuveiro, torneiras e/ou vaso sanitário') ?></p>
            </div>
          </div>

          <div class="col-12 col-md-6 mb-2">
            <div class="multi-option option-box p-3 h-100 d-flex align-items-center gap-2" data-key="tomadas">
              <span class="check-indicator"><img src="images/checked.png" alt=""></span>
              <img class="flex-shrink-0" height="42" src="images/icons/questao_9/Default/tomadas.png" alt="">
              <p class=" m-0 flex-grow-1 text-start f-exo"><?= t('Trocar/adicionar tomadas e interruptores') ?></p>
            </div>
          </div>

        </div>



        <!-- ⬩ 3-B · Nova área? (rádio) -->
        <h2 class="f-anek mt-3 azul fw-semibold text-start f-18">
          <?= t('Você planeja construir uma nova área ou renovar fachadas?') ?>
        </h2>
        <div id="novaAreaWrap" class="row justify-content-center mb-4 ">
          <div class="col-md-6 mb-3">
            <div class="option-box border rounded p-3 h-100 d-flex align-items-center gap-3"
              data-reg="SIM">
              <div class="radio-indicator"></div>
              <span class="f-exo text-start"><?= t('Sim, eu planejo construir um novo ambiente') ?></span>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="option-box border rounded p-3 h-100 d-flex align-items-center gap-3 selected"
              data-reg="NAO">
              <div class="radio-indicator"></div>
              <span class="f-exo  text-start"><?= t('Não, eu não planejo construir um novo ambiente') ?></span>
            </div>
          </div>
        </div>

        <!-- ⬩ 3-C · Prazo (rádio) -->
        <h2 class="f-anek mt-3 text-blue  text-start fw-semibold f-18">
          <?= t('Em quantos dias você quer receber o seu projeto?') ?>
        </h2>
        <!-- Prazo (mobile) -->
        <div id="prazoWrap" class="row g-3 mb-3">

          <!-- 15 dias -->
          <div class="col-6"> <!-- 50 % -->
            <div class="option-box border rounded p-3 h-100 d-flex align-items-center gap-3 prazo-box" data-prazo="15">
              <span class="radio-indicator"></span>
              <span class="f-exo"><?= t('15 dias') ?></span>
              <img src="images/aumento.svg" height="18" alt="aumento">
            </div>
          </div>

          <!-- 21 dias -->
          <div class="col-6">
            <div class="option-box border rounded p-3 h-100 d-flex align-items-center gap-3 prazo-box selected" data-prazo="21">
              <span class="radio-indicator"></span>
              <span class="f-exo"><?= t('21 dias') ?></span>
            </div>
          </div>

          <!-- 28 dias (desce sozinho) -->
          <div class="col-6">

            <div class="option-box border rounded p-3 h-100 d-flex align-items-center gap-3 prazo-box" data-prazo="28">
              <span class="radio-indicator"></span>
              <span class="f-exo"><?= t('28 dias') ?></span>
              <img src="images/desconto.svg" height="18" alt="desconto">
            </div>
          </div>

        </div>

        <p class=" text-secondary mb-4 text-start"><?= t('Entrega dos materiais visuais.') ?></p>

        <!-- hiddens para API -->
        <input type="hidden" id="propostas" name="adicionais" class="required">
        <input type="hidden" id="nova_area" name="nova_area" class="required" value="NAO">
        <input type="hidden" id="prazo_dias" name="prazo_dias" class="required" value="21">
        <button type="button" class="btn btn-disabled w-100 btn-lg" id="btnAvancar3"
          disabled>
          <?= t('Avançar') ?>
        </button>
      </div>
    </div>
  </div>
</div>
