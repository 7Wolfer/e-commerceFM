-- Migración: usa las imágenes locales (assets/img/productos/) en vez de URLs de Unsplash.
-- Para una base de datos EXISTENTE. Ejecútalo una vez:
--   mysql -u root fruteria_madrid < migration_images.sql
-- (Es idempotente: puedes correrlo varias veces sin problema.)

USE fruteria_madrid;

UPDATE productos SET imagen='assets/img/productos/uvaVerdeSelecta.jpg'       WHERE sku='uva-verde';
UPDATE productos SET imagen='assets/img/productos/jitomateSaladetMini.jpg'   WHERE sku='jitomate-saladette';
UPDATE productos SET imagen='assets/img/productos/limon.jpg'                 WHERE sku='limon-semilla';
UPDATE productos SET imagen='assets/img/productos/mandarina.jpg'             WHERE sku='mandarina';
UPDATE productos SET imagen='assets/img/productos/platanoChiapasSelecto.jpg' WHERE sku='platano-chiapas';
UPDATE productos SET imagen='assets/img/productos/zanahoriaSelecta.jpg'      WHERE sku='zanahoria';
UPDATE productos SET imagen='assets/img/productos/cebollaBlanca.jpg'         WHERE sku='cebolla-blanca';
UPDATE productos SET imagen='assets/img/productos/aguacateHass.jpg'          WHERE sku='aguacate-hass';
UPDATE productos SET imagen='assets/img/productos/espinacaOrganica.jpg'      WHERE sku='espinaca';
UPDATE productos SET imagen='assets/img/productos/datil.jpg'                 WHERE sku='datil-medjool';
