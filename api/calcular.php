<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');
error_reporting(0);
ini_set('display_errors', '0');

/* ===== Entrada ===== */
$uuid = $_POST['uuid'] ?? '';
if (!$uuid) {
  http_response_code(400);
  echo json_encode(['erro' => 'UUID ausente']);
  exit;
}

/* ===== Busca no Laravel (cache Redis) ===== */
$api = api_url_parametros($uuid);           // mesma helper que você já usa
$raw = @file_get_contents($api);
if ($raw === false) {
  http_response_code(404);
  echo json_encode(['erro' => 'Cache não encontrado']);
  exit;
}

$j = json_decode($raw, true);
if (!is_array($j) || ($j['status'] ?? '') !== 'success' || !isset($j['parametros'])) {
  http_response_code(502);
  echo json_encode(['erro' => 'Resposta inválida do backend']);
  exit;
}

/* ===== Só repassa os parâmetros (sem cálculos) ===== */
echo json_encode([
  'parametros' => $j['parametros'],
  'planos'     => null
]);
