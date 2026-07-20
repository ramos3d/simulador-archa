<!-- etapa7.php -->
<div class="step" id="step-7" style="display:none;" data-step-name="construir_nova_area" data-origem="archa.com.br">
  <h2 class="f-anek fw-bold mb-3 text-center">
    Você planeja construir uma nova área do zero ou renovar fachadas?
  </h2>
  <p class="f-exo text-center mb-4">
    Caso queira construir uma garagem coberta, criar uma casa em um terreno
    vazio, modernizar a fachada ou algo do gênero, selecione sim.
  </p>

  <div class="row justify-content-center g-3 mb-4" style="max-width:580px;margin:0 auto;">
    <!-- SIM -->
    <div class="col-10">
      <div class="option-box border rounded p-3 d-flex gap-3 align-items-center height-card"
           data-value="sim">
        <div class="radio-indicator"></div>
        <span class="f-exo font-blue">
          Sim, eu planejo construir um novo ambiente
        </span>
      </div>
    </div>

    <!-- NÃO -->
    <div class="col-10">
      <div class="option-box border rounded p-3 d-flex gap-3 align-items-center height-card"
           data-value="nao">
        <div class="radio-indicator"></div>
        <span class="f-exo text-muted">
          Não, eu não planejo construir um novo ambiente
        </span>
      </div>
    </div>
  </div>

  <input type="hidden" id="nova_area" name="nova_area" class="required">

  <div class="d-flex justify-content-center gap-3 mb-5">
    <button type="button" class="btn btn-outline-dark px-4" onclick="voltarEtapa(6)">Voltar</button>
    <button type="button" class="btn btn-green px-4" onclick="proximaEtapa(7)">Próximo</button>
  </div>
</div>
