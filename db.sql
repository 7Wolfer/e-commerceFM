
-- Base de datos: fruteria_madrid
CREATE DATABASE IF NOT EXISTS fruteria_madrid CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fruteria_madrid;

-- Tablas básicas
CREATE TABLE IF NOT EXISTS categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL
);

CREATE TABLE IF NOT EXISTS marcas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL
);

CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(50) UNIQUE,
  nombre VARCHAR(200) NOT NULL,
  categoria_id INT,
  marca_id INT,
  precio DECIMAL(10,2) NOT NULL DEFAULT 0,
  unidad VARCHAR(40) DEFAULT '/kg',
  imagen VARCHAR(255),
  nuevo TINYINT(1) DEFAULT 0,
  oferta TINYINT(1) DEFAULT 0,
  organico TINYINT(1) DEFAULT 0,
  creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id),
  FOREIGN KEY (marca_id) REFERENCES marcas(id)
);

CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  uid VARCHAR(120),
  nombre VARCHAR(150),
  email VARCHAR(150) UNIQUE,
  telefono VARCHAR(40),
  creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS direcciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  etiqueta VARCHAR(100),
  linea1 VARCHAR(200),
  linea2 VARCHAR(200),
  ciudad VARCHAR(120),
  estado VARCHAR(120),
  cp VARCHAR(20),
  lat DECIMAL(10,6),
  lng DECIMAL(10,6),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS pedidos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  total DECIMAL(10,2) DEFAULT 0,
  metodo VARCHAR(40) DEFAULT 'pickup', -- pickup|delivery
  direccion_id INT,
  estado VARCHAR(30) DEFAULT 'pendiente',
  payment_id VARCHAR(255) DEFAULT NULL,
  creado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (direccion_id) REFERENCES direcciones(id)
);

CREATE TABLE IF NOT EXISTS pedido_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT NOT NULL,
  producto_id INT NULL,
  nombre VARCHAR(200),
  precio DECIMAL(10,2),
  cantidad INT DEFAULT 1,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (producto_id) REFERENCES productos(id)
);

-- Datos semilla
INSERT INTO categorias (nombre) VALUES
('Frutas'),('Verduras'),('Granel');

INSERT INTO marcas (nombre) VALUES
('Justo Frescos'),('Mr. Lucky'),('Campo Vivo'),('Justo'),('Earthbound'),('Del Desierto');

INSERT INTO productos (sku, nombre, categoria_id, marca_id, precio, unidad, imagen, nuevo, oferta, organico) VALUES
('uva-verde','Uva Verde sin Semilla Selecta',1,1,128.99,'/kg','assets/img/productos/uvaVerdeSelecta.jpg',1,0,0),
('jitomate-saladette','Jitomate Saladette Mini',2,2,22.90,'/kg','assets/img/productos/jitomateSaladetMini.jpg',0,1,0),
('limon-semilla','Limón con Semilla',1,3,20.10,'/kg','assets/img/productos/limon.jpg',0,0,0),
('mandarina','Mandarina',1,4,7.91,'/200g','assets/img/productos/mandarina.jpg',1,0,0),
('platano-chiapas','Plátano Chiapas Selecto',1,1,26.90,'/kg','assets/img/productos/platanoChiapasSelecto.jpg',0,0,0),
('zanahoria','Zanahoria Selecta',2,4,19.90,'/kg','assets/img/productos/zanahoriaSelecta.jpg',0,0,0),
('cebolla-blanca','Cebolla Blanca',2,3,34.80,'/kg','assets/img/productos/cebollaBlanca.jpg',0,0,1),
('aguacate-hass','Aguacate Hass',1,3,106.60,'/kg','assets/img/productos/aguacateHass.jpg',1,0,1),
('espinaca','Espinaca Orgánica',2,5,139.90,'/454g','assets/img/productos/espinacaOrganica.jpg',0,0,1),
('datil-medjool','Dátil Medjool Orgánico',3,6,279.93,'/908g','assets/img/productos/datil.jpg',0,0,1);
