/* =========================================================
   SITE.JS — UI estática (sem recálculo)
   ========================================================= */

document.addEventListener('DOMContentLoaded', () => {
  /* utils */
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];

  /* ========== PASSO 1 — abas + cards (exclusivo global) ========== */
  const btnsTipo = $$('.btn-tipo');
  const groups = {
    residencial: $('#cards-residencial'),
    comercial: $('#cards-comercial'),
    corporativo: $('#cards-corporativo'),
  };
  const wrapper = $('#propertyWrapper');
  const hTipo = $('#tipo_projeto');

  const setImgDefault = (img) => { if (img) img.src = img.src.replace('/Active/', '/Default/'); };
  const setImgActive = (img) => { if (img) img.src = img.src.replace('/Default/', '/Active/'); };


  function clearAllCards() {
    $$('.tipo-imovel', wrapper).forEach(c => {
      c.classList.remove('selected');
      setImgDefault(c.querySelector('img'));
    });
  }

  function selectCard(card) {
    if (!card) return;
    clearAllCards();                     // exclusividade global
    card.classList.add('selected');
    setImgActive(card.querySelector('img'));

    // escreve hidden e deixa o simulador reagir via listeners dele
    if (hTipo) {
      hTipo.value = card.dataset.value || '';
      hTipo.dispatchEvent(new Event('input', { bubbles: true }));
      hTipo.dispatchEvent(new Event('change', { bubbles: true }));
    }
    toggleVerPreco();
  }

  function showGroup(cat) {
    btnsTipo.forEach(b => b.classList.toggle('active', b.dataset.cat === cat));
    Object.entries(groups).forEach(([k, el]) => el && el.classList.toggle('d-none', k !== cat));
    clearAllCards();
    const first = $('.tipo-imovel', groups[cat]);
    if (first) selectCard(first);
  }

  btnsTipo.forEach(b => b.addEventListener('click', () => showGroup(b.dataset.cat)));
  wrapper?.addEventListener('click', (e) => {
    const card = e.target.closest('.tipo-imovel'); if (!card) return;
    selectCard(card);
  });

  /* mínimos + validação “Ver preço” (somente UI) */
  const amb = $('#qtd_amb');
  const area = $('#area');
  const nome = $('#nome');
  const email = $('#email');
  const fone = $('#fone');
  const btnVerPreco = $('#btnVerPreco');

  const clampMin = (el, min) => {
    if (!el) return;
    const n = Number(el.value || 0);
    if (!Number.isFinite(n) || n < min) el.value = String(min);
  };
  const nomeValido = (v) => v && v.trim().replace(/\s+/g, ' ').split(' ')
    .filter(x => /\p{L}{2,}/u.test(x)).length >= 2;

  function formValido() {
    return (hTipo?.value || '') &&
      Number(amb?.value ?? 0) >= 1 &&
      Number(area?.value ?? 0) >= 1 &&
      nomeValido(nome?.value || '') &&
      (email?.value || '').trim().length > 5 &&
      (fone?.value || '').trim().length > 5;
  }

  function toggleVerPreco() {
    if (!btnVerPreco) return;
    if (btnVerPreco.dataset.locked === '1') {
      btnVerPreco.disabled = true;
      btnVerPreco.classList.add('btn-disabled');
      return;
    }
    const ok = formValido();
    btnVerPreco.disabled = !ok;
    btnVerPreco.classList.toggle('btn-disabled', !ok);
    btnVerPreco.classList.toggle('btn-azul', ok);
  }

  function validateMinOnly(el, min) {
    const raw = (el?.value || '').trim();
    const num = Number(raw.replace(',', '.'));
    if (raw === '' || !Number.isFinite(num) || num < min) {
      el.classList.add('is-invalid');
    } else {
      el.classList.remove('is-invalid');
    }
  }


  amb?.addEventListener('blur', () => { validateMinOnly(amb, 1); toggleVerPreco(); });
  area?.addEventListener('blur', () => { validateMinOnly(area, 1); toggleVerPreco(); });

  amb?.addEventListener('input', toggleVerPreco);
  area?.addEventListener('input', toggleVerPreco);
  nome?.addEventListener('input', toggleVerPreco);
  email?.addEventListener('input', toggleVerPreco);
  fone?.addEventListener('input', toggleVerPreco);

  // estado inicial
  showGroup('residencial');
  toggleVerPreco();

  /* ========== PASSO 2 — rádios (categoria/região) ========== */
  const hCat = $('#categoria');
  const hReg = $('#regiao_key');

  function radioSelect(allBoxes, clicked, hiddenInput, dataAttr, imgFolder = 'images/icons/questao_2') {
    allBoxes.forEach(box => {
      const key = box.dataset[dataAttr];
      const img = box.querySelector('img');
      const isSel = box === clicked;
      box.classList.toggle('selected', isSel);
      if (img && !img.hasAttribute('data-noswap')) {
        img.src = `${imgFolder}/${isSel ? 'Active' : 'Default'}/${key.toLowerCase()}.png`;
      }
      if (isSel && hiddenInput) {
        hiddenInput.value = key;
        // só dispara eventos; quem recalcula é o simulador
        hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
        hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });
  }

  const catBoxes = $$('#proCatWrap .option-box');
  catBoxes.forEach(b => b.addEventListener('click', () => {
    radioSelect(catBoxes, b, hCat, 'cat');
  }));

  const regBoxes = $$('#regWrap .option-box');
  regBoxes.forEach(b => b.addEventListener('click', () => {
    regBoxes.forEach(x => x.classList.remove('selected'));
    b.classList.add('selected');
    if (hReg) {
      hReg.value = b.dataset.reg;
      hReg.dispatchEvent(new Event('input', { bubbles: true }));
      hReg.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }));

  /* ========== PASSO 3 — REMOVIDO AQUI ==========
     (multi-option, nova área, prazo → ficam no simulador.js)
  */

  /* ========== VANTAGENS (accordion dos planos) ========== */
  let vantCache = null;
  async function getVantagens() {
    if (vantCache) return vantCache;
    try {
      const r = await fetch('json/vantagens.json', { cache: 'no-store' });
      vantCache = await r.json();
    } catch (err) {
      console.warn('[vantagens] falhou:', err);
      vantCache = {};
    }
    return vantCache;
  }
  /*async function renderVants(planKey = 'duo') {
    const v = await getVantagens();
    const list = v[planKey] || v[planKey.toUpperCase()] || [];
    const ul = $('#vantagensExtras'); if (!ul) return;
    ul.innerHTML = list.map(t => `<li>${t}</li>`).join('');
  }*/

  async function renderVants(planKey = 'duo') {
    const v = await getVantagens();
    const list = v[planKey] || v[planKey.toUpperCase()] || [];

    const html = list.map(t => `<li>${normalizeAllCaps(t)}</li>`).join('');


    // Preenche todas as listas (topo + a de fluxo normal)
    document.querySelectorAll('.js-vantagens-list').forEach((ul) => {
      ul.innerHTML = html;
      // se vier vazio, mantém escondido; se vier conteúdo, mostra
      if (ul.id === 'vantagensExtras') {
        ul.classList.toggle('d-none', list.length === 0);
      }
    });
  }


  const planAcc = $('#planAccordion');
  if (planAcc) {
    planAcc.addEventListener('shown.bs.collapse', (e) => {
      const plan = e.target.closest('.plan');
      if (!plan) return;
      let key = 'duo';
      if (plan.classList.contains('plan-solo')) key = 'solo';
      else if (plan.classList.contains('plan-trio')) key = 'trio';
      renderVants(key);
    });
    const opened = $('#planAccordion .accordion-collapse.show');
    let key = 'duo';
    if (opened?.closest('.plan')?.classList.contains('plan-solo')) key = 'solo';
    else if (opened?.closest('.plan')?.classList.contains('plan-trio')) key = 'trio';
    renderVants(key);
  }

  /* reset “R$” inicial (placeholder visual) */
  ['#priceSolo', '#priceDuo', '#priceTrio'].forEach(sel => {
    const el = $(sel);
    if (el && (!el.textContent || el.textContent.trim() === '')) el.textContent = 'R$';
  });

  // Força o primeiro plano aberto a ser marcado como selected
  const openItem = document.querySelector('#planAccordion .accordion-collapse.show');
  if (openItem) {
    const plan = openItem.closest('.plan');
    if (plan) plan.classList.add('selected');
  }
});


// em site.js (fora do DOMContentLoaded), SUBSTITUA a função por esta:
window.updateVantCard = function ({
  tipo, ambientes, metragem, categoria, regiao, prazo, novaArea, adicionalCount = 0
}) {
  // 1) Esconde o placeholder do card (“Nenhuma informação preenchida”)
  const resumo = document.getElementById('orcamento-resumo');
  if (resumo) resumo.classList.add('visually-hidden');

  // 2) Define singular/plural
  const itensLabel = (adicionalCount === 1)
    ? 'item adicional inserido'
    : 'itens adicionais inseridos';

  // 3) HTML (texto à esquerda + ícone lápis à direita)
  const html = `
    <li><span class="text-part">${normalizeAllCaps(tipo || '-')}</span><a class="edit" href="#accordionStep1"><img src="images/pencil.png" alt="editar"></a></li>
    <li><span class="text-part"><strong>${ambientes}</strong> ${ambientes === 1 ? 'ambiente' : 'ambientes'} a serem projetados</span><a class="edit" href="#accordionStep1"><img src="images/pencil.png" alt="editar"></a></li>
    <li><span class="text-part">Área do projeto de <strong>${metragem} m²</strong></span><a class="edit" href="#accordionStep1"><img src="images/pencil.png" alt="editar"></a></li>
    <li><span class="text-part">Arquitetos <strong>${normalizeAllCaps(String(categoria || ''))}</strong></span><a class="edit" href="#accordionStep2"><img src="images/pencil.png" alt="editar"></a></li>
    <li><span class="text-part">Escritórios de <strong>${regiao}</strong></span><a class="edit" href="#accordionStep2"><img src="images/pencil.png" alt="editar"></a></li>
    <li><span class="text-part">Receber em <strong>${prazo} dias</strong></span><a class="edit" href="#accordionStep3"><img src="images/pencil.png" alt="editar"></a></li>
    <li><span class="text-part">Construção de nova área <strong>${normalizeAllCaps(novaArea)}</strong></span><a class="edit" href="#accordionStep3"><img src="images/pencil.png" alt="editar"></a></li>
    <li><span class="text-part"><strong>${adicionalCount}</strong> ${itensLabel}</span><a class="edit" href="#accordionStep3"><img src="images/pencil.png" alt="editar"></a></li>
  `;

  // 4) Atualiza TODAS as listas (mobile + desktop)
   document.querySelectorAll('#vantItensMobile, .js-vant-itens').forEach(ul => {
    ul.innerHTML = html;
  });
};


window.normalizeAllCaps = function (str) {
  if (!str) return str;
  return str.replace(/\p{L}+/gu, w => {
    if (/^[\p{Lu}]{4,}$/u.test(w)) {
      const lower = w.toLowerCase();
      return lower[0].toUpperCase() + lower.slice(1);
    }
    return w;
  });
};

