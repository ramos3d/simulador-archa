<div class="step" id="step-4" style="display:none;" data-step-name="tipo_espaco_projetado" data-origem="archa.com.br">
  <h2 class="f-anek fw-bold mb-3 text-center">
    Que tipo de espaço será projetado?
  </h2>
  <p class="f-exo text-center mb-4">
    Escolha a categoria que melhor define o seu espaço:
  </p>

  <!-- Grid de 5 × 2 cartas -------------------------------------------->
  <div class="d-flex flex-wrap justify-content-center gap-3 mb-4 tipo-grid">

    <?php
    /* slug => [label-html , valor-para-API] */
    $opcoes = [
      'apartamento' => ['APARTAMENTO', 'RESIDENCIAL: Apartamento'],
      'casa'        => ['CASA', 'RESIDENCIAL: Casa'],
      'hotelaria'   => ['HOTELARIA', 'COMERCIAL: Hotelaria'],
      'bares'       => ['BARES, RESTAURANTES<br>E CASAS NOTURNAS', 'COMERCIAL: Bares, restaurantes e casas noturnas'],
      'escritorio'  => ['ESCRITÓRIO', 'CORPORATIVO: Escritório'],
      'lojas'       => ['LOJAS VAREJO', 'COMERCIAL: Lojas varejo'],
      'clinicas'    => ['CLÍNICAS E ESPAÇOS<br>ESTÉTICOS', 'COMERCIAL: Clínicas e espaços estéticos'],
      'estandes'    => ['ESTANDES', 'EVENTOS: Estandes'],
      'eventos'     => ['ESPAÇOS DE EVENTOS,<br>MASTERPLAN E PALCO', 'EVENTOS: Espaços para eventos e/ou masterplan/palco'],
    ];

    foreach ($opcoes as $slug => [$label, $api]):
    ?>
      <div class="tipo-card">
        <div class="space-option border  p-3 h-100 text-center"
          data-value="<?= htmlspecialchars($api, ENT_QUOTES, 'UTF-8') ?>"
          data-icon="<?= $slug ?>.png">
          <img src="images/icons/questao_4/Default/<?= $slug ?>.png"
            class="space-icon mb-2" height="60" alt="">
          <p class="small f-exo m-0"><?= $label ?></p>
        </div>
      </div>
    <?php endforeach; ?>

  </div>

  <!-- hidden para o payload -->
  <input type="hidden" id="tipo_projeto" name="tipo_projeto" class="required">

  <div class="d-flex justify-content-center gap-3 mb-5">
    <button type="button" class="btn btn-outline-dark px-4" onclick="voltarEtapa(3)">Voltar</button>
    <button type="button" class="btn btn-green px-4" onclick="proximaEtapa(4)">Próximo</button>
  </div>
</div>