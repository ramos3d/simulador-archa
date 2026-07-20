<!-- etapa8.php -->
<div class="step" id="step-8" style="display:none;" data-step-name="liberdade_profissional" data-origem="archa.com.br">
  <h2 class="f-anek fw-bold mb-3 text-center">
    O que o profissional poderá propor no seu projeto?
  </h2>
  <p class="f-exo text-center mb-4">
    Você pode selecionar mais de uma opção.
  </p>

  <?php
  // slug => label (HTML)
  $opcoes = [
      'marcenaria'   => 'Uso de marcenaria',
      'pinturas'     => 'Pinturas de paredes <br> e/ou pisos',
      'novos_pisos'  => 'Novos pisos (madeira, <br> porcelanato ou outro tipo)',
      'marmore'      => 'Uso de mármore ou <br> granito',
      'altura_teto'  => 'Alteração da altura do <br> teto',
      'nada'         => 'Não quero nada disso'
  ];
  ?>

  <div id="propostas-container"
     class="row g-3 justify-content-center">

  <?php
  // slug => label (HTML)
  $opcoes = [
      'marcenaria'  => 'Uso de marcenaria',
      'pinturas'    => 'Pinturas de paredes <br> e/ou pisos',
      'novos_pisos' => 'Novos pisos (madeira, <br> porcelanato ou outro tipo)',
      'marmore'     => 'Uso de mármore ou <br> granito',
      'altura_teto' => 'Alteração da altura do teto',
      'nada'        => 'Não quero nada disso'
  ];

  foreach ($opcoes as $slug => $label): ?>
    <div class="col-12 col-md-4">
      <div class="multi-option opcao-8 d-flex align-items-center gap-2"
           data-value="<?= $slug ?>"
           data-icon="<?= $slug ?>.png">

        <!-- bolinha tipo checkbox -->
        <span class="check-indicator"></span>

        <!-- ícone à esquerda -->
        <img src="images/icons/questao_8/Default/<?= $slug ?>.png"
             alt=""
             class="flex-shrink-0"
             height="46">

        <!-- texto -->
        <p class="small f-exo m-0 flex-grow-1"><?= $label ?></p>
      </div>
    </div>
  <?php endforeach; ?>
</div>


  <input type="hidden" id="propostas" name="propostas" class="required">

  <div class="d-flex justify-content-center gap-2 mt-4 mb-5">
    <button type="button" class="btn btn-outline-dark px-4" onclick="voltarEtapa(7)">Voltar</button>
    <button type="button" class="btn btn-green px-4" onclick="proximaEtapa(8)">Próximo</button>
  </div>
</div>
