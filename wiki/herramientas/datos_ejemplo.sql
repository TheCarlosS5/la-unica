-- Datos de ejemplo usados para las capturas de la wiki.
-- Ejecutar DESPUÉS de referencias/db.sql, sobre una base la_unica vacía.
USE la_unica;

INSERT INTO clientes VALUES
(1001,'Tienda Don Pepe','2026-08-03'),(1002,'Supermercado La Esquina','2026-08-11'),
(1003,'Panadería El Trigal','2026-08-20'),(1004,'Restaurante Sabor Criollo','2026-09-02'),
(1005,'Cafetería Aroma','2026-09-15'),(1006,'Minimercado Los Andes','2026-10-01'),
(1007,'Droguería San Rafael','2026-10-12'),(1008,'Frutería Doña Rosa','2026-10-20');

INSERT INTO productos (nombre,unidad_medida,stock,precio_unitario) VALUES
('Arroz Diana','kg',120,4800.00),('Aceite Gourmet','litro',45,15900.00),
('Azúcar Manuelita','kg',80,4200.00),('Café Sello Rojo','libra',30,12500.00),
('Leche Alpina','litro',60,4300.00),('Huevos AA','cubeta',18,16500.00),
('Panela','unidad',0,3500.00),('Frijol cargamanto','kg',25,9800.00),
('Harina PAN','kg',40,5200.00),('Sal Refisal','kg',3,1900.00);

-- Pendiente = venta por cobrar; la aplicación también puede crearla desde «Nueva factura».
INSERT INTO facturas (id_cliente,estado_factura,fecha_factura) VALUES
(1001,'Pagado','2026-10-02 09:15:00'),(1002,'Pagado','2026-10-05 11:40:00'),
(1001,'Cancelado','2026-10-08 16:05:00'),(1003,'Pagado','2026-10-14 08:30:00'),
(1004,'Pendiente','2026-10-18 13:20:00'),(1002,'Pagado','2026-10-22 10:00:00'),
(1005,'Pendiente','2026-10-25 17:45:00'),(1006,'Pagado','2026-10-28 12:10:00');

-- Cada línea guarda el precio de la venta; aquí se copia el precio del catálogo al momento de cargar.
INSERT INTO detalle_facturas (id_factura,id_producto,cantidad,precio_unitario)
SELECT d.id_factura, d.id_producto, d.cantidad, p.precio_unitario
FROM (
    SELECT 1 AS id_factura, 1 AS id_producto, 10 AS cantidad UNION ALL SELECT 1,2,2 UNION ALL SELECT 1,5,6
    UNION ALL SELECT 2,3,15 UNION ALL SELECT 2,4,4 UNION ALL SELECT 2,9,5 UNION ALL SELECT 3,6,2
    UNION ALL SELECT 4,9,12 UNION ALL SELECT 4,3,8 UNION ALL SELECT 4,5,10 UNION ALL SELECT 5,2,3
    UNION ALL SELECT 5,8,6 UNION ALL SELECT 6,1,25 UNION ALL SELECT 6,6,3 UNION ALL SELECT 6,4,2
    UNION ALL SELECT 7,4,3 UNION ALL SELECT 7,5,4 UNION ALL SELECT 8,1,8 UNION ALL SELECT 8,8,4
    UNION ALL SELECT 8,10,5
) AS d
INNER JOIN productos p ON p.id_producto = d.id_producto
ORDER BY d.id_factura, d.id_producto;
