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
 * Resume cantidades e importes vendidos por producto dentro del rango consultado;
 * solo suman facturas Pagado.
 * ============================================================================
 */

/**
 * Archivo: unidades_vendidas_por_producto.php
 * Módulo: Reportes — unidades vendidas por producto.
 * Propósito: mostrar qué productos se venden más en un rango de fechas.
 * Funcionamiento: lee desde/hasta por GET (por defecto, hoy) y llama a reporte_unidades_vendidas().
 * Solo cuenta facturas Pagado.
 */

require __DIR__ . '/../config/conexion.php';

$titulo = 'Unidades vendidas por producto';
$seccionActiva = 'reportes';
$hoy = date('Y-m-d');
$desde = trim((string)($_GET['desde'] ?? $hoy));
$hasta = trim((string)($_GET['hasta'] ?? $hoy));
$errorFiltro = '';
$errorLectura = false;
$productos = [];

if (!validarFecha($desde) || !validarFecha($hasta)) {
    $errorFiltro = 'Ingresa fechas válidas.';
} elseif ($desde > $hasta) {
    $errorFiltro = 'La fecha "desde" no puede ser posterior a la fecha "hasta".';
} else {
    try {
        $productos = llamarProcedimiento($conexion, 'CALL reporte_unidades_vendidas(?, ?)', 'ss', [$desde, $hasta]);
    } catch (Throwable $e) {
        registrarErrorServidor('reporte_unidades_vendidas', $e);
        $errorLectura = true;
    }
}

$totalUnidades = array_sum(array_column($productos, 'unidades_vendidas'));
$totalVendido = array_sum(array_map('floatval', array_column($productos, 'total_vendido')));

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
            <a href="ver_reportes.php">Reportes</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Unidades vendidas</span>
        </nav>
        <h1 class="encabezado-titulo">Unidades vendidas por producto</h1>
        <p class="encabezado-descripcion">Productos más vendidos primero. Solo cuenta facturas pagadas; las canceladas no suman.</p>
    </div>
</div>

<form class="filtros-facturas" method="get" action="unidades_vendidas_por_producto.php" aria-label="Filtrar por fechas">
    <div class="campo">
        <label for="desde">Desde</label>
        <input id="desde" name="desde" type="date" value="<?= h($desde) ?>" required>
    </div>
    <div class="campo">
        <label for="hasta">Hasta</label>
        <input id="hasta" name="hasta" type="date" value="<?= h($hasta) ?>" required>
    </div>
    <div class="filtro-acciones">
        <button type="submit">Consultar</button>
        <a class="boton secundario" href="unidades_vendidas_por_producto.php">Hoy</a>
    </div>
</form>

<?php if ($errorFiltro !== ''): ?>
    <div class="mensaje error alerta alerta-error" role="alert"><?= h($errorFiltro) ?></div>
<?php elseif ($errorLectura): ?>
    <div class="mensaje error alerta alerta-error" role="alert">No se pudo generar el reporte. Revisa que el procedimiento reporte_unidades_vendidas exista en la base de datos.</div>
<?php else: ?>
    <section class="tarjetas reportes-resumen-grid" aria-label="Totales del período">
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Total vendido</h2>
            <p class="reportes-resumen-valor"><?= h(formatearMonedaCop($totalVendido)) ?></p>
            <p class="reportes-resumen-detalle">Solo facturas pagadas.</p>
        </article>
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Unidades vendidas</h2>
            <p class="reportes-resumen-valor"><?= number_format($totalUnidades, 0, ',', '.') ?></p>
            <p class="reportes-resumen-detalle">Suma de todos los productos.</p>
        </article>
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Productos</h2>
            <p class="reportes-resumen-valor"><?= count($productos) ?></p>
            <p class="reportes-resumen-detalle">Productos distintos vendidos.</p>
        </article>
    </section>

    <?php if ($productos === []): ?>
    <div class="sin-datos estado-vacio" role="status">
        <h2 class="estado-vacio-titulo">No hay productos vendidos en este período</h2>
        <p class="estado-vacio-texto">Prueba con otro rango de fechas.</p>
    </div>
    <?php else: ?>
    <div class="tabla-wrap tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Producto</th>
                    <th scope="col">Unidad</th>
                    <th scope="col" class="texto-derecha">Unidades vendidas</th>
                    <th scope="col" class="texto-derecha">Total vendido</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?= h($producto['producto']) ?></td>
                        <td><span class="insignia-unidad"><?= h($producto['unidad_medida']) ?></span></td>
                        <td class="texto-derecha"><?= number_format((int)$producto['unidades_vendidas'], 0, ',', '.') ?></td>
                        <td class="texto-derecha celda-destacada"><?= h(formatearMonedaCop($producto['total_vendido'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
