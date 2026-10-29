<?php
/**
 * Archivo: facturas_pendientes.php
 * Módulo: Reportes — facturas pendientes.
 * Propósito: listar facturas cuyo estado registrado es Pendiente.
 * Funcionamiento: asigna el tipo pendientes y reutiliza reporte_individual.php para consultar
 * reporte_facturas_pendientes() y mostrar sus resultados.
 * Punto clave: es una consulta; no cambia estado ni inventario.
 */

$tipoReporte = 'pendientes';
require __DIR__ . '/reporte_individual.php';
