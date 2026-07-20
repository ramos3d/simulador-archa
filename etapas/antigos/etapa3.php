<div class="step" id="step-3" style="display: none;" data-step-name="regiao_profissional" data-origem="archa.com.br">
  <h2 class="f-anek fw-bold mb-3 text-center">Você prefere arquitetos da sua região ou de todo Brasil?</h2>
  <p class="f-exo text-center mb-4">
    Ampliar sua busca para todo o Brasil aumenta as chances de encontrar o<br>
    profissional ideal. Para profissionais da sua região, há um acréscimo no valor.
  </p>

  <div class="row justify-content-center mb-4">
    <div class="col-md-6 mb-3">
      <div class="option-box border rounded p-3 h-100 text-start d-flex align-items-center gap-3" 
           data-value="BRASIL_TODO">
        <div class="radio-indicator"></div>
        <span class="f-exo">
          Estou aberto a escritórios de todo Brasil.
        </span>
      </div>
    </div>
    <div class="col-md-6 mb-3">
      <div class="option-box border rounded p-3 h-100 text-start d-flex align-items-center gap-3"
           data-value="MINHA_REGIAO">
        <div class="radio-indicator"></div>
        <span class="f-exo">
          Quero apenas profissionais da minha região.
        </span>
      </div>
    </div>
  </div>

  <!-- input hidden para armazenar a escolha -->
  <input type="hidden" id="regiao_key" name="regiao_key" class="required">

  <div class="d-flex justify-content-center gap-3 mb-5">
    <button type="button" class="btn btn-outline-dark px-4" onclick="voltarEtapa(2)">Voltar</button>
    <button type="button" class="btn btn-green px-4" onclick="proximaEtapa(3)">Próximo</button>
  </div>
</div>
