<div class="accordion mb-1 step-card qa-accordion" id="accordionStep1">
    <div class="accordion-item">
        <h2 class="accordion-header" id="head1">
            <button class="accordion-button gap-3" type="button"
                data-bs-toggle="collapse" data-bs-target="#step1"
                aria-expanded="true" aria-controls="step1">
                <span class="step-badge d-inline-flex align-items-center
                             justify-content-center flex-shrink-0">1</span>
                <span class="fw-bold fs-5 text-blue f-anek"><?= t('Descreva o perfil do seu espaço') ?></span>
            </button>
        </h2>

        <!-- 🔸 data-bs-parent REMOVIDO -->
        <div id="step1" class="accordion-collapse collapse show">
            <div class="accordion-body pt-2">
                <p class="text-blue mb-4 text-start f-14 f-exo">
                    <?= t('Compartilhe com a gente o tamanho e a finalidade do seu imóvel. Essas informações são essenciais para gerarmos as opções de investimento ideais para o seu projeto de arquitetura e decoração.') ?>
                </p>

                <!-- Botões categoria -->
                <div class="d-flex flex-wrap gap-1 justify-content-center mb-4 flex-nowrap">

                    <button type="button" class="btn btn-tipo active f-zilla-m"
                        data-cat="residencial"><?= t('Residencial') ?></button>
                    <button type="button" class="btn btn-tipo f-zilla-m"
                        data-cat="comercial"><?= t('Comercial') ?></button>
                    <button type="button" class="btn btn-tipo f-zilla-m"
                        data-cat="corporativo"><?= t('Corporativo') ?></button>
                </div>

                <!-- Cards dinâmicos -->
                <!-- Cards ESTÁTICOS (um único selected por vez em TODA a pergunta) -->
                <div id="propertyWrapper" class="mb-4">
                    <!-- RESIDENCIAL (default visível) -->
                    <div id="cards-residencial" class="row g-4 justify-content-center">
                        <div class="col-6 col-md-3">
                            <div class="tipo-imovel text-center selected"
                                data-key="apartamento"
                                data-value="RESIDENCIAL: Apartamento">
                                <img class="mb-2" src="images/icons/questao_4/Active/apartamento.png" alt="apartamento">
                                <p class="mb-0 text-secondary"><?= t('APARTAMENTO') ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="tipo-imovel text-center"
                                data-key="casa"
                                data-value="RESIDENCIAL: Casa">
                                <img class="mb-2" src="images/icons/questao_4/Default/casa.png" alt="casa">
                                <p class="mb-0 text-secondary"><?= t('CASA') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- COMERCIAL (começa escondido) -->
                    <div id="cards-comercial" class="row g-4 justify-content-center d-none">
                        <div class="col-6 col-md-3">
                            <div class="tipo-imovel text-center"
                                data-key="hotelaria"
                                data-value="COMERCIAL: Hotelaria">
                                <img class="mb-2" src="images/icons/questao_4/Default/hotelaria.png" alt="hotelaria">
                                <p class="mb-0 text-secondary"><?= t('HOTELARIA') ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="tipo-imovel text-center"
                                data-key="bares"
                                data-value="COMERCIAL: Bares, restaurantes e casas noturnas">
                                <img class="mb-2" src="images/icons/questao_4/Default/bares.png" alt="bares">
                                <p class="mb-0 text-secondary"><?= t('BARES, RESTAURANTES<br>E CASAS NOTURNAS') ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="tipo-imovel text-center"
                                data-key="lojas"
                                data-value="COMERCIAL: Lojas varejo">
                                <img class="mb-2" src="images/icons/questao_4/Default/lojas.png" alt="lojas">
                                <p class="mb-0 text-secondary"><?= t('LOJAS VAREJO') ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="tipo-imovel text-center"
                                data-key="clinicas"
                                data-value="COMERCIAL: Clínicas e espaços estéticos">
                                <img class="mb-2" src="images/icons/questao_4/Default/clinicas.png" alt="clinicas">
                                <p class="mb-0 text-secondary"><?= t('CLÍNICAS E ESPAÇOS<br>ESTÉTICOS') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- CORPORATIVO (começa escondido) -->
                    <div id="cards-corporativo" class="row g-4 justify-content-center d-none">
                        <div class="col-6 col-md-3">
                            <div class="tipo-imovel text-center"
                                data-key="escritorio"
                                data-value="CORPORATIVO: Escritório">
                                <img class="mb-2" src="images/icons/questao_4/Default/escritorio.png" alt="escritorio">
                                <p class="mb-0 text-secondary"><?= t('ESCRITÓRIO') ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="tipo-imovel text-center"
                                data-key="estandes"
                                data-value="EVENTOS: Estandes">
                                <img class="mb-2" src="images/icons/questao_4/Default/estandes.png" alt="estandes">
                                <p class="mb-0 text-secondary"><?= t('ESTANDES') ?></p>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="tipo-imovel text-center"
                                data-key="eventos"
                                data-value="EVENTOS: Espaços para eventos e/ou masterplan/palco">
                                <img class="mb-2" src="images/icons/questao_4/Default/eventos.png" alt="eventos">
                                <p class="mb-0 text-secondary"><?= t('ESPAÇOS DE EVENTOS,<br>MASTERPLAN E PALCO') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- hidden para o payload -->
                <input type="hidden" id="tipo_projeto" name="tipo_projeto" class="required">
                <!-- Inputs -->
                <!-- ░░ Métricas – ambientes / área ──────────────────────────────── -->
                <!-- blocão que agrupa as duas métricas -->
                <div class="row g-3 metrics mb-4">

                    <!-- ▸ Ambientes ------------------------------------------------- -->
                    <div class="col-6 d-flex flex-column">
                        <label class="metric-label text-blue lil mb-1 f-14 f-exo">
                            <?= t('Quantos ambientes serão projetados?') ?>
                            <button type="button"
                                class="info-ico"
                                data-bs-toggle="tooltip"
                                data-bs-trigger="click focus"
                                data-bs-placement="bottom"
                                title="<?= t('Informe o número de cômodos que receberão intervenção. Ex.: sala de estar, sala de jantar e cozinha = 3 ambientes') ?>"
                                aria-label="<?= t('Ajuda: como preencher') ?>">
                                <img src="images/info.png" height="18" alt="">
                            </button>
                        </label>

                    </div>
                    <div class="col-6 d-flex align-items-end">
                        <input id="qtd_amb"
                            name="ambientes"
                            type="text"
                            class="metric-input flex-grow-1 text-center f-24"
                            placeholder="0">
                        <div class="invalid-feedback"><?= t('Informe pelo menos 1 ambiente.') ?></div>

                    </div>

                    <!-- ▸ Área ------------------------------------------------------- -->
                    <div class="col-6 d-flex flex-column">
                        <label class="metric-label text-blue lil mb-1 f-14 f-exo">
                            <?= t('Qual o tamanho da área a ser projetada?') ?>
                            <button type="button"
                                class="info-ico"
                                data-bs-toggle="tooltip"
                                data-bs-trigger="click focus"
                                data-bs-placement="bottom"
                                title="<?= t('Considere apenas a área que deseja transformar. Se for reforma parcial, informe só os ambientes envolvidos. Ex.: seu apê tem 120m², mas o projeto será apenas para dois quartos de 20m² cada = responda 40.') ?>"
                                aria-label="<?= t('Ajuda: como preencher') ?>">
                                <img src="images/info.png" height="18" alt="">
                            </button>
                        </label>
                    </div>
                    <div class="col-6 d-flex align-items-end">
                        <div class="metric-field w-100"> <!-- wrapper relativo -->
                            <input id="area"
                                name="metragem"
                                type="text"
                                class="metric-input  text-center f-24"
                                placeholder="0">
                            <span class="metric-unit">m²</span>
                            <div class="invalid-feedback row"><?= t('A área mínima é 20 m².') ?></div>
                        </div>
                    </div>

                </div>
                <!-- /.metrics -->


                <!-- Contato -->
                <div class="row g-3 align-items-center mb-4">

                    <!-- rótulo à esquerda, quebrado em duas linhas -->
                    <span class="form-label text-blue  mb-0 text-center">
                        <strong><?= t('Um pouco sobre você!') ?></strong>
                    </span>

                    <div class="col-md-4">
                        <input id="nome" name="nome" type="text" class="form-control input-borders f-14 f-exo " placeholder="<?= t('Nome completo') ?>" style="padding:.5rem;border-radius:6px!important;">
                        <div class="invalid-feedback"><?= t('Informe nome completo.') ?></div>
                    </div>

                    <div class="col-md-3">
                        <input id="email" name="email" type="email" class="form-control input-borders f-14 f-exo" placeholder="<?= t('e-mail') ?>" style="padding:.5rem;border-radius:6px!important;">
                        <div class="invalid-feedback"><?= t('Informe o seu email.') ?></div>
                    </div>

                </div>




                <div class="col-12 mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <!-- Código do país: largura fixa p/ até 3 dígitos -->
                        <div style="flex:0 0 72px; max-width:72px">
                            <input id="codigo_pais"
                                name="codigo_pais"
                                type="text"
                                class="form-control input-borders text-center"
                                placeholder="+55"
                                value="55"
                                inputmode="numeric"
                                pattern="\d{1,3}"
                                maxlength="3"
                                style="padding:.5rem;border-radius:6px!important;" required>
                            <div class="invalid-feedback"><?= t('Informe o código do país') ?></div>
                        </div>

                        <!-- WhatsApp: ocupa todo o restante da linha -->
                        <div class="flex-grow-1" style="min-width:0">
                            <input id="fone"
                                name="telefone"
                                type="text"
                                class="form-control input-borders f-14 f-exo"
                                placeholder="<?= t('whatsapp') ?>"
                                style="padding:.5rem;border-radius:6px!important;">
                            <div class="invalid-feedback"><?= t('Informe o Whatsapp.') ?></div>
                        </div>
                    </div>
                </div>
                <button id="btnVerPreco" type="button" class="btn btn-disabled w-100 btn-lg"><?= t('Ver preço') ?></button>
                <a href="https://api.whatsapp.com/send/?phone=5511942892984&text=<?= rawurlencode(t('Para iniciar seu atendimento, envie uma mensagem como esta: Olá! Gostaria de contratar um projeto de arquitetura com a Archa.')) ?>&type=phone_number&app_absent=0"
                    target="_blank"
                    class="btn-outline-azull mt-2  w-100 btn-lg">
                    <span class="btn-icon-whatsapp">
                        <img src="images/icon-colored-whatsapp.svg" alt="Whatsapp Archa">
                    </span>
                    <span class="f-exo f-22 m-short-6"><?= t('Falar com a Archa') ?></span>
                </a>

            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const nomeInput = document.getElementById("nome");
        const feedback = nomeInput.nextElementSibling; // div.invalid-feedback já existente

        nomeInput.addEventListener("blur", function() {
            const valor = nomeInput.value.trim();
            const partes = valor.split(/\s+/); // separa por espaços

            if (partes.length < 2) {
                nomeInput.classList.add("is-invalid");
                feedback.textContent = "<?= addslashes(t('Informe nome e sobrenome.')) ?>";
            } else {
                nomeInput.classList.remove("is-invalid");
                feedback.textContent = "";
            }
        });
    });

    // Exibe tooltips - info img
    document.addEventListener('DOMContentLoaded', () => {
        const tps = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tps.forEach(el => new bootstrap.Tooltip(el));
    });
