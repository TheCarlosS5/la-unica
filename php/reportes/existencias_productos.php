<?php
/**
 * Archivo: existencias_productos.php
 * Módulo: Reportes — existencias de productos.
 * Propósito: mostrar stock actual y precio vigente del catálogo.
 * Funcionamiento: asigna el tipo stock_productos y delega consulta y renderizado a
 * reporte_individual.php.
 * Punto clave: ejecuta reporte_stock_productos(), ordenado desde las existencias menores.
 */

$tipoReporte = 'stock_productos';
require __DIR__ . '/reporte_individual.php';
