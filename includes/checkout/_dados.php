<?php

/** includes/checkout/_dados.php
 * Bloco: Dados para contratação (somente dados do cliente)
 * Obs.: Este partial NÃO abre/fecha <form>. Envolva todos os partials em um único <form id="formPersona"> no template pai.
 */
?>
<div class="ck-card mb-4">
  <div class="ck-head">
    <h6 class="ck-title mb-0">Dados para contratação</h6>
  </div>
  <div class="ck-body">

    <!-- Nome completo -->
    <div class="mb-4">
      <label for="nome" class="form-label f-anek">Nome completo</label>
      <input id="nome" name="nome" type="text" class="form-control f-exo" placeholder="Insira seu nome completo" required>
      <div class="invalid-feedback">Informe nome e sobrenome.</div>
    </div>
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label for="email" class="form-label f-anek">E-mail</label>
        <input type="text" class="form-control f-exo" placeholder="Email" id="email">
        <div class="invalid-feedback">Informe o seu email.</div>

      </div>
      <div class="col-3 col-md-2">
        <label for="codigo_pais" class="form-label f-anek">Código</label>
        <input type="text" class="form-control f-exo text-center"
          name="codigo_pais" id="codigo_pais"
          placeholder="+55" value="55"
          inputmode="numeric" pattern="\d{1,3}" maxlength="3">
        <div class="invalid-feedback">Informe o código do país.</div>
      </div>

      <div class="col-9 col-md-4">
        <label for="fone" class="form-label f-anek">Whatsapp</label>
        <input type="text" class="form-control f-exo"
          id="fone" name="fone" placeholder="(11) 99999-9999">
        <div class="invalid-feedback">Informe o seu whatsapp.</div>
      </div>

    </div>
    <!-- Linha 1: Endereço / Número / Complemento -->
    <div class="row g-3 mb-3 address-grid">
      <div class="col-md-6 fld-endereco">
        <label for="endereco_obra" class="form-label f-anek">Endereço</label>
        <input id="endereco_obra" name="endereco_obra" type="text" class="form-control f-exo" placeholder="Endereço" required>
        <div class="invalid-feedback">Informe o endereço da obra.</div>
      </div>

      <div class="col-md-2-1 fld-numero">
        <label for="numero" class="form-label f-anek">Número</label>
        <input id="numero" name="numero" type="text" class="form-control f-exo" placeholder="000000" inputmode="numeric" maxlength="6" required>
        <div class="invalid-feedback">Informe o número.</div>
      </div>

      <div class="col-md-4-1 fld-complemento">
        <label for="complemento" class="form-label f-anek">Complemento</label>
        <input id="complemento" name="complemento" type="text" class="form-control f-exo">
      </div>

      <div class="col-md-2-1 fld-estado">
        <label for="estado" class="form-label f-anek">Estado</label>
        <select id="estado" name="estado" class="form-select f-exo select-placeholder" required>
          <option value="" selected disabled hidden>UF</option>
          <option value="AC">AC</option>
          <option value="AL">AL</option>
          <option value="AP">AP</option>
          <option value="AM">AM</option>
          <option value="BA">BA</option>
          <option value="CE">CE</option>
          <option value="DF">DF</option>
          <option value="ES">ES</option>
          <option value="GO">GO</option>
          <option value="MA">MA</option>
          <option value="MT">MT</option>
          <option value="MS">MS</option>
          <option value="MG">MG</option>
          <option value="PA">PA</option>
          <option value="PB">PB</option>
          <option value="PR">PR</option>
          <option value="PE">PE</option>
          <option value="PI">PI</option>
          <option value="RJ">RJ</option>
          <option value="RN">RN</option>
          <option value="RS">RS</option>
          <option value="RO">RO</option>
          <option value="RR">RR</option>
          <option value="SC">SC</option>
          <option value="SP">SP</option>
          <option value="SE">SE</option>
          <option value="TO">TO</option>
        </select>
        <div class="invalid-feedback">Informe o Estado.</div>
      </div>

      <div class="col-md-6 fld-bairro">
        <label for="bairro" class="form-label f-anek">Bairro</label>
        <input id="bairro" name="bairro" type="text" class="form-control f-exo" placeholder="Selecione seu bairro" required>
        <div class="invalid-feedback">Informe o bairro.</div>
      </div>

      <div class="col-md-4-1 fld-cep">
        <label for="cep" class="form-label f-anek">CEP</label>
        <input id="cep" name="cep" type="text" class="form-control f-exo" placeholder="00000-000" inputmode="numeric" maxlength="9" required>
        <div class="invalid-feedback">Informe o CEP.</div>
      </div>
    </div>


    <!-- Linha 3: Número de documento / Tipo de documento -->
    <div class="row g-3 mt-0 mb-2">
      <!-- Número de documento: 8 colunas no mobile e no desktop -->
      <div class="col-7 col-md-9">
        <label for="documento" class="form-label f-anek">Número de documento</label>
        <input id="documento" name="documento" type="text" class="form-control f-exo" placeholder="000.000.000-00" required>
        <div class="invalid-feedback">Informe o número do documento.</div>
      </div>

      <!-- Tipo de documento: 4 colunas no mobile e no desktop -->
      <div class="col-5 col-md-3">
        <label for="documento_tipo" class="form-label f-anek">Tipo de documento</label>
        <select id="documento_tipo" name="documento_tipo" class="form-select select-placeholder f-exo" required>
          <option value="" selected disabled>CPF</option>
          <option value="CPF">CPF</option>
          <option value="CNPJ">CNPJ</option>
        </select>
        <div class="invalid-feedback">Informe o tipo de documento.</div>
      </div>
    </div>


    <p class="ck-muted mt-3 mb-0 f-exo f-14">
      Esses dados serão usados para o envio do contrato após o pagamento.
    </p>

  </div>
</div>

<script>
  (function() {
    let checkoutAddressSent = false;

    function trySendCheckoutAddress() {
      if (checkoutAddressSent) return;
      if (!window.ArchaAnalytics || typeof ArchaAnalytics.trackCheckoutAddress !== "function") return;

      const nome = (document.getElementById("nome") || {}).value?.trim() || "";
      const email = (document.getElementById("email") || {}).value?.trim() || "";
      const whatsapp = (document.getElementById("fone") || {}).value?.trim() || "";
      const uf = (document.getElementById("estado") || {}).value || "";
      const bairro = (document.getElementById("bairro") || {}).value?.trim() || "";

      // Só dispara quando os campos principais estiverem preenchidos
      if (!nome || !email || !whatsapp || !uf || !bairro) {
        return;
      }

      checkoutAddressSent = true;

      ArchaAnalytics.trackCheckoutAddress(
        nome,
        email,
        whatsapp,
        uf,
        bairro // vai em "city" no GA4
      );
    }

    document.addEventListener("DOMContentLoaded", function() {
      const fields = ["nome", "email", "fone", "estado", "bairro"];

      fields.forEach(function(id) {
        const el = document.getElementById(id);
        if (!el) return;

        el.addEventListener("blur", trySendCheckoutAddress);
        el.addEventListener("change", trySendCheckoutAddress);
      });
    });
  })();
</script>