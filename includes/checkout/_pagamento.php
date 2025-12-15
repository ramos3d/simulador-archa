<!-- Section: Pagamento -->
<div class="ck-card mb-5">
  <hr class="mobile-only margem-custom-bottom mobile-pagamento-section">
  <div class="ck-head">
    <h6 class="ck-title mb-0">Escolha sua forma de pagamento</h6>
  </div>

  <div class="ck-body">
    <!-- Seleção do método -->
    <div class="d-flex gap-2 mb-4">
      <button type="button" id="btnPayCard" class="pay-btn d-flex align-items-center active me-3">
        <img src="images/icon-card.png" alt="Cartão" class="img-fluid">
        <span class="f-exo">Cartão de Crédito</span>
      </button>

      <button type="button" id="btnPayPix" class="pay-btn d-inline-flex align-items-center gap-2 lh-1 py-2 px-3">
        <img src="images/icon-pix.png" alt="PIX" class="flex-shrink-0 align-self-center">
        <span class="f-exo">PIX</span>
      </button>
    </div>

    <!-- CARTÃO -->
    <div id="boxCard">
      <div class="row g-3 mb-4">
        <div class="col-12 col-md-5 position-relative">
          <label for="cardName" class="form-label f-anek">Nome no cartão</label>
          <input id="cardName" name="cardName" type="text" class="form-control f-exo"
            placeholder="Como está no cartão" autocomplete="cc-name" data-brand-target="true" required>
          <span class="brand-badge position-absolute end-0 top-50 translate-middle-y me-3 d-none"></span>
        </div>

        <div class="col-12 col-md-7 position-relative">
          <label for="cardNumber" class="form-label f-anek">Número do cartão</label>
          <input id="cardNumber" name="cardNumber" type="text"
            class="form-control f-exo card-number-with-icon"
            inputmode="numeric" autocomplete="cc-number"
            placeholder="9999 9999 9999 9999" required>
          <img id="cardBrandIcon" class="card-brand-icon d-none" alt="Bandeira">
        </div>

      </div>


      <div class="row g-3">


        <div class="col-6 col-md-4">
          <label for="cardCvv" class="form-label f-anek">Código de segurança</label>
          <input
            id="cardCvv"
            name="cardCvv"
            type="text"
            class="form-control f-exo"
            inputmode="numeric"
            autocomplete="cc-csc"
            placeholder="999"
            maxlength="4" required>
        </div>
        <!-- VALIDADE -->
        <div class="col-6 col-md-4">
          <label for="cardExpiry" class="form-label f-anek">Validade (MM/AAAA)</label>
          <input
            id="cardExpiry"
            name="cardExpiry"
            type="text"
            class="form-control f-exo"
            inputmode="numeric"
            placeholder="MM/AAAA"
            autocomplete="cc-exp"
            maxlength="7">
        </div>


        <div class="col-6 col-md-4">
          <label for="installments" class="form-label f-anek">Parcelas</label>
          <select id="installments" name="installments" class="form-select f-exo">
            <option value="">Carregando parcelas…</option>
          </select>
        </div>
      </div>
    </div>
    <!-- PIX -->

    <div id="boxPix" class="d-none">
      <div class="alert-light d-flex align-items-center mb-0" role="alert">
        <div id="pix-section" style="display:none">
          <div class="text-center mt-4 mx-auto">
            <div id="pix-qr-container" class="my-4">
              <p class="text-muted">Gerando código PIX...</p>
            </div>
            <div id="pix-payload" class="text-muted small"></div>
            <div id="pix-expiration" class="text-muted small"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  (function() {
    /* ============ Config (direct / bridge) ============ */
    const ABS_RAW = '<?= (defined("API_BASE") ? addslashes(API_BASE) : (isset($API_BASE) ? addslashes($API_BASE) : "")) ?>';
    const ABS = (ABS_RAW && ABS_RAW !== 'undefined') ? ABS_RAW : '';
    const TOKEN = '<?= addslashes(API_TOKEN ?? "") ?>';
    const BRIDGE = 'bridge.php';

    function make(path) {
      // path vem tipo "/checkout/pix" ou "/checkout/doc"
      if (ABS) {
        // Quando API_BASE estiver definido (produção ou local com API direta)
        // Ex.: https://app.archapro.com/api + /checkout/pix
        return ABS.replace(/\/+$/, '') + path;
      }

      // Quando NÃO tiver API_BASE no checkout.php → usa bridge.php
      // remove barras iniciais
      let op = path.replace(/^\/+/, '');

      // Se vier "api/checkout/pix", remove "api/"
      if (op.startsWith('api/')) {
        op = op.slice(4);
      }

      // "checkout/pix" -> "checkout_pix"
      op = op.replace(/\//g, '_');

      return BRIDGE + '?op=' + op;
    }

    const API = {
      BASE: ABS,
      TOKEN,
      checkoutCard: () => make('/checkout/card'),
      checkoutPix: () => make('/checkout/pix'),
      checkoutDoc: () => make('/checkout/doc') // atualiza o cpf em checkouts pro Asaas
    };
    // console.log('[API cfg]', { BASE: API.BASE || '(bridge)', via: API.BASE ? 'direct' : 'bridge' });

    /* ================== helpers UI/validação ================== */
    const $ = (s) => document.querySelector(s);
    const $$ = (s) => Array.from(document.querySelectorAll(s));

    // Converte "R$ 1.234,56" em número JS
    function parseBRToNumber(str) {
      if (!str) return 0;
      const s = String(str)
        .replace(/[^\d,.,-]/g, '') // mantém dígitos, ponto e sinal
        .replace(/\./g, '') // remove separador de milhar
        .replace(',', '.'); // vírgula → ponto
      const n = Number(s);
      return Number.isFinite(n) ? n : 0;
    }

    // Contexto padrão usado em payment_intent / payment_status
    function buildPaymentContext(method, installments) {
      const elNome = document.getElementById('nome');
      const elEmail = document.getElementById('email');
      const elFone = document.getElementById('fone');
      const elTotal = document.getElementById('sumTotal');

      let value = 0;
      if (elTotal) {
        const avistaAttr = Number(elTotal.dataset.avista || '0');
        const parceladoAttr = Number(elTotal.dataset.parceladoTotal || '0');

        if (method === 'pix') {
          value = avistaAttr || parseBRToNumber(elTotal.textContent || '');
        } else {
          value = parceladoAttr || parseBRToNumber(elTotal.textContent || '');
        }
      }

      return {
        name: (elNome?.value || '').trim(),
        email: (elEmail?.value || '').trim(),
        whatsapp: (elFone?.value || '').trim(),
        value,
        method,
        installments: String(installments || '')
      };
    }


    function getSlug() {
      const qs = new URLSearchParams(location.search);
      return (qs.get('projeto') || '').trim();
    }

    function feedbackFor(el) {
      let fb = el?.parentElement?.querySelector('.invalid-feedback');
      if (!fb && el) {
        fb = document.createElement('div');
        fb.className = 'invalid-feedback';
        el.insertAdjacentElement('afterend', fb);
      }
      return fb;
    }

    function setFieldError(el, msg) {
      if (!el) return;
      el.classList.toggle('is-invalid', !!msg);
      if (el.setCustomValidity) el.setCustomValidity(msg || '');
      const fb = feedbackFor(el);
      if (fb) fb.textContent = msg || '';
    }

    function clearFieldError(el) {
      if (!el) return;
      el.classList.remove('is-invalid');
      if (el.setCustomValidity) el.setCustomValidity('');
      const fb = feedbackFor(el);
      if (fb && !fb.dataset.static) fb.textContent = '';
    }

    // limpa erro ao digitar
    ['#cardName', '#cardNumber', '#cardCvv', '#cardExpiry', '#documento', '#documento_tipo', '#installments']
    .forEach(sel => $(sel)?.addEventListener('input', e => clearFieldError(e.target)));

    function parseExpiry(v) {
      const s = String(v || '').replace(/\s+/g, '');
      const m = s.slice(0, 2).replace(/\D/g, '');
      const y = s.slice(-4).replace(/\D/g, '');
      return {
        m,
        y
      };
    }

    function luhnOk(num) {
      const s = String(num || '').replace(/\D/g, '');
      let sum = 0,
        dbl = false;
      for (let i = s.length - 1; i >= 0; i--) {
        let d = +s[i];
        if (dbl) {
          d *= 2;
          if (d > 9) d -= 9;
        }
        sum += d;
        dbl = !dbl;
      }
      return s.length >= 12 && sum % 10 === 0;
    }

    function notPastExpiry(mm, yyyy) {
      const M = +mm,
        Y = +yyyy;
      if (!(M >= 1 && M <= 12) || String(yyyy).length !== 4) return false;
      const now = new Date();
      const lim = new Date(Y, M, 0, 23, 59, 59); // último dia do mês
      return lim >= now;
    }

    function firstInvalidFocus(form) {
      const el = form.querySelector(':invalid') || form.querySelector('.is-invalid');
      if (!el) return false;
      const top = el.getBoundingClientRect().top + window.pageYOffset - 16;
      window.scrollTo({
        top,
        behavior: 'smooth'
      });
      setTimeout(() => el.focus({
        preventScroll: true
      }), 220);
      return true;
    }


    function ensureCpfForPix() {
      const form = document.getElementById('formPersona') || document.body;
      const elDocT = $('#documento_tipo');
      const elDocN = $('#documento');

      clearFieldError(elDocT);
      clearFieldError(elDocN);

      const tipo = (elDocT?.value || '').toUpperCase();
      const numero = (elDocN?.value || '').replace(/\D/g, '');

      // PIX: obrigatoriamente CPF
      if (tipo !== 'CPF') {
        setFieldError(elDocT, 'Para PIX, selecione CPF.');
      }

      if (!numero) {
        setFieldError(elDocN, 'Informe o número do documento.');
      }

      // Se criou algum erro, foca e não deixa seguir
      if (form.querySelector('.is-invalid')) {
        firstInvalidFocus(form);
        return null;
      }

      return {
        tipo,
        numero
      };
    }

    async function syncCheckoutDocumentForPix() {
      const slug = getSlug();
      if (!slug) return;

      // valida CPF / tipo = CPF usando a função que você já tem
      const doc = ensureCpfForPix();
      if (!doc) {
        // se deu erro, a ensureCpfForPix já mostrou as mensagens
        return;
      }

      const payload = {
        slug: slug,
        documento_tipo: doc.tipo, // "CPF"
        documento_numero: doc.numero // só dígitos
      };

      const url = API.checkoutDoc();
      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...(API.BASE ? {
          'X-API-TOKEN': API.TOKEN
        } : {})
      };

      try {
        await fetch(url, {
          method: 'POST',
          headers,
          body: JSON.stringify(payload)
        });
        // não precisa fazer nada na UI; é só sincronização silenciosa
      } catch (e) {
        console.warn('[checkout] Falha ao sincronizar CPF para PIX', e);
      }
    }

    const btnPixMethod = document.getElementById('btnPayPix');
    if (btnPixMethod) {
      btnPixMethod.addEventListener('click', function() {
        // marca método atual como PIX para o submit
        document.body.dataset.payMethod = 'pix';
        // dispara fluxo completo do PIX (valida CPF, atualiza doc e gera QR)
        submitCheckoutPix();
        // ao escolher PIX, manda o CPF pra API atualizar a linha do checkout
        //syncCheckoutDocumentForPix();
      });
    }


    /* =================== envio: cartão =================== */
    let redirected = false;

    async function submitCheckoutCard() {
      redirected = false;
      const form = document.getElementById('formPersona') || document.body;
      form.classList.add('was-validated');

      // limpa mensagens prévias
      $$('#boxCard .is-invalid').forEach(el => clearFieldError(el));
      $('#formError')?.remove();

      const slug = getSlug();
      if (!slug) {
        return;
      }

      // Campos
      const elName = $('#cardName');
      const elNum = $('#cardNumber');
      const elCvv = $('#cardCvv');
      const elExp = $('#cardExpiry');
      const elInst = $('#installments');
      const elDocT = $('#documento_tipo');
      const elDocN = $('#documento');
      const elCep = $('#cep');
      const elEnd = $('#endereco_obra');
      const elNumEnd = $('#numero');
      const elUF = $('#estado');

      const elEmail = $('#email');
      const elNome = $('#nome');
      const elPais = $('#codigo_pais');
      const elFone = $('#fone');
      const elComp = $('#complemento');
      const elBairro = $('#bairro');

      const numberRaw = (elNum?.value || '').replace(/\s+/g, '');
      const cvv = elCvv?.value || '';
      const {
        m: expiryMonth,
        y: expiryYear
      } = parseExpiry(elExp?.value);

      // validações
      if (!elName?.value?.trim()) setFieldError(elName, 'Informe o nome impresso no cartão.');
      if (!luhnOk(numberRaw)) setFieldError(elNum, 'Informe um número de cartão válido.');
      if (!/^\d{3,4}$/.test(cvv)) setFieldError(elCvv, 'CVV deve ter 3 ou 4 dígitos.');
      if (!notPastExpiry(expiryMonth, expiryYear))
        setFieldError(elExp, 'Validade inválida ou no passado (MM/AAAA).');

      const inst = Number(elInst?.value || '0');
      if (!(inst >= 1)) setFieldError(elInst, 'Selecione as parcelas.');

      const docTipo = (elDocT?.value || '').toUpperCase();
      const docNum = (elDocN?.value || '').replace(/\D/g, '');
      if (!(docTipo === 'CPF' || docTipo === 'CNPJ'))
        setFieldError(elDocT, 'Para cartão, selecione CPF ou CNPJ.');
      if (!docNum) setFieldError(elDocN, 'Informe o CPF/CNPJ do titular.');

      if (firstInvalidFocus(form)) return;

      // === GA4: payment_intent (cartão) === Analytics
      const ctx = buildPaymentContext('card', inst);
      if (window.ArchaAnalytics && typeof ArchaAnalytics.trackPaymentIntent === 'function') {
        ArchaAnalytics.trackPaymentIntent(
          ctx.name,
          ctx.email,
          ctx.whatsapp,
          ctx.value,
          ctx.method,
          ctx.installments
        );
      }


      // payload ORIGINAL
      const payload = {
        slug,
        installments: inst,
        externalRef: `Checkout-${slug}`,
        card: {
          number: numberRaw,
          expiryMonth,
          expiryYear,
          ccv: cvv
        },
        holder: {
          name: elName?.value || $('#nome')?.value || '',
          nome: elNome?.value || elName?.value || '',
          cpfCnpj: docNum,
          postalCode: (elCep?.value || '').replace(/\D/g, ''),
          address: elEnd?.value || '',
          addressNumber: elNumEnd?.value || '',
          documentType: docTipo,
          estado: elUF?.value || '',
          email: (elEmail?.value || '').trim(),
          countryCode: (elPais?.value || '').trim().toUpperCase(),
          telefone: (elFone?.value || '').replace(/\D/g, ''),
          complemento: elComp?.value || '',
          bairro: elBairro?.value || ''
        }
      };

      const url = API.checkoutCard();
      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...(API.BASE ? {
          'X-API-TOKEN': API.TOKEN
        } : {})
      };

      const btn = document.getElementById('btnFinish');
      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Processando...';
      }

      if (typeof showOverlay === 'function') {
        showOverlay('Processando pagamento, aguarde...');
      }

      try {
        const resp = await fetch(url, {
          method: 'POST',
          headers,
          body: JSON.stringify(payload)
        });
        const json = await resp.json().catch(() => ({}));

        if (!resp.ok || json.ok === false) {
          // Erros por campo vindos do back
          if (json?.errors && typeof json.errors === 'object') {
            for (const [k, v] of Object.entries(json.errors)) {
              const msg = Array.isArray(v) ? v[0] : String(v);
              const map = {
                number: '#cardNumber',
                card_number: '#cardNumber',
                ccv: '#cardCvv',
                cvv: '#cardCvv',
                expiryMonth: '#cardExpiry',
                expiryYear: '#cardExpiry',
                installments: '#installments',
                cpfCnpj: '#documento',
                document: '#documento'
              };
              const id = map[k] || null;
              if (id) setFieldError($(id), msg);
            }
            firstInvalidFocus(form);
          } else {
            // Erro genérico: mostra inline acima do botão
            const warn = document.createElement('div');
            warn.id = 'formError';
            warn.className = 'alert alert-danger mt-3';
            warn.textContent = json?.message || json?.error || 'Não foi possível processar o pagamento.';
            btn?.closest('.ck-body')?.appendChild(warn);
          }

          // === GA4: payment_status (erro de gateway) ===
          if (window.ArchaAnalytics && typeof ArchaAnalytics.trackPaymentStatus === 'function') {
            ArchaAnalytics.trackPaymentStatus(
              ctx.name,
              ctx.email,
              ctx.whatsapp,
              ctx.value,
              ctx.method,
              ctx.installments,
              'error'
            );
          }

          return;
        }

        // === GA4: payment_status (sucesso) ===
        if (window.ArchaAnalytics && typeof ArchaAnalytics.trackPaymentStatus === 'function') {
          ArchaAnalytics.trackPaymentStatus(
            ctx.name,
            ctx.email,
            ctx.whatsapp,
            ctx.value,
            ctx.method,
            ctx.installments,
            'success'
          );
        }

        // SUCESSO → salvar dados p/ thank-you e redirecionar
        redirected = true;

        const elNomeShow = document.querySelector('#nome') || document.querySelector('#cardName');
        const elAmb = document.querySelector('#sumAmb');
        const elTipo = document.querySelector('#sumTipo');
        const elM2 = document.querySelector('#sumM2');

        const thankData = {
          nome: elNomeShow?.value || elNomeShow?.textContent || '',
          ambientes: elAmb?.textContent || '',
          tipo: elTipo?.textContent || '',
          metragem: elM2?.textContent || ''
        };

        try {
          localStorage.setItem('archa_thankyou', JSON.stringify(thankData));
        } catch (e) {
          console.warn('não foi possível salvar no localStorage', e);
        }

        window.location.href = 'thank-you.php';
      } catch (err) {
        const warn = document.createElement('div');
        warn.id = 'formError';
        warn.className = 'alert alert-danger mt-3';
        warn.textContent = err?.message || 'Falha de rede. Tente novamente.';
        btn?.closest('.ck-body')?.appendChild(warn);

        // === GA4: payment_status (erro de rede) ===
        if (window.ArchaAnalytics && typeof ArchaAnalytics.trackPaymentStatus === 'function') {
          ArchaAnalytics.trackPaymentStatus(
            ctx.name,
            ctx.email,
            ctx.whatsapp,
            ctx.value,
            ctx.method,
            ctx.installments,
            'network_error'
          );
        }
      } finally {

        if (!redirected) {
          if (typeof hideOverlay === 'function') hideOverlay();
          if (btn) {
            btn.disabled = false;
            btn.textContent = 'Finalizar pagamento';
          }
        }
      }
    }

    // dispara no seu botão (já existe no _aceite_cta.php)
    //document.addEventListener('checkout:submit', submitCheckoutCard);

    /* =================== envio: PIX =================== */
    async function submitCheckoutPix() {
      const form = document.getElementById('formPersona') || document.body;
      form.classList.add('was-validated');

      const slug = getSlug();
      if (!slug) return;

      // valida CPF obrigatório para PIX
      const doc = ensureCpfForPix();
      if (!doc) return;

      const btn = document.getElementById('btnFinish');
      const headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...(API.BASE ? {
          'X-API-TOKEN': API.TOKEN
        } : {})
      };
      // Mostra o box do PIX e reforça a mensagem de "Gerando código PIX..."
      const boxPix = document.getElementById('boxPix');
      const pixSection = document.getElementById('pix-section');
      const qrContainer = document.getElementById('pix-qr-container');
      const payloadEl = document.getElementById('pix-payload');
      const expirationEl = document.getElementById('pix-expiration');

      if (boxPix) boxPix.classList.remove('d-none');
      if (pixSection) pixSection.style.display = 'block';

      if (qrContainer) {
        qrContainer.innerHTML = '<p class="text-muted">Gerando código PIX...</p>';
      }
      if (payloadEl) payloadEl.textContent = '';
      if (expirationEl) expirationEl.textContent = '';

      if (btn) {
        btn.disabled = true;
        btn.textContent = 'Processando...';
      }
      if (typeof showOverlay === 'function') {
        showOverlay('Gerando PIX, aguarde...');
      }

      // contexto para GA4
      const ctx = buildPaymentContext('pix', '');

      try {
        // 1) Atualiza CPF/CNPJ no checkout antes de falar com o Asaas
        const docResp = await fetch(API.checkoutDoc(), {
          method: 'POST',
          headers,
          body: JSON.stringify({
            slug,
            documento_tipo: doc.tipo,
            documento_numero: doc.numero
          })
        });
        const docJson = await docResp.json().catch(() => ({}));

        if (!docResp.ok || docJson.ok === false) {
          alert(docJson.message || 'Não foi possível atualizar o documento para PIX.');
          return;
        }

        // GA4: payment_intent (PIX)
        if (window.ArchaAnalytics && typeof ArchaAnalytics.trackPaymentIntent === 'function') {
          ArchaAnalytics.trackPaymentIntent(
            ctx.name,
            ctx.email,
            ctx.whatsapp,
            ctx.value,
            ctx.method,
            ctx.installments
          );
        }

        // 2) Cria cobrança PIX + QR Code
        const pixResp = await fetch(API.checkoutPix(), {
          method: 'POST',
          headers,
          body: JSON.stringify({
            slug,
            externalRef: `Checkout-${slug}`
          })
        });
        const pixJson = await pixResp.json().catch(() => ({}));

        if (!pixResp.ok || pixJson.ok === false) {
          // GA4: payment_status (erro)
          if (window.ArchaAnalytics && typeof ArchaAnalytics.trackPaymentStatus === 'function') {
            ArchaAnalytics.trackPaymentStatus(
              ctx.name,
              ctx.email,
              ctx.whatsapp,
              ctx.value,
              ctx.method,
              ctx.installments,
              'error'
            );
          }

          alert(pixJson.message || pixJson.error || 'Não foi possível gerar o PIX.');
          return;
        }

        // GA4: payment_status (sucesso de criação do PIX)
        if (window.ArchaAnalytics && typeof ArchaAnalytics.trackPaymentStatus === 'function') {
          ArchaAnalytics.trackPaymentStatus(
            ctx.name,
            ctx.email,
            ctx.whatsapp,
            ctx.value,
            ctx.method,
            ctx.installments,
            'success'
          );
        }

        // Renderiza QR Code e payload na UI
        const boxPix = document.getElementById('boxPix');
        const pixSection = document.getElementById('pix-section');
        const qrContainer = document.getElementById('pix-qr-container');
        const payloadEl = document.getElementById('pix-payload');
        const expirationEl = document.getElementById('pix-expiration');

        if (boxPix) boxPix.classList.remove('d-none');
        if (pixSection) pixSection.style.display = 'block';

        const qr = pixJson.qr || {};

        if (qrContainer) {
          if (qr.encodedImage) {
            qrContainer.innerHTML =
              `<img src="data:image/png;base64,${qr.encodedImage}" ` +
              `alt="QR Code PIX" class="img-fluid">`;
          } else {
            qrContainer.innerHTML = '<p class="text-muted">QR Code gerado. Abra seu app do banco para pagar.</p>';
          }
        }

        if (payloadEl && qr.payload) {
          payloadEl.textContent = qr.payload;
        }

        if (expirationEl && qr.expirationDate) {
          expirationEl.textContent = 'Validade: ' + qr.expirationDate;
        }

      } catch (err) {
        // GA4: payment_status (erro de rede)
        if (window.ArchaAnalytics && typeof ArchaAnalytics.trackPaymentStatus === 'function') {
          ArchaAnalytics.trackPaymentStatus(
            ctx.name,
            ctx.email,
            ctx.whatsapp,
            ctx.value,
            ctx.method,
            ctx.installments,
            'network_error'
          );
        }

        alert(err?.message || 'Falha de rede ao gerar PIX. Tente novamente.');
      } finally {
        if (typeof hideOverlay === 'function') hideOverlay();
        if (btn) {
          btn.disabled = false;
          btn.textContent = 'Finalizar pagamento';
        }
      }
    }

    // dispara no botão, escolhendo entre cartão e PIX
    document.addEventListener('checkout:submit', () => {
      const method = (document.body.dataset.payMethod === 'pix') ? 'pix' : 'card';
      if (method === 'pix') {
        submitCheckoutPix();
      } else {
        submitCheckoutCard();
      }
    });


  })();
