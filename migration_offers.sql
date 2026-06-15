-- Migración: precios de oferta (precio anterior, para mostrar tachado y % de descuento).
-- Para una base de datos EXISTENTE. Ejecútalo una vez:
--   mysql -u root fruteria_madrid < migration_offers.sql
USE fruteria_madrid;

ALTER TABLE productos ADD COLUMN precio_anterior DECIMAL(10,2) DEFAULT NULL AFTER precio;

UPDATE productos SET oferta = 1, precio_anterior = 29.90 WHERE sku = 'jitomate-saladette';
UPDATE productos SET oferta = 1, precio_anterior = 27.00 WHERE sku = 'limon-semilla';
UPDATE productos SET oferta = 1, precio_anterior = 34.90 WHERE sku = 'platano-chiapas';
UPDATE productos SET oferta = 1, precio_anterior = 24.90 WHERE sku = 'zanahoria';
