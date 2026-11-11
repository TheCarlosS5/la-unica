<?php
/*
 * ============================================================================
 * MODULO  REPORTES E IMPRESION
 * ============================================================================
 * Transforma los datos de ventas, clientes e inventario en consultas para revisar la
 * tienda. Las consultas conservan las reglas de cada reporte; la presentación ofrece
 * impresión y guardado PDF mediante el navegador.
 *
 * RESPONSABILIDAD DE ESTE ARCHIVO
 * Selecciona la consulta de líneas de venta con cliente, estado, fecha, producto,
 * cantidad, precio histórico y subtotal.
 * ============================================================================
 */

/**
 * Archivo: detalle_facturas.php
 * Módulo: Reportes — detalle de facturas.
 * Propósito: exponer una fila por cada línea de producto facturada.
 * Funcionamiento: asigna el tipo detalle_facturas y reutiliza reporte_individual.php para ejecutar
 * reporte_detalle_facturas() y presentar sus campos.
 * Punto clave: incluye todos los estados; precio y subtotal son los registrados en la venta.
 */

$tipoReporte = 'detalle_facturas';
require __DIR__ . '/reporte_individual.php';
