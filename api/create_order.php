<?php
require_once __DIR__.'/config.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

$input     = json_decode(file_get_contents('php://input'), true);
$metodo    = $input['metodo']    ?? 'pickup';
$direccion = $input['direccion'] ?? null;
$rawItems  = $input['items']     ?? [];

if(!$rawItems || !is_array($rawItems)){
  http_response_code(400); echo json_encode(['error'=>'Carrito vacío']); exit;
}

// Cantidades por producto. IMPORTANTE: los precios y nombres se toman de la BD,
// nunca de lo que envía el navegador (si no, el cliente podría falsear el precio).
$qtyById = [];
foreach($rawItems as $it){
  $pid = intval($it['id'] ?? 0);
  $qt  = max(1, intval($it['qty'] ?? 1));
  if($pid > 0){ $qtyById[$pid] = ($qtyById[$pid] ?? 0) + $qt; }
}
if(!$qtyById){
  http_response_code(400); echo json_encode(['error'=>'Carrito inválido']); exit;
}

// Precios reales desde la base de datos
$ids = array_keys($qtyById);
$ph  = implode(',', array_fill(0, count($ids), '?'));
$stmt = $mysqli->prepare("SELECT id, nombre, precio FROM productos WHERE id IN ($ph)");
$stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
$total = 0;
while($row = $res->fetch_assoc()){
  $pid = intval($row['id']);
  $pr  = floatval($row['precio']);
  $qt  = $qtyById[$pid];
  $items[] = ['id'=>$pid, 'name'=>$row['nombre'], 'price'=>$pr, 'qty'=>$qt];
  $total  += $pr * $qt;
}
if(!$items){
  http_response_code(400); echo json_encode(['error'=>'Ningún producto válido en el carrito']); exit;
}

$usuario_id = $_SESSION['user_db_id'] ?? null;

// Si es entrega a domicilio, guardar la dirección
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

// Crear pedido (total calculado en el servidor)
$insP = $mysqli->prepare("INSERT INTO pedidos (usuario_id,total,metodo,direccion_id,estado) VALUES (?,?,?,?,?)");
$estado = 'pendiente';
$insP->bind_param('idsss',$usuario_id,$total,$metodo,$direccion_id,$estado);
$insP->execute();
$pedido_id = $insP->insert_id;

// Items
$insI = $mysqli->prepare("INSERT INTO pedido_items (pedido_id,producto_id,nombre,precio,cantidad) VALUES (?,?,?,?,?)");
foreach($items as $it){
  $pid = $it['id']; $nm = $it['name']; $pr = $it['price']; $qt = $it['qty'];
  $insI->bind_param('iisdi',$pedido_id,$pid,$nm,$pr,$qt);
  $insI->execute();
}

echo json_encode(['ok'=>true,'pedido_id'=>$pedido_id,'total'=>$total]);
