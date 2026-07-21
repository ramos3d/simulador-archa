<!-- ========== PASSO 2 ===================================== -->
<div class="accordion shadow-sm mb-5  step-card" id="accordionStep2">
  <div class="accordion-item">
    <h2 class="accordion-header" id="head2">
      <button class="accordion-button collapsed gap-3 " type="button"
        data-bs-toggle="collapse" data-bs-target="#step2"
        aria-expanded="false" aria-controls="step2">
        <span class="step-badge d-inline-flex align-items-center
                     justify-content-center flex-shrink-0">2</span>
        <span class="fw-bold style-title f-anek azul"><?= t('Encontre o arquiteto ideal') ?></span>
      </button>
    </h2>

    <div id="step2" class="accordion-collapse collapse show">
      <div class="accordion-body">

        <p class="small paragrafo mb-5 intro-indent sec2-mt-sml-23">
          <?= t('Todos os profissionais da nossa plataforma passam por uma seleção criteriosa. Escolha a categoria de profissionais que você prefere realizar o seu projeto de arquitetura e decoração.') ?>
        </p>

        <!-- CATEGORIA DE PROFISSIONAL (um único selecionado) -->
        <div id="proCatWrap" class="row text-center g-3 font-exo">

          <div class="col-md-4">
            <div class="option-box p-3 h-100 selected" data-cat="ESTREANTES">
              <img class="mb-2"
                src="images/icons/questao_2/Active/estreantes.png">
              <p class="mb-0 fw-bold"><?= t('ESTREANTES') ?></p>
              <p class="small mb-0"><?= t('Qualificados, mas com pouca<br>experiência em concorrências.') ?></p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="option-box p-3 h-100" data-cat="VERIFICADOS">
              <img class="mb-2"
                src="images/icons/questao_2/Default/verificados.png">
              <p class="mb-0 fw-bold"><?= t('VERIFICADOS') ?></p>
              <p class="small mb-0"><?= t('Qualificados, treinados e com experiência em projetos pela Archa.') ?></p>
            </div>
          </div>

          <div class="col-md-4">
            <div class="option-box p-3 h-100" data-cat="PREFERIDOS">
              <img class="mb-2"
                src="images/icons/questao_2/Default/preferidos.png">
              <p class="mb-0 fw-bold"><?= t('PREFERIDOS') ?></p>
              <p class="small mb-0"><?= t('Os mais escolhidos pelos nossos clientes! Portfólio e desempenho de excelência.') ?></p>
            </div>
          </div>
        </div>
        <h2 class="f-anek mt-5 mb-3 azul ms-1 text-start fw-semibold">
          <?= t('Localidade do profissional:') ?>
        </h2>
        <!-- REGIÃO (um único selecionado) -->
        <div id="regWrap" class="row justify-content-center">
          <div class="col-md-6 mb-3">
            <div class="option-box border rounded p-3 h-100 text-start d-flex align-items-center gap-3 selected"
              data-reg="BRASIL_TODO">
              <div class="radio-indicator"></div>
              <span class="f-exo small"><?= t('Estou aberto a escritórios de todo Brasil') ?></span>
            </div>
          </div>
          <div class="col-md-6 mb-3">
            <div class="option-box border rounded p-3 h-100 text-start d-flex align-items-center gap-3"
              data-reg="MINHA_REGIAO">
              <div class="radio-indicator"></div>
              <span class="f-exo small"><?= t('Quero apenas profissionais da minha região') ?></span>
              <img src="images/mais-caro.svg" alt="aumento" data-noswap>

            </div>
          </div>
        </div>

        <!-- hidden para API -->
        <input type="hidden" id="categoria" name="categoria" class="required" value="ESTREANTES">
        <input type="hidden" id="regiao_key" name="regiao_key" class="required" value="BRASIL_TODO">

      </div>
    </div>
  </div>
</div><!-- /PASSO 2 -->
