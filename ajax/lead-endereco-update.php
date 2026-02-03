<?php

/** ajax/lead-endereco-update.php */
require_once __DIR__ . '/../config.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'method_not_allowed']);
    exit;
}

$raw = file_get_contents('php://input');
if ($raw === false || trim($raw) === '') {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'payload_missing']);
    exit;
}

$ch = curl_init(api_url('lead/endereco')); // <<< rota
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => api_headers(),
    CURLOPT_POSTFIELDS     => $raw,
    CURLOPT_TIMEOUT        => 20,
]);

$resp = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 500;
$err  = curl_error($ch);
curl_close($ch);

header('Content-Type: application/json; charset=utf-8');

if ($resp === false) {
    http_response_code(500);
    echo json_encode(['error' => 'proxy_failed', 'detail' => $err]);
    exit;
}

http_response_code($http);


echo $resp;
