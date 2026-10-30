-- Archivo: referencias/migraciones/001_precio_historico.sql
-- Módulo: Persistencia — migración de una base creada con db.sql anterior a esta versión.
-- Propósito: guardar el precio de venta en detalle_facturas y excluir las canceladas de los reportes de ventas.
-- Uso: ejecutar una sola vez sobre la base la_unica existente (haz una copia de seguridad antes).
-- Limitación: las líneas ya registradas no conservaban su precio original; se rellenan con el precio
-- actual del catálogo, que es el mejor dato disponible. Las ventas nuevas guardan su propio precio.

USE la_unica;

ALTER TABLE detalle_facturas
    ADD COLUMN precio_unitario DECIMAL(10,2) NULL AFTER cantidad;

UPDATE detalle_facturas
INNER JOIN productos ON productos.id_producto = detalle_facturas.id_producto
SET detalle_facturas.precio_unitario = productos.precio_unitario
WHERE detalle_facturas.precio_unitario IS NULL;

ALTER TABLE detalle_facturas
    MODIFY precio_unitario DECIMAL(10,2) NOT NULL,
    ADD CONSTRAINT chk_detalle_precio CHECK (precio_unitario >= 0);

CREATE OR REPLACE VIEW vista_total_facturas AS
SELECT
    facturas.id_factura,
    clientes.nombre AS cliente,
    facturas.estado_factura,
    SUM(detalle_facturas.cantidad * detalle_facturas.precio_unitario) AS total_factura
FROM facturas
INNER JOIN clientes ON facturas.id_cliente = clientes.id_cliente
INNER JOIN detalle_facturas ON facturas.id_factura = detalle_facturas.id_factura
WHERE facturas.estado_factura <> 'Cancelado'
GROUP BY facturas.id_factura, clientes.nombre, facturas.estado_factura;

CREATE OR REPLACE VIEW vista_unidades_productos AS
SELECT
    productos.id_producto,
    productos.nombre AS producto,
    SUM(detalle_facturas.cantidad) AS unidades_facturadas
FROM productos
INNER JOIN detalle_facturas ON productos.id_producto = detalle_facturas.id_producto
INNER JOIN facturas ON detalle_facturas.id_factura = facturas.id_factura
WHERE facturas.estado_factura <> 'Cancelado'
GROUP BY productos.id_producto, productos.nombre;

DROP PROCEDURE IF EXISTS reporte_detalle_facturas;

DELIMITER //
CREATE PROCEDURE reporte_detalle_facturas()
BEGIN
    SELECT
        facturas.id_factura,
        clientes.nombre AS cliente,
        facturas.estado_factura,
        facturas.fecha_factura,
        productos.nombre AS producto,
        detalle_facturas.cantidad,
        detalle_facturas.precio_unitario,
        detalle_facturas.cantidad * detalle_facturas.precio_unitario AS subtotal
    FROM facturas
    INNER JOIN clientes ON facturas.id_cliente = clientes.id_cliente
    INNER JOIN detalle_facturas ON facturas.id_factura = detalle_facturas.id_factura
    INNER JOIN productos ON detalle_facturas.id_producto = productos.id_producto
    ORDER BY facturas.id_factura, detalle_facturas.id_detalle_factura;
END //
DELIMITER ;
