<?php
// Prevent accidental HTML/PHP warnings from breaking JSON output
ini_set('display_errors', 0);
ob_start();
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config_firebase.php';
require_once __DIR__ . '/config.php'; // para DB (opcional upsert)

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if(!$data || !isset($data['idToken'])){
  http_response_code(400);
  // clear buffer then respond
  $buf = ob_get_clean(); if($buf) error_log('login_verify unexpected output: '. $buf);
  echo json_encode(['error'=>'Falta idToken']); exit;
}

try{
  $auth = firebaseAuth();
  $verifiedIdToken = $auth->verifyIdToken($data['idToken']);
  $uid = $verifiedIdToken->claims()->get('sub');

  // Obtener datos del usuario desde Firebase
  $userRecord = $auth->getUser($uid);
  $email = $userRecord->email ?? '';
  $name = $userRecord->displayName ?? '';
  $photo = $userRecord->photoUrl ?? '';
  $providerId = $userRecord->providerData[0]->providerId ?? 'firebase';

  // Crear sesión segura
  $_SESSION['user'] = [
    'uid' => $uid, 'name' => $name, 'email' => $email,
    'photoURL' => $photo, 'provider' => $providerId
  ];

  // Upsert en tabla usuarios por email (si hay email)
  if($email){
    // Use PDO $db from config.php (avoid undefined $mysqli)
    try {
      $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
      $stmt->execute([$email]);
      if ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $id = intval($row['id']);
        $up = $db->prepare("UPDATE usuarios SET nombre = ? WHERE id = ?");
        $up->execute([$name, $id]);
        $_SESSION['user_db_id'] = $id;
      } else {
        $ins = $db->prepare("INSERT INTO usuarios (uid, nombre, email) VALUES (?, ?, ?)");
        $ins->execute([$uid, $name, $email]);
        $_SESSION['user_db_id'] = $db->lastInsertId();
      }
    } catch (Exception $ex) {
      error_log('DB upsert error in login_verify: ' . $ex->getMessage());
    }
  }

  // After upsert, fetch DB user to check if password exists
  $needsPassword = false;
  $userDb = null;
  if (!empty($_SESSION['user_db_id'])){
    try {
      $q = $db->prepare('SELECT id, nombre, email, password FROM usuarios WHERE id = ?');
      $q->execute([$_SESSION['user_db_id']]);
      $userDb = $q->fetch(PDO::FETCH_ASSOC);
      if ($userDb && empty($userDb['password'])) $needsPassword = true;
    } catch (Exception $ex) {
      error_log('Error fetching user after upsert: ' . $ex->getMessage());
    }
  }

  // Clear any buffer output and return clean JSON
  $buf = ob_get_clean(); if($buf) error_log('login_verify unexpected output: '. $buf);
  echo json_encode(['ok'=>true, 'user'=>$_SESSION['user'], 'user_db' => $userDb, 'needsPasswordSetup' => $needsPassword]);
} catch (\Throwable $e){
  // Clear buffer and return structured JSON error
  $buf = ob_get_clean(); if($buf) error_log('login_verify unexpected output: '. $buf);
  http_response_code(401);
  error_log('login_verify error: ' . $e->getMessage());
  echo json_encode(['error'=>'Token inválido','details'=>$e->getMessage()]);
}
?>