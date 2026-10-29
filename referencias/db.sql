-- Archivo: referencias/db.sql
-- Módulo: Persistencia y reportes.
-- Propósito: crear la base la_unica, sus tablas, vistas y procedimientos almacenados.
-- Funcionamiento: debe ejecutarse en una instancia nueva; crea la base y después define sus objetos.
-- Puntos clave:
-- - No inserta datos de ejemplo ni es un script de actualización/reinicio.
-- - facturas.estado_factura admite Pagado, Cancelado y Pendiente; el flujo PHP crea ventas como Pagado.
-- - Las vistas calculan importes con productos.precio_unitario vigente; no almacenan precio histórico.
-- - Las relaciones de detalle_facturas preservan la integridad entre facturas y productos.
-- - Los procedimientos alimentan el resumen y los reportes individuales del módulo PHP.

CREATE DATABASE la_unica;
USE la_unica;

-- Tabla clientes: ID manual, nombre comercial y fecha con valor predeterminado actual.
CREATE TABLE clientes (
    id_cliente INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    fecha_registro DATE DEFAULT (CURRENT_DATE)
);

-- Tabla facturas: ID y fecha automáticos, cliente relacionado y estado inicial Pendiente en SQL.
CREATE TABLE facturas (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    estado_factura ENUM('Pagado', 'Cancelado', 'Pendiente') DEFAULT 'Pendiente',
    fecha_factura DATETIME DEFAULT (CURRENT_TIMESTAMP),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);

-- Tabla productos: unidad de medida, stock entero no negativo y precio decimal no negativo.
CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    unidad_medida VARCHAR(20) NOT NULL,
    stock INT NOT NULL CHECK (stock >= 0),
    precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0)
);

-- Tabla detalle_facturas: cantidad positiva y claves foráneas a factura y producto.
CREATE TABLE detalle_facturas (
    id_detalle_factura INT AUTO_INCREMENT PRIMARY KEY,
    id_factura INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL CHECK (cantidad > 0),
    FOREIGN KEY (id_factura) REFERENCES facturas(id_factura),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- Vista: conserva clientes sin facturas y cuenta las asociadas, cualquiera sea su estado.
CREATE VIEW vista_clientes_total_facturas AS
SELECT
    clientes.id_cliente,
    clientes.nombre,
    COUNT(facturas.id_factura) AS total_facturas
FROM clientes
LEFT JOIN facturas ON clientes.id_cliente = facturas.id_cliente
GROUP BY clientes.id_cliente, clientes.nombre;

-- Vista: calcula el total por factura con el precio vigente; solo incluye facturas con detalles.
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

-- Vista: suma cantidades de detalle por producto, incluidos detalles de todos los estados.
CREATE VIEW vista_unidades_productos AS
SELECT
    productos.id_producto,
    productos.nombre AS producto,
    SUM(detalle_facturas.cantidad) AS unidades_facturadas
FROM productos
INNER JOIN detalle_facturas ON productos.id_producto = detalle_facturas.id_producto
GROUP BY productos.id_producto, productos.nombre;

-- Procedimientos almacenados usados por el catálogo de reportes PHP.
DELIMITER //
-- Detalle: devuelve cada línea de todos los estados y calcula el subtotal al precio actual.
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

-- Clientes: cuenta facturas por cliente con LEFT JOIN para incluir clientes sin ventas.
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

-- Existencias: lista productos por stock ascendente para mostrar primero los más bajos.
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

-- Estados: cuenta solo los estados que tienen facturas registradas.
CREATE PROCEDURE reporte_facturas_por_estado()
BEGIN
    SELECT
        facturas.estado_factura,
        COUNT(facturas.id_factura) AS total_facturas
    FROM facturas
    GROUP BY facturas.estado_factura
    ORDER BY facturas.estado_factura ASC;
END //

-- Pendientes: filtra el estado Pendiente y ordena desde la fecha más antigua.
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