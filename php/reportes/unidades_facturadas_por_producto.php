<?php
/**
 * Archivo: unidades_facturadas_por_producto.php
 * Módulo: Reportes — unidades facturadas por producto.
 * Propósito: exponer la suma de cantidades de detalle por artículo.
 * Funcionamiento: asigna el tipo unidades_productos y delega lectura y presentación a
 * reporte_individual.php.
 * Punto clave: consulta vista_unidades_productos; incluye detalles de todos los estados y solo muestra
 * productos que aparecen al menos una vez en una factura.
 */

$tipoReporte = 'unidades_productos';
require __DIR__ . '/reporte_individual.php';
