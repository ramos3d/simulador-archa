<div class="step" id="step-6" style="display:none;" data-step-name="quantidade_ambientes" data-origem="archa.com.br">
  <h2 class="f-anek fw-bold mb-3 text-center">
    Quantos ambientes serão projetados?
  </h2>
  <p class="f-exo text-center mb-4">
    Informe quantos ambientes serão projetados.
  </p>

  <!-- Ícone + input -->
  <div class="d-flex justify-content-center align-items-end mb-2 gap-2" style="max-width:240px;margin:0 auto;">
    <img src="images/icons/questao_6/area.png" alt="Ambientes" height="55">
    <div class="flex-grow-1 position-relative">
      <input type="number"
        class="form-control text-center required size-lg"
        id="ambientes"
        name="ambientes"
        placeholder="Ex: 6"
        min="1"
        step="1">
    </div>
  </div>

  <div class="mt-5">
    <!-- Dica -->
    <p class="f-exo small text-center mb-4 text-secondary">
      Todo espaço que tiver contato com, pelo menos, uma porta de<br>
      entrada/saída é considerado um ambiente.
    </p>
  </div>

  <div class="d-flex justify-content-center gap-3 mb-5">
    <button type="button" class="btn btn-outline-dark px-4" onclick="voltarEtapa(5)">Voltar</button>
    <button type="button" class="btn btn-green px-4" onclick="proximaEtapa(6)">Próximo</button>
  </div>
</div>