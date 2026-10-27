<?php
/**
 * Archivo: clientes_y_facturas.php
 * Módulo: Reportes — clientes y cantidad de facturas.
 * Propósito: ser la ruta pública del reporte que cuenta facturas relacionadas con cada cliente.
 * Funcionamiento: selecciona el tipo clientes y delega consulta, errores y presentación a
 * reporte_individual.php; los metadatos y fuentes están definidos en datos_reportes.php.
 * Punto clave: incluye clientes con cero facturas y cuenta todos los estados.
 */

$tipoReporte = 'clientes';
require __DIR__ . '/reporte_individual.php';
