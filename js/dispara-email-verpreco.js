document.addEventListener('DOMContentLoaded', function () {
    const btnVerPreco = document.getElementById('btnVerPreco');

    if (btnVerPreco) {
        btnVerPreco.addEventListener('click', function () {
            try {
                dispararEmailPrecoSimulado();
            } catch (e) {
                console.error('Erro ao disparar e-mail de simulação:', e);
            }
        });
    }
});

/**
 * Coleta os dados básicos do simulador para o e-mail.
 * Ajuste os IDs conforme o seu HTML.
 */
function coletarDadosSimuladorParaEmail() {
    const get = (id) => {
        const el = document.getElementById(id);
        return el && el.value ? el.value.trim() : '';
    };

    return {
        nome: get('nome_cliente'),
        email: get('email_cliente'),
        tipo: get('tipo_projeto'),
        ambientes: get('qtd_ambientes'),
        metragem: get('metragem'),
        cidade: get('cidade'),
        estado: get('estado'),
        link: window.location.href
    };
}

/**
 * Dispara o e-mail "preço simulado" para o cliente.
 * Fire-and-forget: não bloqueia o fluxo do botão "Ver preço".
 */
async function dispararEmailPrecoSimulado() {
    const data = coletarDadosSimuladorParaEmail();

    // Se não tiver e-mail, não faz nada
    if (!data.email) {
        console.warn('Sem e-mail preenchido, não enviando e-mail de simulação.');
        return;
    }

    try {
        const resp = await fetch('/api/send-preco-simulado', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nome: data.nome,
                email: data.email,
                tipo: data.tipo,
                ambientes: data.ambientes,
                metragem: data.metragem,
                cidade: data.cidade,
                estado: data.estado,
                link: data.link
            })
        });

        if (!resp.ok) {
            console.warn('Falha ao enviar e-mail de simulação:', resp.status);
            return;
        }

        const json = await resp.json().catch(() => ({}));
        console.log('E-mail de simulação disparado:', json);
    } catch (err) {
        console.error('Erro ao chamar /api/send-preco-simulado:', err);
    }
}