</script>

<style>
    /* ——— Campos “slim” da seção Métricas ——— */
    .metric-input.is-filled {
        border-bottom-color: var(--verde-borda) !important;
        /* mantém verdinho após blur */
    }

    /* deixa o “m²” verdinho quando válido */
    .metric-field .metric-input.is-filled~.metric-unit {
        color: var(--verde-borda) !important;
    }


    /* ——— Campos padrão com borda (nome, e-mail, whatsapp) ——— */
    .form-control.input-borders.is-filled {
        border-color: var(--verde-borda) !important;
        box-shadow: 0 0 0 2px rgba(127, 221, 83, .15);
        /* sutil, opcional */
    }

    .form-control.input-borders.is-invalid {

        box-shadow: none;
    }

    /* transição suave (opcional) */
    .metric-input,
    .form-control.input-borders {
        transition: border-color .15s ease, box-shadow .15s ease;
    }




    /* SECTION 1 – Métricas (ambientes / área)
   Força a mensagem de erro a quebrar para a linha de baixo */
    /* Ambientes: feedback abaixo e só quando houver erro */
    .metrics .col-6.d-flex.align-items-end {
        flex-wrap: wrap;
        /* mantém quebra para jogar o erro abaixo */
    }

    .metrics .col-6.d-flex.align-items-end .invalid-feedback {
        display: none;
        /* escondido por padrão */
        flex: 0 0 100%;
        width: 100%;
        order: 2;
        margin-top: .35rem;
        text-align: center;
    }

    /* Quando o input tiver erro, exibe a mensagem */
    .metrics .col-6.d-flex.align-items-end .is-invalid~.invalid-feedback {
        display: block;
    }

    /* Área: feedback está dentro de .metric-field */
    .metrics .metric-field {
        position: relative;
        /* garante posicionamento do m² */
        display: block;
        /* bloco padrão, sem interferir no input */
    }

    .metrics .metric-field .invalid-feedback {
        display: none;
        /* escondido por padrão */
        width: 100%;
        margin-top: .35rem;
        text-align: center;
    }

    /* Exibe só quando o input de metragem estiver inválido */
    .metrics .metric-field .metric-input.is-invalid~.invalid-feedback {
        display: block;
    }
</style>