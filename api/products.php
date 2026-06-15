<?php
require_once __DIR__.'/config.php';

$filters = [];
$params = [];

if(isset($_GET['categoria_id']) && $_GET['categoria_id']!==''){
  $filters[] = 'p.categoria_id = ?'; $params[] = $_GET['categoria_id'];
}
if(isset($_GET['marca_id']) && $_GET['marca_id']!==''){
  $filters[] = 'p.marca_id = ?'; $params[] = $_GET['marca_id'];
}
if(isset($_GET['nuevo']) && $_GET['nuevo']=='1'){
  $filters[] = 'p.nuevo = 1';
}
if(isset($_GET['oferta']) && $_GET['oferta']=='1'){
  $filters[] = 'p.oferta = 1';
}
if(isset($_GET['organico']) && $_GET['organico']=='1'){
  $filters[] = 'p.organico = 1';
}

$where = count($filters)? 'WHERE '.implode(' AND ', $filters) : '';

$sort = 'p.id DESC';
if(isset($_GET['sort']) && $_GET['sort']=='price_asc') $sort = 'p.precio ASC';
if(isset($_GET['sort']) && $_GET['sort']=='price_desc') $sort = 'p.precio DESC';

$sql = "SELECT p.*, c.nombre AS categoria, m.nombre AS marca
        FROM productos p
        LEFT JOIN categorias c ON c.id=p.categoria_id
        LEFT JOIN marcas m ON m.id=p.marca_id
        $where
        ORDER BY $sort";

$stmt = $mysqli->prepare($sql);
if($params){
  $types = str_repeat('i', count($params));
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();
$data = [];
while($row = $res->fetch_assoc()){
  $data[] = [
    'id'=>$row['id'],'sku'=>$row['sku'],'name'=>$row['nombre'],
    'brand'=>$row['marca'],'brand_id'=>$row['marca_id'],
    'price'=>floatval($row['precio']),
    'oldPrice'=> (isset($row['precio_anterior']) && $row['precio_anterior']!==null) ? floatval($row['precio_anterior']) : null,
    'unit'=>$row['unidad'],
    'img'=>$row['imagen'],'category'=>$row['categoria'],'category_id'=>$row['categoria_id'],
    'tags'=>array_values(array_filter([ $row['nuevo']?'nuevo':null, $row['oferta']?'oferta':null, $row['organico']?'organico':null ]))
  ];
}
json_response($data);
?>