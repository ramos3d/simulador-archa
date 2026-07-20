<!-- etapa11.php -->
<div class="step" id="step-11" style="display:none;" data-step-name="pretende_investir" data-origem="archa.com.br">
    <h2 class="f-anek fw-bold mb-3 text-center">
        Quanto você pretende investir no seu projeto
    </h2>
    <p class="f-exo text-center mb-4">
        Considere o investimento total,
        <strong>do projeto de arquitetura à execução da obra.</strong>
    </p>

    <!-- Campo monetário ----------------------------------------------------->
    <!-- Campo monetário ---------------------------------------------->
    <div class="d-flex justify-content-center" style="max-width:260px;margin:0 auto;">
        <div class="money-wrapper position-relative w-100">
            <span class="money-prefix text-secondary-soft">R$</span>

            <input type="text"
                class="form-control required size-lg money-mask ps-4"
                id="orcamento"
                name="orcamento"
                placeholder="0,00">
        </div>
    </div>


    <p class="f-exo small text-center text-secondary mt-4 mb-5">
        <strong>IMPORTANTE:</strong> o valor do seu projeto não é influenciado por essa resposta, ok?
    </p>

    <div class="d-flex justify-content-center gap-3 mb-5">
        <button type="button" class="btn btn-outline-dark px-4" onclick="voltarEtapa(10)">Voltar</button>
        <button type="submit" class="btn btn-green px-4">Finalizar</button>
    </div>
</div>