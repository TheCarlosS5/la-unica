<?php
/**
 * Archivo: reporte_individual.php
 * Módulo: Reportes — plantilla individual reutilizable.
 * Propósito: renderizar cualquiera de los siete reportes sin duplicar la estructura de página y tabla.
 * Funcionamiento: cada archivo pequeño asigna tipoReporte y requiere esta plantilla; aquí se valida la
 * clave, se carga el catálogo, se consultan filas y se muestran error, estado vacío o tabla detallada.
 * Puntos clave: columnas y formatos provienen de datos_reportes.php; los datos dinámicos se escapan;
 * cuando aplica, la fila enlaza al comprobante de la factura.
 */

require __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/datos_reportes.php';

$reportes = catalogoReportes();
if (!isset($tipoReporte) || !is_string($tipoReporte) || !isset($reportes[$tipoReporte])) {
    http_response_code(404);
    exit('El reporte solicitado no está disponible.');
}

$reporteSeleccionado = $reportes[$tipoReporte];
$titulo = $reporteSeleccionado['titulo'];
$seccionActiva = 'reportes';
$errorLectura = false;
$filasReporte = [];

try {
    $filasReporte = obtenerFilasReporte($conexion, $tipoReporte);
} catch (Throwable $e) {
    registrarErrorServidor('leer_reporte_' . $tipoReporte, $e);
    http_response_code(500);
    $errorLectura = true;
}

require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <a href="<?= h($baseUrl) ?>/php/reportes/ver_reportes.php">Reportes</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span><?= h($reporteSeleccionado['titulo']) ?></span>
        </nav>
        <h1 class="encabezado-titulo"><?= h($reporteSeleccionado['titulo']) ?></h1>
        <p class="encabezado-descripcion"><?= h($reporteSeleccionado['descripcion']) ?></p>
    </div>
</div>

<div class="filtro-acciones">
    <a class="boton secundario" href="<?= h($baseUrl) ?>/php/reportes/ver_reportes.php">Volver al resumen de reportes</a>
</div>

<?php if ($errorLectura): ?>
    <div class="mensaje error alerta alerta-error" role="alert">No se pudo generar el reporte. Revisa la conexión y que estén disponibles las vistas y procedimientos definidos en <code>referencias/db.sql</code>.</div>
<?php elseif ($filasReporte === []): ?>
    <div class="sin-datos estado-vacio" role="status">
        <h2 class="estado-vacio-titulo"><?= h($reporteSeleccionado['vacio']) ?></h2>
        <p class="estado-vacio-texto"><?= h($reporteSeleccionado['descripcion']) ?></p>
    </div>
<?php else: ?>
    <section class="panel-reporte" aria-labelledby="titulo-reporte">
        <div class="panel-reporte-cabecera">
            <div>
                <p class="reporte-fuente">Fuente: <?= h($reporteSeleccionado['fuente']) ?></p>
                <h2 id="titulo-reporte" class="reporte-titulo"><?= h($reporteSeleccionado['titulo']) ?></h2>
                <p class="reporte-descripcion"><?= h($reporteSeleccionado['descripcion']) ?></p>
            </div>
            <p class="facturas-conteo" role="status">Registros: <?= number_format(count($filasReporte), 0, ',', '.') ?></p>
        </div>
        <p id="nota-reporte" class="nota-reporte"><?= h($reporteSeleccionado['nota']) ?></p>
        <div class="tabla-wrap tabla-contenedor">
            <table class="tabla" aria-describedby="nota-reporte">
                <thead>
                    <tr>
                        <?php foreach ($reporteSeleccionado['columnas'] as $columna): ?>
                            <th scope="col"<?= in_array($columna['tipo'], ['entero', 'moneda'], true) ? ' class="texto-derecha"' : '' ?>><?= h($columna['titulo']) ?></th>
                        <?php endforeach; ?>
                        <?php if ($reporteSeleccionado['enlace_factura']): ?>
                            <th scope="col" class="texto-derecha">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filasReporte as $fila): ?>
                        <tr>
                            <?php foreach ($reporteSeleccionado['columnas'] as $columna): ?>
                                <?php $valor = $fila[$columna['campo']] ?? ''; ?>
                                <td<?= in_array($columna['tipo'], ['entero', 'moneda'], true) ? ' class="texto-derecha"' : '' ?>>
                                    <?php if ($columna['tipo'] === 'id'): ?>
                                        <span class="chip-id">#<?= h($valor) ?></span>
                                    <?php elseif ($columna['tipo'] === 'moneda'): ?>
                                        <span class="celda-destacada"><?= h(formatearMonedaCop($valor)) ?></span>
                                    <?php elseif ($columna['tipo'] === 'entero'): ?>
                                        <?= number_format((int)$valor, 0, ',', '.') ?>
                                    <?php elseif ($columna['tipo'] === 'fecha'): ?>
                                        <?php try { $fechaReporte = (new DateTimeImmutable((string)$valor))->format('d/m/Y H:i'); } catch (Throwable $e) { $fechaReporte = 'Sin fecha disponible'; } ?>
                                        <span class="celda-fecha"><?= h($fechaReporte) ?></span>
                                    <?php elseif ($columna['tipo'] === 'estado'): ?>
                                        <span class="estado-factura <?= strtolower(h($valor)) ?>"><?= h($valor) ?></span>
                                    <?php else: ?>
                                        <?= h($valor) ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <?php if ($reporteSeleccionado['enlace_factura']): ?>
                                <td class="texto-derecha">
                                    <a class="boton secundario boton-factura-detalle" href="<?= h($baseUrl) ?>/php/facturas/imprimir_factura.php?id=<?= (int)$fila['id_factura'] ?>">Ver factura</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/../includes/pie.php'; ?>
