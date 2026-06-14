<?php
// Configuración de Firebase Admin (Kreait)
require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

function firebaseAuth(){
  // Producción: el JSON completo de la cuenta de servicio viene en la variable
  // de entorno FIREBASE_SERVICE_ACCOUNT. Local: el archivo en /api.
  $saJson = getenv('FIREBASE_SERVICE_ACCOUNT');
  $saPath = __DIR__ . '/firebase-service-account.json';

  if ($saJson) {
    // kreait necesita una ruta de archivo: escribimos el JSON en un temporal.
    $tmp = sys_get_temp_dir() . '/firebase-service-account.json';
    if (!file_exists($tmp)) {
      file_put_contents($tmp, $saJson);
    }
    $saPath = $tmp;
  }

  if (!file_exists($saPath)) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Falta la credencial de Firebase (FIREBASE_SERVICE_ACCOUNT o api/firebase-service-account.json)']);
    exit;
  }

  $factory = (new Factory)->withServiceAccount($saPath);
  return $factory->createAuth();
}
?>