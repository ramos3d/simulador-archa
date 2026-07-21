<?php
declare(strict_types=1);

/**
 * Configuração única do archa-checkout.
 * Estrutura espelhada do legado /archa-form/config.php para coerência de deploy.
 * Em produção (sem env var), o fallback é 'prod'.
 */

// ===============================
// Ambiente e credenciais
// ===============================
$ENV = getenv('APP_ENV') ?: getenv('API_ENV') ?: '';
if (!$ENV) {
    // Fallback por hostname (sem env var): 127.0.0.1/localhost => local; resto => prod.
    $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
    if ($host === '' || str_starts_with($host, '127.0.0.1') || str_starts_with($host, 'localhost')) {
        $ENV = 'local';
    } elseif (str_contains($host, 'dev.archa.pro')) {
        $ENV = 'dev';
    } else {
        $ENV = 'prod';
    }
}
$ENV = in_array($ENV, ['local', 'dev', 'prod'], true) ? $ENV : 'prod';

if (!defined('API_ENV')) define('API_ENV', $ENV);
if (!defined('APP_ENV')) define('APP_ENV', $ENV); // alias usado por includes do checkout

$API_ROOTS = [
    'local' => 'http://127.0.0.1:8000/api',
    'dev'   => 'https://dev.archa.pro/api',
    'prod'  => 'https://archa.pro/api',
];

if (!defined('API_BASE'))  define('API_BASE',  $API_ROOTS[API_ENV]);
if (!defined('API_TOKEN')) define('API_TOKEN', getenv('API_TOKEN') ?: 'sbDvcQEBipLSP2IkXRDwyzJdmvk2APeKJcCBUEvmD3LqLjs0xLI7tQZmcBhJvts9');

if (!defined('BRIDGE_DEBUG')) define('BRIDGE_DEBUG', API_ENV !== 'prod');

