// analytics e eventos
window.ArchaAnalytics = (function () {

    // Garantir que o gtag existe
    function sendEvent(eventName, params = {}) {
        if (typeof gtag !== "function") {
            // console.warn("⚠️ gtag não disponível, evento não enviado:", eventName);
            return;
        }
        gtag("event", eventName, params);
    }

    return {
        /** Usuário acessa a página do simulador */
        trackSimulatorView: function (pageUrl) {
            sendEvent("simulator_view", {
                page_url: pageUrl || window.location.href
            });
        },

        /** Primeiro preço após preencher bloco 1 */
        trackSimulationStart: function (
            projectType,
            spaceSize,
            roomsCount,
            initialPrice,
            leadInfoFilled
        ) {
            sendEvent("simulation_start", {
                project_type: projectType,
                space_size_m2: spaceSize,
                rooms_count: roomsCount,
                initial_price: initialPrice,
                lead_info_filled: leadInfoFilled
            });
        },

        /** Usuário altera parâmetros no simulador */
        trackSimulationUpdate: function (fieldChanged, newValue, newPrice, stepContext) {
            sendEvent("simulation_update", {
                field_changed: fieldChanged,
                new_value: newValue,
                new_price: newPrice,
                step_context: stepContext
            });
        },

        /** Escolha de pacote (Solo, Duo, Trio) */
        trackSelectPackage: function (
            packageSelected,
            finalPrice,
            projectType,
            // architectType,
            spaceSize,
            roomsCount,
            extras,
            prazo
        ) {
            sendEvent("select_package", {
                package_selected: packageSelected,
                final_price: finalPrice,
                project_type: projectType,
                // architect_type: architectType,
                space_size_m2: spaceSize,
                rooms_count: roomsCount,
                extras: Array.isArray(extras) ? extras.join(",") : (extras || ""), // GA4 não aceita array
                prazo: prazo
            });
        },

        /** Usuário chega à página de contrato */
        trackContractView: function (packageSelected, finalPrice, projectSummary) {
            sendEvent("contract_view", {
                package_selected: packageSelected,
                final_price: finalPrice,
                project_summary: projectSummary
            });
        },

        /* ===================== NOVOS EVENTOS CHECKOUT / PAGAMENTO ===================== */

        /** checkout_view – Usuário acessa a página do checkout */
        trackCheckoutView: function (pageUrl) {
            sendEvent("checkout_view", {
                page_url: pageUrl || window.location.href
            });
        },

        /**
         * checkout_address – Usuário preenche o bloco de dados para a contratação
         * name, email, whatsapp, uf, city
         */
        trackCheckoutAddress: function (name, email, whatsapp, uf, city) {
            sendEvent("checkout_address", {
                name: name || "",
                email: email || "",
                whatsapp: whatsapp || "",
                uf: uf || "",
                city: city || ""
            });
        },

        /**
         * checkout_payment_method – Usuário escolhe método de pagamento / parcelas
         * method, installments
         */
        trackCheckoutPaymentMethod: function (method, installments) {
            sendEvent("checkout_payment_method", {
                method: method || "",
                installments: installments || ""
            });
        },


        /**
         * checkout_cupom – Usuário usa um cupom
         * cupom_name, valid (boolean ou "true"/"false")
         */
        trackCheckoutCupom: function (cupomName, valid) {
            sendEvent("checkout_cupom", {
                cupom_name: cupomName || "",
                valid: typeof valid === "boolean" ? valid : !!valid
            });
        },

        /**
         * payment_intent – Usuário tenta realizar pagamento
         * name, email, whatsapp, value, payment_method, installments
         */
        trackPaymentIntent: function (
            name,
            email,
            whatsapp,
            value,
            paymentMethod,
            installments
        ) {
            sendEvent("payment_intent", {
                name: name || "",
                email: email || "",
                whatsapp: whatsapp || "",
                value: value != null ? value : 0,
                payment_method: paymentMethod || "",
                installments: installments != null ? installments : ""
            });
        },

        /**
         * payment_status – Status do pagamento (aprovado, recusado, pendente etc.)
         * name, email, whatsapp, value, payment_method, installments, status
         */
        trackPaymentStatus: function (
            name,
            email,
            whatsapp,
            value,
            paymentMethod,
            installments,
            status
        ) {
            sendEvent("payment_status", {
                name: name || "",
                email: email || "",
                whatsapp: whatsapp || "",
                value: value != null ? value : 0,
                payment_method: paymentMethod || "",
                installments: installments != null ? installments : "",
                status: status || ""
            });
        }

        /** Antigo trackPaymentView foi descontinuado (mantido como comentário no código legado) */
        /* trackPaymentView: function (contractSigned, finalPrice) {
            sendEvent("payment_view", {
                contract_signed: contractSigned,
                final_price: finalPrice
            });
        } */
    };

})();
