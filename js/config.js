/*
  Configurações gerais do sistema
  config.js — agora sem lógica de ambiente duplicada.
  Lê tudo dos <meta> emitidos pelo PHP.
*/

// Lê <meta> definidos por emit_app_meta() no PHP
const API_BASE = (document.querySelector('meta[name="api-base"]')?.content || '').replace(/\/+$/, ''); // já vem com /api
const API_TOKEN = document.querySelector('meta[name="api-token"]')?.content ?? '';
const APP_BASE = (document.querySelector('meta[name="app-base"]')?.content || '').replace(/\/+$/, ''); // base pública do app

// Mantém compatibilidade com nomes já usados

const BASE = API_BASE.replace(/\/api$/, ''); // caso alguém ainda use BASE sem /api

// Exponha CONFIG
const CONFIG = {
  /* legado — não será mais chamado */
  API_FORM: 'api/calcular.php',

  /* novos fluxos (usando API_BASE diretamente) */
  API_CACHE: `${API_BASE}/gerar-cache`,
  API_LEAD: `${API_BASE}/leads`,
  API_LEAD_CACHE: `${API_BASE}/lead/cache`,
  apiParametros: (uuid) => `${API_BASE}/parametros/${uuid}`,
  API_CONTRATAR: `${API_BASE}/opcao/contratar`,
  API_CHECKOUT_CACHE: `${API_BASE}/checkouts/cache-only`,

  // mantém ponte com PHP (sem quebrar nada)
  API_CHECKOUT_START: `${APP_BASE}/ajax/checkout-start.php`,
  CHECKOUT_URL: `${APP_BASE}/checkout.php`,

  // se algum script usa diretamente o token:
  API_TOKEN
};

// Opcional: expor no window p/ legados que esperam essas refs
window.API_BASE = API_BASE;
window.API_TOKEN = API_TOKEN;
window.APP_BASE = APP_BASE;
window.CONFIG = CONFIG;

