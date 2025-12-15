# Documentação - Simulador Archa Form

> Documentação técnica resumida para desenvolvedores — foco em configuração (local/dev/prod), endpoints e pontos de extensão.

## Índice

- Visão Geral
- Pré-requisitos
- Estrutura do Repositório (rápido)
- Configuração — `config.php` (Local / Dev / Prod)
- Variáveis de Ambiente & Segredos
- Endpoints de API (localização e responsabilidades)
- Rotas AJAX
- Páginas e Fluxos Críticos
- Fluxo: Simulação → Cálculo → Checkout
- Geração de UUID
- Onde adicionar novos Endpoints
- Scripts Front-end e Assets
- Dados Estáticos
- Uploads, Cache e Permissões
- Logs e Depuração
- Deploy: checklist rápido
- Onboarding Rápido para Novos Devs

---

## Visão Geral

Resumo rápido do sistema: aplica-se ao fluxo de simulação de planos → cálculo → checkout. O projeto é PHP com front-end JS/CSS estático, endpoints internos em `api/` e rotas AJAX em `ajax/`.

## Pré-requisitos

- Ambiente local recomendado: Windows + XAMPP
- Caminho de desenvolvimento esperado: `c:\xampp\htdocs\archa-form`
- PHP compatível (ver `config.php` no projeto)
- Navegador moderno para testes front-end

## Estrutura do Repositório (rápido)

- `api/` — endpoints principais (ex.: `calcular.php`, `novo-uuid.php`)
- `ajax/` — scripts PHP consumidos via chamadas AJAX (ex.: `checkout-start.php`)
- `includes/` — partials e templates (`header.php`, `footer.php`, `checkout/`)
- `etapas/` — views por etapa do simulador (desktop e `etapas/mobile/`)
- `js/` — scripts front-end
- `css/` — estilos
- `data/planos.json` — dados de planos usados pelo simulador
- `images/`, `fonts/` — assets estáticos

## Configuração — `config.php` (Local / Dev / Prod)

- Objetivo: garantir que o desenvolvedor não esqueça de informar o ambiente (local, dev, prod) e ajustar flags e credenciais correspondentes.
- Localização do arquivo principal: `config.php` na raiz do projeto.

**Itens que o desenvolvedor deve confirmar em `config.php`:**
- Variável de ambiente/flag (ex.: `ENV = 'local' | 'dev' | 'prod'`)
- `display_errors` / `error_reporting` ajustados conforme ambiente
- Credenciais de API / DB para cada ambiente
- URLs base (ex.: `BASE_URL`, endpoints de teste em dev)

> IMPORTANTE: Ao deployar para produção, `ENV` deve estar em `prod` e `display_errors` DESLIGADO.

## Variáveis de Ambiente & Segredos

- Atualmente as credenciais podem estar em `config.php`. Evite commitar segredos.
- Indicar no `config.php` claramente quais chaves/valores precisam ser substituídos em cada ambiente.

## Endpoints de API (localização e responsabilidades)

- Pasta: `api/`
- Endpoints importantes:
  - `api/calcular.php` — recebe payload da simulação e retorna valores calculados (JSON).
  - `api/novo-uuid.php` — gera UUIDs para sessão/transação.
- Convenção: endpoints retornam JSON para consumo pelo front-end. Colocar nova lógica de backend aqui.

## Rotas AJAX

- Pasta: `ajax/`
- Papéis: chamadas assíncronas do front-end relacionadas ao fluxo de checkout (ex.: iniciar checkout, validar cupom).
- Ex.: `ajax/checkout-start.php` — inicia o processo de checkout via AJAX.

## Páginas e Fluxos Críticos

- `index.php`, `calculo-precificacao.php`, `checkout.php`, `thank-you.php` — páginas principais de navegação.
- Includes principais: `includes/checkout/*` (partials do fluxo de checkout).

## Fluxo: Simulação → Cálculo → Checkout

1. Usuário interage nas `etapas/` (formulários/seleção).
2. Front-end envia requisição para `api/calcular.php` (JSON), recebe cálculo.
3. Após confirmação, inicia-se checkout via `ajax/` ou `checkout.php`.
4. UUIDs gerados por `api/novo-uuid.php` para identificar sessão/transação.

## Geração de UUID

- Endpoint: `api/novo-uuid.php`
- Uso: identificar simulações/transações; garantir idempotência em processos de pagamento.

## Onde adicionar novos Endpoints

- Backend: criar novos arquivos em `api/` com nomes descritivos e retornar JSON consistente.
- Para chamadas assíncronas específicas do front, colocar em `ajax/` e seguir padrões existentes.

## Scripts Front-end e Assets

- Scripts principais: (`js/checkout-start.js`, `js/checkout-review.js`, `js/simulador.js`, etc.)
- Estilos: `css/` — manter consistência com os arquivos existentes (separar por responsabilidade).

