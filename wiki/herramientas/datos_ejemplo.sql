-- Datos de ejemplo usados para las capturas de la wiki.
-- Solo demostración aislada: ajustar USE al mismo nombre exclusivo del esquema nuevo.
-- No es migración ni limpieza de una base existente. Stock del catálogo = existencia actual ficticia.
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

-- Dos estados vigentes. Se guarda la copia histórica del nombre del cliente.
INSERT INTO facturas (id_factura,id_cliente,nombre_cliente,estado_factura,fecha_factura)
SELECT d.id_factura,c.id_cliente,c.nombre,d.estado,TIMESTAMP(CURDATE() - INTERVAL d.dias DAY,d.hora)
FROM (
 SELECT 1 id_factura,1001 id_cliente,'Pagado' estado,0 dias,'09:15:00' hora UNION ALL
 SELECT 2,1002,'Pagado',1,'11:40:00' UNION ALL SELECT 3,1001,'Cancelado',1,'16:05:00' UNION ALL
 SELECT 4,1003,'Pagado',2,'08:30:00' UNION ALL SELECT 5,1004,'Pagado',0,'13:20:00' UNION ALL
 SELECT 6,1002,'Pagado',0,'10:00:00' UNION ALL SELECT 7,1005,'Cancelado',1,'17:45:00' UNION ALL
 SELECT 8,1006,'Pagado',0,'12:10:00'
) d INNER JOIN clientes c ON c.id_cliente=d.id_cliente;

-- Cada línea guarda el precio de la venta; aquí se copia el precio del catálogo al momento de cargar.
INSERT INTO detalle_facturas (id_factura,id_producto,nombre_producto,unidad_medida,cantidad,precio_unitario)
SELECT d.id_factura, d.id_producto, p.nombre, p.unidad_medida, d.cantidad, p.precio_unitario
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
