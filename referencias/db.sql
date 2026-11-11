-- Archivo: referencias/db.sql
-- Módulo: Persistencia y reportes.
-- Propósito: crear la base la_unica, sus tablas y los procedimientos almacenados de los reportes.
-- Funcionamiento: debe ejecutarse en una instancia nueva; crea la base y después define sus objetos.
-- Puntos clave:
-- - No inserta datos de ejemplo ni es un script de actualización/reinicio.
-- - facturas.estado_factura admite Pagado y Cancelado; las ventas se crean como Pagado.
-- - Cada factura guarda una copia del nombre del cliente y cada detalle una copia del nombre, la unidad
--   y el precio del producto al momento de la venta. Así, editar clientes o productos no altera facturas.
-- - El total de una factura no se almacena: es la suma de cantidad × precio guardado en sus detalles.
-- - Las relaciones de detalle_facturas preservan la integridad entre facturas y productos.
-- - Los cuatro procedimientos alimentan los reportes: Ventas (por factura o agrupado por período),
--   Unidades vendidas y Existencias.
-- - log_acciones guarda el historial de acciones; los errores técnicos van al archivo logs/errores.log.

CREATE DATABASE la_unica;
USE la_unica;

-- Tabla clientes: ID manual, nombre comercial y fecha con valor predeterminado actual.
CREATE TABLE clientes (
    id_cliente INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    fecha_registro DATE DEFAULT (CURRENT_DATE)
);

-- Tabla facturas: ID y fecha automáticos, cliente relacionado (con su nombre al momento de la venta)
-- y estado inicial Pagado.
CREATE TABLE facturas (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    nombre_cliente VARCHAR(100) NOT NULL,
    estado_factura ENUM('Pagado', 'Cancelado') NOT NULL DEFAULT 'Pagado',
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

-- Tabla detalle_facturas: cantidad positiva, copia del producto al momento de la venta
-- y claves foráneas a factura y producto.
CREATE TABLE detalle_facturas (
    id_detalle_factura INT AUTO_INCREMENT PRIMARY KEY,
    id_factura INT NOT NULL,
    id_producto INT NOT NULL,
    nombre_producto VARCHAR(100) NOT NULL,
    unidad_medida VARCHAR(20) NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0),
    cantidad INT NOT NULL CHECK (cantidad > 0),
    FOREIGN KEY (id_factura) REFERENCES facturas(id_factura),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- Tabla log_acciones: log general de lo que hace el operador (crear, editar, eliminar, cancelar).
-- La aplicación solo inserta y consulta; no edita ni borra registros.
CREATE TABLE log_acciones (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATETIME NOT NULL DEFAULT (CURRENT_TIMESTAMP),
    modulo VARCHAR(30) NOT NULL,
    accion VARCHAR(30) NOT NULL,
    descripcion VARCHAR(255) NOT NULL
);

-- Procedimientos de los reportes. Las fechas p_desde y p_hasta incluyen el día completo;
-- si se envían en NULL, ese extremo del rango no se filtra.
DELIMITER //

-- Ventas por fecha: facturas del rango con su total (suma de cantidad × precio guardado).
CREATE PROCEDURE reporte_ventas_por_fecha(IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT
        facturas.id_factura,
        facturas.fecha_factura,
        facturas.nombre_cliente AS cliente,
        facturas.estado_factura,
        SUM(detalle_facturas.cantidad * detalle_facturas.precio_unitario) AS total_factura
    FROM facturas
    INNER JOIN detalle_facturas ON facturas.id_factura = detalle_facturas.id_factura
    WHERE (p_desde IS NULL OR facturas.fecha_factura >= p_desde)
      AND (p_hasta IS NULL OR facturas.fecha_factura < p_hasta + INTERVAL 1 DAY)
    GROUP BY facturas.id_factura, facturas.fecha_factura, facturas.nombre_cliente, facturas.estado_factura
    ORDER BY facturas.fecha_factura DESC;
END //

-- Ventas por período: agrupa las facturas del rango por día, semana (inicia el lunes), mes o año.
-- p_agrupacion admite 'dia', 'semana', 'mes' o 'anio'; inicio_periodo es el primer día de cada grupo.
CREATE PROCEDURE reporte_ventas_por_periodo(IN p_desde DATE, IN p_hasta DATE, IN p_agrupacion VARCHAR(10))
BEGIN
    SELECT
        CASE p_agrupacion
            WHEN 'semana' THEN DATE(facturas.fecha_factura) - INTERVAL WEEKDAY(facturas.fecha_factura) DAY
            WHEN 'mes' THEN CAST(DATE_FORMAT(facturas.fecha_factura, '%Y-%m-01') AS DATE)
            WHEN 'anio' THEN CAST(DATE_FORMAT(facturas.fecha_factura, '%Y-01-01') AS DATE)
            ELSE DATE(facturas.fecha_factura)
        END AS inicio_periodo,
        COUNT(DISTINCT CASE WHEN facturas.estado_factura = 'Pagado' THEN facturas.id_factura END) AS ventas,
        SUM(CASE WHEN facturas.estado_factura = 'Pagado'
            THEN detalle_facturas.cantidad * detalle_facturas.precio_unitario ELSE 0 END) AS total_vendido,
        COUNT(DISTINCT CASE WHEN facturas.estado_factura = 'Cancelado' THEN facturas.id_factura END) AS canceladas,
        SUM(CASE WHEN facturas.estado_factura = 'Cancelado'
            THEN detalle_facturas.cantidad * detalle_facturas.precio_unitario ELSE 0 END) AS total_cancelado
    FROM facturas
    INNER JOIN detalle_facturas ON facturas.id_factura = detalle_facturas.id_factura
    WHERE facturas.fecha_factura >= p_desde
      AND facturas.fecha_factura < p_hasta + INTERVAL 1 DAY
    GROUP BY inicio_periodo
    ORDER BY inicio_periodo;
END //

-- Unidades vendidas: suma por producto solo en facturas Pagado, de más a menos vendido.
CREATE PROCEDURE reporte_unidades_vendidas(IN p_desde DATE, IN p_hasta DATE)
BEGIN
    SELECT
        productos.id_producto,
        productos.nombre AS producto,
        productos.unidad_medida,
        SUM(detalle_facturas.cantidad) AS unidades_vendidas,
        SUM(detalle_facturas.cantidad * detalle_facturas.precio_unitario) AS total_vendido
    FROM detalle_facturas
    INNER JOIN facturas ON detalle_facturas.id_factura = facturas.id_factura
    INNER JOIN productos ON detalle_facturas.id_producto = productos.id_producto
    WHERE facturas.estado_factura = 'Pagado'
      AND (p_desde IS NULL OR facturas.fecha_factura >= p_desde)
      AND (p_hasta IS NULL OR facturas.fecha_factura < p_hasta + INTERVAL 1 DAY)
    GROUP BY productos.id_producto, productos.nombre, productos.unidad_medida
    ORDER BY unidades_vendidas DESC, productos.nombre;
END //

-- Existencias: lista productos por stock ascendente para mostrar primero los más bajos.
CREATE PROCEDURE reporte_existencias()
BEGIN
    SELECT
        productos.id_producto,
        productos.nombre,
        productos.unidad_medida,
        productos.stock,
        productos.precio_unitario
    FROM productos
    ORDER BY productos.stock ASC, productos.nombre;
END //

DELIMITER ;
