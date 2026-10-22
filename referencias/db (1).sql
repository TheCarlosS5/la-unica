-- La_Unica - 13-10-2026 - Ficha: 3407799

-- 1. CREAR LA BASE DE DATOS Y USARLA

-- Cre la base de datos 
CREATE DATABASE la_unica;

-- Se ubica en la base de datos
USE la_unica;

-- 2. CREAR LAS ENTIDADES DE LAS BASES DE DATOS Y SUS ATRIBUTOS

-- Crea la entidad clientes con sus atributos
CREATE TABLE clientes (
 id_cliente INT PRIMARY KEY,
 nombre VARCHAR(100) NOT NULL,
 fecha_registro DATE DEFAULT (CURRENT_DATE)
);

-- Crea la entidad facturas con sus atributos
CREATE TABLE facturas (
 id_factura INT AUTO_INCREMENT PRIMARY KEY,
 id_cliente INT NOT NULL,
 estado_factura ENUM('Pagado', 'Cancelado', 'Pendiente') DEFAULT 'Pendiente',
 fecha_factura DATETIME DEFAULT (CURRENT_TIMESTAMP),
 FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);

-- Crea la entidad productos con sus atributos
CREATE TABLE productos (
 id_producto INT AUTO_INCREMENT PRIMARY KEY,
 nombre VARCHAR(100) NOT NULL,
 unidad_medida VARCHAR(20) NOT NULL,
 stock INT NOT NULL CHECK (stock >= 0),
 precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0)
);

