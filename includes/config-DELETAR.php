<?php
// Ambiente: 'local' | 'dev' | 'prod'
define('API_ENV', 'local');

$API_ROOTS = [
    'local' => 'http://127.0.0.1:8000/api',
    'dev'   => 'https://dev.archa.pro/api',
    'prod'  => 'https://archa.pro/api',
];

define('API_BASE', $API_ROOTS[API_ENV]);
define ('BRIDGE_DEBUG', true); // true para logar erros no bridge.php

// Token secreto (NÃO expor no front). Ideal: vir de .env
define('API_TOKEN', 'sbDvcQEBipLSP2IkXRDwyzJdmvk2APeKJcCBUEvmD3LqLjs0xLI7tQZmcBhJvts9');

// ===== Helpers API =====
function api_url(string $path): string {
    $path = ltrim($path, '/');
    return API_BASE . '/' . $path;
}
function api_headers(): array {
    return [
        'Accept: application/json',
        'Content-Type: application/json',
        'x-api-token: ' . API_TOKEN,
    ];
}

// ===== Detecção de mobile =====
function is_mobile(): bool {
    return isset($_SERVER['HTTP_USER_AGENT']) &&
        preg_match('/(android|iphone|ipad|ipod|windows phone|mobile)/i', $_SERVER['HTTP_USER_AGENT']);
}
define('IS_MOBILE', is_mobile());

// ===== Base pública do app (subpasta segura) =====
// Ex.: se o app vive em http://127.0.0.1/archa-form, APP_BASE = "/archa-form"
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\');
define('APP_BASE', $scriptDir === '' ? '/' : $scriptDir);

// (legado)
define('BASE_URL', APP_BASE);
define('ROOT_PATH', dirname(__DIR__)); // ajuste conforme sua estrutura
define('SEC_DIR', IS_MOBILE ? ROOT_PATH . '/etapas/mobile/' : ROOT_PATH . '/etapas/');

// Conveniência pra injetar no <head>
function print_app_base_meta(): void {
    echo '<meta name="app-base" content="', htmlspecialchars(APP_BASE, ENT_QUOTES), '">', PHP_EOL;
}
