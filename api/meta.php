<?php
require_once __DIR__.'/config.php';

$cats = $mysqli->query("SELECT id, nombre FROM categorias ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
$brands = $mysqli->query("SELECT id, nombre FROM marcas ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);

json_response(['categorias'=>$cats, 'marcas'=>$brands]);
?>