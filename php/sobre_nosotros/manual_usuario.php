<?php
/**
 * Archivo: manual_usuario.php
 * Módulo: Sobre nosotros — Manual del Usuario.
 * Propósito: mostrar el manual PDF dentro de la interfaz administrativa.
 * Funcionamiento: incrusta referencias/manual.pdf en un visor integrado del navegador, conservando
 * el menú, la cabecera y la navegación del sistema.
 */

require __DIR__ . '/inicializar.php';

$titulo = 'Manual del Usuario';
$seccionActiva = 'sobre_nosotros';
$manualUrl = $baseUrl . '/referencias/manual.pdf';
$manualPath = dirname(__DIR__, 2) . '/referencias/manual.pdf';
$manualDisponible = is_file($manualPath);

require __DIR__ . '/../includes/menu.php';
?>

<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <a href="<?= h($baseUrl) ?>/php/sobre_nosotros/index.php">Sobre nosotros</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span aria-current="page">Manual del Usuario</span>
        </nav>
        <h1 class="encabezado-titulo">Manual del Usuario</h1>
        <p class="encabezado-descripcion">Lee la guía de La Única desde esta misma pantalla. Usa los controles del visor para cambiar de página, buscar texto y ajustar el zoom.</p>
    </div>
    <div class="encabezado-acciones">
        <a class="boton secundario" href="<?= h($baseUrl) ?>/php/sobre_nosotros/index.php">
            <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6" /></svg>
            <span>Volver a Sobre nosotros</span>
        </a>
    </div>
</div>

<section class="panel-reporte manual-panel" aria-label="Visor del manual del usuario">
    <div class="manual-panel-cabecera">
        <div>
            <h2 class="modulo-titulo">Guía de uso de La Única</h2>
            <p class="manual-panel-descripcion">Documento PDF integrado en el aplicativo; no se abre una pestaña adicional.</p>
        </div>
        <span class="manual-formato">PDF</span>
    </div>

    <?php if ($manualDisponible): ?>
        <div class="manual-pdf-marco">
            <iframe
                class="manual-pdf-visor"
                src="<?= h($manualUrl) ?>#view=FitH"
                title="Manual del Usuario de La Única en formato PDF"
                loading="eager">
                Tu navegador no permite mostrar PDF dentro de la página.
                <a href="<?= h($manualUrl) ?>">Consultar el archivo del manual</a>.
            </iframe>
        </div>
    <?php else: ?>
        <div class="mensaje error alerta alerta-error" role="alert">
            No se encontró <code>referencias/manual.pdf</code>. Verifica que el documento esté dentro de la carpeta <code>referencias</code> del proyecto.
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/pie.php'; ?>
