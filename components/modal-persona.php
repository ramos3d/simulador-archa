<!-- MODAL -->
<div class="modal fade" id="modalPersona" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0">

      <!-- 🔵 Tarja no topo do modal (sem logo) -->
      <!-- 🔵 Tarja no topo do modal -->
      <div class="modal-banner" role="status" aria-live="polite">
        <div class="modal-banner-inner">
          Ao continuar, um profissional da Archa entrará em contato para prosseguir com o atendimento.
        </div>

        <!-- X dentro da tarja -->
        <button type="button"
          class="btn-close modal-banner-close"
          data-bs-dismiss="modal"
          aria-label="Fechar"></button>
      </div>


      <form id="formPersona" method="post" class="needs-validation modal-body pt-0 azul mt-3" novalidate>
        <!-- Localização + UF -->
        <div class="mb-3 text-start">
          <label class="form-label">Localização do Projeto</label>
          <div class="row g-2">
            <div class="col-8">
              <input id="endereco_obra" name="endereco_obra" class="form-control regular-inputs"
                placeholder="Endereço / bairro / cidade" required>
            </div>
            <div class="col-4">
              <select id="estado" name="estado" class="form-select regular-inputs" required>
                <option value="">UF</option>
                <!-- opções preenchidas via JS -->
              </select>
            </div>
          </div>
          <input type="hidden" name="pais" value="BR" id="pais">
        </div>

        <!-- CEP -->
        <div class="mb-3 text-start">
          <label class="form-label">CEP</label>
          <input name="cep" id="cep" class="form-control regular-inputs"
            placeholder="00000-000" inputmode="numeric" pattern="\d{5}-?\d{3}" maxlength="9">
        </div>

        <!-- Tipo de Pessoa -->
        <div class="mb-3 text-start">
          <label class="form-label">Tipo de Pessoa</label>
          <select name="documento_tipo" class="form-select" required>
            <option value="CPF" selected>CPF</option>
            <option value="CNPJ">CNPJ</option>
          </select>
        </div>

        <!-- Documento principal (CPF ou CNPJ) -->
        <div class="mb-3 text-start">
          <label id="lblDocumento" class="form-label">CPF</label>
          <input name="documento" id="inpDocumento" class="form-control regular-inputs"
            placeholder="000.000.000-00" required aria-describedby="docHelp">
          <div id="docHelp" class="form-text text-muted small">Informe seu CPF.</div>
        </div>

        <!-- Razão social (apenas CNPJ) -->
        <div id="wrapRazao" class="mt-2 d-none text-start mb-3">
          <label class="form-label">Razão social</label>
          <input name="razao_social" class="form-control regular-inputs" placeholder="Razão social da empresa">
        </div>

        <!-- CPF do representante (apenas CNPJ) -->
        <div id="wrapRepresentante" class="d-none">
          <div class="mb-3 text-start">
            <label class="form-label">CPF do representante legal</label>
            <input name="rep_cpf" class="form-control regular-inputs"
              placeholder="000.000.000-00" inputmode="numeric" maxlength="14">
            <div class="invalid-feedback">Informe um CPF válido do representante.</div>
            <div class="form-text text-muted small">Usado para assinar no Clicksign.</div>
          </div>
        </div>

        <button type="submit" class="btn btn-azul w-100">Continuar</button>
      </form>
    </div>
  </div>
</div>

<style>
  .modal-dialog-centered .modal-content {
    max-width: 420px;
    margin: auto
  }

  .modal-content {
    border-radius: 12px;
    box-shadow: 0 8px 40px rgba(0, 0, 0, .12)
  }

  .modal-body .form-control,
  .modal-body .form-select {
    padding: .6rem .75rem;
    font-size: .95rem
  }

  .modal-body .mb-3 {
    margin-bottom: 1.1rem !important
  }

  #processingOverlay {
    position: fixed;
    inset: 0;
    background: rgba(255, 255, 255, .8);
    display: flex;
    align-items: center;
    justify-content: center;
    font: 600 1.1rem/1 "Inter", sans-serif;
    z-index: 1090;
  }
