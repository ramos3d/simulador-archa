<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Carregando orçamento | Archa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
        }

        .text-pink {
            color: #E893C7;
        }

        .check-icon {
            width: 18px;
            height: 18px;
        }

        /* ilustração: 50 px abaixo do topo e encostada à lateral */
        .hero-img {
            height: calc(100vh - 50px);
            margin-top: 50px;
            object-fit: cover;
            object-position: right top;
        }

        .loading-dots span {
            display: inline-block;
            font-weight: bold;
            animation: bounce 1.2s infinite;
            transform-origin: 50% 75%;
        }

        /* cada ponto começa em momento diferente */
        .loading-dots span:nth-child(1) {
            animation-delay: 0s;
        }

        .loading-dots span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .loading-dots span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {

            0%,
            80%,
            100% {
                transform: translateY(0) scale(1);
            }

            40% {
                transform: translateY(-4px) scale(1.4);
            }
        }
    </style>
</head>

<body>
    <main class="d-flex align-items-center" style="min-height:100vh;">
        <!-- p-0 remove padding lateral do container, tirando a folga à direita -->
        <div class="container-fluid p-0">
            <div class="row g-0 align-items-center">

                <!-- texto -->
                <div class="col-lg-5 offset-lg-1 px-4 px-lg-5">
                    <!-- substitua a linha atual por: -->
                    <p class="text-pink mb-2 f-exo">
                        Aguarde um pouco
                        <span class="loading-dots">
                            <span>.</span><span>.</span><span>.</span>
                        </span>
                    </p>


                    <h1 class="f-anek fw-bold mb-4 font-blue">
                        Estamos montando<br>seu orçamento
                    </h1>

                    <ul class="list-unstyled f-exo lh-lg text-muted">
                        <li class="d-flex align-items-center gap-2">
                            <img src="images/check-circle.png" class="check-icon" alt="">
                            <span>Analisando perfis dos arquitetos</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <img src="images/check-circle.png" class="check-icon" alt="">
                            <span>Verificando as preferências do projeto</span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <img src="images/check-circle.png" class="check-icon" alt="">
                            <span>Finalizando seu orçamento</span>
                        </li>
                    </ul>
                </div>

                <!-- ilustração -->
                <div class="col-lg-6 d-none d-lg-block p-0">
                    <img src="images/building.png" alt="Ilustração prédio" class="hero-img w-100">
                </div>

            </div>
        </div>
    </main>

    <script>
        /*setTimeout(() => {
            window.location.href = "tela-orcamento.php";
        }, 10000);*/
    </script>
</body>

</html>