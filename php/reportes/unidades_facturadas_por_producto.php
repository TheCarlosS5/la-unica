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
 * Redirige el acceso acumulado al reporte de unidades por fechas, usando un rango
 * que cubre el historial.
 * ============================================================================
 */

/** Ruta anterior: redirige al reporte consolidado de todo el historial. */
date_default_timezone_set('America/Bogota');
header('Location: unidades_vendidas_por_producto.php?desde=1900-01-01&hasta=' . date('Y-m-d'), true, 302);
exit;
