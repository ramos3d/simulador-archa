# Simulador Archa

## Fluxo de automações (SendPulse)

Este sistema **não “dispara e-mails diretamente”**. O backend apenas **atualiza contatos e variáveis no SendPulse via API**, e **as automações do SendPulse** são acionadas com base nesses eventos/variáveis.

### 1) Entrada em Leads (primeiro cadastro)
**Quando acontece:** o usuário envia os dados do simulador e um lead é criado pela primeira vez.

**O que o backend faz:**
- Cria/atualiza o registro em `leads` (MySQL) por `token`.
- Envia o contato para o SendPulse (Address Book / lista de leads), com variáveis principais do simulador (nome, e-mail, telefone, tipo_projeto, metragem, ambientes, preços, produto_code etc).

**Onde está no backend:**
- **Controller:** `App\Http\Controllers\Api\LeadController@store`
- **Método SendPulse:** `SendPulseService::pushLeadToSendpulse($lead)`

**O que acontece no SendPulse:**
- O contato entra/atualiza na lista (address book).
- Automação de “Bem-vindo / novo assinante” é disparada quando configurada para “Novo assinante”.

---

### 2) Clique em “Avançar” (início do checkout)
**Quando acontece:** o usuário avança do simulador para o checkout.

**O que o backend faz:**
- Cria/atualiza o registro em `checkouts`.
- Atualiza o contato no SendPulse com variáveis do checkout (ex.: `checkout_id`, `checkout_status`, `checkout_slug`, preços e dados do projeto).
- Para o gatilho no SendPulse, usa uma variável que **muda sempre** (ex.: `checkout_started_at` como timestamp numérico).

**Onde está no backend:**
- **Controller:** `App\Http\Controllers\Api\CheckoutController@start` (endpoint `/api/checkout/start`)
- **Método SendPulse:** `SendPulseService::pushCheckoutStarted($checkout)`

**O que acontece no SendPulse:**
- A automação é disparada por **“Atualização da variável”** (ex.: `checkout_started_at`).

---

### 3) Pagamento confirmado
**Quando acontece:** o pagamento é confirmado (PIX/cartão) via webhook do provedor.

**O que o backend faz:**
- Atualiza o status do checkout no banco para **CONFIRMED** de forma **idempotente** (não re-disparar se já estiver confirmado).
- Atualiza variáveis de checkout no SendPulse apenas quando há **transição real** (ex.: `PENDING -> CONFIRMED`), evitando “downgrade” ou reenvio repetido.

**Onde está no backend:**
- **Controller/Webhook:** `PaymentController` (webhook Asaas)
- **Método SendPulse:** (rotina de atualização de checkout confirmado, conforme serviço do SendPulse usado no projeto)

**O que acontece no SendPulse:**
- A automação de “Pagamento confirmado” é disparada por atualização de variável/status (conforme configurado no fluxo do SendPulse).

---

## Atenção: erro comum em templates (variável “quebrada”)
Às vezes uma variável parece correta no editor visual do SendPulse, mas o template salva HTML **dentro das chaves** e a variável falha no processamento.

**Exemplo errado (div dentro das chaves):**
`{<div>{checkout_avista}}</div>`

**Certo (HTML fora das chaves):**
`<div>{{checkout_avista}}</div>`

**Como verificar:**
1. Abra o template no SendPulse
2. Selecione a variável
3. Use **“Exibir código-fonte”**
4. Garanta que **nenhuma tag HTML** esteja dentro de `{{ ... }}`.

