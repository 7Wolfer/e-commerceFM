<?php
// Ajusta si tu contraseña/root difiere
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'fruteria_madrid';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(['error'=>'Error de conexión: '.$mysqli->connect_error]);
  exit;
}
$mysqli->set_charset('utf8mb4');

function json_response($data){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
?>