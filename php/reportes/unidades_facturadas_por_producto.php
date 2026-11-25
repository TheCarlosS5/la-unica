<?php
/** Ruta anterior: redirige al reporte consolidado de todo el historial. */
date_default_timezone_set('America/Bogota');
header('Location: unidades_vendidas_por_producto.php?desde=1900-01-01&hasta=' . date('Y-m-d'), true, 302);
exit;
