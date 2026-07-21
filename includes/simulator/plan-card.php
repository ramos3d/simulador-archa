<?php /* Card de preços / accordion de planos — extraído de archa-form/etapas/etapa0.php */ ?>
<div id="aside-stick">
  <div id="card-preco" class="card shadow-sm p-4">
    <?php if (defined('PROMO_CLIENTE') && PROMO_CLIENTE): ?>
      <div class="promo-header-blackfriday f-exo">
        <span class="promo-text"><b><?= t('Black Friday Archa') ?> </b><strong class="text-rosa">15% OFF</strong></span>
      </div>
    <?php endif; ?>

    <h5 class="fw-bold mb-1 text-start f-exo card-m9"><?= t('Orçamento do seu projeto de arquitetura') ?></h5>
    <p id="orcamento-resumo" class="card-m9 small text-secondary text-start itens-margem-b f-anek f-14"><?= t('Nenhuma informação preenchida') ?></p>
    <div id="vant-card" class="vant-card text-start f-exo lista-desktop-margem" style="margin-bottom:-15px;">
      <ul id="vantagensExtras" class="vant-list benef-list mb-2 d-none"></ul>
      <ul id="vantItensMobile" class="vant-list benef-list mb-2"></ul>
    </div>

    <hr class="my-3 card-m9">
    <p class="text-start f-14 f-anek card-m9" style="margin-bottom:-1px;"><?= t('Opções de contratação') ?></p>

    <div class="accordion accordion-flush" id="planAccordion">

      <!-- SOLO -->
      <div class="accordion-item plan plan-solo" data-produto="solo">
        <h2 class="accordion-header">
          <button type="button" class="accordion-button collapsed plan-header-btn"
            data-bs-toggle="collapse" data-bs-target="#planSolo"
            aria-expanded="false" aria-controls="planSolo">
            <div class="plan-header w-100 d-flex align-items-center">
              <div class="plan-main flex-grow-1">
                <div class="plan-title text-uppercase f-anek">Solo</div>
                <div class="d-flex align-items-center">
                  <span class="plan-radio me-2"><span class="radio-indicator"></span></span>
                  <div class="plan-price-block">
                    <div class="install-line">
                      <span class="install-prefix"><?= t('10x de') ?></span>
                      <span id="priceSolo" class="plan-price">R$ 0,00</span>
                      <span class="badge-off ms-auto"><?= t('15% OFF à vista') ?></span>
                    </div>
                    <div class="cash-line">
                      <span class="cash-prefix">+</span> <strong id="priceSoloAvista" class="cash">R$ 0,00</strong> <span class="cash-suffix"><?= t('após contratação') ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </button>
        </h2>
        <div id="planSolo" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
          <div class="accordion-body py-2">
            <ul class="benef-list mb-0 text-start mb-2">
              <li><strong><?= t('1 projeto completo:') ?></strong> <?= t('Moodboard e Referências; Imagens 3D, Planta de Layout; Planta de Obra Civil; Memorial Descritivo') ?></li>
              <li><strong>2</strong> <?= t('revisões') ?></li>
              <li><strong><?= t('Assistente pessoal') ?></strong> <?= t('do início ao fim') ?></li>
              <li><strong><?= t('Desconto') ?></strong> <?= t('com marcas parceiras') ?></li>
            </ul>
          </div>
        </div>
      </div>

      <!-- DUO -->
      <div class="accordion-item plan plan-duo selected" data-produto="duo">
        <h2 class="accordion-header">
          <button type="button" class="accordion-button collapsed plan-header-btn"
            data-bs-toggle="collapse" data-bs-target="#planDuo"
            aria-expanded="false" aria-controls="planDuo">
            <div class="plan-header w-100 d-flex align-items-center">
              <div class="plan-main flex-grow-1">
                <div class="d-flex align-items-center mb-1">
                  <div class="plan-title text-uppercase f-anek mb-0">Duo</div>
                  <span class="badge-recomendado ms-2"><?= t('Recomendado') ?></span>
                </div>
                <div class="d-flex align-items-center">
                  <span class="plan-radio me-2"><span class="radio-indicator"></span></span>
                  <div class="plan-price-block">
                    <div class="install-line">
                      <span class="install-prefix"><?= t('10x de') ?></span>
                      <span id="priceDuo" class="plan-price">R$ 0,00</span>
                      <span class="badge-off ms-auto"><?= t('15% OFF à vista') ?></span>
                    </div>
                    <div class="cash-line">
                      <span class="cash-prefix">+</span> <strong id="priceDuoAvista" class="cash">R$ 0,00</strong> <span class="cash-suffix"><?= t('após contratação') ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </button>
        </h2>
        <div id="planDuo" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
          <div class="accordion-body py-2">
            <ul class="benef-list mb-0 text-start mb-2">
              <li><strong><?= t('Tudo do Solo +') ?></strong></li>
              <li><strong>2</strong> <?= t('projetos completos') ?></li>
              <li><strong><?= t('Vídeos explicativos') ?></strong> <?= t('dos projetos') ?></li>
              <li>
                <strong><?= t('Desconto de 10%') ?></strong> <?= t('na contratação de') ?>
                <a href="<?= BASE_URL ?>/arquivos/tabela_de_serviços_adicionais.pdf" target="_blank" rel="noopener noreferrer"><?= t('serviços adicionais') ?></a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- TRIO -->
      <div class="accordion-item plan plan-trio" data-produto="trio">
        <h2 class="accordion-header">
          <button type="button" class="accordion-button collapsed plan-header-btn"
            data-bs-toggle="collapse" data-bs-target="#planTrio"
            aria-expanded="false" aria-controls="planTrio">
            <div class="plan-header w-100 d-flex align-items-center">
              <div class="plan-main flex-grow-1">
                <div class="plan-title text-uppercase f-anek">Trio</div>
                <div class="d-flex align-items-center">
                  <span class="plan-radio me-2"><span class="radio-indicator"></span></span>
                  <div class="plan-price-block">
                    <div class="install-line">
                      <span class="install-prefix"><?= t('10x de') ?></span>
                      <span id="priceTrio" class="plan-price">R$ 0,00</span>
                      <span class="badge-off ms-auto"><?= t('15% OFF à vista') ?></span>
                    </div>
                    <div class="cash-line">
                      <span class="cash-prefix">+</span> <strong id="priceTrioAvista" class="cash">R$ 0,00</strong> <span class="cash-suffix"><?= t('após contratação') ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </button>
        </h2>
        <div id="planTrio" class="accordion-collapse collapse" data-bs-parent="#planAccordion">
          <div class="accordion-body py-2">
            <ul class="benef-list mb-0 text-start mb-2">
              <li><strong><?= t('Tudo do Duo +') ?></strong></li>
              <li><strong>3</strong> <?= t('propostas de projetos') ?></li>
              <li><?= t('Planilha de <strong>orçamento</strong> de produtos') ?></li>
              <li><strong>+1</strong> <?= t('reunião de revisão') ?></li>
              <li>
                <strong><?= t('Desconto de 20%') ?></strong> <?= t('na contratação de') ?>
                <a href="<?= BASE_URL ?>/arquivos/tabela_de_serviços_adicionais.pdf" target="_blank" rel="noopener noreferrer"><?= t('serviços adicionais') ?></a>
              </li>
            </ul>
          </div>
        </div>
      </div>

    </div><!-- /accordion -->

    <button type="button" id="btnIrPagamento"
      class="text-secondary btn-outline-grey mt-4 w-100 btnIrParaPagamento btnCustomizado f-exo"
      style="margin-top:-1px; padding:3%; width:105%; margin-left:-9px;">
      <?= t('Avançar') ?>
    </button>

    <!-- Garantia: devolução do dinheiro -->
    <div class="guarantee-box f-exo mt-3">
      <div class="d-flex align-items-start gap-2">
        <span style="font-size:1.3rem;line-height:1">🛡️</span>
        <div>
          <strong style="display:block;color:#262942;font-size:.85rem"><?= t('Garantia de devolução') ?></strong>
          <span style="font-size:.82rem;color:#555;line-height:1.4">
            <?= t('Se a experiência não atender suas expectativas, <strong>devolvemos seu dinheiro</strong>.') ?>
          </span>
        </div>
      </div>
    </div>

  </div><!-- /card-preco -->

  <div id="vant-anchor" aria-hidden="true"></div>

  <div class="card-preco">
    <div id="vant-card-bottom" class="vant-card text-start mt-4 lista-beneficios-customizada">
      <ul class="vant-list mb-2 js-vantagens-list space-line"></ul>
    </div>
  </div>
