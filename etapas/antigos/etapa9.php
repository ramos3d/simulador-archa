<!-- etapa9.php -->
<div class="step" id="step-9" style="display:none;" data-step-name="alteracoes_estruturais" data-origem="archa.com.br">
  <h2 class="f-anek fw-bold mb-3 text-center">
    O projeto pode incluir alterações estruturais ou técnicas?
  </h2>
  <p class="f-exo text-center mb-4">
    Marque tudo que deseja mudar ou revisar.
  </p>

  <?php
  // slug => label HTML
  $opcoes = [
    'eletro'   => 'Eletrodomésticos, <br> luminárias e/ou lâmpadas',
    'chuveiro' => 'Chuveiro, torneiras <br> e/ou vaso sanitário',
    'paredes'  => 'Paredes de alvenaria <br> ou drywall',
    'tomadas'  => 'Tomadas e/ou <br> interruptores',
    'nada'     => 'Não quero mexer em <br> nada disso'
  ];
  ?>

  <div class="row g-3 justify-content-center">
    <?php foreach ($opcoes as $slug => $label): ?>
      <div class="col-12 col-md-4">
        <div class="multi-option opcao-9 d-flex align-items-center gap-2"
          data-value="<?= $slug ?>"
          data-icon="<?= $slug ?>.png">

          <!-- bolinha check -->
          <span class="check-indicator"></span>

          <!-- ícone (fica colado à esquerda, centrado no eixo-y por align-items-center) -->
          <img src="images/icons/questao_9/Default/<?= $slug ?>.png"
            alt="" height="46" class="flex-shrink-0">

          <!-- legenda ─ cresce e quebra linha -->
          <p class="small f-exo m-0 flex-grow-1"><?= $label ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <input type="hidden" id="estrutural" name="estrutural" class="required">

  <div class="d-flex justify-content-center gap-3 mt-4 mb-5">
    <button type="button" class="btn btn-outline-dark px-4" onclick="voltarEtapa(8)">Voltar</button>
    <button type="button" class="btn btn-green px-4" onclick="proximaEtapa(9)">Próximo</button>
  </div>
</div>