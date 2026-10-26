-- Esquema de la base de datos La Única (modelo del SRS v1.1, sección 3.5).
-- Este archivo crea la estructura; no inserta datos.

CREATE DATABASE la_unica CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE la_unica;

CREATE TABLE clientes (
    id_cliente BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_documento ENUM('CC', 'CE', 'NIT', 'PAS') NOT NULL,
    numero_documento VARCHAR(20) NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    telefono VARCHAR(15) NULL,
    correo VARCHAR(120) NULL,
    direccion VARCHAR(150) NULL,
    UNIQUE (tipo_documento, numero_documento)
);

CREATE TABLE productos (
    id_producto BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(30) NOT NULL UNIQUE,
    nombre VARCHAR(120) NOT NULL,
    descripcion VARCHAR(500) NULL,
    unidad_medida ENUM('UND', 'KG', 'G', 'LB', 'L', 'ML') NOT NULL,
    precio DECIMAL(12,2) NOT NULL CHECK (precio >= 0),
    stock DECIMAL(12,3) NOT NULL CHECK (stock >= 0)
);

CREATE TABLE factura (
    id_factura BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(20) NOT NULL UNIQUE,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_cliente BIGINT UNSIGNED NOT NULL,
    estado ENUM('Pagado', 'Cancelado') NOT NULL DEFAULT 'Pagado',
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);

-- Tabla pivote: una línea por producto en cada factura.
-- El subtotal y el total no se guardan: se calculan (cantidad * precio_unitario).
CREATE TABLE detalle_factura (
    id_factura BIGINT UNSIGNED NOT NULL,
    id_producto BIGINT UNSIGNED NOT NULL,
    cantidad DECIMAL(12,3) NOT NULL CHECK (cantidad > 0),
    precio_unitario DECIMAL(12,2) NOT NULL CHECK (precio_unitario >= 0),
    PRIMARY KEY (id_factura, id_producto),
    FOREIGN KEY (id_factura) REFERENCES factura(id_factura),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

CREATE VIEW vista_clientes_total_facturas AS
SELECT
    clientes.id_cliente,
    clientes.nombre,
    COUNT(factura.id_factura) AS total_facturas
FROM clientes
LEFT JOIN factura ON clientes.id_cliente = factura.id_cliente
GROUP BY clientes.id_cliente, clientes.nombre;

CREATE VIEW vista_total_facturas AS
SELECT
    factura.id_factura,
    factura.numero,
    clientes.nombre AS cliente,
    SUM(ROUND(detalle_factura.cantidad * detalle_factura.precio_unitario, 2)) AS total_factura
FROM factura
INNER JOIN clientes ON factura.id_cliente = clientes.id_cliente
INNER JOIN detalle_factura ON factura.id_factura = detalle_factura.id_factura
GROUP BY factura.id_factura, factura.numero, clientes.nombre;

CREATE VIEW vista_unidades_productos AS
SELECT
    productos.id_producto,
    productos.nombre AS producto,
    SUM(detalle_factura.cantidad) AS unidades_facturadas
FROM productos
INNER JOIN detalle_factura ON productos.id_producto = detalle_factura.id_producto
GROUP BY productos.id_producto, productos.nombre;

DELIMITER //
CREATE PROCEDURE reporte_detalle_factura()
BEGIN
    SELECT
        factura.numero,
        clientes.nombre AS cliente,
        factura.estado,
        factura.fecha,
        productos.codigo,
        productos.nombre AS producto,
        productos.unidad_medida,
        detalle_factura.cantidad,
        detalle_factura.precio_unitario,
        ROUND(detalle_factura.cantidad * detalle_factura.precio_unitario, 2) AS subtotal
    FROM factura
    INNER JOIN clientes ON factura.id_cliente = clientes.id_cliente
    INNER JOIN detalle_factura ON factura.id_factura = detalle_factura.id_factura
    INNER JOIN productos ON detalle_factura.id_producto = productos.id_producto
    ORDER BY factura.id_factura;
END //

CREATE PROCEDURE reporte_facturas_clientes()
BEGIN
    SELECT
        clientes.id_cliente,
        clientes.nombre,
        COUNT(factura.id_factura) AS total_facturas
    FROM clientes
    LEFT JOIN factura ON clientes.id_cliente = factura.id_cliente
    GROUP BY clientes.id_cliente, clientes.nombre
    ORDER BY total_facturas DESC;
END //

CREATE PROCEDURE reporte_stock_productos()
BEGIN
    SELECT
        productos.id_producto,
        productos.codigo,
        productos.nombre,
        productos.unidad_medida,
        productos.stock,
        productos.precio
    FROM productos
    ORDER BY productos.stock ASC;
END //

CREATE PROCEDURE reporte_facturas_por_estado()
BEGIN
    SELECT
        factura.estado,
        COUNT(factura.id_factura) AS total_facturas
    FROM factura
    GROUP BY factura.estado
    ORDER BY factura.estado ASC;
END //
DELIMITER ;
