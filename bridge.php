<?php

/**
 * bridge.php — Proxy server-side p/ API Laravel.
 * Mantém X-API-TOKEN no servidor e aceita múltiplos formatos de `op`.
 */

declare(strict_types=1);

// 1) Usa a config unificada da RAIZ (não mais includes/config.php)
require_once __DIR__ . '/config.php';

// 2) Utilitários básicos
function json_input(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') return [];
    $j = json_decode($raw, true);
    return is_array($j) ? $j : [];
}
function out_json($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 3) Chamada HTTP para sua API Laravel
 *    IMPORTANTE: aqui usamos a constante API_BASE do config.php (já contém /api)
 *    Ex.: api_call('POST', '/checkout/card', $payload) -> API_BASE.'/checkout/card'
 */
function api_call(string $method, string $path, array $body = null): array
{
    $url = rtrim(API_BASE, '/') . '/' . ltrim($path, '/');

    $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
        'X-API-TOKEN: ' . API_TOKEN,
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_HEADER         => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_USERAGENT      => 'ArchaBridge/1.1',
    ]);

    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE));
    }

    $resp = curl_exec($ch);
    if ($resp === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return ['status' => 502, 'json' => ['error' => 'Bad Gateway', 'detail' => $err]];
    }

    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $status     = curl_getinfo($ch, CURLINFO_RESPONSE_CODE) ?: 500;
    $bodyRaw    = substr($resp, $headerSize);
    curl_close($ch);

    $json = json_decode($bodyRaw, true);
    if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
        $json = ['raw' => $bodyRaw];
    }

    if (defined('BRIDGE_DEBUG') && BRIDGE_DEBUG) {
        $json['_bridge_debug'] = [
            'url'     => $url,
            'method'  => $method,
            'status'  => $status,
            'headers' => ['Accept', 'Content-Type', 'X-API-TOKEN: ****'],
        ];
    }

    return ['status' => $status, 'json' => $json];
}

/**
 * 4) Normalização de `op` para manter compatibilidade:
 *    - aceita: /api/checkout/card | checkout/card | checkout_card
 *    - mapeia p/ chaves canônicas: editor_show, editor_update, checkout_card, checkout_pix, checkout_show
 */
$opRaw = $_GET['op'] ?? '';
$slug  = $_GET['slug'] ?? null;

$op = trim((string)$opRaw);
$op = preg_replace('#^/+#', '', $op);             // remove / inicial
$op = (stripos($op, 'api/') === 0) ? substr($op, 4) : $op;   // remove "api/" se vier
$op = str_replace('-', '_', $op);

// se vier com barras, converte p/ underscore
$opCanon = str_replace('/', '_', $op);
// casos comuns que chegam sem barra
if ($opCanon === '') $opCanon = '';

// 5) Router
try {
    switch ($opCanon) {

        // GET editor/{slug}
        case 'editor_show': {
                if (!$slug || !preg_match('/^[A-Za-z0-9_-]{5,20}$/', $slug)) {
                    out_json(['error' => 'slug inválido'], 422);
                }
                $res = api_call('GET', "/editor/" . urlencode($slug));
                $j   = is_array($res['json']) ? $res['json'] : [];

                // enriquecimento leve (mesmo do seu bridge anterior)
                $code   = $j['produto_code'] ?? null;
                $vistos = $j['precos_vistos'] ?? null;
                $fmt2   = static fn($x) => is_numeric($x) ? round((float)$x, 2) : null;
                $fromV  = (is_array($vistos) && $code && isset($vistos[$code]) && is_array($vistos[$code])) ? $vistos[$code] : null;

                if (!isset($j['valor_avista']) && $fromV)           $j['valor_avista'] = $fmt2($fromV['avista'] ?? null);
                if (!isset($j['valor_parcelado_total']) && $fromV)  $j['valor_parcelado_total'] = $fmt2($fromV['parcelado_total'] ?? null);
                if (!isset($j['valor_parcela']) && $fromV)          $j['valor_parcela'] = $fmt2($fromV['parcela'] ?? null);

                $subtotal = $fmt2($j['valor_subtotal'] ?? $j['valor_parcelado_total'] ?? null);
                $total    = $fmt2($j['valor_total']    ?? $j['valor_avista']          ?? null);
                if ($subtotal !== null && $total !== null) {
                    $desconto = max(0, $fmt2($subtotal - $total));
                    $j['valor_subtotal'] = $j['valor_subtotal'] ?? $subtotal;
                    $j['valor_total']    = $j['valor_total']    ?? $total;
                    $j['valor_desconto'] = $j['valor_desconto'] ?? $desconto;
                }

                out_json($j, $res['status']);
            }

            // PATCH editor/{slug}
        case 'editor_update': {
                if (!$slug || !preg_match('/^[A-Za-z0-9_-]{5,20}$/', $slug)) {
                    out_json(['error' => 'slug inválido'], 422);
                }
                $payload = json_input();
                $res = api_call('PATCH', "/editor/" . urlencode($slug), $payload);
                out_json($res['json'], $res['status']);
            }

            // POST checkout/card
        case 'checkout_card': {
                $payload = json_input();
                if (empty($payload['slug']) || !preg_match('/^[A-Za-z0-9_-]{5,20}$/', $payload['slug'])) {
                    out_json(['error' => 'slug ausente/ inválido'], 422);
                }
                $res = api_call('POST', "/checkout/card", $payload);
                out_json($res['json'], $res['status']);
            }

            // POST checkout/pix
        case 'checkout_pix': {
                $payload = json_input();
                if (empty($payload['slug']) || !preg_match('/^[A-Za-z0-9_-]{5,20}$/', $payload['slug'])) {
                    out_json(['error' => 'slug ausente/ inválido'], 422);
                }
                $res = api_call('POST', "/checkout/pix", $payload);
                out_json($res['json'], $res['status']);
            }

            // GET checkout/{slug} — útil p/ quem chamar pelo bridge
        case 'checkout':
        case 'checkout_show': {
                if (!$slug || !preg_match('/^[A-Za-z0-9_-]{5,20}$/', $slug)) {
                    out_json(['error' => 'slug inválido'], 422);
                }
                $res = api_call('GET', "/checkout/" . urlencode($slug));
                out_json($res['json'], $res['status']);
            }

            // POST checkout/doc  — atualizar documento no checkout
        case 'checkout_doc': {
                $payload = json_input();
                if (empty($payload['slug']) || !preg_match('/^[A-Za-z0-9_-]{5,20}$/', $payload['slug'])) {
                    out_json(['error' => 'slug ausente/ inválido'], 422);
                }
                $res = api_call('POST', "/checkout/doc", $payload);
                out_json($res['json'], $res['status']);
            }



        default:
            // ajuda no diagnóstico mostrando como normalizamos o `op`
            out_json([
                'error' => 'operação não suportada',
                'received' => $opRaw,
                'normalized' => $opCanon,
                'ops' => ['editor_show', 'editor_update', 'checkout_card', 'checkout_pix', 'checkout_show']
            ], 400);
    }
} catch (Throwable $e) {
    out_json(['error' => 'exception', 'detail' => $e->getMessage()], 500);
}
