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
 * Consulta un rango de fechas y muestra ventas por factura, día, semana, mes o año,
 * separando las canceladas.
 * ============================================================================
 */

/**
 * Archivo: ventas.php
 * Módulo: Reportes — ventas.
 * Propósito: responder cuánto se vendió en un rango de fechas, ya sea factura por factura (cierre de
 * caja) o agrupado por día, semana, mes o año (tendencia).
 * Funcionamiento: lee desde/hasta (por defecto, hoy) y "ver" por GET. Con ver=factura llama a
 * reporte_ventas_por_fecha(); con las demás opciones, a reporte_ventas_por_periodo().
 * Las tarjetas de totales se calculan igual en todas las vistas. Solo las facturas pagadas suman.
 */

require __DIR__ . '/../config/conexion.php';

$titulo = 'Ventas';
$seccionActiva = 'reportes';
$vistas = ['factura' => 'Factura', 'dia' => 'Día', 'semana' => 'Semana', 'mes' => 'Mes', 'anio' => 'Año'];
$hoy = date('Y-m-d');
$desde = trim((string)($_GET['desde'] ?? $hoy));
$hasta = trim((string)($_GET['hasta'] ?? $hoy));
$ver = (string)($_GET['ver'] ?? 'factura');
$errorFiltro = '';
$errorLectura = false;
$filas = [];

if (!validarFecha($desde) || !validarFecha($hasta)) {
    $errorFiltro = 'Ingresa fechas válidas.';
} elseif ($desde > $hasta) {
    $errorFiltro = 'La fecha "desde" no puede ser posterior a la fecha "hasta".';
} elseif (!isset($vistas[$ver])) {
    $errorFiltro = 'La opción "Ver por" no es válida.';
} else {
    try {
        if ($ver === 'factura') {
            $filas = llamarProcedimiento($conexion, 'CALL reporte_ventas_por_fecha(?, ?)', 'ss', [$desde, $hasta]);
        } else {
            $filas = llamarProcedimiento($conexion, 'CALL reporte_ventas_por_periodo(?, ?, ?)', 'sss', [$desde, $hasta, $ver]);
        }
    } catch (Throwable $e) {
        registrarErrorServidor('reporte_ventas', $e);
        $errorLectura = true;
    }
}

// Totales del rango: en la vista por factura se cuentan las filas; en las agrupadas se suman los grupos.
$ventas = 0;
$totalVendido = 0.0;
$canceladas = 0;
$totalCancelado = 0.0;
foreach ($filas as $fila) {
    if ($ver === 'factura') {
        if ($fila['estado_factura'] === 'Pagado') {
            $ventas++;
            $totalVendido += (float)$fila['total_factura'];
        } else {
            $canceladas++;
            $totalCancelado += (float)$fila['total_factura'];
        }
    } else {
        $ventas += (int)$fila['ventas'];
        $totalVendido += (float)$fila['total_vendido'];
        $canceladas += (int)$fila['canceladas'];
        $totalCancelado += (float)$fila['total_cancelado'];
    }
}
$promedio = $ventas > 0 ? $totalVendido / $ventas : 0;

/**
 * Convierte el primer día de un período en un texto legible según la agrupación.
 * @param string $inicio Fecha YYYY-MM-DD del primer día del período.
 * @param string $ver dia, semana, mes o anio.
 * @return string Por ejemplo «Mié 04/11/2026», «02/11 al 08/11/2026», «Noviembre 2026» o «2026».
 */
