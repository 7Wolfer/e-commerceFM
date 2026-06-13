-- Migración: alinea una base de datos EXISTENTE con el flujo de pago de Stripe.
-- (db.sql ya queda correcto para instalaciones nuevas; esto es para la BD que ya tienes.)
--
-- Ejecútalo UNA sola vez:
--   mysql -u root fruteria_madrid < migration.sql
--
-- O pégalo en phpMyAdmin → pestaña SQL.

USE fruteria_madrid;

-- 1) Guardar la referencia de pago de Stripe en el pedido.
ALTER TABLE pedidos
  ADD COLUMN payment_id VARCHAR(255) DEFAULT NULL AFTER estado;

-- 2) Permitir items sin producto_id (los items que vienen de Stripe Checkout
--    no traen el id de producto de tu catálogo).
ALTER TABLE pedido_items
  MODIFY producto_id INT NULL;
