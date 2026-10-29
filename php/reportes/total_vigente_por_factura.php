<?php
/**
 * Archivo: total_vigente_por_factura.php
 * Módulo: Reportes — total vigente por factura.
 * Propósito: exponer la consulta de importes actuales calculados por factura.
 * Funcionamiento: asigna el tipo total_facturas y reutiliza reporte_individual.php para obtener y
 * presentar las filas definidas en datos_reportes.php.
 * Punto clave: consulta vista_total_facturas; solo aparecen facturas con detalles y usa el precio
 * vigente del producto, sin filtrar por estado.
 */

$tipoReporte = 'total_facturas';
require __DIR__ . '/reporte_individual.php';
