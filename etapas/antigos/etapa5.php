<div class="step" id="step-5" style="display:none;" data-step-name="metragem" data-origem="archa.com.br">
  <h2 class="f-anek fw-bold mb-3 text-center">
    Qual o tamanho aproximado da área a ser projetada?
  </h2>
  <p class="f-exo text-center mb-4">
    Considere apenas a área que deseja transformar. Se for reforma parcial, <br>
    informe só os ambientes envolvidos.
  </p>

  <!-- Ícone + input -->
  <div class="d-flex justify-content-center align-items-end mb-2 gap-2" style="max-width:215px;margin:0 auto;">
    <img src="images/icons/questao_5/area_projetada.png" alt="Área" height="60">
    <div class="flex-grow-1 position-relative">
      <input type="number"
        class="form-control text-center required size-lg"
        id="metragem"
        name="metragem"
        placeholder="Ex: 30"
        min="1"
        step="1">
      <span class="position-absolute top-50 translate-middle-y" style="right:-20px;">m²</span>
    </div>
  </div>

  <div class="mt-5">
    <!-- Dicas -->
    <p class="f-exo small text-center mb-3">
      <strong>Exemplo:</strong> Se seu apartamento tem 120 m², mas o projeto será <br>
      apenas para dois quartos de 20 m² cada, responda <strong>40</strong>.
    </p>
    <p class="f-exo small text-center mb-4">
      Para novas construções, insira a área construída desejada ou, <br>
      caso não tenha certeza, o tamanho total do terreno.
    </p>
  </div>

  <div class="d-flex justify-content-center gap-3 m-5">
    <button type="button" class="btn btn-outline-dark px-4" onclick="voltarEtapa(4)">Voltar</button>
    <button type="button" class="btn btn-green px-4" onclick="proximaEtapa(5)">Próximo</button>
  </div>
</div>