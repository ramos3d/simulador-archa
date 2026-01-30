<footer id="footer-mobile" class="bg-azul text-white mt-auto py-4">
    <div class="container text-center px-4">

        <!-- logo -->
        <img src="images/logo-branca.png" alt="Archa" style="height:25px" class="mb-5 mx-auto">
        <hr class="my-3 opacity-25 mx-auto custom-line mb-5">

        <!-- ícones sociais -->
        <div class="d-flex justify-content-center gap-4 mb-5">
            <a href="https://www.youtube.com/channel/UCObA-uzVeGRphE9L4Yv7-jw" target="_blank"><img src="images/youtube.png" alt="YouTube" height="22"></a>
            <a href="https://www.instagram.com/archa.company/" target="_blank"><img src="images/instagram.png" alt="Instagram" height="22"></a>
            <a href="https://www.linkedin.com/company/archacompany/posts/?feedView=all" target="_blank"><img src="images/linkedin.png" alt="LinkedIn" height="22"></a>
        </div>
        <small>&copy; 2025 Archa</small>
    </div>

    <!-- scripts comuns -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="js/site.js?v=<?= filemtime('js/site.js'); ?>"></script>
    <script src="js/inputs.js?v=<?= filemtime('js/inputs.js'); ?>"></script>
    <script src="js/config.js?v=<?= filemtime('js/config.js'); ?>"></script>
    <script src="js/calculator.js?v=<?= filemtime('js/calculator.js'); ?>"></script>
    <script src="js/getSeenPrices.js?v=<?= filemtime('js/getSeenPrices.js'); ?>"></script>

    <script src="js/simulador.js?v=<?= filemtime('js/simulador.js'); ?>"></script>
    <!-- footer (depois de simulador.js) -->
    <script src="js/lead-sync.js?v=<?= filemtime('js/lead-sync.js'); ?>"></script>

<!--    <script src="js/lead-cache.js?v=<?= filemtime('js/lead-cache.js'); ?>"></script>-->



    <script>
        /* =========================================================
         *  MOBILE – integra-se ao simulador.js (desktop)
         * ========================================================= */

        document.addEventListener('DOMContentLoaded', () => {

            /* ---------- helpers DOM ------------------------------ */
            const banner = document.getElementById('banner-preenchimento');
            const cardPreco = document.getElementById('card-preco');
            const btnPreco = document.getElementById('btnVerPreco');
            const precoMin = document.getElementById('precoMin'); // faixa verde
            const priceSolo = document.getElementById('priceSolo'); // span do Solo ↑

            if (!btnPreco) return; // segurança (mobile inexistente no desktop)

            /* ---------- formatação BRL --------------------------- */
            const fmtBRL = v => (+v).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });

            /* ---------- UUID / cache (mesma lógica desktop) ------ */
            //const CACHE_URL = 'http://127.0.0.1:8000/api/gerar-cache';
            const CACHE_URL = CONFIG.API_CACHE;

            async function gerarCache() {
                const r = await fetch(CACHE_URL, {
                    method: 'POST'
                });
                const j = await r.json();
                if (!j.uuid) throw Error('Falha ao gerar cache');
                localStorage.setItem('uuidLaravel', j.uuid);
                return j.uuid;
            }
            const getUuid = () =>
                localStorage.getItem('uuidLaravel') || gerarCache();

            /* ---------- 1º cálculo c/ retry ---------------------- */
            async function primeiroCalculoComRetry() {
                try {
                    return await primeiroCalculo(); // ← função global (simulador.js)
                } catch (e) {
                    if (!/cache/i.test(e.message)) throw e; // erro diferente
                    await gerarCache(); // renova 1×
                    return primeiroCalculo();
                }
            }

            /* ---------- mantém faixa “A partir de:” -------------- */
            const syncPrecoMin = () => {
                if (priceSolo && precoMin) {
                    const txt = priceSolo.textContent.trim();
                    if (txt) precoMin.textContent = txt; // sempre cópia do Solo
                }
            };
            /* observa alterações futuras (recalcs) */
            if (priceSolo) {
                new MutationObserver(syncPrecoMin)
                    .observe(priceSolo, {
                        childList: true,
                        characterData: true,
                        subtree: true
                    });
            }

            /* ---------- clique em “Ver preço” -------------------- */
            btnPreco.addEventListener('click', async () => {

                /* (A) UI */
                banner?.classList.add('d-none');
                cardPreco?.classList.remove('d-none');
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

                /* (B) backend */
                try {
                    await getUuid(); // cria se não houver
                    await primeiroCalculoComRetry(); // popula Solo/Duo/Trio
                } catch (err) {
                    return alert(err.message || err);
                }
                /* (C) atualiza faixa verde (solo) ----------------- */
                syncPrecoMin(); // força 1ª sincronização
            });
        });


        // Para o selecotr dos planos
        document.querySelectorAll('#planAccordion .accordion-button')
            .forEach(btn => {
                btn.addEventListener('click', () => {
                    // limpa seleção anterior
                    document.querySelectorAll('#planAccordion .plan')
                        .forEach(p => p.classList.remove('selected'));
                    // aplica ao plano clicado
                    btn.closest('.plan').classList.add('selected');
                });
            });
    </script>
    <script>
        /* Efeito de escurecer tela ao expandir preços */
        document.addEventListener('DOMContentLoaded', () => {
            const card = document.getElementById('card-preco');
            const acc = document.getElementById('orcAccordion');
            const backdrop = document.getElementById('mobile-backdrop');

            if (!card || !acc || !backdrop) return;

            // instancia/recupera o Collapse (sem toggle automático)
            const bsColl = bootstrap.Collapse.getOrCreateInstance(acc, {
                toggle: false
            });

            const setOpen = (open) => {
                card.classList.toggle('is-open', open);
                backdrop.classList.toggle('show', open);
                document.body.classList.toggle('backdrop-open', open);
            };

            acc.addEventListener('shown.bs.collapse', () => setOpen(true));
            acc.addEventListener('hidden.bs.collapse', () => setOpen(false));

            // clicar fora fecha (comportamento de modal)
            backdrop.addEventListener('click', () => bsColl.hide());

            // estado inicial
            setOpen(acc.classList.contains('show'));
        });
    </script>

    <script src="js/editar-simulacao.js?v=<?= filemtime('js/editar-simulacao.js'); ?>"></script>
    <!--<script src="js/checkout-cache.js?v=<?= filemtime('js/checkout-cache.js'); ?>"></script>-->
    <script src="js/checkout-functions.js?v=<?= filemtime('js/checkout-functions.js'); ?>"></script>
    <script src="js/checkout-start.js?v=<?= filemtime('js/checkout-start.js'); ?>"></script>
</footer>