function etiquetaPeriodo($inicio, $ver) {
    $meses = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    $fecha = strtotime($inicio);
    if ($ver === 'semana') {
        return date('d/m', $fecha) . ' al ' . date('d/m/Y', strtotime('+6 days', $fecha));
    }
    if ($ver === 'mes') {
        return $meses[(int)date('n', $fecha)] . ' ' . date('Y', $fecha);
    }
    if ($ver === 'anio') {
        return date('Y', $fecha);
    }
    return $dias[(int)date('w', $fecha)] . ' ' . date('d/m/Y', $fecha);
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
            <a href="ver_reportes.php">Reportes</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Ventas</span>
        </nav>
        <h1 class="encabezado-titulo">Ventas</h1>
        <p class="encabezado-descripcion">Por factura para el cierre de caja, o por día, semana, mes o año para ver cómo van las ventas. Solo las facturas pagadas suman.</p>
    </div>
</div>

<form class="filtros-facturas filtros-historial" method="get" action="ventas.php" aria-label="Filtrar ventas">
    <div class="campo">
        <label for="desde">Desde</label>
        <input id="desde" name="desde" type="date" value="<?= h($desde) ?>" required>
    </div>
    <div class="campo">
        <label for="hasta">Hasta</label>
        <input id="hasta" name="hasta" type="date" value="<?= h($hasta) ?>" required>
    </div>
    <div class="campo">
        <label for="ver">Ver por</label>
        <select id="ver" name="ver">
            <?php foreach ($vistas as $valor => $texto): ?>
                <option value="<?= h($valor) ?>"<?= $valor === $ver ? ' selected' : '' ?>><?= h($texto) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filtro-acciones">
        <button type="submit">Consultar</button>
        <a class="boton secundario" href="ventas.php">Hoy</a>
    </div>
</form>

<?php if ($errorFiltro !== ''): ?>
    <div class="mensaje error alerta alerta-error" role="alert"><?= h($errorFiltro) ?></div>
<?php elseif ($errorLectura): ?>
    <div class="mensaje error alerta alerta-error" role="alert">No se pudo generar el reporte. Revisa que los procedimientos de ventas existan en la base de datos; el detalle quedó en Historial › Errores.</div>
<?php else: ?>
    <section class="tarjetas reportes-resumen-grid" aria-label="Totales del rango">
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Total vendido</h2>
            <p class="reportes-resumen-valor"><?= h(formatearMonedaCop($totalVendido)) ?></p>
            <p class="reportes-resumen-detalle">Solo facturas pagadas.</p>
        </article>
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Ventas</h2>
            <p class="reportes-resumen-valor"><?= number_format($ventas, 0, ',', '.') ?></p>
            <p class="reportes-resumen-detalle">Facturas pagadas en el rango.</p>
        </article>
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Promedio por venta</h2>
            <p class="reportes-resumen-valor"><?= h(formatearMonedaCop($promedio)) ?></p>
            <p class="reportes-resumen-detalle">Total vendido ÷ número de ventas.</p>
        </article>
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Canceladas</h2>
            <p class="reportes-resumen-valor"><?= number_format($canceladas, 0, ',', '.') ?></p>
            <p class="reportes-resumen-detalle">Por <?= h(formatearMonedaCop($totalCancelado)) ?>; no suman al total vendido.</p>
        </article>
    </section>

    <?php if ($filas === []): ?>
        <div class="sin-datos estado-vacio" role="status">
            <h2 class="estado-vacio-titulo">No hay facturas en este rango</h2>
            <p class="estado-vacio-texto">Prueba con otras fechas.</p>
        </div>
    <?php elseif ($ver === 'factura'): ?>
        <div class="tabla-wrap tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr>
                        <th scope="col">Factura</th>
                        <th scope="col">Fecha y hora</th>
                        <th scope="col">Cliente</th>
                        <th scope="col">Estado</th>
                        <th scope="col" class="texto-derecha">Total</th>
                        <th scope="col" class="texto-derecha no-imprimir-reporte">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filas as $factura): ?>
                        <tr>
                            <td><span class="chip-id">#<?= (int)$factura['id_factura'] ?></span></td>
                            <td><span class="celda-fecha"><?= h(date('d/m/Y H:i', strtotime($factura['fecha_factura']))) ?></span></td>
                            <td><?= h($factura['cliente']) ?></td>
                            <td><span class="estado-factura <?= strtolower(h($factura['estado_factura'])) ?>"><?= h($factura['estado_factura']) ?></span></td>
                            <td class="texto-derecha celda-destacada"><?= h(formatearMonedaCop($factura['total_factura'])) ?></td>
                            <td class="texto-derecha no-imprimir-reporte"><a class="boton secundario boton-factura-detalle" href="<?= h($baseUrl) ?>/php/facturas/imprimir_factura.php?id=<?= (int)$factura['id_factura'] ?>">Ver factura</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="tabla-wrap tabla-contenedor">
            <table class="tabla">
                <thead>
                    <tr>
                        <th scope="col"><?= h($vistas[$ver]) ?></th>
                        <th scope="col" class="texto-derecha">Ventas</th>
                        <th scope="col" class="texto-derecha">Total vendido</th>
                        <th scope="col" class="texto-derecha">Canceladas</th>
                        <th scope="col" class="texto-derecha">Total cancelado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filas as $periodo): ?>
                        <tr>
                            <td><?= h(etiquetaPeriodo($periodo['inicio_periodo'], $ver)) ?></td>
                            <td class="texto-derecha"><?= number_format((int)$periodo['ventas'], 0, ',', '.') ?></td>
                            <td class="texto-derecha celda-destacada"><?= h(formatearMonedaCop($periodo['total_vendido'])) ?></td>
                            <td class="texto-derecha"><?= number_format((int)$periodo['canceladas'], 0, ',', '.') ?></td>
                            <td class="texto-derecha"><?= h(formatearMonedaCop($periodo['total_cancelado'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
