<?php
// Configuración de Firebase Admin (Kreait)
require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

function firebaseAuth(){
  $serviceAccountPath = __DIR__ . '/firebase-service-account.json'; // coloca aquí tu JSON de servicio
  if(!file_exists($serviceAccountPath)){
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error'=>'No se encontró firebase-service-account.json en /api']); exit;
  }
  $factory = (new Factory)->withServiceAccount($serviceAccountPath);
  return $factory->createAuth();
}
?>