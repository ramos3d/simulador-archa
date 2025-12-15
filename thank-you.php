<?php
include 'config.php';
include 'includes/header-thank-you.php';


// quando o back estiver pronto você pode preencher isso via PHP
$contractUrl = isset($contractUrl) ? $contractUrl : '#'; // link do contrato no Clicksign
?>
<style>
    /* Garantir que nada estoure a largura */
    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    html,
    body {
        margin: 0;
        padding: 0;
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
        /* mata qualquer scroll horizontal */
    }

    .logo-mobile {
        max-width: 75px;
    }

    .w-700 {
        font-weight: 700 !important;
    }

    .w-800 {
        font-weight: 800 !important;
    }

    .f-18 {
        font-size: 18px;
    }

    .f-42 {
        font-size: 42px;
    }
.bg-azul{
    background-color: #262942;
}
    .blue {
        color: #262942 !important;
    }

    /* Estrutura geral da página */
    .checkout-wrap {
        padding: 2rem 0 4rem;
    }



    /* Bloco central da thank-you */
    .thank-hero {
        text-align: center;
    }

    /* No desktop, limita a largura e centraliza o bloco */
    @media (min-width: 992px) {
        .thank-hero {
            max-width: 960px;
            margin-left: auto;
            margin-right: auto;
        }

        .ilustracao {
            max-width: 372px;
        }
    }

    /* Botão azul (contrato) */
    .btn-blue {
        background-color: #262942;
        color: #D4FFAD;
    }

    .btn-blue:hover {
        background-color: #1E2545;
        color: #c2ea9e;
    }

    /* Botão WhatsApp com hover cinza claro */
    .btn-whats {
        border-color: #262942;
        color: #262942;
        background-color: #fff;
    }

    .btn-whats:hover,
    .btn-whats:focus {
        background-color: #f2f2f2;
        /* cinza claro */
        color: #262942;
    }

    /* Largura / comportamento dos botões */
    .btn-thank {
        width: 100%;
        /* full-width no mobile */
    }

    @media (min-width: 576px) {
        .btn-thank {
            width: auto;
            min-width: 220px;
            /* parecido com o layout do Figma */
        }

        .checkout-wrap {
            padding: 3rem 0 5rem;
        }

        .ilustracao {
            max-width: 520px;
        }
    }

    .obrigado {
        font-weight: 800;
        font-size: 55px !important;
    }
</style>





<main class="flex-grow-1 checkout-wrap f-anek">
    <div class="container py-5 py-md-6 thank-hero">

        <!-- Imagem em cima -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <img
                    src="images/arte-thankyou.png"
                    alt="Seu projeto com a Archa"
                    class="img-fluid ilustracao">
            </div>
        </div>

        <!-- Texto -->
        <div class="row justify-content-center">
            <div class="col-12">

                <h1 class="mb-3 obrigado blue">
                    Obrigado,
                    <span id="tkNome" style="color:#E893C7;">[Nome do Cliente]</span>!
                </h1>

                <h2 class=" mb-4 f-anek w-800 bluef-42">
                    Seu projeto com a Archa está pronto para começar.
                </h2>

                <p class="lead f-exo mb-2 f-18 ">
                    O <span id="tkPlano" class="w-700 blue">Plano Archa</span>
                    <span id="tkTipo" class="w-700 blue">Apartamento</span> já começou!
                    Serão <span id="tkAmb" class="w-700 blue">X ambientes</span>
                    em <span id="tkM2" class="w-700 blue">XX m²</span>, feitos para refletir o seu estilo.
                </p>

                <p class="f-exo mb-4 f-18">
                    Seu contrato está disponível e nosso time entrará em contato em breve.
                    Confira no e-mail todos os detalhes e próximos passos.
                </p>

                <!-- Botões -->
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3 mt-5">

                    <!-- Contrato: primeiro no mobile, segundo no desktop -->
                    <!--<a
                        href="<?= htmlspecialchars($contractUrl, ENT_QUOTES) ?>"
                        class="btn btn-lg btn-blue f-exo btn-thank order-1 order-sm-2 d-flex justify-content-center">
                        Acessar meu contrato
                    </a>-->

                    <!-- WhatsApp: segundo no mobile, primeiro no desktop -->
                    <a
                        href="https://api.whatsapp.com/send/?phone=5511942892984&text=Para+iniciar+seu+atendimento%2C+envie+uma+mensagem+como+esta%3A+Ol%C3%A1%21+Gostaria+de+contratar+um+projeto+de+arquitetura+com+a+Archa.+%EF%BF%BD&type=phone_number&app_absent=0"
                        class="btn btn-lg btn-whats f-exo btn-thank order-2 order-sm-1 d-inline-flex align-items-center justify-content-center"
                        target="_blank" rel="noopener">
                        <img src="images/icon-whatsapp.png" alt="WhatsApp"
                            class="me-2" style="width:18px; height:auto;">
                        Falar com a Archa
                    </a>
                </div>

            </div>
        </div>
    </div>
</main>

<script>
    (function() {
        // defaults para testes / quando não houver nada salvo
        const defaults = {
            nome: 'Cliente Archa',
            ambientes: 'X ambientes',
            tipo: 'Apartamento',
            metragem: 'XX m²',
            plano: 'Plano Archa'
        };

        let data = {};
        const raw = localStorage.getItem('archa_thankyou');
        if (raw) {
            try {
                data = JSON.parse(raw) || {};
            } catch (e) {
                data = {};
            }
        }

        const nome = (data.nome || '').trim() || defaults.nome;
        const amb = (data.ambientes || '').trim() || defaults.ambientes;
        const tipo = (data.tipo || '').trim() || defaults.tipo;
        let m2 = (data.metragem || '').trim() || defaults.metragem;
        const plano = defaults.plano;

        if (m2 && !/m²/.test(m2)) {
            m2 = m2 + ' m²';
        }

        const elNome = document.getElementById('tkNome');
        const elAmb = document.getElementById('tkAmb');
        const elTipo = document.getElementById('tkTipo');
        const elM2 = document.getElementById('tkM2');
        const elPlano = document.getElementById('tkPlano');

        if (elNome) elNome.textContent = nome;
        if (elAmb) elAmb.textContent = amb;
        if (elTipo) elTipo.textContent = tipo;
        if (elM2) elM2.textContent = m2;
        if (elPlano) elPlano.textContent = plano;

        try {
            localStorage.removeItem('archa_thankyou');
        } catch (e) {}
    })();
</script>

<script>
    window.CHECKOUT_SLUG = "<?= htmlspecialchars($slug ?? '', ENT_QUOTES) ?>";
</script>

<?php include 'includes/footer-thank-you.php'; ?>