</div><!-- /aside-stick -->

<style>
  /* Garantia de devolução */
  .guarantee-box{
    background:#fdf6e8;
    border:1px solid #f5d68b;
    border-radius:10px;
    padding:.7rem .8rem;
  }

  /* === Lista de vantagens (resumo) === */
  .lista-desktop-margem { margin-left: -6%; }
  @media (min-width: 992px) {
    .lista-desktop-margem .vant-list li::before { content: none !important; background: none !important; }
    .lista-desktop-margem .vant-list li { padding-left: 0 !important; justify-content: space-between; align-items: baseline; }
    .lista-desktop-margem .vant-list li .text-part { margin-left: 0 !important; }
    .vant-card .js-vantagens-list li { display: block !important; justify-content: initial !important; gap: 0 !important; padding-left: 22px; position: relative; }
  }

  /* === Override: faz o badge "15% OFF" fluir no flex (não absoluto) === */
  /* Garante que prices e badge não se sobrepõem, mesmo em colunas estreitas */
  #card-preco .plan-header {
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
  }
  #card-preco .plan-main { min-width: 0; }
  #card-preco .plan-price-block { min-width: 0; width: 100%; }
  #card-preco .install-line { display: flex; align-items: center; gap: 6px; flex-wrap: nowrap; }
  #card-preco .plan-price { font-size: 1.1rem; }

  /* Badge "15% OFF à vista" no final da linha do preço 10x */
  #card-preco .install-line .badge-off {
    position: static !important;
    top: auto !important;
    right: auto !important;
    margin-left: auto;
    flex-shrink: 0;
    font-size: .65rem;
    padding: .15rem .4rem;
    white-space: nowrap;
  }

  /* Em telas grandes (xl+), o badge pode voltar pra direita com mais espaço */
  @media (min-width: 1400px) {
    #card-preco .plan-price { font-size: 1.25rem; }
    #card-preco .badge-off { font-size: .7rem; padding: .25rem .55rem; }
  }

  /* Card de preço: garante largura mínima confortável */
  #card-preco { min-width: 100%; }
  @media (min-width: 1200px) {
    #card-preco { min-width: 380px; }
  }

  /* Linha "após contratação" — discreta como o "ou à vista" original */
  #card-preco .cash-line { font-size: .82rem; color: #555; }
  #card-preco .cash-line .cash-prefix { font-weight: 600; color: #262942; }
  #card-preco .cash-line .cash { color: #262942; }
  #card-preco .cash-line .cash-suffix { color: #666; }
