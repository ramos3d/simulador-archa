<?php
/* REFATORADO */
require_once __DIR__ . '/../config.php';

// Mantém nomes/assinaturas existentes
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST') {
  $raw = file_get_contents('php://input');
  if ($raw === false || $raw === '') {
    http_response_code(400);
    echo json_encode(['error' => 'payload missing']);
    exit;
  }

  $ch = curl_init(api_url('checkout/start'));
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

  if ($resp === false) {
    http_response_code(502);
    echo json_encode(['error' => 'gateway error', 'detail' => $err]);
    exit;
  }

  http_response_code($http);
  echo $resp;
  exit;
}

if ($method === 'GET') {
  $slug = trim($_GET['slug'] ?? '');
  if ($slug === '') {
    http_response_code(400);
    echo json_encode(['error' => 'slug ausente']);
    exit;
  }

  $ch = curl_init(api_url("checkout/{$slug}"));
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => api_headers(),
    CURLOPT_TIMEOUT        => 15,
  ]);
  $resp = curl_exec($ch);
  $http = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 500;
  curl_close($ch);

  http_response_code($http);
  echo $resp ?: json_encode(['error' => 'sem resposta']);
  exit;
}

http_response_code(405);
echo json_encode(['error' => 'method not allowed']);