-- Crea la tabla intermedio o pivote llamada detalle_factura con sus atributos
CREATE TABLE detalle_facturas (
 id_detalle_factura INT AUTO_INCREMENT PRIMARY KEY,
 id_factura INT NOT NULL,
 id_producto INT NOT NULL,
 cantidad INT NOT NULL CHECK (cantidad > 0),
 FOREIGN KEY (id_factura) REFERENCES facturas(id_factura),
 FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- 3. INSERTAR DATOS EN LA BASE DE DATOS

-- Inserta datos en la tabla clientes
INSERT INTO clientes (id_cliente, nombre, fecha_registro) VALUES
	(1, 'Carlos Ramirez', '2026-01-10'),
	(2, 'Laura Martinez', '2026-01-15'),
	(3, 'Andres Rodriguez', '2026-02-01'),
	(4, 'Sofia Hernandez', '2026-02-12'),
	(5, 'Daniel Torres', '2026-03-05'),
	(6, 'Valentina Garcia', '2026-03-18'),
	(7, 'Juan Castro', '2026-04-02'),
	(8, 'Camila Moreno', '2026-04-15'),
	(9, 'Mateo Lopez', '2026-05-08'),
	(10, 'Isabella Sanchez', '2026-05-20');

-- Inserta datos en la tabla productos
INSERT INTO productos (id_producto, nombre, unidad_medida, stock, precio_unitario) VALUES
	(1, 'Arroz', 'Kilogramo', 100, 3500.00),
	(2, 'Azucar', 'Kilogramo', 80, 4200.00),
	(3, 'Aceite vegetal', 'Litro', 60, 8500.00),
	(4, 'Leche entera', 'Litro', 50, 4000.00),
	(5, 'Huevos', 'Unidad', 200, 700.00),
	(6, 'Pan tajado', 'Paquete', 40, 5500.00),
	(7, 'Cafe molido', 'Paquete', 30, 12000.00),
	(8, 'Pasta', 'Paquete', 75, 2800.00),
	(9, 'Sal', 'Kilogramo', 45, 2000.00),
	(10, 'Chocolate en polvo', 'Paquete', 35, 6500.00);

-- Inserta datos en la entidad facturas
INSERT INTO facturas (id_factura, id_cliente, estado_factura, fecha_factura) VALUES
	(1, 1, 'Pagado', '2026-06-01 09:15:00'),
	(2, 2, 'Pagado', '2026-06-02 10:30:00'),
	(3, 3, 'Pendiente', '2026-06-03 11:00:00'),
	(4, 4, 'Pagado', '2026-06-04 14:20:00'),
	(5, 5, 'Cancelado', '2026-06-05 15:45:00'),
	(6, 6, 'Pagado', '2026-06-06 08:50:00'),
	(7, 7, 'Pendiente', '2026-06-07 12:10:00'),
	(8, 8, 'Pagado', '2026-06-08 16:30:00'),
	(9, 9, 'Pagado', '2026-06-09 17:00:00'),
	(10, 10, 'Pendiente', '2026-06-10 18:15:00');

-- Inserta datos en la tabla intermedio o pivote llamada detalle_factura
INSERT INTO detalle_facturas (id_detalle_factura, id_factura, id_producto, cantidad) VALUES
	(1, 1, 1, 2),
	(2, 2, 2, 3),
	(3, 3, 3, 1),
	(4, 4, 4, 4),
	(5, 5, 5, 12),
	(6, 6, 6, 2),
	(7, 7, 7, 1),
	(8, 8, 8, 5),
	(9, 9, 9, 2),
	(10, 10, 10, 3);
	
-- 4. CONSULTAS EN LA BASE DE DATOS Y JOINS

-- Consulta todos los datos de la entidad clientes

SELECT * FROM clientes;

-- Consulta todos los datos de la entidad productos

SELECT * FROM productos;

-- Consulta todos los datos de la entidad facturas

SELECT * FROM facturas;

-- Consulta todos los datos de la entidad detalle_facturas

SELECT * FROM detalle_facturas;

-- Muestra quién realizó cada factura, su estado y la fecha de emisión.

SELECT
   facturas.id_factura,
   clientes.nombre AS cliente,
   facturas.estado_factura,
   facturas.fecha_factura
FROM facturas
INNER JOIN clientes ON facturas.id_cliente = clientes.id_cliente;

-- Muestra los productos incluidos en los detalles de las facturas, la cantidad y el subtotal correspondiente.

SELECT
   detalle_facturas.id_detalle_factura,
   productos.nombre AS producto,
   detalle_facturas.cantidad,
   productos.precio_unitario,
   detalle_facturas.cantidad * productos.precio_unitario AS subtotal
FROM detalle_facturas
INNER JOIN productos ON detalle_facturas.id_producto = productos.id_producto;

-- Muestra únicamente las facturas pendientes y el nombre del cliente correspondiente.

SELECT
   facturas.id_factura,
   clientes.nombre AS cliente,
   facturas.fecha_factura,
   facturas.estado_factura
FROM facturas
INNER JOIN clientes ON facturas.id_cliente = clientes.id_cliente
WHERE facturas.estado_factura = 'Pendiente'
ORDER BY facturas.fecha_factura ASC;

-- 5. CREACION DE VISTAS Y CONSULTA DE LAS MISMAS

-- Cantidad de facturas registradas por cliente

CREATE VIEW vista_clientes_total_facturas AS
SELECT
   clientes.id_cliente,
   clientes.nombre,
   COUNT(facturas.id_factura) AS total_facturas
FROM clientes
LEFT JOIN facturas ON clientes.id_cliente = facturas.id_cliente
GROUP BY clientes.id_cliente, clientes.nombre;

-- Comprobar que la vista sirva

SELECT * FROM vista_clientes_total_facturas;

-- Valor total de cada factura

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

-- Comprobar que la vista sirva

SELECT * FROM vista_total_facturas;

-- Cantidad total de unidades facturadas por producto

CREATE VIEW vista_unidades_productos AS
SELECT
   productos.id_producto,
   productos.nombre AS producto,
   SUM(detalle_facturas.cantidad) AS unidades_facturadas
FROM productos
INNER JOIN detalle_facturas ON productos.id_producto = detalle_facturas.id_producto
GROUP BY productos.id_producto, productos.nombre;

-- Comprobar que la vista sirva

SELECT * FROM vista_unidades_productos;

-- 6. CREACION PROCEDIMIENTOS Y COMPROBACION DE QUE FUNCIONE

-- Consultar los productos y subtotales de cada factura

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
   INNER JOIN productos ON detalle_facturas.id_producto = productos.id_producto ORDER BY facturas.id_factura;
END //
DELIMITER ;

-- COMPROBACIÓN: Consultar los productos y subtotales de cada factura
CALL reporte_detalle_facturas();

-- Consultar la cantidad de facturas registradas por cliente

DELIMITER //
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
DELIMITER ;

-- COMPROBACIÓN: Consultar la cantidad de facturas de cada cliente

CALL reporte_facturas_clientes();

-- Listar los productos ordenados por stock, de menor a mayor

DELIMITER //
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
DELIMITER ;

-- COMPROBACIÓN 3: Comprobar el orden de los productos por stock ascendente

CALL reporte_stock_productos();

-- Contar las facturas por estado en orden alfabético

DELIMITER //
CREATE PROCEDURE reporte_facturas_por_estado()
BEGIN
   SELECT
      facturas.estado_factura,
      COUNT(facturas.id_factura) AS total_facturas
   FROM facturas
   GROUP BY facturas.estado_factura
   ORDER BY facturas.estado_factura ASC;
END //
DELIMITER ;

-- COMPROBACIÓN: Comprobar la cantidad de facturas por estado
CALL reporte_facturas_por_estado();

-- PROCEDIMIENTO: Consultar las facturas pendientes, de la más antigua a la más reciente

DELIMITER //
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

-- COMPROBACIÓN: Consultar las facturas que están pendientes
CALL reporte_facturas_pendientes();