</script>

<!-- Tracking Analytics -->

<script>
  (function() {
    document.addEventListener("DOMContentLoaded", function() {
      const btnCard = document.getElementById("btnPayCard");
      const btnPix = document.getElementById("btnPayPix");
      const installmentsSelect = document.getElementById("installments");

      if (!window.ArchaAnalytics || typeof ArchaAnalytics.trackCheckoutPaymentMethod !== "function") {
        return; // se o analytics não carregou, não quebra nada
      }

      // método atual (começa como 'card' porque o botão já vem .active)
      let currentMethod = (btnCard && btnCard.classList.contains("active")) ? "card" : "";
      let lastMethod = "";
      let lastInstallments = "";

      function emitCheckoutPaymentMethod(methodOverride) {
        const method = methodOverride || currentMethod || "";
        if (!method) return;

        // só cartão tem parcelas; PIX manda vazio
        const installments =
          (method === "card" && installmentsSelect) ?
          (installmentsSelect.value || "").trim() :
          "";

        // evita enviar evento duplicado com os mesmos valores
        if (method === lastMethod && installments === lastInstallments) {
          return;
        }

        lastMethod = method;
        lastInstallments = installments;

        ArchaAnalytics.trackCheckoutPaymentMethod(method, installments);
      }

      // Clique em Cartão de Crédito
      if (btnCard) {
        btnCard.addEventListener("click", function() {
          currentMethod = "card";
          emitCheckoutPaymentMethod("card");
        });
      }

      // Clique em PIX
      if (btnPix) {
        btnPix.addEventListener("click", function() {
          currentMethod = "pix";
          emitCheckoutPaymentMethod("pix");
        });
      }

      // Mudança de parcelas
      if (installmentsSelect) {
        installmentsSelect.addEventListener("change", function() {
          if (!currentMethod) {
            currentMethod = "card"; // fallback: se nunca clicou, assume cartão
          }
          emitCheckoutPaymentMethod();
        });
      }
    });
  })();
</script>