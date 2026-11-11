-- Archivo: referencias/clientes_prueba.sql
-- Propósito: cargar 10 clientes de prueba en la base la_unica.
-- Uso: ejecutar después de importar db.sql. id_cliente es el documento; la fecha la asigna MySQL.

USE la_unica;

INSERT INTO clientes (id_cliente, nombre) VALUES
(1075234567, 'María Fernanda Gómez'),
(1075345678, 'Juan Carlos Perdomo'),
(1080123456, 'Luisa Fernanda Cabrera'),
(7712345, 'Jorge Enrique Vargas'),
(36178902, 'Ana Lucía Trujillo'),
(1075456789, 'Andrés Felipe Rojas'),
(1081234567, 'Daniela Polanía Muñoz'),
(12123456, 'Carlos Alberto Medina'),
(55167890, 'Gloria Patricia Losada'),
(1075567890, 'Santiago Andrade Charry');
