// analytics e eventos
window.ArchaAnalytics = (function () {

    // Garantir que o gtag existe
    function sendEvent(eventName, params = {}) {
        if (typeof gtag !== "function") {
            //console.warn("⚠️ gtag não disponível, evento não enviado:", eventName);
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
        trackSimulationStart: function (projectType, spaceSize, roomsCount, initialPrice, leadInfoFilled) {
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
                //architect_type: architectType,
                space_size_m2: spaceSize,
                rooms_count: roomsCount,
                extras: Array.isArray(extras) ? extras.join(",") : (extras || ""), // << GA4 não aceita array
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

        /** Usuário chega à página de pagamento */
        /* pode remover em definitivo em dez 2025 */
        /*trackPaymentView: function (contractSigned, finalPrice) {
            sendEvent("payment_view", {
                contract_signed: contractSigned,
                final_price: finalPrice
            });
        }*/
    };

})();
