<?php
// Debug-friendly set_password endpoint
ini_set('display_errors', 0);
ob_start();
session_start();
require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');
try{
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  if(!$data || empty($data['password'])){
    http_response_code(400);
    echo json_encode(['error'=>'Falta password']);
    exit;
  }

  // Log session and input for debugging (dev only)
  error_log('set_password session: ' . json_encode($_SESSION));
  error_log('set_password input (no password): ' . json_encode(array_diff_key($data, ['password'=>''])));

  if(!isset($_SESSION['user_db_id'])){
    http_response_code(401);
    echo json_encode(['error'=>'No autorizado: no hay sesión PHP. Asegura que las cookies se envían en la petición (fetch con credentials).']);
    exit;
  }

  $id = intval($_SESSION['user_db_id']);
  $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

  // obtain current name if not provided
  $nombre = isset($data['nombre']) ? trim($data['nombre']) : null;
  if($nombre === null){
    $stmt2 = $db->prepare('SELECT nombre FROM usuarios WHERE id = ?');
    $stmt2->execute([$id]);
    $row = $stmt2->fetch(PDO::FETCH_ASSOC);
    $nombre = $row['nombre'] ?? '';
  }

  $stmt = $db->prepare('UPDATE usuarios SET password = ?, nombre = ? WHERE id = ?');
  $ok = $stmt->execute([$password_hash, $nombre, $id]);

  if($ok){
    $buf = ob_get_clean(); if($buf) error_log('set_password unexpected output: '. $buf);
    echo json_encode(['ok'=>true]);
    exit;
  } else {
    $err = $db->errorInfo();
    error_log('set_password DB error: ' . json_encode($err));
    $buf = ob_get_clean(); if($buf) error_log('set_password unexpected output: '. $buf);
    http_response_code(500);
    echo json_encode(['error'=>'No fue posible establecer la contraseña (DB).', 'db_error'=>$err]);
    exit;
  }
} catch(Exception $e){
  error_log('set_password exception: ' . $e->getMessage());
  $buf = ob_get_clean(); if($buf) error_log('set_password unexpected output: '. $buf);
  http_response_code(500);
  echo json_encode(['error'=>'Error al guardar contraseña', 'details'=>$e->getMessage()]);
}
