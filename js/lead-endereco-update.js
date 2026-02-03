/**
 * lead-endereco-update.js
 * Atualiza endereço do lead em tempo real (via proxy PHP)
 * - Envia apenas campos preenchidos (não sobrescreve com vazio)
 * - Debounce + anti-duplicação
 */

(function () {
    // Campos de endereço do _dados.php
    const FIELD_IDS = ["endereco_obra", "numero", "complemento", "estado", "bairro", "cep"];

    function getEl(id) {
        return document.getElementById(id);
    }

    function getLeadToken() {
        return localStorage.getItem("leadUuid") || localStorage.getItem("uuidLaravel") || "";
    }

    function buildEnderecoPayload() {
        const endereco = {};

        const logradouro = (getEl("endereco_obra")?.value || "").trim();
        const numero = (getEl("numero")?.value || "").trim();
        const complemento = (getEl("complemento")?.value || "").trim();
        const estado = (getEl("estado")?.value || "").trim();
        const bairro = (getEl("bairro")?.value || "").trim();
        const cep = (getEl("cep")?.value || "").trim();

        // nomes que o controller espera:
        if (logradouro) endereco.logradouro = logradouro;
        if (numero) endereco.numero = numero;
        if (complemento) endereco.complemento = complemento;
        if (estado) endereco.estado = estado;
        if (bairro) endereco.bairro = bairro;
        if (cep) endereco.cep = cep;

        return endereco;
    }

    let timer = null;
    let lastSignature = "";

    function scheduleSend() {
        clearTimeout(timer);
        timer = setTimeout(sendIfNeeded, 700);
    }

    async function sendIfNeeded() {
        const token = getLeadToken();
        if (!token) return;

        const endereco = buildEnderecoPayload();
        if (!endereco || Object.keys(endereco).length === 0) return;

        const signature = token + "::" + JSON.stringify(endereco);
        if (signature === lastSignature) return;
        lastSignature = signature;

        try {
            await fetch("./ajax/lead-endereco-update.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ token, endereco })
            });
        } catch (e) {
            // silencioso
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        FIELD_IDS.forEach(function (id) {
            const el = getEl(id);
            if (!el) return;

            el.addEventListener("input", scheduleSend);
            el.addEventListener("blur", scheduleSend);
            el.addEventListener("change", scheduleSend);
        });
    });
})();
