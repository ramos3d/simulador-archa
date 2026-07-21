/* input.js */
$(function () {
  const on = (evts, sel, fn) => $(document).on(evts, sel, fn);

  /* === marca/desmarca preenchido válido === */
  const setFilled = ($el, ok) => {
    // se ok não foi passado, deduz: tem texto e não está inválido
    if (typeof ok === 'undefined') {
      const has = String($el.val() ?? '').trim() !== '';
      ok = has && !$el.hasClass('is-invalid');
    }
    $el.toggleClass('is-filled', !!ok);
  };

  /* ========== Telefone BR (#fone) — 10 ou 11 dígitos, sem parênteses ========== */
  (function phoneMaskBR() {
    const sel = '#fone';

    function fmt(d) {
      d = String(d || '').replace(/\D+/g, '').slice(0, 11);
      const ddd = d.slice(0, 2), rest = d.slice(2);
      if (!ddd) return '';
      if (rest.length <= 8) {
        const a = rest.slice(0, 4), b = rest.slice(4, 8);
        return ddd + (a ? ' ' + a : '') + (b ? '-' + b : '');
      } else {
        const a = rest.slice(0, 5), b = rest.slice(5, 9);
        return ddd + (a ? ' ' + a : '') + (b ? '-' + b : '');
      }
    }

    // Digitação: aplica máscara e limpa estado de erro
    on('input', sel, function () {
      this.value = fmt(this.value);
      const $el = $(this);
      $el.removeClass('is-invalid');
      $el.next('.invalid-feedback').text('');
      setFilled($el);
    });

    // Focus: reaplica máscara e posiciona o cursor no fim
    on('focus', sel, function () {
      this.value = fmt(this.value);
      try { this.setSelectionRange(this.value.length, this.value.length); } catch { }
    });

    // Blur: exibe erro se vazio ou com menos de 10 dígitos (10/11 válidos)
    on('blur', sel, function () {
      const $el = $(this);
      const fb = $el.next('.invalid-feedback');
      const digits = this.value.replace(/\D+/g, '');
      if (!digits) {
        $el.addClass('is-invalid');
        fb.text(window.jt('Informe o seu Whatsapp'));
        return setFilled($el, false);
      }
      const ok = digits.length >= 10; // aceita 10 ou 11
      $el.toggleClass('is-invalid', !ok);
      if (!ok) fb.text(window.jt('Informe um Whatsapp válido.'));
      setFilled($el, ok);
    });

    // Evita scroll alterar valor quando focado
    on('wheel', `${sel}:focus`, e => e.preventDefault());
  })();


  /* ========== Sanitização numérica ========== */
  const onlyInt = v => String(v || '').replace(/\D+/g, '');
  const numberWithComma = v => {
    let x = String(v || '').replace(/\./g, ',').replace(/[^\d,]/g, '');
    const i = x.indexOf(',');
    if (i !== -1) {
      const L = x.slice(0, i).replace(/,/g, ''), R = x.slice(i + 1).replace(/,/g, '');
      x = `${L},${R}`;
    }
    return x;
  };
  on('input paste', '#qtd_amb', function () {
    const c = onlyInt(this.value);
    if (this.value !== c) this.value = c;
    $(this).removeClass('is-invalid'); setFilled($(this));
  });
  on('input paste', '#area', function () {
    const c = numberWithComma(this.value);
    if (this.value !== c) this.value = c;
    $(this).removeClass('is-invalid'); setFilled($(this));
  });

  /* ========== E-mail (validação simples) ========== */
  /* ========== E-mail (exibe erro se vazio ou inválido) ========== */
  on('input', '#email', function () {
    const $el = $(this);
    $el.removeClass('is-invalid');           // digitou → tira erro
    $el.next('.invalid-feedback').text('');
    setFilled($el);
  });

  on('blur', '#email', function () {
    const $el = $(this);
    const fb = $el.next('.invalid-feedback');
    const email = $el.val().trim();
    if (!email) {
      $el.addClass('is-invalid');
      fb.text(window.jt('Informe o e-mail.'));
      return setFilled($el, false);
    }
    const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/i.test(email);
    $el.toggleClass('is-invalid', !ok);
    if (!ok) fb.text(window.jt('E-mail inválido.'));
    setFilled($el, ok);
  });


  /* ========== Nome: apenas letras/espaços (sem validação “nome e sobrenome” aqui) ========== */
  $('#nome').on('input', function () {
    const raw = String($(this).val() || '');
    // permite letras (qualquer idioma), marcas de acento, espaço e apóstrofos ' e ’
    const v = raw.replace(/[^\p{L}\p{M}\s'’]/gu, '');
    if (this.value !== v) this.value = v;
    setFilled($(this));
  });






  /* ========== Evita mudar number com scroll ========== */
  on('wheel', 'input[type=number]:focus', e => e.preventDefault());

  /* ========== Validações de mínimo (sem auto-preencher) ========== */
  const markInvalidIf = ($el, cond) => $el.toggleClass('is-invalid', !!cond);

  const validateIntMin = ($el) => {
    const min = parseInt($el.attr('min') || '0', 10);
    const raw = String($el.val() ?? '').trim();
    if (!raw) { $el.addClass('is-invalid'); setFilled($el, false); return; }
    const v = parseInt(raw, 10);
    const bad = !Number.isFinite(v) || v < min;
    markInvalidIf($el, bad);
    if (!bad) $el.val(String(v));
    setFilled($el, !bad);
  };

  const validateFloatMin = ($el) => {
    const min = parseFloat($el.attr('min') || '0');
    const raw = String($el.val() ?? '').trim();
    if (!raw) { $el.addClass('is-invalid'); setFilled($el, false); return; }
    const v = parseFloat(raw.replace(',', '.'));
    const bad = !Number.isFinite(v) || v < min;
    markInvalidIf($el, bad);
    if (!bad) $el.val(String(v));
    setFilled($el, !bad);
  };

  /* Ambientes (>=1) */
  const $amb = $('#qtd_amb');
  if ($amb.length) {
    if (!$amb.attr('min')) $amb.attr('min', '1');
    on('blur change', '#qtd_amb', function () { validateIntMin($(this)); });
  }

  /* Metragem (>= min do input) */
  const $area = $('#area');
  if ($area.length) {
    on('blur change', '#area', function () { validateFloatMin($(this)); });
  }


  /* === Código do país (DDI) — notifica se vazio ou inválido === */
  on('input', '#codigo_pais', function () {
    const $el = $(this);
    const clean = String($el.val() || '').replace(/\D+/g, '').slice(0, 3);
    if ($el.val() !== clean) $el.val(clean);
    $el.removeClass('is-invalid');
    $el.next('.invalid-feedback').text('');
    setFilled($el);
  });

  on('blur', '#codigo_pais', function () {
    const $el = $(this);
    const fb = $el.next('.invalid-feedback');
    const v = String($el.val() || '').replace(/\D+/g, '');
    if (!v) {
      $el.addClass('is-invalid');
      fb.text(window.jt('Informe o (DDI).'));
      return setFilled($el, false);
    }
    const ok = v.length >= 1 && v.length <= 3;
    $el.toggleClass('is-invalid', !ok);
    if (!ok) fb.text(window.jt('DDI deve ter 1 a 3 dígitos.'));
    setFilled($el, ok);
  });


});



/* === Nome completo: inválido se só tiver um nome (tempo real, sem bloquear espaços) === */
$(function () {
  const on = (evts, sel, fn) => $(document).on(evts, sel, fn);

  function hasTwoNames(s) {
    const words = s
      .replace(/\s+/g, ' ')   // normaliza para validar
      .trim()
      .split(' ')
      .filter(w => w.replace(/['’]/g, '').length >= 2);
    return words.length >= 2;
  }

  // INPUT: só remove caracteres proibidos; não trim, não colapsa espaço
  on('input', '#nome', function () {
    const $el = $(this);
    const before = String($el.val() || '');
    const sanitized = before.replace(/[^\p{L}\p{M}\s'’]/gu, '');
    if (before !== sanitized) $el.val(sanitized);

    const forCheck = sanitized;                 // mantém espaços do usuário
    const normalized = forCheck.replace(/\s+/g, ' ').trim(); // só para validar
    const filled = normalized.length > 0;
    const invalid = filled && !hasTwoNames(normalized);

    $el.toggleClass('is-invalid', invalid);
    $el.next('.invalid-feedback').text(invalid ? 'Digite nome e sobrenome.' : '');

    if (typeof setFilled === 'function') setFilled($el, filled && !invalid);
  });

  // BLUR: aí sim normaliza visualmente (trim + colapso de espaços)
  on('blur', '#nome', function () {
    const $el = $(this);
    const normalized = String($el.val() || '').replace(/[^\p{L}\p{M}\s'’]/gu, '')
      .replace(/\s+/g, ' ')
      .trim();
    $el.val(normalized);

    const filled = normalized.length > 0;
    const invalid = filled && !hasTwoNames(normalized);

    $el.toggleClass('is-invalid', invalid);
    $el.next('.invalid-feedback').text(invalid ? 'Digite nome e sobrenome.' : '');
    if (typeof setFilled === 'function') setFilled($el, filled && !invalid);
  });
});


const elPais = document.querySelector('#codigo_pais');
elPais.addEventListener('input', () => {
  elPais.value = (elPais.value || '').replace(/\D/g, '').slice(0, 3);
});



