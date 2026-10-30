<?php
/**
 * Archivo: unidades_facturadas_por_producto.php
 * Módulo: Reportes — unidades vendidas por producto.
 * Propósito: exponer la suma de cantidades de detalle por artículo.
 * Funcionamiento: asigna el tipo unidades_productos y delega lectura y presentación a
 * reporte_individual.php.
 * Punto clave: consulta vista_unidades_productos; excluye las facturas canceladas y solo muestra
 * productos con al menos una venta vigente.
 */

$tipoReporte = 'unidades_productos';
require __DIR__ . '/reporte_individual.php';
