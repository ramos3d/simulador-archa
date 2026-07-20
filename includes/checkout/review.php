<?php /** Revisão compacta */ ?>
<div class="ck-card mb-3">
  <div class="ck-head py-2 px-3 d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center gap-2">
      <div class="ck-resume-icon ck-resume-icon-sm">
        <img src="<?= ASSETS_BASE ?>/images/icon-opcoes-contratacao.png" alt="">
      </div>
      <div>
        <div class="ck-muted" style="font-size:.7rem;line-height:1">Opção de contratação</div>
        <div id="sumPlano" class="fw-bold f-anek" style="font-size:1.05rem;line-height:1.2">Archa Duo</div>
      </div>
    </div>
    <button id="btnEditarProjeto" type="button" class="btn btn-sm btn-link p-1 text-decoration-none" title="Editar projeto">
      <img src="<?= ASSETS_BASE ?>/images/pencil-green.png" alt="Editar" width="16">
    </button>
  </div>
  <div class="ck-body py-2 px-3">
    <div class="review-grid small">
      <div><strong id="sumM2">30 m²</strong> · <strong><span id="sumAmb">3</span></strong> ambientes · <strong id="sumTipo">Apartamento</strong></div>
      <div>Profissionais <strong id="sumCategoria">Estreantes</strong> de <strong id="sumRegiao">todo Brasil</strong></div>
      <div>Entrega em <strong><span id="sumPrazo">21</span> dias</strong> · <strong><span id="sumAdic">0</span></strong> itens adicionais</div>
    </div>
  </div>
</div>

<script>
(function () {
  const btn = document.getElementById('btnEditarProjeto');
  if (!btn) return;
  btn.addEventListener('click', () => {
    const qs   = new URLSearchParams(location.search);
    const slug = (window.CHECKOUT_SLUG || qs.get('projeto') || localStorage.getItem('checkoutSlug') || '').trim();
    if (!slug) { alert('Projeto não identificado. Recarregue e tente novamente.'); return; }
    localStorage.setItem('checkoutSlug', slug);
    const base = location.pathname.replace(/checkout\.php$/i, '');
    location.href = `${base}?projeto=${slug}&mode=edit`;
  });
})();
</script>
