<div class="step" id="step-1" data-step-name="informacoes_pessoais" data-origem="archa.com.br">
    <h1 class="mb-3 f-anek f-bold font-blue text-center">
        Seu novo espaço começa por aqui
    </h1>

    <p class="f-exo text-center mb-1 font-blue">
        Que bom ter você com a gente!
    </p>

    <p class="f-exo text-center mb-4 font-blue">
        Para te apresentar os profissionais e as opções de investimento ideais para o<br>
        seu projeto de arquitetura, precisamos de algumas informações.
    </p>

    <p class="fw-semibold f-exo text-center font-blue mb-4">Vamos nessa?</p>

    <!--  MAX-WIDTH fixado + centralização  -->
    <div class="col-md-8 mx-auto ">
        <div class="mb-3">
            <input type="text"
                class="form-control required"
                id="nome"
                name="nome"
                placeholder="Nome completo">
        </div>

        <div class="mb-3">
            <input type="tel"
                class="form-control required"
                id="telefone"
                name="telefone"
                placeholder="11999999999">
        </div>

        <div class="mb-4">
            <input type="email"
                class="form-control required"
                id="email"
                name="email"
                placeholder="E-mail">
        </div>
        <span id="email-erro" class="text-danger small d-none">E-mail inválido</span>
    </div>

    <div class="text-center">
        <button type="button"
            class="btn btn-green mt-2 mb-5"
            onclick="proximaEtapa(1)">
            Vamos lá
        </button>
    </div>
</div>