-- Archivo: referencias/productos_prueba.sql
-- Propósito: cargar 30 productos de prueba en la base la_unica.
-- Uso: ejecutar después de importar db.sql. Los IDs se generan automáticamente.

USE la_unica;

INSERT INTO productos (nombre, unidad_medida, stock, precio_unitario) VALUES
('Arroz Diana 500 g', 'paquete', 120, 2800.00),
('Azúcar Manuelita 1 kg', 'paquete', 80, 4500.00),
('Aceite Premier 1 L', 'botella', 45, 11900.00),
('Sal Refisal 500 g', 'paquete', 60, 1500.00),
('Café Sello Rojo 250 g', 'paquete', 50, 9800.00),
('Chocolate Corona 250 g', 'paquete', 40, 7200.00),
('Panela 500 g', 'unidad', 70, 2500.00),
('Lentejas 500 g', 'paquete', 55, 4200.00),
('Fríjol bola roja 500 g', 'paquete', 50, 6500.00),
('Pasta Doria spaghetti 250 g', 'paquete', 90, 2300.00),
('Harina PAN 1 kg', 'paquete', 65, 4900.00),
('Atún Van Camps 175 g', 'lata', 75, 6800.00),
('Leche Alquería 1 L', 'bolsa', 100, 3900.00),
('Huevos AA', 'unidad', 300, 600.00),
('Queso campesino 500 g', 'unidad', 25, 12000.00),
('Mantequilla La Fina 125 g', 'unidad', 30, 4100.00),
('Pan tajado Bimbo', 'paquete', 35, 6500.00),
('Gaseosa Coca-Cola 1.5 L', 'botella', 60, 5500.00),
('Agua Cristal 600 ml', 'botella', 150, 1800.00),
('Jugo Hit 1 L', 'botella', 40, 4300.00),
('Galletas Saltín Noel', 'paquete', 45, 3600.00),
('Papas Margarita 105 g', 'paquete', 70, 4000.00),
('Detergente Fab 1 kg', 'bolsa', 30, 12500.00),
('Jabón Rey en barra', 'unidad', 80, 2700.00),
('Papel higiénico Familia x4', 'paquete', 50, 8900.00),
('Crema dental Colgate 100 ml', 'unidad', 40, 6200.00),
('Shampoo Sedal 340 ml', 'botella', 25, 13500.00),
('Blanqueador Límpido 1 L', 'botella', 35, 3800.00),
('Velas de cera', 'paquete', 20, 3000.00),
('Fósforos El Sol', 'caja', 100, 500.00);
