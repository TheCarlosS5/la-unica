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
 * Mantiene un enlace anterior y lo redirige al resumen: esta copia no implementa
 * ventas por cobrar.
 * ============================================================================
 */

/** Compatibilidad: esta versión no tiene ventas por cobrar. */
header('Location: ver_reportes.php', true, 302);
exit;