// ===============================
// Helpers de API
// ===============================
if (!function_exists('api_url')) {
    function api_url(string $path): string {
        return rtrim(API_BASE, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('api_headers')) {
    function api_headers(): array {
        return [
            'Accept: application/json',
            'Content-Type: application/json',
            'X-API-TOKEN: ' . API_TOKEN,
        ];
    }
}

// ===============================
// Detecção de mobile e paths base
// ===============================
if (!function_exists('is_mobile')) {
    function is_mobile(): bool {
        return isset($_SERVER['HTTP_USER_AGENT']) &&
            (bool) preg_match('/(android|iphone|ipad|ipod|windows phone|mobile)/i', $_SERVER['HTTP_USER_AGENT']);
    }
}
if (!defined('IS_MOBILE')) define('IS_MOBILE', is_mobile());

if (!defined('APP_ROOT'))    define('APP_ROOT', __DIR__);
if (!defined('ROOT_PATH'))   define('ROOT_PATH', __DIR__);

if (!defined('APP_BASE')) {
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\');
    define('APP_BASE', $scriptDir === '' ? '/' : $scriptDir);
}

/**
 * Caminhos do legado (CSS, JS, etapas, GTM).
 * - Local (XAMPP): pasta irmã /archa-form
 * - Produção: tudo coexiste na mesma pasta (projeto-de-arquitetura-online/)
 *   A detecção é pela presença real do diretório irmão.
 */
$legacyFsLocal = APP_ROOT . '/../archa-form';
if (is_dir($legacyFsLocal)) {
    if (!defined('LEGACY_FS'))  define('LEGACY_FS',  realpath($legacyFsLocal));
    if (!defined('LEGACY_URL')) define('LEGACY_URL', '/archa-form');
} else {
    if (!defined('LEGACY_FS'))  define('LEGACY_FS',  APP_ROOT);
    if (!defined('LEGACY_URL')) define('LEGACY_URL', APP_BASE === '/' ? '' : APP_BASE);
}

// Compat: ASSETS_BASE continua usável onde já está referenciado.
if (!defined('ASSETS_BASE')) define('ASSETS_BASE', LEGACY_URL);
// Site institucional (links de termos, navbar)
if (!defined('BASE_URL'))    define('BASE_URL',   'https://archa.com.br');

// ===============================
// Metas para o front
// ===============================
if (!function_exists('emit_app_meta')) {
    /**
     * @param string|null $appBase  Quando null, usa APP_BASE.
     * @param bool        $exposeApiToken  Se true, emite <meta api-token> (use só onde necessário).
     *                                     Por padrão NÃO expõe — o front roteia por bridge.php.
     */
    function emit_app_meta(?string $appBase = null, bool $exposeApiToken = false): void {
        $appBase = $appBase ?? APP_BASE;
        echo '<meta name="app-base" content="', htmlspecialchars($appBase, ENT_QUOTES), '">', PHP_EOL;
        echo '<meta name="api-base" content="', htmlspecialchars(API_BASE, ENT_QUOTES), '">', PHP_EOL;
        if ($exposeApiToken) {
            echo '<meta name="api-token" content="', htmlspecialchars(API_TOKEN, ENT_QUOTES), '">', PHP_EOL;
        }
    }
}

if (!function_exists('emit_robots')) {
    function emit_robots(): void {
        if (API_ENV !== 'prod') {
            echo '<meta name="robots" content="noindex,nofollow">', PHP_EOL;
        }
    }
}

if (!function_exists('emit_seo_indexing')) {
    function emit_seo_indexing(string $canonicalProdUrl): void {
        if (API_ENV !== 'prod') {
            echo '<meta name="robots" content="noindex,nofollow">', PHP_EOL;
        } else {
            echo '<link rel="canonical" href="', htmlspecialchars($canonicalProdUrl, ENT_QUOTES), '">', PHP_EOL;
        }
    }
}

if (!function_exists('api_url_parametros')) {
    function api_url_parametros(string $uuid): string {
        return api_url("/parametros/{$uuid}");
    }
}

// ===============================
// Idioma (PT/EN) — PT é o idioma fonte (fica direto no código),
// EN vive em lang/en.php. Chave do dicionario = texto original em PT.
// ===============================
if (!defined('SIM_LANG')) {
    $simLangParam = $_GET['lang'] ?? null;
    if ($simLangParam === 'en' || $simLangParam === 'pt') {
        $simLang = $simLangParam;
    } else {
        $simLang = ($_COOKIE['sim_lang'] ?? 'pt') === 'en' ? 'en' : 'pt';
    }
    define('SIM_LANG', $simLang);

    if (($_COOKIE['sim_lang'] ?? null) !== SIM_LANG && !headers_sent()) {
        setcookie('sim_lang', SIM_LANG, [
            'expires'  => time() + 60 * 60 * 24 * 365,
            'path'     => '/',
            'samesite' => 'Lax',
        ]);
    }
}

if (!isset($GLOBALS['SIM_EN_DICT'])) {
    $GLOBALS['SIM_EN_DICT'] = SIM_LANG === 'en' ? require APP_ROOT . '/lang/en.php' : [];
}

if (!function_exists('t')) {
    function t(string $pt, array $vars = []): string {
        $text = $GLOBALS['SIM_EN_DICT'][$pt] ?? $pt;
        foreach ($vars as $k => $v) $text = str_replace('{' . $k . '}', (string) $v, $text);
        return $text;
    }
}

if (!function_exists('emit_jt_bootstrap')) {
    /** Injeta window.SIM_LANG/SIM_STRINGS/jt() — equivalente em JS do t() do PHP, mesmo dicionario. */
    function emit_jt_bootstrap(): void {
        echo '<script>', PHP_EOL;
        echo '  window.SIM_LANG = ', json_encode(SIM_LANG), ';', PHP_EOL;
        echo '  window.SIM_STRINGS = ', json_encode($GLOBALS['SIM_EN_DICT'], JSON_UNESCAPED_UNICODE), ';', PHP_EOL;
        echo '  window.jt = function (pt, vars) {', PHP_EOL;
        echo '    var s = (window.SIM_STRINGS && window.SIM_STRINGS[pt]) || pt;', PHP_EOL;
        echo "    if (vars) { for (var k in vars) s = s.split('{' + k + '}').join(vars[k]); }", PHP_EOL;
        echo '    return s;', PHP_EOL;
        echo '  };', PHP_EOL;
        echo '</script>', PHP_EOL;
    }
}

// ===============================
// Google Tag Manager
// ===============================
if (!function_exists('emit_gtm_head')) {
    function emit_gtm_head(): void {
        if (API_ENV === 'prod') {
            $f = LEGACY_FS . '/includes/google-tag-manager-HEAD.php';
            if (file_exists($f)) include $f;
        }
    }
}

if (!function_exists('emit_gtm_body')) {
    function emit_gtm_body(): void {
        if (API_ENV === 'prod') {
            $f = LEGACY_FS . '/includes/google-tag-manager-BODY.php';
            if (file_exists($f)) include $f;
        }
    }
}
