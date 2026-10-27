<?php
/**
 * Archivo: ver_reportes.php
 * Módulo: Reportes — resumen general.
 * Propósito: reunir los resultados de los siete reportes y presentar sus indicadores en tarjetas.
 * Funcionamiento: carga cada conjunto mediante obtenerFilasReporte(), agrega clientes, facturas,
 * totales, unidades, detalles, existencias y estados, y enlaza cada tarjeta con su reporte individual.
 * Punto clave: conserva redirección de compatibilidad para enlaces antiguos con ?reporte=; un error
 * al consultar cualquier fuente impide mostrar un resumen parcial.
 */

require __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/datos_reportes.php';

$reportes = catalogoReportes();

// Conserva compatibilidad con los enlaces antiguos basados en ?reporte=.
$reporteSolicitado = $_GET['reporte'] ?? null;
if (is_string($reporteSolicitado) && isset($reportes[$reporteSolicitado])) {
    header('Location: ' . $baseUrl . '/php/reportes/' . rawurlencode($reportes[$reporteSolicitado]['archivo']));
    exit;
}

$titulo = 'Reportes';
$seccionActiva = 'reportes';
$errorLectura = false;
$filasPorReporte = [];

try {
    foreach ($reportes as $claveReporte => $reporte) {
        $filasPorReporte[$claveReporte] = obtenerFilasReporte($conexion, $claveReporte);
    }
} catch (Throwable $e) {
    registrarErrorServidor('leer_resumen_reportes', $e);
    http_response_code(500);
    $errorLectura = true;
}

if (!$errorLectura) {
    $totalClientes = count($filasPorReporte['clientes']);
    $totalFacturasClientes = array_sum(array_map(
        static fn(array $fila): int => (int)$fila['total_facturas'],
        $filasPorReporte['clientes']
    ));
    $totalVigente = array_reduce(
        $filasPorReporte['total_facturas'],
        static fn(float $total, array $fila): float => $total + (float)$fila['total_factura'],
        0.0
    );
    $totalUnidades = array_sum(array_map(
        static fn(array $fila): int => (int)$fila['unidades_facturadas'],
        $filasPorReporte['unidades_productos']
    ));
    $totalStock = array_sum(array_map(
        static fn(array $fila): int => (int)$fila['stock'],
        $filasPorReporte['stock_productos']
    ));
    $totalFacturasPorEstado = array_sum(array_map(
        static fn(array $fila): int => (int)$fila['total_facturas'],
        $filasPorReporte['por_estado']
    ));
    $desgloseEstados = array_map(
        static fn(array $fila): string => (string)$fila['estado_factura'] . ': ' . number_format((int)$fila['total_facturas'], 0, ',', '.'),
        $filasPorReporte['por_estado']
    );
    $facturasEnDetalle = count(array_unique(array_column($filasPorReporte['detalle_facturas'], 'id_factura')));

    $resumen = [
        'clientes' => [
            'valor' => number_format($totalClientes, 0, ',', '.'),
            'detalle' => number_format($totalFacturasClientes, 0, ',', '.') . ' facturas registradas entre todos los clientes, sin filtrar por estado.',
        ],
        'total_facturas' => [
            'valor' => formatearMonedaCop(number_format($totalVigente, 2, '.', '')),
            'detalle' => number_format(count($filasPorReporte['total_facturas']), 0, ',', '.') . ' facturas con detalle; los importes usan los precios vigentes e incluyen todos los estados.',
        ],
        'unidades_productos' => [
            'valor' => number_format($totalUnidades, 0, ',', '.'),
            'detalle' => number_format(count($filasPorReporte['unidades_productos']), 0, ',', '.') . ' productos con unidades facturadas; incluye detalles de todos los estados.',
        ],
        'detalle_facturas' => [
            'valor' => number_format(count($filasPorReporte['detalle_facturas']), 0, ',', '.'),
            'detalle' => 'Líneas de detalle distribuidas en ' . number_format($facturasEnDetalle, 0, ',', '.') . ' facturas.',
        ],
        'stock_productos' => [
            'valor' => number_format($totalStock, 0, ',', '.'),
            'detalle' => 'Unidades disponibles en ' . number_format(count($filasPorReporte['stock_productos']), 0, ',', '.') . ' productos registrados.',
        ],
        'por_estado' => [
            'valor' => number_format($totalFacturasPorEstado, 0, ',', '.'),
            'detalle' => $desgloseEstados === [] ? 'No hay facturas registradas.' : implode(' · ', $desgloseEstados) . '.',
        ],
        'pendientes' => [
            'valor' => number_format(count($filasPorReporte['pendientes']), 0, ',', '.'),
            'detalle' => 'Facturas cuyo estado actual es Pendiente.',
        ],
    ];
}

require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Reportes</span>
        </nav>
        <h1 class="encabezado-titulo">Resumen de reportes</h1>
        <p class="encabezado-descripcion">Vista general de clientes, ventas, productos, existencias y estados. Abre cada reporte para consultar su detalle.</p>
    </div>
</div>

<?php if ($errorLectura): ?>
    <div class="mensaje error alerta alerta-error" role="alert">No se pudo generar el resumen. Revisa la conexión y que estén disponibles las vistas y procedimientos definidos en <code>referencias/db.sql</code>.</div>
<?php else: ?>
    <section class="tarjetas reportes-resumen-grid" aria-label="Resumen general de los reportes">
        <?php foreach ($reportes as $claveReporte => $reporte): ?>
            <article class="reportes-resumen-card">
                <div class="modulo-cabecera">
                    <div class="modulo-icono" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18" />
                            <path d="M18 17V9M13 17V5M8 17v-3" />
                        </svg>
                    </div>
                    <h2 class="modulo-titulo"><?= h($reporte['titulo']) ?></h2>
                </div>
                <p class="reportes-resumen-valor"><?= h($resumen[$claveReporte]['valor']) ?></p>
                <p class="reportes-resumen-descripcion"><?= h($reporte['descripcion']) ?></p>
                <p class="reportes-resumen-detalle"><?= h($resumen[$claveReporte]['detalle']) ?></p>
                <div class="modulo-acciones">
                    <a class="boton" href="<?= h($baseUrl) ?>/php/reportes/<?= h($reporte['archivo']) ?>">Abrir reporte</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/../includes/pie.php'; ?>
