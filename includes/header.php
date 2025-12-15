<head>
    <meta charset="UTF-8">
    <?php emit_gtm_head(); ?>
    <?php emit_app_meta(APP_BASE /*, false */); ?>
    <?php emit_seo_indexing('https://www.archa.com.br/projeto-de-arquitetura-online'); ?>


    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Title SEO-friendly -->
    <title>Projeto de Arquitetura Online</title>

    <!-- Meta Description -->
    <meta name="description" content="Simule o preço do seu projeto de arquitetura online. Compare opções, encontre arquitetos verificados e receba propostas personalizadas com a Archa.">

    <link rel="icon" type="image/png" href="https://archa.com.br/projeto-de-arquitetura-online/images/favicon.png">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.archa.com.br/projeto-de-arquitetura-online">
    <meta property="og:title" content="Simulador de projeto de arquitetura online">
    <meta property="og:description" content="Calcule o preço do seu projeto de arquitetura online. Compare planos, escolha arquitetos e receba propostas personalizadas.">
    <meta property="og:image" content="https://www.archa.com.br/images/og-simulador.jpg">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://www.archa.com.br/projeto-de-arquitetura-online">
    <meta name="twitter:title" content="Simulador de projeto de arquitetura online">
    <meta name="twitter:description" content="Descubra quanto custa seu projeto de arquitetura em minutos. Compare planos Solo, Duo e Trio com arquitetos verificados.">
    <meta name="twitter:image" content="https://www.archa.com.br/images/og-simulador.jpg">

    <!-- Schema.org (JSON-LD) -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "Archa - Simulador de Orçamento",
            "url": "https://www.archa.com.br/projeto-de-arquitetura-online",
            "description": "Simulador online para calcular o custo de projetos de arquitetura e decoração.",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "All",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "BRL"
            }
        }
    </script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS dinâmico -->
    <?php if (IS_MOBILE): ?>
        <link rel="stylesheet"
            href="<?= BASE_URL ?>/css/mobile.css?v=<?= filemtime(ROOT_PATH . '/css/mobile.css'); ?>">
    <?php else: ?>
        <link rel="stylesheet"
            href="<?= BASE_URL ?>/css/styles.css?v=<?= filemtime(ROOT_PATH . '/css/styles.css'); ?>">
    <?php endif; ?>

    <link href="css/fontes.css?v=<?= filemtime('css/fontes.css'); ?>" rel="stylesheet">
    <link href="css/form-layouts.css?v=<?= filemtime('css/form-layouts.css'); ?>" rel="stylesheet">



    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-0B713MY059"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-0B713MY059', {
            'linker': {
                'domains': ['archa.com.br', 'archa.pro', 'dev.archa.pro']
            }
        });
    </script>

    <script src="js/analytics.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            ArchaAnalytics.trackSimulatorView(window.location.href);
        });
    </script>
    <!-- UTM capture (simulador: dev-hml e produção) -->
    <script>
        (function() {
            const KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid'];
            const qs = new URLSearchParams(location.search || '');
            const host = location.hostname.replace(/^www\./, '');
            const domainAttr = host.endsWith('archa.com.br') ? '; domain=.archa.com.br' : '';
            const secureAttr = location.protocol === 'https:' ? '; Secure' : '';

            const setCookie = (n, v, days) => {
                const exp = new Date(Date.now() + days * 864e5).toUTCString();
                document.cookie = `${n}=${encodeURIComponent(v)}; expires=${exp}${domainAttr}; path=/; SameSite=Lax${secureAttr}`;
            };

            KEYS.forEach(k => {
                const val = qs.get(k);
                if (val) {
                    setCookie(k, val, 90);
                    try {
                        localStorage.setItem(k, val);
                    } catch (_) {}
                }
            });
        })();
    </script>


</head>

<body class="d-flex flex-column min-vh-100">
    <?php emit_gtm_body(); ?>
    <header class="mb-3">
        <nav class="navbar py-3 f-exo font-blue" id="navbar">

            <div class="container-lg d-flex align-items-center mt-3">

                <!-- Logo (mantém regra mobile x desktop) -->
                <a class="navbar-brand me-2" href="https://www.archa.com.br">
                    <?php if (IS_MOBILE): ?>
                        <img src="images/logo-azul.png" alt="Archa" class="logo-mobile img-fluid">
                    <?php else: ?>
                        <img src="images/logo-azul.png" alt="Archa" style="height: 26px; margin-left: -55px;">
                    <?php endif; ?>
                </a>

                <!-- Ícones à direita, alinhados ao limite do container -->
                <div class="ms-auto d-flex align-items-center gap-3 icones-header">
                    <a href="https://api.whatsapp.com/send/?phone=5511942892984&text=Para+iniciar+seu+atendimento%2C+envie+uma+mensagem+como+esta%3A+Ol%C3%A1%21+Gostaria+de+contratar+um+projeto+de+arquitetura+com+a+Archa.&type=phone_number&app_absent=0"
                        target="_blank"
                        class="btn-outline-azull none" style="margin-right: -15px;">
                        <span class="btn-icon-whatsapp">
                            <img src="images/icon-colored-whatsapp.svg" alt="Whatsapp Archa">
                        </span>
                        <span>Falar com a Archa</span>
                    </a>
                </div>
            </div>
        </nav>
    </header>