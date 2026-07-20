<?php
declare(strict_types=1);
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'method_not_allowed']);
    exit;
}

$raw  = file_get_contents('php://input');
$body = $raw ? json_decode($raw, true) : null;

if (!is_array($body)) {
    http_response_code(400);
    echo json_encode(['error' => 'payload_missing']);
    exit;
}

// Valida campos obrigatórios específicos da modalidade entrada
$slug           = trim($body['slug'] ?? '');
$entradaForma   = trim($body['entrada_forma'] ?? '');   // pix | cartao
$saldoParcelas  = (int) ($body['saldo_parcelas'] ?? 0); // 2-10

if (!$slug || !preg_match('/^[A-Za-z0-9_-]{5,20}$/', $slug)) {
    http_response_code(422);
    echo json_encode(['error' => 'slug ausente ou inválido']);
    exit;
}

if (!in_array($entradaForma, ['pix', 'cartao'], true)) {
    http_response_code(422);
    echo json_encode(['error' => 'entrada_forma deve ser pix ou cartao']);
    exit;
}

if ($saldoParcelas < 2 || $saldoParcelas > 10) {
    http_response_code(422);
    echo json_encode(['error' => 'saldo_parcelas deve ser entre 2 e 10']);
    exit;
}

// Determina endpoint de pagamento da entrada
$path = $entradaForma === 'pix' ? 'checkout/entrada/pix' : 'checkout/entrada/card';

$ch = curl_init(api_url($path));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => api_headers(),
    CURLOPT_POSTFIELDS     => json_encode($body, JSON_UNESCAPED_UNICODE),
    CURLOPT_TIMEOUT        => 30,
]);
$resp = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 500;
$err  = curl_error($ch);
curl_close($ch);

if ($resp === false) {
    http_response_code(502);
    echo json_encode(['error' => 'gateway_error', 'detail' => $err]);
    exit;
}

http_response_code($http);
echo $resp;
