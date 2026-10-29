<?php
/**
 * Archivo: detalle_facturas.php
 * Módulo: Reportes — detalle de facturas.
 * Propósito: exponer una fila por cada línea de producto facturada.
 * Funcionamiento: asigna el tipo detalle_facturas y reutiliza reporte_individual.php para ejecutar
 * reporte_detalle_facturas() y presentar sus campos.
 * Punto clave: incluye todos los estados; precio y subtotal se calculan con el precio vigente.
 */

$tipoReporte = 'detalle_facturas';
require __DIR__ . '/reporte_individual.php';
