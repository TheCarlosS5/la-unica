<?php
/*
 * ============================================================================
 * MODULO  DOCUMENTACION Y AYUDA
 * ============================================================================
 * Reúne SRS, manuales, PAC, wiki y video. Los documentos se consultan dentro de la
 * interfaz; los PDF utilizan una huella de contenido para actualizar su enlace
 * cuando se reemplaza el archivo.
 *
 * RESPONSABILIDAD DE ESTE ARCHIVO
 * Muestra el Plan de Acción de Mejora Continua con acceso al PDF completo.
 * ============================================================================
 */

/**
 * Archivo: pac.php
 * Módulo: Sobre nosotros — Plan de Acción de Mejora Continua.
 * Propósito: mostrar el manual PDF dentro de la interfaz administrativa.
 * Funcionamiento: incrusta referencias/PAC.pdf en un visor integrado del navegador, conservando
 * el menú, la cabecera y la navegación del sistema.
 */

require __DIR__ . '/../config/conexion.php';

$titulo = 'Plan de Acción de Mejora Continua';
$seccionActiva = 'sobre_nosotros';
$manualUrl = $baseUrl . '/referencias/PAC.pdf';
$manualPath = dirname(__DIR__, 2) . '/referencias/PAC.pdf';
$manualDisponible = is_file($manualPath) && is_readable($manualPath);
if ($manualDisponible) {
    $huellaPdf = hash_file('sha256', $manualPath);
    if ($huellaPdf !== false) {
        $manualUrl .= '?v=' . substr($huellaPdf, 0, 16);
    }
}

/*
 * ============================================================================
 * PRESENTACION DEL MODULO
 * Con los datos y mensajes preparados, carga la navegación compartida y presenta el contenido de esta pantalla.
 * ============================================================================
 */
require __DIR__ . '/../includes/menu.php';
?>

<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <a href="<?= h($baseUrl) ?>/php/sobre_nosotros/index.php">Sobre nosotros</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span aria-current="page">Plan de Acción de Mejora Continua</span>
        </nav>
        <h1 class="encabezado-titulo">Plan de Acción de Mejora Continua</h1>
        <p class="encabezado-descripcion">Consulta el PAC con las acciones de mejora, las prioridades, los criterios de cierre y el seguimiento del proyecto. Usa los controles del visor para cambiar de página, buscar texto y ajustar el zoom.</p>
    </div>
    <div class="encabezado-acciones">
        <a class="boton secundario" href="<?= h($baseUrl) ?>/php/sobre_nosotros/index.php">
            <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6" /></svg>
            <span>Volver a Sobre nosotros</span>
        </a>
    </div>
</div>

<section class="panel-reporte manual-panel" aria-label="Visor del plan de acción de mejora continua">
    <div class="manual-panel-cabecera">
        <div>
            <h2 class="modulo-titulo">Plan de Acción de Mejora Continua de La Única</h2>
            <p class="manual-panel-descripcion">Documento PDF integrado en el aplicativo; no se abre una pestaña adicional.</p>
        </div>
        <span class="manual-formato">PDF</span>
    </div>

    <?php if ($manualDisponible): ?>
        <div class="manual-pdf-marco">
            <iframe
                class="manual-pdf-visor"
                src="<?= h($manualUrl) ?>#view=FitH"
                title="Plan de Acción de Mejora Continua de La Única en formato PDF"
                loading="eager">
                Tu navegador no permite mostrar PDF dentro de la página.
                <a href="<?= h($manualUrl) ?>">Consultar el archivo del manual</a>.
            </iframe>
        </div>
        <p class="manual-panel-descripcion">Si el visor no aparece, <a href="<?= h($manualUrl) ?>" download>descarga el PDF para consultarlo</a>.</p>
    <?php else: ?>
        <div class="mensaje error alerta alerta-error" role="alert">
            No se encontró <code>referencias/PAC.pdf</code>. Verifica que el documento esté dentro de la carpeta <code>referencias</code> del proyecto.
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/pie.php'; ?>
