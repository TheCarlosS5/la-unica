<?php
/**
 * Archivo: total_vigente_por_factura.php
 * Módulo: Reportes — total por factura (el nombre del archivo se conserva para no romper enlaces).
 * Propósito: exponer el importe de cada venta vigente.
 * Funcionamiento: asigna el tipo total_facturas y reutiliza reporte_individual.php para obtener y
 * presentar las filas definidas en datos_reportes.php.
 * Punto clave: consulta vista_total_facturas; usa el precio guardado en la venta y excluye las
 * facturas canceladas y las que no tienen detalles.
 */

$tipoReporte = 'total_facturas';
require __DIR__ . '/reporte_individual.php';
