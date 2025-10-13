<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if(!$data){
  $provider = $_GET['provider'] ?? 'email';
  $_SESSION['user'] = ['provider'=>$provider, 'name'=>'Cliente Frutería', 'email'=>'cliente@example.com'];
  echo json_encode($_SESSION['user']); exit;
}
$_SESSION['user'] = [
  'uid' => $data['uid'] ?? '',
  'name' => $data['displayName'] ?? '',
  'email' => $data['email'] ?? '',
  'photoURL' => $data['photoURL'] ?? '',
  'provider' => $data['providerId'] ?? 'firebase'
];
echo json_encode(['ok'=>true,'user'=>$_SESSION['user']]);
?>