$(document).ready(function () {
  $('.card-option').on('click', function () {
    // Desmarcar todas
    $('.card-option').removeClass('card-selected');
    $('.option-icon').each(function () {
      const iconName = $(this).closest('.card-option').data('icon');
      $(this).attr('src', `images/icons/questao_2/Default/${iconName}`);
    });

    // Marcar a clicada
    $(this).addClass('card-selected');

    // Atualizar imagem para versão ativa
    const iconName = $(this).data('icon');
    $(this).find('.option-icon').attr('src', `images/icons/questao_2/Active/${iconName}`);

    // Armazenar valor no input hidden
    const valor = $(this).data('value');
    $('#categoria').val(valor).removeClass('is-invalid');
  });

  /* Etapa 3 */
  /* handler genérico para QUALQUER .option-box */
  $(document).on('click', '.option-box', function () {
    const $step = $(this).closest('.step');           // limita-se ao passo atual

    // remove seleção anterior desse passo
    $step.find('.option-box').removeClass('selected')
      .find('.radio-indicator').css({ background: '#fff', borderColor: '#ccc' });

    // marca a clicada
    $(this).addClass('selected')
      .find('.radio-indicator')
      .css({ background: '#7FDD53', borderColor: '#7FDD53' });

    // grava valor no hidden desse passo
    const valor = $(this).data('value');
    $step.find('input[type="hidden"]').val(valor).removeClass('is-invalid');
  });


  // seleção única para etapa 4
  $(document).on('click', '.space-option', function () {
    $('.space-option').removeClass('selected');
    $('.space-icon').each(function () {
      const base = $(this).closest('.space-option').data('icon');
      $(this).attr('src', `images/icons/questao_4/Default/${base}`);
    });

    $(this).addClass('selected');
    const iconFile = $(this).data('icon');
    $(this).find('.space-icon')
      .attr('src', `images/icons/questao_4/Active/${iconFile}`);

    const val = $(this).data('value');
    $('#tipo_projeto').val(val).removeClass('is-invalid');
  });


  /* seleção múltipla na etapa 8 */
  /* seleção múltipla (etapas 8 e 9) */
  $(document).on('click', '.multi-option', function () {
    const $this = $(this);
    const $step = $this.closest('.step');                  // step-8, step-9, etc.
    const slug = $this.data('value');                     // ex.: "paredes"
    const icon = $this.data('icon');                      // ex.: "paredes.png"
    const folder = 'questao_' + $step.attr('id').split('-')[1]; // questao_8 / questao_9
    const isNada = slug === 'nada';

    /* --- lógica de seleção --- */
    if (isNada) {
      // “Nada” = limpa todas as outras opções e marca só ela
      $step.find('.multi-option').removeClass('selected')
        .each(function () {
          const base = $(this).data('icon');
          $(this).find('.multi-icon')
            .attr('src', `images/icons/${folder}/Default/${base}`);
        });

      $this.addClass('selected')
        .find('.multi-icon')
        .attr('src', `images/icons/${folder}/Active/${icon}`);
    } else {
      // Alterna seleção da opção clicada
      $this.toggleClass('selected');
      const dir = $this.hasClass('selected') ? 'Active' : 'Default';
      $this.find('.multi-icon')
        .attr('src', `images/icons/${folder}/${dir}/${icon}`);

      // Se alguma opção “normal” foi marcada, garante que “nada” fique desmarcado
      const $nadaCard = $step.find('.multi-option[data-value="nada"]');
      if ($nadaCard.hasClass('selected')) {
        $nadaCard.removeClass('selected')
          .find('.multi-icon')
          .attr('src', `images/icons/${folder}/Default/nada.png`);
      }
    }

    /* --- atualiza hidden como ARRAY JSON --- */
    const values = [];
    $step.find('.multi-option.selected').each(function () {
      values.push($(this).data('value'));                  // slug
    });

    // existe apenas UM hidden por passo, com id dinâmico
    const $hidden = $step.find('input[type="hidden"]');
    $hidden.val(JSON.stringify(values))
      .toggleClass('is-invalid', values.length === 0);
  });

  /* Etapa 11 Máscara de moeda (formato brasileiro) */
  $(document).on('input', '.money-mask', function () {
    let v = $(this).val().replace(/\D/g, '');
    if (v.length < 3) v = v.padStart(3, '0');
    v = (v / 100).toFixed(2)               // 3458400 => "34584.00"
      .replace('.', ',')         // decimal vírgula
      .replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // milhar ponto
    $(this).val(v);
  });




});

