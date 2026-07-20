<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

function json_in(): array
{
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $j = json_decode($raw, true);
    return is_array($j) ? $j : [];
}

function out(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function call(string $method, string $path, ?array $body = null): array
{
    $url = api_url($path);
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_HTTPHEADER     => api_headers(),
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
        CURLOPT_HEADER         => true,
        CURLOPT_USERAGENT      => 'ArchaBridge/2.0',
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE));
    }
    $resp = curl_exec($ch);
    if ($resp === false) {
        $err = curl_error($ch); curl_close($ch);
        return ['status' => 502, 'json' => ['error' => 'gateway_error', 'detail' => $err]];
    }
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $status     = curl_getinfo($ch, CURLINFO_RESPONSE_CODE) ?: 500;
    $rawBody    = substr($resp, $headerSize);
    curl_close($ch);
    $json = json_decode($rawBody, true);
    if ($json === null) $json = ['raw' => $rawBody];
    return ['status' => $status, 'json' => $json];
}

function valid_slug(?string $s): bool
{
    return $s !== null && $s !== '' && preg_match('/^[A-Za-z0-9_-]{5,20}$/', $s) === 1;
}

$op   = str_replace(['/', '-'], '_', ltrim(preg_replace('#^/?api/#i', '', trim($_GET['op'] ?? '')), '/'));
$slug = $_GET['slug'] ?? null;

try {
    switch ($op) {
        case 'editor_show':
            if (!valid_slug($slug)) out(['error' => 'slug inválido'], 422);
            $r = call('GET', "editor/{$slug}");
            out($r['json'], $r['status']);

        case 'editor_update':
            if (!valid_slug($slug)) out(['error' => 'slug inválido'], 422);
            $r = call('PATCH', "editor/{$slug}", json_in());
            out($r['json'], $r['status']);

        case 'checkout_card':
            $p = json_in();
            if (!valid_slug($p['slug'] ?? null)) out(['error' => 'slug ausente/inválido'], 422);
            $r = call('POST', 'checkout/card', $p);
            out($r['json'], $r['status']);

        case 'checkout_pix':
            $p = json_in();
            if (!valid_slug($p['slug'] ?? null)) out(['error' => 'slug ausente/inválido'], 422);
            $r = call('POST', 'checkout/pix', $p);
            out($r['json'], $r['status']);

        case 'checkout_doc':
            $p = json_in();
            if (!valid_slug($p['slug'] ?? null)) out(['error' => 'slug ausente/inválido'], 422);
            $r = call('POST', 'checkout/doc', $p);
            out($r['json'], $r['status']);

        case 'checkout_entrada_pix':
            $p = json_in();
            if (!valid_slug($p['slug'] ?? null)) out(['error' => 'slug ausente/inválido'], 422);
            $r = call('POST', 'checkout/entrada/pix', $p);
            out($r['json'], $r['status']);

        case 'checkout_entrada_card':
            $p = json_in();
            if (!valid_slug($p['slug'] ?? null)) out(['error' => 'slug ausente/inválido'], 422);
            $r = call('POST', 'checkout/entrada/card', $p);
            out($r['json'], $r['status']);

        case 'checkout':
        case 'checkout_show':
            if (!valid_slug($slug)) out(['error' => 'slug inválido'], 422);
            $r = call('GET', "checkout/{$slug}");
            out($r['json'], $r['status']);

        default:
            out(['error' => 'operação não suportada', 'received' => $_GET['op'] ?? '', 'normalized' => $op], 400);
    }
} catch (Throwable $e) {
    out(['error' => 'exception', 'detail' => $e->getMessage()], 500);
}
