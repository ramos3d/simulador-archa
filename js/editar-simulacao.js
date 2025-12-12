
/**
 * editar-simulacao.js
 * Editor de Simulação (modo edição)
 * - Busca snapshot via bridge.php
 * - Preenche a UI
 * - Simula automaticamente o clique no "Ver preço" (fluxo original)
 * - Não cria lead duplicado: back-end deve suprimir notificações se vier "slug"
 */
(function () {
  const BRIDGE = 'bridge.php';
  const params = new URLSearchParams(location.search);
  const slug = params.get('projeto');

  // ---------- Overlay ----------
  function showOverlay(msg = 'Aguarde') {
    let el = document.getElementById('editorLoading');
    if (!el) {
      el = document.createElement('div');
      el.id = 'editorLoading';
      el.style.cssText = `
        position:fixed; inset:0; z-index:99999; display:flex;
        align-items:center; justify-content:center;
        background:rgba(0,0,0,.35); backdrop-filter:saturate(140%) blur(2px);
        font-family:system-ui,-apple-system,Segoe UI,Roboto; color:#fff`;
      const box = document.createElement('div');
      box.id = 'editorLoadingBox';
      box.style.cssText = `
        background:#111; padding:14px 18px; border-radius:12px;
        box-shadow:0 6px 24px rgba(0,0,0,.35); font-weight:600`;
      box.textContent = msg;
      el.appendChild(box);
      document.body.appendChild(el);
    } else {
      el.querySelector('#editorLoadingBox').textContent = msg;
      el.style.display = 'flex';
    }
  }
  function hideOverlay() {
    const el = document.getElementById('editorLoading');
    if (el) el.style.display = 'none';
  }

  // ---------- Helpers ----------
  const qs = (s) => document.querySelector(s);
  const qsa = (s) => Array.from(document.querySelectorAll(s));

  function setValue(id, val) {
    const el = document.getElementById(id);
    if (!el) return;
    el.value = (val ?? '').toString();
    if (el.classList.contains('metric-input') || el.classList.contains('form-control')) {
      if (String(el.value).trim()) el.classList.add('is-filled');
    }
    el.dispatchEvent(new Event('input', { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function selectOptionBox(containerSel, dataAttr, value) {
    const list = qsa(`${containerSel} .option-box`);
    if (!list.length) return;
    const norm = String(value ?? '').toUpperCase();
    list.forEach(el => {
      const v = String(el.dataset[dataAttr] || '').toUpperCase();
      el.classList.toggle('selected', v === norm);
      const chk = el.querySelector('.check-indicator img');
      if (chk) chk.style.display = el.classList.contains('selected') ? 'block' : 'none';
    });
  }

  function setPlanSelection(code) {
    if (!code) return;
    const norm = String(code).toLowerCase(); // solo|duo|trio
    qsa('#planAccordion .plan').forEach(p => p.classList.remove('selected'));
    const plan = qs(`#planAccordion .plan[data-produto="${norm}"]`);
    if (plan) {
      plan.classList.add('selected');
      const btn = plan.querySelector('.accordion-button');
      if (btn && btn.classList.contains('collapsed')) { try { btn.click(); } catch (_) { } }
      if (window.state) window.state.planCode = norm;
      window.planoSelecionado = norm;
    }


  }

  function categoriaUIFromApi(apiVal) {
    const t = String(apiVal || '').trim().toUpperCase();
    if (t === 'VERIFICADOS') return 'VERIFICADOS';
    if (t === 'PREFERIDOS') return 'PREFERIDOS';
    return 'ESTREANTES';
  }

  function applySnapshotAll(snap = {}) {
    // Passo 1 — métricas e tipo
    setValue('area', Number(snap.metragem) || 0);
    setValue('qtd_amb', Number(snap.qtd_ambientes) || 0);
    setValue('tipo_projeto', snap.tipo_projeto || '');

    // === Restaurar visual do "Perfil do espaço" ===
    try {
      const parsed = parseTipoProjeto(snap.tipo_projeto || '');
      if (parsed) {
        setTipoProjetoUI(parsed.catKey, parsed.itemKey, parsed.label, { silent: true });
        updateTipoProjetoImages(); // troca Default/Active
      }
    } catch { }


    // Contatos (nome, email, whatsapp)
    const nome = snap.nome ?? snap?.cliente?.nome ?? snap.customer_name ?? '';
    const email = snap.email ?? snap?.cliente?.email ?? snap.customer_email ?? '';
    const fone = snap.telefone ?? snap?.cliente?.telefone ?? snap.phone ?? snap.whatsapp ?? '';
    const codigo_pais = snap.codigo_pais ?? '';
    setValue('nome', nome);
    setValue('email', email);
    setValue('fone', fone);
    setValue('codigo_pais', codigo_pais);

    // Passo 2 — categoria & região
    const catUI = categoriaUIFromApi(snap.categoria);
    setValue('categoria', catUI);
    selectOptionBox('#proCatWrap', 'cat', catUI);

    const reg = (snap.regiao_arquiteto || 'BRASIL_TODO').toString();
    setValue('regiao_key', reg);
    selectOptionBox('#regWrap', 'reg', reg);

    // Passo 3 — adicionais, nova área, prazo
    const arr = Array.isArray(snap.adicionais) ? snap.adicionais.slice() : [];
    const novaPair = arr.find(v => /^nova_area=/.test(String(v || '')));
    let novaArea = (novaPair ? String(novaPair).split('=')[1] : (snap.nova_area || 'NAO')) || 'NAO';
    novaArea = String(novaArea).toUpperCase();

    const itemKeys = arr.map(v => String(v || '').trim()).filter(v => v && !/^nova_area=/.test(v));

    qsa('#workItems .multi-option').forEach(el => {
      el.classList.remove('selected');
      const chk = el.querySelector('.check-indicator img');
      if (chk) chk.style.display = 'none';
    });
    itemKeys.forEach(key => {
      const el = qs(`#workItems .multi-option[data-key="${CSS.escape(key)}"]`);
      if (el) {
        el.classList.add('selected');
        const chk = el.querySelector('.check-indicator img');
        if (chk) chk.style.display = 'block';
      }
    });
    setValue('propostas', itemKeys.join(','));
    setValue('nova_area', novaArea);
    selectOptionBox('#novaAreaWrap', 'reg', novaArea);

    const prazo = Number(snap.prazo_dias) || 21;
    setValue('prazo_dias', String(prazo));
    selectOptionBox('#prazoWrap', 'prazo', prazo);

    // Plano
    setPlanSelection(snap.produto_code || 'duo');

    // libera botão de avançar (se existir)
    const btnAv3 = qs('#btnAvancar3');
    if (btnAv3) { btnAv3.classList.remove('btn-disabled'); btnAv3.removeAttribute('disabled'); }
  }

  // —— dispara o mesmo fluxo do botão "Ver preço"
  function autoPressVerPreco() {
    const btn = document.getElementById('btnVerPreco');
    if (!btn) return;

    // expõe um “extra” que pode ser lido pelo handler do lead (para enviar slug)
    window.__LEAD_EDITOR_EXTRA__ = { slug: slug || null };

    // habilita visualmente
    btn.dataset.locked = '0';
    btn.classList.remove('btn-disabled');
    btn.disabled = false;

    // se existir um handler global, use-o; senão, dispare clique nativo
    if (typeof window.onVerPreco === 'function') {
      try { window.onVerPreco({ fromEditor: true, slug }); } catch (_) { btn.click(); }
    } else {
      btn.dispatchEvent(new Event('click', { bubbles: true }));
    }

    // após disparar, re-travar o botão
    setTimeout(() => {
      btn.dataset.locked = '1';
      btn.classList.add('btn-disabled');
      btn.disabled = true;
    }, 600);
  }

  // —— redireciona removendo ?projeto
  function redirectWithoutProjeto() {
    const url = new URL(location.href);
    url.searchParams.delete('projeto');
    location.replace(url.toString());
  }

  async function loadEditorData(s) {
    showOverlay('Carregando simulação…');

    try {
      const r = await fetch(`${BRIDGE}?op=editor_show&slug=${encodeURIComponent(s)}`, {
        method: 'GET',
        headers: { 'Accept': 'application/json' },
        cache: 'no-store'
      });
      const j = await r.json().catch(() => ({}));
      if (!r.ok) return redirectWithoutProjeto();

      // confirmado → modal BS5 e redireciona sem ?projeto
      if (j.status === 'CONFIRMED' || j.reason === 'already_confirmed') {
        showBsAlertAndRedirect('Este projeto já foi confirmado. Edição desativada.');
        return;
      }

      // snapshot + fallback de contato
      const raw = j.snapshot || {};
      const snap = {
        ...raw,
        nome: (raw.nome ?? j?.cliente?.nome ?? ''),
        email: (raw.email ?? j?.cliente?.email ?? ''),
        telefone: (raw.telefone ?? j?.cliente?.telefone ?? j?.cliente?.whatsapp ?? '')
      };

      // 1) Preenche toda a UI
      applySnapshotAll(snap);

      // 2) Simula o clique no "Ver preço" (fluxo original do front)
      autoPressVerPreco();


      // 2.1) Prime o cache com o snapshot carregado (evita patch vazio sobrescrevendo contato)
      window.__EDITOR_SNAPSHOT__ = {
        slug,
        token: window.CHECKOUT_TOKEN || null,
        nome: snap.nome || '',
        email: snap.email || '',
        telefone: snap.telefone || '',
        tipo_projeto: snap.tipo_projeto || '',
        metragem: Number(snap.metragem) || 0,
        ambientes: Number(snap.qtd_ambientes) || 0,
        categoria: snap.categoria || '',
        regiao: snap.regiao_arquiteto || 'BRASIL_TODO',
        nova_area: (snap.nova_area || 'NAO').toString().toUpperCase(),
        prazo_dias: Number(snap.prazo_dias) || 21,
        produto_code: snap.produto_code || 'duo',
        // se vier array de adicionais, preserve
        adicionais: Array.isArray(snap.adicionais) ? snap.adicionais.slice() : null,
        // se você tem os preços vistos salvos no servidor, pode injetar aqui também
      };

      // Notifica o checkout-cache para semear o último snapshot
      window.dispatchEvent(new CustomEvent('checkout:prime', { detail: window.__EDITOR_SNAPSHOT__ }));

      // 2.2) Agora sim: libera o cache incremental
      window.CHECKOUT_SUSPENDED = false;
      window.CHECKOUT_ACTIVE = true;
      localStorage.setItem('checkoutActive', '1');





    } catch (err) {
      // falha silenciosa → volta sem ?projeto
      redirectWithoutProjeto();
      return;
    } finally {
      hideOverlay();
    }
  }

  // ---------- Checkout Cache (ativo) ----------
  window.CHECKOUT_ACTIVE = false;                 // não envia nada ainda
  window.CHECKOUT_SUSPENDED = true;               // flag extra (honrada pelo checkout-cache.js)
  localStorage.removeItem('checkoutActive');      // garante off no primeiro load
  window.CHECKOUT_SLUG = slug;
  window.CHECKOUT_TOKEN = localStorage.getItem('uuidLaravel') || null;




  // ---------- Rascunho / Pagamento (inalterado) ----------
  window.salvarRascunho = async function (snapshot) {
    if (!slug) return;
    const r = await fetch(`${BRIDGE}?op=editor_update&slug=${encodeURIComponent(slug)}`, {
      method: 'PATCH',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify(snapshot || {})
    });
    if (!r.ok) console.warn('editor_update erro', await r.json().catch(() => ({})));
  };

  window.pagarCartao = async function (payload) {
    payload = payload || {};
    payload.slug = slug;
    payload.externalRef = `checkout:${slug}:cartao`;
    const r = await fetch(`${BRIDGE}?op=checkout_card`, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    return r.json();
  };

  window.pagarPix = async function (payload) {
    payload = payload || {};
    payload.slug = slug;
    payload.externalRef = `checkout:${slug}:pix`;
    const r = await fetch(`${BRIDGE}?op=checkout_pix`, {
      method: 'POST',
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    return r.json();
  };

  if (slug) loadEditorData(slug);
})();



// Modal BS5 + redirect ao fechar (reuso)
function showBsAlertAndRedirect(msg) {
  let el = document.getElementById('editorAlertModal');
  if (!el) {
    el = document.createElement('div');
    el.id = 'editorAlertModal';
    el.className = 'modal fade';
    el.tabIndex = -1;
    el.innerHTML = `
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0 pb-0">
        
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>

      <div class="modal-body text-center pt-3">
        <p id="editorAlertMsg" class="mb-0"></p>
      </div>

      <div class="modal-footer border-0 justify-content-center">
        <button type="button" class="btn btn-archa-primary px-4" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>`;

    document.body.appendChild(el);
  }
  el.querySelector('#editorAlertMsg').textContent = msg;

  const modal = new bootstrap.Modal(el, { backdrop: 'static', keyboard: false });
  const onHidden = () => {
    el.removeEventListener('hidden.bs.modal', onHidden);
    const url = new URL(window.location.href);
    url.searchParams.delete('projeto');
    window.location.replace(url.toString());
  };
  el.addEventListener('hidden.bs.modal', onHidden);
  modal.show();
}


// editar-simulador.js — redireciona para a raiz ao clicar no "OK" do modal
document.addEventListener('click', (e) => {
  if (e.target.matches('.modal-footer .btn[data-bs-dismiss="modal"]')) {
    window.location.href = '/';
  }
});




// --- Parseia "tipo_projeto" do backend em {catKey, itemKey, label}
function parseTipoProjeto(raw) {
  // aceita formatos:
  //  - "RESIDENCIAL: Apartamento"
  //  - "residencial: apartamento"
  //  - "residencial|apartamento"
  //  - "apartamento", "casa", "escritorio", etc. (default cat = residencial)
  const txt = String(raw || '').trim();
  if (!txt) return null;

  const norm = txt.replace(/\s+/g, ' ').trim();

  let cat = null, item = null;

  // formato "CAT: Nome"
  const m1 = norm.match(/^([A-ZÇÃÉÍÓÚÄËÏÖÜ]+)\s*:\s*(.+)$/i);
  if (m1) {
    cat = m1[1].toLowerCase();
    item = m1[2].toLowerCase();
  } else {
    // formato "cat|item"
    const m2 = norm.match(/^([a-zçãéíóúäëïöü]+)\s*\|\s*([a-zçãéíóúäëïöü]+)$/i);
    if (m2) {
      cat = m2[1].toLowerCase();
      item = m2[2].toLowerCase();
    } else {
      // apenas item → assume residencial
      cat = 'residencial';
      item = norm.toLowerCase();
    }
  }

  // normaliza chave de item para bater com data-key dos cards
  const MAP_ITEM = {
    'apartamento': 'apartamento',
    'apto': 'apartamento',
    'casa': 'casa',
    'hotelaria': 'hotelaria',
    'bares': 'bares',
    'bares, restaurantes e casas noturnas': 'bares',
    'lojas': 'lojas',
    'lojas varejo': 'lojas',
    'clinicas': 'clinicas',
    'clínicas e espaços estéticos': 'clinicas',
    'escritorio': 'escritorio',
    'escritório': 'escritorio',
    'estandes': 'estandes',
    'eventos': 'eventos',
  };

  const MAP_CAT = {
    'residencial': 'residencial',
    'comercial': 'comercial',
    'corporativo': 'corporativo',
    // às vezes backend manda "EVENTOS" junto da família corporativa
    'eventos': 'corporativo'
  };

  const catKey = MAP_CAT[cat] || 'residencial';
  const itemKey = MAP_ITEM[item] || item.replace(/[^\w]+/g, '').toLowerCase();

  // label final padrão igual ao que você já usa
  const LABELS = {
    residencial: {
      apartamento: 'RESIDENCIAL: Apartamento',
      casa: 'RESIDENCIAL: Casa'
    },
    comercial: {
      hotelaria: 'COMERCIAL: Hotelaria',
      bares: 'COMERCIAL: Bares, restaurantes e casas noturnas',
      lojas: 'COMERCIAL: Lojas varejo',
      clinicas: 'COMERCIAL: Clínicas e espaços estéticos'
    },
    corporativo: {
      escritorio: 'CORPORATIVO: Escritório',
      estandes: 'EVENTOS: Estandes',
      eventos: 'EVENTOS: Espaços para eventos e/ou masterplan/palco'
    }
  };

  const label = LABELS[catKey]?.[itemKey] || norm;
  return { catKey, itemKey, label };
}

// --- Aplica visualmente (botões, grids e card selecionado) e sincroniza #tipo_projeto
function setTipoProjetoUI(catKey, itemKey, label, { silent = false } = {}) {
  // 1) Botões de categoria (.btn-tipo)
  document.querySelectorAll('.btn.btn-tipo').forEach(btn => {
    const active = (btn.dataset.cat === catKey);
    btn.classList.toggle('active', active);
  });

  // 2) Grids: mostra o certo e esconde os demais
  const grids = ['residencial', 'comercial', 'corporativo'];
  grids.forEach(g => {
    const el = document.getElementById(`cards-${g}`);
    if (!el) return;
    el.classList.toggle('d-none', g !== catKey);
  });

  // 3) Seleção de card (.tipo-imovel data-key=…)
  //    zera "selected" em todos, marca apenas o escolhido no grid atual
  document.querySelectorAll('.tipo-imovel.selected').forEach(x => x.classList.remove('selected'));
  const selected = document.querySelector(`#cards-${catKey} .tipo-imovel[data-key="${CSS.escape(itemKey)}"]`);
  if (selected) selected.classList.add('selected');

  // 4) Hidden padronizado
  const hid = document.getElementById('tipo_projeto');
  if (hid) {
    hid.value = label || '';
    if (!silent) {
      hid.dispatchEvent(new Event('input', { bubbles: true }));
      hid.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }
}
