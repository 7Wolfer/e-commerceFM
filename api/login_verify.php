<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/config_firebase.php';
require_once __DIR__ . '/config.php'; // para DB (opcional upsert)

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if(!$data || !isset($data['idToken'])){
  http_response_code(400);
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
    // ¿existe ya?
    $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email=?");
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_assoc()){
      $id = intval($row['id']);
      $up = $mysqli->prepare("UPDATE usuarios SET nombre=? WHERE id=?");
      $up->bind_param('si',$name,$id);
      $up->execute();
      $_SESSION['user_db_id'] = $id;
    } else {
      $ins = $mysqli->prepare("INSERT INTO usuarios (uid,nombre,email) VALUES (?,?,?)");
      $ins->bind_param('sss',$uid,$name,$email);
      $ins->execute();
      $_SESSION['user_db_id'] = $ins->insert_id;
    }
  }

  echo json_encode(['ok'=>true, 'user'=>$_SESSION['user']]);
} catch (\Throwable $e){
  http_response_code(401);
  echo json_encode(['error'=>'Token inválido','details'=>$e->getMessage()]);
}
?>