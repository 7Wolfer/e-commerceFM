<?php
require_once __DIR__.'/config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');
$method = $_SERVER['REQUEST_METHOD'];

if($method === 'POST'){
  $input = json_decode(file_get_contents('php://input'), true);
  $nombre = $input['nombre'] ?? '';
  $telefono = $input['telefono'] ?? '';
  $email = $input['email'] ?? '';

  // upsert por email
  $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email=?");
  $stmt->bind_param('s',$email);
  $stmt->execute();
  $res = $stmt->get_result();
  if($row = $res->fetch_assoc()){
    $id = $row['id'];
    $up = $mysqli->prepare("UPDATE usuarios SET nombre=?, telefono=? WHERE id=?");
    $up->bind_param('ssi',$nombre,$telefono,$id);
    $up->execute();
  } else {
    $ins = $mysqli->prepare("INSERT INTO usuarios (nombre,email,telefono) VALUES (?,?,?)");
    $ins->bind_param('sss',$nombre,$email,$telefono);
    $ins->execute();
    $id = $ins->insert_id;
  }
  $_SESSION['user_db_id'] = $id;
  echo json_encode(['ok'=>true,'id'=>$id]);
} else {
  // recuperar perfil de sesión
  if(isset($_SESSION['user_db_id'])){
    $id = intval($_SESSION['user_db_id']);
    $u = $mysqli->query("SELECT id,nombre,email,telefono FROM usuarios WHERE id=$id")->fetch_assoc();
    echo json_encode($u);
  } else {
    echo json_encode(new stdClass());
  }
}
?>