</style>

<script>
  // Popular UF se necessário
  document.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('estado');
    if (sel && sel.options.length <= 1) {
      const UF = ['AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MG', 'MS', 'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RJ', 'RN', 'RO', 'RR', 'RS', 'SC', 'SE', 'SP', 'TO'];
      sel.insertAdjacentHTML('beforeend', UF.map(uf => `<option value="${uf}">${uf}</option>`).join(''));
    }
  });

  // Apenas UI do modal (não interfere no simulador.js)
  (function() {
    if (window.ARCHA_MODAL_PERSONA_INIT) return;
    window.ARCHA_MODAL_PERSONA_INIT = true;

    function applyTipoPessoaUI() {
      const form = document.getElementById('formPersona');
      if (!form) return;

      const tipoSel = form.querySelector('[name="documento_tipo"]');
      const lblDoc = document.getElementById('lblDocumento');
      const inpDoc = document.getElementById('inpDocumento');
      const docHelp = document.getElementById('docHelp');
      const wrapRazao = document.getElementById('wrapRazao');
      const wrapRep = document.getElementById('wrapRepresentante');
      const razaoInp = form.querySelector('[name="razao_social"]');
      const repCpf = form.querySelector('[name="rep_cpf"]');

      const isPJ = (tipoSel?.value === 'CNPJ');

      // Label/placeholder/ajuda do documento principal
      if (isPJ) {
        lblDoc.textContent = 'Nº do CNPJ';
        inpDoc.placeholder = '00.000.000/0001-00';
        docHelp.textContent = 'Informe o CNPJ da empresa.';
      } else {
        lblDoc.textContent = 'CPF';
        inpDoc.placeholder = '000.000.000-00';
        docHelp.textContent = 'Informe seu CPF.';
      }

      // Mostrar/ocultar blocos e required dinâmico
      wrapRazao.classList.toggle('d-none', !isPJ);
      wrapRep.classList.toggle('d-none', !isPJ);
      if (razaoInp) razaoInp.required = isPJ;
      if (repCpf) repCpf.required = isPJ;
    }

    document.addEventListener('DOMContentLoaded', applyTipoPessoaUI);
    document.addEventListener('shown.bs.modal', (ev) => {
      if (ev.target && ev.target.id === 'modalPersona') applyTipoPessoaUI();
    });

    // Escuta a troca do select
    document.addEventListener('change', (ev) => {
      if ((ev.target)?.name === 'documento_tipo') applyTipoPessoaUI();
    });
  })();
</script>

<style>
  :root {
    --modal-banner-bg: #94ccfc;
    /* azul claro */
    --modal-banner-fg: #2263d2;
    /* texto azul escuro */
  }

  /* garanta que o conteúdo do modal respeite os cantos arredondados */
  .modal-content {
    border-radius: 12px;
    overflow: hidden;
    /* faz a tarja “colar” no topo/laterais */
  }

  /* a tarja precisa ser o container de posicionamento do X */
  .modal-banner {
    position: relative;
    /* <— necessário pro botão absoluto */
    margin: 0;
    background: var(--modal-banner-bg);
    color: var(--modal-banner-fg);
    border: 0;
    border-bottom: 1px solid rgba(0, 0, 0, .08);
    border-radius: 12px 12px 0 0;
  }

  /* conteúdo centralizado */
  .modal-banner-inner {
    min-height: 75px;
    padding: 10px 48px 10px 14px;
    color: #000;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font: 800 1.25rem "Exo2", sans-serif;
  }

  /* botão X na própria tarja */
  .modal-banner-close {
    position: absolute;
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    opacity: .75;
  }

  .modal-banner-close:hover {
    opacity: 1;
  }

  /* se manteve o <div class="modal-header">, esconda-o */
  .modal-header {
    display: none;
  }


  /* um pequeno respiro entre tarja e título */
  .modal-header {
    padding-top: 10px;
  }
</style>