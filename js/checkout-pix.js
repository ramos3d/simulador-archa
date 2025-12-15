/* checkout-pix.js */
(function () {
  const BRIDGE = `${APP_BASE}/bridge.php`;

  let pixBusy = false;     // trava enquanto gera
  let pixOnce = false;     // garante geração única
  let pixIv = null;      // interval do polling

  // ---------- utils ----------
  async function postJSON(url, body) {
    const r = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(body)
    });
    const j = await r.json().catch(() => ({}));
    if (!r.ok) throw new Error(j.message || j.error || 'Falha na requisição');
    return j;
  }
  async function getJSON(url) {
    const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const j = await r.json().catch(() => ({}));
    if (!r.ok) throw new Error(j.message || j.error || 'Falha na requisição');
    return j;
  }
  function getSlug() {
    const u = new URL(location.href);
    return (
      u.searchParams.get('projeto') ||
      document.getElementById('slug')?.value ||
      window.state?.slug ||
      ''
    ).trim();
  }
  function showPixUI() {
    const sec = document.getElementById('pix-section');
    if (sec) sec.style.display = '';               // revela área do PIX
    const card = document.getElementById('cartao-section');
    if (card && card.style) card.style.display = 'none'; // opcional: esconde cartão
  }

  // ---------- fluxo PIX ----------
  async function gerarPix() {
    if (pixBusy || pixOnce) return;
    pixBusy = true;

    const slug = getSlug();
    const box = document.getElementById('pix-qr') || document.getElementById('pix-qr-container');
    const pay = document.getElementById('pix-payload');
    const exp = document.getElementById('pix-exp') || document.getElementById('pix-expiration');

    if (!slug) {
      if (box) box.innerHTML = '<span class="text-danger">Slug do checkout ausente.</span>';
      pixBusy = false; return;
    }

    if (box) box.innerHTML = '<em>Gerando QR Code <span id="spinnerVerPreco" class="spinner-grow spinner-grow-sm ms-2" role="status" aria-hidden="true" style="width: 10px!important;height: 10px!important;"></span></em>';
    if (pay) pay.textContent = '';
    if (exp) exp.textContent = '';

    // exigido pela API (/api/checkout/pix) — camelCase
    const externalRef = (`PIX-${slug}-${Date.now()}`).slice(0, 50);

    try {
      const resp = await postJSON(`${BRIDGE}?op=checkout_pix`, { slug, externalRef });

      // normaliza estrutura do QR
      const qr = resp.qr?.encodedImage ? resp.qr : (resp.qrCode || resp.qrcode || resp);
      if (qr?.encodedImage) {
        if (box) {
          box.innerHTML =
            `<img class="img-fluid" alt="QR Code PIX" src="data:image/png;base64,${qr.encodedImage}">`;
        }
        if (pay && qr.payload) pay.textContent = `Copia e cola: ${qr.payload}`;
        if (exp && qr.expirationDate) exp.textContent = `Expira em: ${qr.expirationDate}`;
      } else {
        if (box) box.innerHTML = '<span class="text-danger">QR Code não gerado.</span>';
      }

      // inicia polling uma única vez
      if (pixIv) { clearInterval(pixIv); pixIv = null; }
      let tries = 0;
      pixIv = setInterval(async () => {
        tries++;
        try {
          // GET /api/editor/{slug} via bridge (retorna status)
          const st = await getJSON(`${BRIDGE}?op=editor_show&slug=${encodeURIComponent(slug)}`);
          const s = (st.status || '').toLowerCase();
          if (['approved', 'paid', 'confirmed', 'settled', 'completed'].includes(s)) {
            clearInterval(pixIv); pixIv = null;
            const go = st.redirect_url || st.url;
            if (go) location.href = go;
            else if (box) box.innerHTML = '<strong>Pagamento confirmado!</strong>';
          }
        } catch { /* ignora erros momentâneos do polling */ }
        if (tries > 90) { clearInterval(pixIv); pixIv = null; } // ~3 min @ 2s
      }, 2000);

      pixOnce = true;
    } catch (e) {
      console.error(e);
      if (box) box.innerHTML = `<span class="text-danger">Erro ao gerar o QR Code: ${e.message || 'Falha'}</span>`;
    } finally {
      pixBusy = false;
    }
  }

  // ---------- ícone como botão ----------
  const pixImg = document.getElementById('pix-icon');
  if (pixImg) {
    pixImg.classList.add('clickable');
    pixImg.style.cursor = 'pointer';
    pixImg.setAttribute('role', 'button');
    pixImg.setAttribute('tabindex', '0');

    const onPixClick = async () => { showPixUI(); await gerarPix(); };

    pixImg.addEventListener('click', onPixClick);
    pixImg.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') onPixClick();
    });

    // se existir o botão antigo, remove
    document.getElementById('btn-pix')?.remove();

    console.log('[PIX] listener ligado no ícone');
  } else {
    console.warn('[PIX] #pix-icon não encontrado.');
  }

  // dispara 1x quando #boxPix perde a classe d-none
  const obs = new MutationObserver(() => {
    const isVisible = !document.getElementById('boxPix')?.classList.contains('d-none');
    if (isVisible) {
      showPixUI();
      gerarPix();
      obs.disconnect();
    }
  });
  obs.observe(document.body, { attributes: true, subtree: true, attributeFilter: ['class'] });

})();
