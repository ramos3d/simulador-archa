<?php

/** includes/checkout/_review.php
 * Bloco: Revisão do projeto (resumo das escolhas do usuário)
 * Obs.: Não contém cupom nem totais; esses ficam nos partials _cupom.php e _totais.php.
 */
?>
<div class="ck-direita">
    <!-- SENTINELA (opcional, JS cria se não existir) -->
    <div id="reviewSentinel" class="d-lg-none"></div>

    <div id="reviewHeader" class="ck-head d-flex justify-content-between align-items-center">
        <h6 class="resume-title mb-0 f-anek">Revisão do projeto</h6>
        <div class="d-flex align-items-center gap-2">
            <button id="btnRevisarProjeto" class="btn btn-md btn-refazer-pedido f-exo">
                <img src="images/pencil-green.png" class="me-2" alt="refazer pedido">Editar projeto
            </button>

        </div>
    </div>

    <div class="ck-body">
        <div class="d-flex align-items-center gap-3 mb-5">
            <div class="ck-resume-icon d-flex align-items-center justify-content-center">
                <img id="sumTipoIcon"
                    src="images/icon-opcoes-contratacao.png"
                    alt="Tipo do projeto">
            </div>
            <div class="me-1">
                <div class="ck-muted small">Opção de contratação:</div>
                <div id="sumPlano">Archa Duo</div>
            </div>
        </div>

        <ul class="list-unstyled resume-list mb-1 mb-lg-5">
            <li><small>Escritórios de <strong><span id="sumRegiao">todo Brasil</span></strong></small> </li>
            <li><small>Construção de <strong><span id="sumNovaArea">novo ambiente</span></strong></small> </li>
            <li><small>Receber em <strong><span id="sumPrazo">14</span> dias</strong></small> </li>
            <li><small>Arquitetos <strong><span id="sumCategoria">--</span></strong></small> </li>
            <li><small>Área do projeto de <strong><span id="sumM2">30 m²</span></strong></small> </li>
            <li><small><strong><span id="sumAmb">6</span></strong> <span id=qtd_label></span></small> </li>
            <li><small><strong><span id="sumTipo">Apartamento</span></strong></small> </li>
            <li><small><strong><span id="sumAdic">X</span> itens adicionais</strong> inseridos</small> </li>
        </ul>

    </div>
    <hr class="mobile-only __review-mobile-section">
</div>


<script>
    (function() {
        const btn = document.getElementById('btnRevisarProjeto');
        if (!btn) return;

        // Lê o slug do projeto a partir da URL, do estado global ou do localStorage
        function getSlug() {
            const qs = new URLSearchParams(location.search);
            const a = (window.CHECKOUT_SLUG || '').trim();
            const b = (qs.get('projeto') || '').trim();
            const c = (localStorage.getItem('checkoutSlug') || '').trim();
            return a || b || c || '';
        }

        // Monta a URL de edição usando a raiz atual do checkout:

        function buildEditUrl(slug) {
            const u = new URL(location.href);
            const basePath = u.pathname.replace(/checkout\.php$/i, ''); // remove checkout.php
            const params = new URLSearchParams({
                projeto: slug,
                mode: 'edit'
            });
            return `${basePath}?${params.toString()}`;
        }

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const slug = getSlug();
            if (!slug) {
                alert('Projeto não identificado. Recarregue a página e tente novamente.');
                return;
            }
            // guarda o slug (ajuda a tela de edição a se auto-hidratar, se precisar)
            localStorage.setItem('checkoutSlug', slug);
            // navega
            location.href = buildEditUrl(slug);
        });
    })();
</script>