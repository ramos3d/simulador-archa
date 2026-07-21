<div class="accordion shadow-sm mb-5 step-card" id="accordionStep1">
    <div class="accordion-item">
        <h2 class="accordion-header style-title" id="head1">
            <button class="accordion-button gap-3" type="button"
                data-bs-toggle="collapse" data-bs-target="#step1"
                aria-expanded="true" aria-controls="step1">
                <span class="step-badge d-inline-flex align-items-center
                             justify-content-center flex-shrink-0 ">1</span>
                <span class="fw-bold azul style-title"><?= t('Descreva o perfil do seu espaço') ?></span>
            </button>
        </h2>
        <!-- 🔸 data-bs-parent REMOVIDO -->
        <div id="step1" class="accordion-collapse collapse show">
            <div class="accordion-body pt-2">
                <p class="small mb-5 intro-indent paragrafo sec1-mt-sml-14">
                    <?= t('Compartilhe com a gente o tamanho e a finalidade do seu imóvel. Essas informações são essenciais para gerarmos as opções de investimento ideais para o seu projeto de arquitetura e decoração.') ?>
                </p>

                <!-- Botões categoria -->
                <div class="d-flex flex-wrap gap-3 justify-content-center mb-4">
                    <button type="button" class="btn btn-tipo active f-zilla-m"
                        data-cat="residencial"><?= t('Residencial') ?></button>
                    <button type="button" class="btn btn-tipo f-zilla-m"
                        data-cat="comercial"><?= t('Comercial') ?></button>
                    <button type="button" class="btn btn-tipo f-zilla-m"
                        data-cat="corporativo"><?= t('Corporativo') ?></button>
                </div>


                <!-- Cards ESTÁTICOS (um único selected por vez em TODA a pergunta) -->
                <div id="propertyWrapper" class="mb-4">
                    <!-- RESIDENCIAL -->
                    <div id="cards-residencial" class="row tipo-grid justify-content-center">
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
                    <div id="cards-comercial" class="row tipo-grid justify-content-center d-none">
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
                    <div id="cards-corporativo" class="row tipo-grid justify-content-center d-none">
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
                <div class="row gx-5 gy-4 mb-4 text-center mt-4">
                    <div class="col-md-6">
                        <label class="form-label azul lil paragrafo" for="qtd_amb">
                            <?= t('Quantos ambientes serão projetados?') ?>

                            <img src="images/info.svg" height="18" alt="info"
                                class="help-icon ms-1"
                                title="<?= t('Informe o número de cômodos que receberão intervenção. Ex.: sala de estar, sala de jantar e cozinha = 3 ambientes') ?>">

                        </label>
                        <!-- mínimo 1 e já inicia em 1 -->
                        <input id="qtd_amb" type="text" class="form-control text-center input-skinny f-24"
                            name="ambientes" placeholder="0">
                        <div class="invalid-feedback"><?= t('Informe pelo menos 1 ambiente.') ?></div>
                    </div>

                    <div class="col-md-6">

                        <label class="form-label azul lil paragrafo" for="area">
                            <?= t('Qual o tamanho da área a ser projetada?') ?>

                            <img src="images/info.svg" height="18" alt="info"
                                class="help-icon ms-1"
                                title="<?= t('Considere apenas a área que deseja transformar. Se for reforma parcial, informe só os ambientes envolvidos. Ex.: seu apê tem 120m², mas o projeto será apenas para dois quartos de 20m² cada = responda 40.') ?>">

                        </label>
                        <div class="unit-input">
                            <div class="field">
                                <input id="area" type="text" class="form-control text-center input-skinny f-24"
                                    name="metragem" placeholder="0">
                                <span class="unit">m²</span>
                            </div>
                            <div class="invalid-feedback"><?= t('Informe pelo menos 1 metro.') ?></div>
                        </div>




                    </div>
                </div>


                <!-- Contato -->
                <div class="row g-3 align-items-center mb-4">

                    <!-- rótulo à esquerda, quebrado em duas linhas -->
                    <label class="form-label azul col-md-2 mb-0 text-start">
                        <strong><?= t('Um pouco<br>sobre você!') ?></strong>
                    </label>

                    <div class="col-md-3">
                        <input id="nome" name="nome" type="text" class="form-control input-borders" placeholder="<?= t('Nome') ?>" style="padding:.5rem;border-radius:6px!important;">
                        <div class="invalid-feedback"><?= t('Informe nome.') ?></div>
                    </div>

                    <div class="col-md-3">
                        <input id="email" name="email" type="email" class="form-control input-borders" placeholder="<?= t('e-mail') ?>" style="padding:.5rem;border-radius:6px!important;">
                        <div class="invalid-feedback"><?= t('Informe o email') ?></div>
                    </div>

                    <div class="col-md-1">
                        <input id="codigo_pais" name="codigo_pais" type="text" class="form-control input-borders text-center" placeholder="+55" style="padding:.5rem;border-radius:6px!important;" inputmode="numeric"
                            pattern="\d*"
                            value="55"
                            maxlength="3">
                        <div class="invalid-feedback"><?= t('Informe o código do país') ?></div>
                    </div>
                    <div class="col-md-3">
                        <input id="fone" name="telefone" type="text" class="form-control input-borders" placeholder="<?= t('whatsapp') ?>" style="padding:.5rem;border-radius:6px!important;">
                        <div class="invalid-feedback"><?= t('Informe o seu Whatsapp') ?></div>
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
        const feedback = nomeInput.nextElementSibling; // div.invalid-feedback

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
</script>