## Dados Estáticos

- `data/planos.json` — fonte de truth para planos/valores; atualizar com cuidado.

## Uploads, Cache e Permissões

- Identificar pastas que precisam de permissão de escrita (ex.: uploads temporários). Definir permissões adequadas no servidor.

## Logs e Depuração

- Em ambiente local: habilitar `display_errors` e `error_reporting` em `config.php`.
- Em produção: evitar `display_errors`; logar erros em arquivo seguro e controlar acesso.

## Deploy: checklist rápido

- Ajustar `config.php` para `ENV = 'prod'` e desligar exibição de erros.
- Gerar assets minificados (CSS/JS) e apontar produção para esses arquivos.
- Configurar HTTPS e cabeçalhos de segurança no servidor.
- Fazer backup antes de qualquer alteração em produção.

## Onboarding Rápido para Novos Devs

1. Clonar projeto para `c:\xampp\htdocs\archa-form`.
2. Configurar `config.php` local com `ENV='local'` e credenciais locais.
3. Iniciar XAMPP (Apache/MySQL) e acessar `http://localhost/archa-form/`.
4. Revisar `api/calcular.php`, `api/novo-uuid.php`, `etapas/` e `includes/checkout/` para entender o fluxo.

---

### Arquivos de referência imediata

- `config.php` — configuração por ambiente
- `api/calcular.php` — lógica de cálculo
- `api/novo-uuid.php` — geração de UUID
- `ajax/checkout-start.php` — iniciar checkout via AJAX
- `data/planos.json` — planos


---

> Observação: esta documentação é um ponto de partida. Para alterações maiores de arquitetura (mover segredos para `.env`, usar DB para `planos.json`, introduzir pipeline de assets), registrar mudanças em um documento separado de RFC/Design.

## Analytics & Eventos

- **Arquivo principal:** `js/analytics.js` (ponto único para registros analíticos).  
- **O que faz:** encapsula envios para provedores de analytics (ex.: Google Analytics, Tag Manager, serviços internos) — padroniza payloads, nomes de eventos e evita duplicação no front.

### Eventos principais (resumo curto)
- `page_view`: quando carregar páginas críticas (landing, simulador, checkout, thank-you).  
  - Quando: ao carregar cada página relevante; útil para medir entrada/fonte de tráfego.
- `simulador_start`: quando o usuário inicia o fluxo de simulação (abertura do formulário / primeira interação).  
  - Quando: primeiro passo nas `etapas/` ou ao focar o primeiro input do simulador.
- `simulador_submit` / `simulador_completed`: quando o usuário finaliza a simulação e solicita cálculo.  
  - Quando: no envio do formulário de simulação que chama `api/calcular.php` (ou equivalente). Payload: `uuid`, `resumo_parametros` (opcional), `utm`.
- `calc_response` / `calc_complete`: quando o backend responde com resultados de cálculo.  
  - Quando: após receber resposta de `api/calcular.php` (sucesso ou erro). Payload: `uuid`, `status` (`success`/`error`), `timings` (opcional).
- `checkout_start`: quando o usuário inicia o processo de checkout (início do fluxo de pagamento).  
  - Quando: antes de chamar `ajax/checkout-start.php` (ou imediatamente ao acionar `enviarCheckoutStart()` no front). Payload: `produto_code`, `slug` (se houver), `token` (somente identificador, nunca o `API_TOKEN`).
- `checkout_success` / `checkout_complete`: quando o checkout é concluído com sucesso (redirecionamento para `thank-you.php`).  
  - Quando: no callback de confirmação / página `thank-you.php`.
- `lead_submitted`: quando um lead é enviado para `CONFIG.API_LEAD` (envio de lead).  
  - Quando: após `fetch(CONFIG.API_LEAD)` retornar sucesso. Payload: `lead_id`/`uuid`, `email`.
- `payment_error` / `checkout_error`: quando ocorre erro no fluxo de pagamento.  
  - Quando: em resposta de erro do checkout (códigos 4xx/5xx) — incluir `status` e `message` no payload.
- `coupon_applied`: quando o usuário aplica um cupom no checkout.  
  - Quando: no momento da validação de cupom via AJAX.

### Boas práticas rápidas
- Dispare eventos do `analytics.js` nos pontos de borda do fluxo (início/submit/recebimento/resultado) para facilitar análise de funil.  
- Não inclua segredos (tokens/API keys) nos payloads. Enviar apenas identificadores (`uuid`, `lead_id`, `slug`).
- Padronize nomes e formatos de payload (ex.: `event: 'checkout_start'`, `payload: { uuid, produto_code, source }`).
- Centralize chamadas no `js/analytics.js` para poder trocar provedores sem alterar múltiplos arquivos.
