<?php
/**
 * Archivo: facturas_por_estado.php
 * Módulo: Reportes — facturas por estado.
 * Propósito: contar las facturas agrupadas por estado actual.
 * Funcionamiento: asigna el tipo por_estado y permite que reporte_individual.php obtenga y dibuje
 * las filas del procedimiento configurado.
 * Punto clave: un estado sin facturas no se agrega como fila con valor cero.
 */

$tipoReporte = 'por_estado';
require __DIR__ . '/reporte_individual.php';
