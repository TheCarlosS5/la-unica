-- Esquema de la base de datos La Única.
-- Este archivo crea la estructura; no inserta datos.

CREATE DATABASE la_unica;
USE la_unica;

CREATE TABLE clientes (
    id_cliente INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    fecha_registro DATE DEFAULT (CURRENT_DATE)
);

CREATE TABLE facturas (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    estado_factura ENUM('Pagado', 'Cancelado', 'Pendiente') DEFAULT 'Pendiente',
    fecha_factura DATETIME DEFAULT (CURRENT_TIMESTAMP),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    unidad_medida VARCHAR(20) NOT NULL,
    stock INT NOT NULL CHECK (stock >= 0),
    precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0)
);

CREATE TABLE detalle_facturas (
    id_detalle_factura INT AUTO_INCREMENT PRIMARY KEY,
    id_factura INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    FOREIGN KEY (id_factura) REFERENCES facturas(id_factura),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

CREATE VIEW vista_clientes_total_facturas AS
SELECT
    clientes.id_cliente,
    clientes.nombre,
    COUNT(facturas.id_factura) AS total_facturas
FROM clientes
LEFT JOIN facturas ON clientes.id_cliente = facturas.id_cliente
GROUP BY clientes.id_cliente, clientes.nombre;

CREATE VIEW vista_total_facturas AS
SELECT
    facturas.id_factura,
    clientes.nombre AS cliente,
    SUM(detalle_facturas.cantidad * productos.precio_unitario) AS total_factura
FROM facturas
INNER JOIN clientes ON facturas.id_cliente = clientes.id_cliente
INNER JOIN detalle_facturas ON facturas.id_factura = detalle_facturas.id_factura
INNER JOIN productos ON detalle_facturas.id_producto = productos.id_producto
GROUP BY facturas.id_factura, clientes.nombre;

CREATE VIEW vista_unidades_productos AS
SELECT
    productos.id_producto,
    productos.nombre AS producto,
    SUM(detalle_facturas.cantidad) AS unidades_facturadas
FROM productos
INNER JOIN detalle_facturas ON productos.id_producto = detalle_facturas.id_producto
GROUP BY productos.id_producto, productos.nombre;

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
        productos.precio_unitario,
        detalle_facturas.cantidad * productos.precio_unitario AS subtotal
    FROM facturas
    INNER JOIN clientes ON facturas.id_cliente = clientes.id_cliente
    INNER JOIN detalle_facturas ON facturas.id_factura = detalle_facturas.id_factura
    INNER JOIN productos ON detalle_facturas.id_producto = productos.id_producto
    ORDER BY facturas.id_factura;
END //

CREATE PROCEDURE reporte_facturas_clientes()
BEGIN
    SELECT
        clientes.id_cliente,
        clientes.nombre,
        COUNT(facturas.id_factura) AS total_facturas
    FROM clientes
    LEFT JOIN facturas ON clientes.id_cliente = facturas.id_cliente
    GROUP BY clientes.id_cliente, clientes.nombre
    ORDER BY total_facturas DESC;
END //

CREATE PROCEDURE reporte_stock_productos()
BEGIN
    SELECT
        productos.id_producto,
        productos.nombre,
        productos.stock,
        productos.precio_unitario
    FROM productos
    ORDER BY productos.stock ASC;
END //

CREATE PROCEDURE reporte_facturas_por_estado()
BEGIN
    SELECT
        facturas.estado_factura,
        COUNT(facturas.id_factura) AS total_facturas
    FROM facturas
    GROUP BY facturas.estado_factura
    ORDER BY facturas.estado_factura ASC;
END //

CREATE PROCEDURE reporte_facturas_pendientes()
BEGIN
    SELECT
        facturas.id_factura,
        clientes.nombre AS cliente,
        facturas.fecha_factura,
        facturas.estado_factura
    FROM facturas
    INNER JOIN clientes ON facturas.id_cliente = clientes.id_cliente
    WHERE facturas.estado_factura = 'Pendiente'
    ORDER BY facturas.fecha_factura ASC;
END //
DELIMITER ;