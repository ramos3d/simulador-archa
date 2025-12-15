<?php

/**
 * Configuração única da aplicação (raiz)
 * - Unifica a antiga includes/config.php
 * - Evita divergência de ambientes/local vs dev vs prod
 * - Exponibiliza metas para o front (api-base, api-token, app-base)
 */

// ===============================
// Ambiente e credenciais
// ===============================

// Podemos definir APP_ENV/ API_ENV via server (.env/Apache/Nginx). Fallback: 'local'
$ENV = getenv('APP_ENV') ?: getenv('API_ENV') ?: 'local';
//echo "ENVIRONMENT" . $ENV;
$ENV = in_array($ENV, ['local', 'dev', 'prod'], true) ? $ENV : 'local';


if (!defined('API_ENV')) {
  define('API_ENV', $ENV);
}

// Raízes por ambiente (mantenha estes valores)
$API_ROOTS = [
  'local' => 'http://127.0.0.1:8000/api',
  'dev'   => 'https://dev.archa.pro/api',
  'prod'  => 'https://archa.pro/api',
];

// Base usada nos endpoints (ex: https://.../api)
if (!defined('API_BASE')) {
  define('API_BASE', $API_ROOTS[API_ENV]);
}

// Token secreto da API (NÃO expor em repositório público). Ideal vir de env.
if (!defined('API_TOKEN')) {
  define('API_TOKEN', getenv('API_TOKEN') ?: 'sbDvcQEBipLSP2IkXRDwyzJdmvk2APeKJcCBUEvmD3LqLjs0xLI7tQZmcBhJvts9');
}

// Flag de debug do bridge (se você ainda usa bridge.php)
if (!defined('BRIDGE_DEBUG')) {
  define('BRIDGE_DEBUG', true);
}


// ===============================
// Helpers de API
// ===============================
if (!function_exists('api_url')) {
  function api_url(string $path): string
  {
    $path = ltrim($path, '/');
    return rtrim(API_BASE, '/') . '/' . $path;
  }
}

if (!function_exists('api_headers')) {
  function api_headers(): array
  {
    return [
      'Accept: application/json',
      'Content-Type: application/json',
      'X-API-TOKEN: ' . API_TOKEN, // padronizado (maiúsculo)
    ];
  }
}


// ===============================
// Detecção de mobile e paths base
// ===============================
if (!function_exists('is_mobile')) {
  function is_mobile(): bool
  {
    return isset($_SERVER['HTTP_USER_AGENT']) &&
      preg_match('/(android|iphone|ipad|ipod|windows phone|mobile)/i', $_SERVER['HTTP_USER_AGENT']);
  }
}
if (!defined('IS_MOBILE')) {
  define('IS_MOBILE', is_mobile());
}

// Base pública onde o app está servido (subpasta). Ex.: "/archa-form"
if (!defined('APP_BASE')) {
  $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\');
  define('APP_BASE', $scriptDir === '' ? '/' : $scriptDir);
}

// Legados usados em includes/views
if (!defined('BASE_URL'))  define('BASE_URL', APP_BASE);
if (!defined('ROOT_PATH')) define('ROOT_PATH', __DIR__); // raiz do projeto (ajuste se necessário)
if (!defined('SEC_DIR'))   define('SEC_DIR', IS_MOBILE ? ROOT_PATH . '/etapas/mobile/' : ROOT_PATH . '/etapas/');


// ===============================
// Metas para o front (consumidas pelo JS)
// ===============================
if (!function_exists('emit_app_meta')) {
  /**
   * Emite metas lidas no front (config.js):
   * - app-base: base pública do app (subpasta)
   * - api-base: URL base da API
   * - api-token: token para chamadas autenticadas
   */

  function emit_app_meta(?string $appBase = null, bool $exposeApiToken = false): void
  {
    $appBase = $appBase ?? APP_BASE;
    echo '<meta name="app-base" content="', htmlspecialchars($appBase, ENT_QUOTES), '">', PHP_EOL;
    echo '<meta name="api-base" content="', htmlspecialchars(API_BASE, ENT_QUOTES), '">', PHP_EOL;
    if ($exposeApiToken) { // manter a opção, mas padrão = false
      echo '<meta name="api-token" content="', htmlspecialchars(API_TOKEN, ENT_QUOTES), '">', PHP_EOL;
    }
  }
}



if (!function_exists('emit_seo_indexing')) {
  function emit_seo_indexing(string $canonicalProdUrl): void
  {
    if (API_ENV !== 'prod') {
      echo '<meta name="robots" content="noindex,nofollow">', PHP_EOL;
    } else {
      echo '<link rel="canonical" href="', htmlspecialchars($canonicalProdUrl, ENT_QUOTES), '">', PHP_EOL;
    }
  }
}


/** Helper dinâmico: /parametros/{uuid} */
if (!function_exists('api_url_parametros')) {
  function api_url_parametros(string $uuid): string
  {
    return api_url("/parametros/{$uuid}");
  }
}


// ===============================
// Google Tag Manager helpers
// ===============================
if (!function_exists('emit_gtm_head')) {
  function emit_gtm_head(): void
  {
    if (API_ENV === 'prod') {
      // ajuste a extensão se usar .js
      include ROOT_PATH . '/includes/google-tag-manager-HEAD.php';
    }
  }
}

if (!function_exists('emit_gtm_body')) {
  function emit_gtm_body(): void
  {
    if (API_ENV === 'prod') {
      include ROOT_PATH . '/includes/google-tag-manager-BODY.php';
    }
  }
}
