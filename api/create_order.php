<?php
require_once __DIR__.'/config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
$metodo = $input['metodo'] ?? 'pickup';
$direccion = $input['direccion'] ?? null;
$items = $input['items'] ?? [];

if(!$items || !is_array($items)){
  http_response_code(400); echo json_encode(['error'=>'Carrito vacío']); exit;
}

$usuario_id = $_SESSION['user_db_id'] ?? null;
if(!$usuario_id){
  // Usuario invitado; lo podrías obligar a completar perfil.
  $usuario_id = null;
}

// Si hay dirección, guardarla
$direccion_id = null;
if($metodo==='delivery' && $direccion){
  $insD = $mysqli->prepare("INSERT INTO direcciones (usuario_id, etiqueta, linea1, ciudad, estado, cp, lat, lng) VALUES (?,?,?,?,?,?,?,?)");
  $et = $direccion['etiqueta'] ?? 'Entrega';
  $l1 = $direccion['linea1'] ?? '';
  $ci = $direccion['ciudad'] ?? '';
  $es = $direccion['estado'] ?? '';
  $cp = $direccion['cp'] ?? '';
  $lat = isset($direccion['lat'])? floatval($direccion['lat']): null;
  $lng = isset($direccion['lng'])? floatval($direccion['lng']): null;
  $insD->bind_param('isssssdd',$usuario_id,$et,$l1,$ci,$es,$cp,$lat,$lng);
  $insD->execute();
  $direccion_id = $insD->insert_id;
}

$total = 0;
foreach($items as $it){
  $total += floatval($it['price'])*intval($it['qty']);
}

$insP = $mysqli->prepare("INSERT INTO pedidos (usuario_id,total,metodo,direccion_id,estado) VALUES (?,?,?,?,?)");
$estado='pendiente';
$insP->bind_param('idsss',$usuario_id,$total,$metodo,$direccion_id,$estado);
$insP->execute();
$pedido_id = $insP->insert_id;

// Items
$insI = $mysqli->prepare("INSERT INTO pedido_items (pedido_id,producto_id,nombre,precio,cantidad) VALUES (?,?,?,?,?)");
foreach($items as $it){
  $pid = intval($it['id']);
  $nm = $it['name'];
  $pr = floatval($it['price']);
  $qt = intval($it['qty']);
  $insI->bind_param('iisdi',$pedido_id,$pid,$nm,$pr,$qt);
  $insI->execute();
}

echo json_encode(['ok'=>true,'pedido_id'=>$pedido_id,'total'=>$total]);
?>