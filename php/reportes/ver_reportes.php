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
 * Muestra indicadores del día y los accesos a los cinco reportes útiles de esta copia.
 * ============================================================================
 */

/**
 * Archivo: ver_reportes.php
 * Módulo: Reportes — resumen del día.
 * Propósito: mostrar de un vistazo las ventas de hoy, los productos vendidos hoy y las existencias
 * agotadas o bajas, con acceso a cada uno de los tres reportes.
 * Funcionamiento: llama a los procedimientos de reportes con la fecha de hoy.
 */

require __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/datos_reportes.php';

$titulo = 'Reportes';
$seccionActiva = 'reportes';
$hoy = date('Y-m-d');
$errorLectura = false;

try {
    $facturasHoy = llamarProcedimiento($conexion, 'CALL reporte_ventas_por_fecha(?, ?)', 'ss', [$hoy, $hoy]);
    $productosHoy = llamarProcedimiento($conexion, 'CALL reporte_unidades_vendidas(?, ?)', 'ss', [$hoy, $hoy]);
    $existencias = llamarProcedimiento($conexion, 'CALL reporte_existencias()');
} catch (Throwable $e) {
    registrarErrorServidor('resumen_reportes', $e);
    $errorLectura = true;
}

if (!$errorLectura) {
    $ventasHoy = 0;
    $totalHoy = 0.0;
    foreach ($facturasHoy as $factura) {
        if ($factura['estado_factura'] === 'Pagado') {
            $ventasHoy++;
            $totalHoy += (float)$factura['total_factura'];
        }
    }
    $unidadesHoy = array_sum(array_column($productosHoy, 'unidades_vendidas'));
    $agotados = 0;
    $bajos = 0;
    foreach ($existencias as $producto) {
        if ((int)$producto['stock'] === 0) {
            $agotados++;
        } elseif ((int)$producto['stock'] <= STOCK_BAJO) {
            $bajos++;
        }
    }
}

/*
 * ============================================================================
 * PRESENTACION DEL MODULO
 * Con los datos y mensajes preparados, carga la navegación compartida y presenta el contenido de esta pantalla.
 * ============================================================================
 */
require __DIR__ . '/../includes/menu.php';
require __DIR__ . '/acciones_impresion.php';
?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Reportes</span>
        </nav>
        <h1 class="encabezado-titulo">Reportes</h1>
        <p class="encabezado-descripcion">Resumen de hoy, <?= h(date('d/m/Y')) ?>. Abre cada reporte para ver el detalle o consultar otras fechas.</p>
    </div>
</div>

<?php if ($errorLectura): ?>
    <div class="mensaje error alerta alerta-error" role="alert">No se pudo generar el resumen. Revisa que los procedimientos de <code>referencias/db.sql</code> existan en la base de datos.</div>
<?php else: ?>
    <section class="tarjetas reportes-resumen-grid" aria-label="Resumen de hoy">
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Ventas de hoy</h2>
            <p class="reportes-resumen-valor"><?= h(formatearMonedaCop($totalHoy)) ?></p>
            <p class="reportes-resumen-detalle"><?= $ventasHoy ?> <?= $ventasHoy === 1 ? 'factura pagada' : 'facturas pagadas' ?>.</p>
            <div class="modulo-acciones"><a class="boton" href="ventas.php">Ventas</a></div>
        </article>
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Unidades vendidas hoy</h2>
            <p class="reportes-resumen-valor"><?= number_format($unidadesHoy, 0, ',', '.') ?></p>
            <p class="reportes-resumen-detalle">De <?= count($productosHoy) ?> <?= count($productosHoy) === 1 ? 'producto' : 'productos distintos' ?>.</p>
            <div class="modulo-acciones"><a class="boton" href="unidades_vendidas_por_producto.php">Unidades vendidas</a></div>
        </article>
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Existencias</h2>
            <p class="reportes-resumen-valor"><?= $agotados ?> agotados · <?= $bajos ?> bajos</p>
            <p class="reportes-resumen-detalle">«Bajo» = <?= STOCK_BAJO ?> unidades o menos.</p>
            <div class="modulo-acciones"><a class="boton" href="existencias_productos.php">Existencias</a></div>
        </article>
    </section>
<?php endif; ?>
<section class="panel-reporte solo-pantalla" aria-labelledby="reportes-adicionales">
    <h2 id="reportes-adicionales" class="modulo-titulo">Clientes y detalle de ventas</h2>
    <p>Consulta el historial completo. Estos reportes no se limitan a las ventas de hoy.</p>
    <div class="tarjetas reportes-resumen-grid">
        <?php foreach (catalogoReportes() as $clave => $reporte): ?>
            <?php if (!in_array($clave, ['clientes', 'detalle_facturas'], true)) { continue; } ?>
            <article class="reportes-resumen-card">
                <h3 class="modulo-titulo"><?= h($reporte['titulo']) ?></h3>
                <p class="reportes-resumen-descripcion"><?= h($reporte['descripcion']) ?></p>
                <a class="boton secundario" href="<?= h($reporte['archivo']) ?>">Consultar reporte</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