</style>

<script>
/* === Reescreve preços do card lateral usando a estrategia 25%/75% (igual checkout) ===
   Legado escreve em #priceX o valor de parcela = total/10, e em #priceXAvista o valor à vista.
   Nós sobrescrevemos:
     - #priceX        = (parcela legado * 0.25)  → 10x de 25% da entrada
     - #priceXAvista  = (parcela legado * 7.5)   → 75% restante após contratação
*/
(function () {
  const PLANS = ['Solo','Duo','Trio'];
  const patched = { Solo:{p:'',a:''}, Duo:{p:'',a:''}, Trio:{p:'',a:''} };
  const origin  = { Solo:{p:0},      Duo:{p:0},      Trio:{p:0}      };
  let scheduled = false;

  function parseMoney(txt) {
    const m = String(txt || '').replace(/[^\d,]/g,'').replace(',','.');
    const n = parseFloat(m);
    return isFinite(n) ? n : 0;
  }
  function fmtMoney(v) {
    return 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits:2, maximumFractionDigits:2 });
  }

  function recompute() {
    scheduled = false;
    PLANS.forEach(name => {
      const elP = document.getElementById('price' + name);
      const elA = document.getElementById('price' + name + 'Avista');
      if (!elP || !elA) return;

      const txtP = elP.textContent;
      if (txtP !== patched[name].p) {
        const parsed = parseMoney(txtP);
        if (parsed > 0) origin[name].p = parsed; // captura parcela legado (total/10)
      }

      const parcela = origin[name].p;
      if (parcela <= 0) return;

      const newP = fmtMoney(parcela * 0.25);
      const newA = fmtMoney(parcela * 7.5);

      if (elP.textContent !== newP) { patched[name].p = newP; elP.textContent = newP; }
      if (elA.textContent !== newA) { patched[name].a = newA; elA.textContent = newA; }
    });
  }

  function schedule() {
    if (scheduled) return;
    scheduled = true;
    requestAnimationFrame(recompute);
  }

  function start() {
    const root = document.getElementById('planAccordion');
    if (!root) return;
    new MutationObserver(schedule).observe(root, { childList:true, subtree:true, characterData:true });
    schedule();
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
  else start();
})();
</script>
