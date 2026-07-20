<?php /** Dados para contratação — layout compacto */ ?>
<div class="ck-card mb-3">
  <div class="ck-head py-2 px-3">
    <h6 class="ck-title mb-0">Dados para contratação</h6>
  </div>
  <div class="ck-body p-3">

    <div class="row g-2 mb-2">
      <div class="col-12">
        <label for="nome" class="form-label f-anek mb-1">Nome completo</label>
        <input id="nome" name="nome" type="text" class="form-control f-exo" placeholder="Insira seu nome completo" required>
        <div class="invalid-feedback">Informe nome e sobrenome.</div>
      </div>
    </div>

    <div class="row g-2 mb-2">
      <div class="col-md-6">
        <label for="email" class="form-label f-anek mb-1">E-mail</label>
        <input id="email" name="email" type="email" class="form-control f-exo" placeholder="seu@email.com" required>
        <div class="invalid-feedback">Informe um e-mail válido.</div>
      </div>
      <div class="col-4 col-md-2">
        <label for="codigo_pais" class="form-label f-anek mb-1">DDI</label>
        <input id="codigo_pais" name="codigo_pais" type="text" class="form-control f-exo text-center"
               placeholder="55" value="55" inputmode="numeric" maxlength="3">
      </div>
      <div class="col-8 col-md-4">
        <label for="fone" class="form-label f-anek mb-1">WhatsApp</label>
        <input id="fone" name="fone" type="tel" class="form-control f-exo" placeholder="(11) 99999-9999" required>
        <div class="invalid-feedback">Informe seu WhatsApp.</div>
      </div>
    </div>

    <div class="row g-2 mb-2">
      <div class="col-md-7">
        <label for="endereco_obra" class="form-label f-anek mb-1">Endereço da obra</label>
        <input id="endereco_obra" name="endereco_obra" type="text" class="form-control f-exo" placeholder="Rua, Avenida..." required>
      </div>
      <div class="col-4 col-md-2">
        <label for="numero" class="form-label f-anek mb-1">Número</label>
        <input id="numero" name="numero" type="text" class="form-control f-exo" placeholder="000" inputmode="numeric" maxlength="6" required>
      </div>
      <div class="col-8 col-md-3">
        <label for="complemento" class="form-label f-anek mb-1">Complemento</label>
        <input id="complemento" name="complemento" type="text" class="form-control f-exo" placeholder="Apto, bloco...">
      </div>
    </div>

    <div class="row g-2 mb-2">
      <div class="col-md-4">
        <label for="bairro" class="form-label f-anek mb-1">Bairro</label>
        <input id="bairro" name="bairro" type="text" class="form-control f-exo" placeholder="Bairro" required>
      </div>
      <div class="col-5 col-md-3">
        <label for="cep" class="form-label f-anek mb-1">CEP</label>
        <input id="cep" name="cep" type="text" class="form-control f-exo" placeholder="00000-000" inputmode="numeric" maxlength="9" required>
      </div>
      <div class="col-3 col-md-2">
        <label for="estado" class="form-label f-anek mb-1">UF</label>
        <select id="estado" name="estado" class="form-select f-exo" required>
          <option value="" disabled selected>UF</option>
          <?php foreach (['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf): ?>
          <option value="<?= $uf ?>"><?= $uf ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-4 col-md-3">
        <label for="cidade" class="form-label f-anek mb-1">Cidade</label>
        <input id="cidade" name="cidade" type="text" class="form-control f-exo" placeholder="Cidade">
      </div>
    </div>

    <div class="row g-2">
      <div class="col-8">
        <label for="documento" class="form-label f-anek mb-1">CPF/CNPJ</label>
        <input id="documento" name="documento" type="text" class="form-control f-exo" placeholder="000.000.000-00" required>
      </div>
      <div class="col-4">
        <label for="documento_tipo" class="form-label f-anek mb-1">Tipo</label>
        <select id="documento_tipo" name="documento_tipo" class="form-select f-exo" required>
          <option value="" disabled selected>Tipo</option>
          <option value="CPF">CPF</option>
          <option value="CNPJ">CNPJ</option>
        </select>
      </div>
    </div>

    <p class="ck-muted mt-2 mb-0 f-exo" style="font-size:.72rem">
      Esses dados serão usados para o envio do contrato após o pagamento.
    </p>
  </div>
</div>
