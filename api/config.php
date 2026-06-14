<?php
// Conexión a la base de datos.
// En local (XAMPP) usa los valores por defecto; en producción se leen de
// variables de entorno (ver .env.example y el README → Deploy).
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_NAME = getenv('DB_NAME') ?: 'fruteria_madrid';
$DB_PORT = (int) (getenv('DB_PORT') ?: 3306);

$mysqli = mysqli_init();

// Conexión cifrada (TLS), requerida por bases gratuitas como TiDB Cloud o Aiven.
// Actívala con DB_SSL=1; DB_SSL_CA puede apuntar al certificado CA del sistema.
$useSsl = (bool) getenv('DB_SSL');
if ($useSsl) {
  $ca = getenv('DB_SSL_CA') ?: null;
  $mysqli->ssl_set(null, null, $ca, null, null);
}

@$mysqli->real_connect(
  $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT, null,
  $useSsl ? MYSQLI_CLIENT_SSL : 0
);

if ($mysqli->connect_errno) {
  error_log('[DB] ' . $mysqli->connect_error);
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['error' => 'Error de conexión a la base de datos']);
  exit;
}
$mysqli->set_charset('utf8mb4');

function json_response($data){
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
?>