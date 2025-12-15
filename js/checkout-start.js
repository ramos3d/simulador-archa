/**
 * checkout-start.js
 * Checkout START — versão final com reuso de slug e proxy PHP
 * - envia todos os campos necessários ao backend
 * - NÃO expõe X-API-TOKEN; quem envia é o ajax/checkout-start.php
 */

// helper: reaproveita slug existente (URL > localStorage)
function getExistingSlug() {
  const qsSlug = new URLSearchParams(location.search).get('projeto');
  if (qsSlug && qsSlug.trim()) return qsSlug.trim();
  const lsSlug = localStorage.getItem('checkoutSlug');
  return (lsSlug && lsSlug.trim()) ? lsSlug.trim() : null;
}

// pega o produto selecionado (ou usa o do estado)
function getProdutoCodeCheckout() {
  const sel = document.querySelector('.accordion-item.plan.selected[data-produto]');
  const code = sel?.dataset?.produto || window.state?.planCode || window.planoSelecionado || null;
  return code ? String(code).toLowerCase() : null;
}

// util local
const onlyDigits = s => String(s || '').replace(/\D+/g, '');

// dispara o /api/checkout/start com TODOS os campos exigidos
async function enviarCheckoutStart() {
  let token = localStorage.getItem('leadUuid') || localStorage.getItem('uuidLaravel');
  if (!token && typeof ensureUuid === 'function') token = await ensureUuid();

  // coleta
  const nome = document.getElementById('nome')?.value?.trim() || '';
  const email = document.getElementById('email')?.value?.trim() || '';
  const telefone = document.getElementById('fone')?.value?.trim() || '';
  const metragem = +document.getElementById('area')?.value || 0;
  const ambientes = +document.getElementById('qtd_amb')?.value || 0; // será mapeado para qtd_ambientes
  const prazo_dias = (typeof getPrazo === 'function') ? getPrazo() : (+document.getElementById('prazo_dias')?.value || 21);
  const categoriaUI = (typeof getCat === 'function') ? getCat() : 'ESTREANTES';
  const categoriaAPI = (typeof categoriaApiFromUI === 'function') ? categoriaApiFromUI(categoriaUI) : 'Estreantes';
  const regiao = (typeof getReg === 'function') ? getReg() : 'BRASIL_TODO';
  const tipo_proj = (typeof getTipo === 'function') ? getTipo() : '';
  const adicionaisArr = (typeof getAdicionaisForLead === 'function') ? getAdicionaisForLead() : [];
  const adicionaisStr = JSON.stringify(adicionaisArr);
  const codigo_pais = document.getElementById('codigo_pais')?.value?.trim() || '';


  // (opcionais/documentos)
  const documento_tipo = document.getElementById('documento_tipo')?.value?.trim() || 'CPF';
  const documentoNumero = onlyDigits(document.getElementById('documento')?.value || '');
  const endereco_obra = document.getElementById('endereco_obra')?.value?.trim() || null;
  const cep = onlyDigits(document.getElementById('cep')?.value || '');
  const cidade = document.getElementById('cidade')?.value?.trim() || null;
  const estado = document.getElementById('estado')?.value?.trim() || null;
  const pais = (document.getElementById('pais')?.value?.trim() || 'BR');

  // analytics / UTMs
  const utmObj = (typeof getAllUTMs === 'function') ? getAllUTMs() : {};
  const utmArray = Object.entries(utmObj).map(([k, v]) => `${k}=${v}`);

  // --- preços vistos (usa a função já carregada no footer) ---
  const vistos = (typeof getSeenPrices === 'function') ? getSeenPrices() : null;

  // plano escolhido
  const produto_code = getProdutoCodeCheckout();

  // número com 2 casas ou null
  const n2 = (x) => {
    const n = Number(x);
    return Number.isFinite(n) ? Math.round(n * 100) / 100 : null;
  };

  // valores do plano selecionado (se existir nos vistos)
  const sel = (vistos && produto_code && vistos[produto_code]) ? vistos[produto_code] : null;
  const valor_avista = sel ? n2(sel.avista) : null;
  const valor_parcelado_total = sel ? n2(sel.parcelado_total) : null;
  const valor_parcela = sel ? n2(sel.parcela) : null;

  const body = {
    token,
    channel: 'archa',
    produto_code,
    forma_pagamento: 'cartao',

    // contato
    nome,
    email,
    telefone,
    codigo_pais,

    // snapshot do simulador (achatado)
    metragem,
    qtd_ambientes: ambientes,
    prazo_dias,
    categoria_arquiteto: categoriaAPI,   // ← alinhado ao fill()
    regiao_arquiteto: regiao,
    tipo_projeto: tipo_proj,
    adicionais: adicionaisStr,           // ← string JSON (validação OK)

    // documentos/endereço (opcionais)
    documento_tipo,
    documento_numero: documentoNumero,
    endereco_obra,
    cep,
    cidade,
    estado,
    pais,

    // analytics
    utms: utmArray,                      // ← array de strings
    origem_cliente: (typeof detectarOrigem === 'function') ? detectarOrigem() : null,

    // --- preços / valores ---
    precos_vistos: vistos || null,       // JSON com solo/duo/trio
    valor_avista,                        // números do plano escolhido
    valor_parcelado_total,
    valor_parcela
  };

  // ✅ Reuso de slug (se existir: URL > localStorage)
  const existingSlug = getExistingSlug();
  if (existingSlug) body.slug = existingSlug;

  // Chamada via proxy PHP (NÃO enviar X-API-TOKEN no front)
  const res = await fetch(CONFIG.API_CHECKOUT_START, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify(body)
  });

  const data = await res.json().catch(() => ({}));

  const slug = data?.checkout?.slug || data?.slug || existingSlug;
  if (slug) {
    localStorage.setItem('checkoutSlug', slug);
    // monta URL relativa ao diretório atual (sem hardcode)
    const url = new URL('./checkout.php', window.location.href);
    url.searchParams.set('projeto', slug);
    window.location.href = url.toString();
  }

  if (res.status === 422 && data?.errors) console.warn('faltantes:', data.errors);
}

// handler compartilhado btnIrPagamento e Avançar - Checkout 
async function handleCheckoutClick(btn) {
  if (btn.disabled) return;

  // bloqueio simples se usuário “burla” sem preencher o básico
  const nomeOk     = (document.getElementById('nome')?.value || '').trim();
  const emailOk    = (document.getElementById('email')?.value || '').trim();
  const telOk      = (document.getElementById('fone')?.value || '').trim();
  const metragem   = +document.getElementById('area')?.value || 0;
  const ambientes  = +document.getElementById('qtd_amb')?.value || 0;

  if (!nomeOk || !emailOk || !telOk || metragem <= 0 || ambientes <= 0) {
    location.reload(); // mesma política do enviarLead()
    return;
  }

  const old = btn.textContent;
  try {
    btn.disabled = true;
    btn.textContent = 'Iniciando…';
    await enviarCheckoutStart(); // verifique na tabela "checkouts"
  } catch (e) {
    console.error('[CHECKOUT START] erro:', e);
  } finally {
    btn.textContent = old;
    btn.disabled = false;
  }
}

// liga nos dois botões: #btnIrPagamento e #btnAvancar3
(function attachCheckoutHandlers() {
  ['btnIrPagamento', 'btnAvancar3'].forEach((id) => {
    const btn = document.getElementById(id);
    if (!btn || btn.dataset.boundCheckout === '1') return;
    btn.dataset.boundCheckout = '1';

    btn.addEventListener('click', () => handleCheckoutClick(btn));
  });
})();
