<?php
// ¡Asegúrate de que este script NO sea accesible en producción!
if ($_SERVER['REMOTE_ADDR'] !== '127.0.0.1' && $_SERVER['REMOTE_ADDR'] !== '::1') {
    die('Acceso denegado');
}

// Ajusta si tu contraseña/root difiere
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'fruteria_madrid';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo json_encode(['error'=>'Error de conexión: '.$mysqli->connect_error]);
  exit;
}

// -------------------------------------------------------------
// 1. RESETEAR TODAS LAS TABLAS EN ORDEN SEGURO
// -------------------------------------------------------------
$mysqli->query("SET FOREIGN_KEY_CHECKS = 0;");
$mysqli->query("TRUNCATE TABLE pedido_items;"); // Primero items de pedidos
$mysqli->query("TRUNCATE TABLE pedidos;");
$mysqli->query("TRUNCATE TABLE direcciones;");
$mysqli->query("TRUNCATE TABLE usuarios;");
$mysqli->query("TRUNCATE TABLE productos;");
$mysqli->query("TRUNCATE TABLE categorias;"); // También categorías y marcas
$mysqli->query("TRUNCATE TABLE marcas;");
$mysqli->query("SET FOREIGN_KEY_CHECKS = 1;");


// -------------------------------------------------------------
// 2. SEMBRAR DATOS BASE (NECESARIOS PARA FOREIGN KEYS)
// -------------------------------------------------------------
// Aseguramos que las FK existan antes de insertar productos
$mysqli->query("INSERT INTO categorias (nombre) VALUES ('Frutas'),('Verduras'),('Granel');");
$mysqli->query("INSERT INTO marcas (nombre) VALUES ('Justo Frescos'),('Mr. Lucky'),('Campo Vivo');");


// -------------------------------------------------------------
// 3. DATOS DE PRUEBA PARA CYPRESS
// -------------------------------------------------------------
$mysqli->query("INSERT INTO productos (sku, nombre, categoria_id, marca_id, precio, unidad, imagen, nuevo, oferta, organico) VALUES
('uva-verde','Uva Verde sin Semilla Selecta',1,1,128.99,'/kg','http:\\localhost\e-commerceFM\assets\img\productos\uvaVerdeSelecta.jpg',1,0,0);");

// REEMPLAZA ESTO con el HASH REAL que COPIASTE
$HASH_DE_LA_CLAVE_123456 = '$2y$10$onKuJH152fic2Wv6p.m5R.OA2ipjqNtA.xj7DNNzuo87fz1RY7LEm'; // password_hash('123456', PASSWORD_DEFAULT);

$mysqli->query("INSERT INTO usuarios (nombre, email, telefono, password) VALUES
('testUser', 'test@usuario.com', '1122334455', '$HASH_DE_LA_CLAVE_123456');");

// -------------------------------------------------------------
// 4. FINALIZAR 
$mysqli->close();
echo "Base de datos reseteada.";
exit;
?>