<?php
/**
 * Archivo: video_explicativo.php
 * Módulo: Sobre nosotros — Video explicativo.
 * Propósito: reproducir el video de presentación del aplicativo dentro de la interfaz.
 * Funcionamiento: carga el archivo local assets/video/1.mp4 mediante el reproductor HTML5,
 * sin abrir pestañas externas ni iniciar la reproducción automáticamente.
 */

require __DIR__ . '/inicializar.php';

$titulo = 'Video explicativo';
$seccionActiva = 'sobre_nosotros';
$videoUrl = $baseUrl . '/assets/video/1.mp4';
$videoPath = dirname(__DIR__, 2) . '/assets/video/1.mp4';
$videoDisponible = is_file($videoPath);

require __DIR__ . '/../includes/menu.php';
?>

<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <a href="<?= h($baseUrl) ?>/php/sobre_nosotros/index.php">Sobre nosotros</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span aria-current="page">Video explicativo</span>
        </nav>
        <h1 class="encabezado-titulo">Video explicativo</h1>
        <p class="encabezado-descripcion">Conoce las principales funciones de La Única mediante esta presentación audiovisual.</p>
    </div>
    <div class="encabezado-acciones">
        <a class="boton secundario" href="<?= h($baseUrl) ?>/php/sobre_nosotros/index.php">
            <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6" /></svg>
            <span>Volver a Sobre nosotros</span>
        </a>
    </div>
</div>

<section class="panel-reporte video-panel" aria-label="Reproductor del video explicativo">
    <div class="video-panel-cabecera">
        <div class="modulo-icono video-panel-icono" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="5" width="18" height="14" rx="2" />
                <path d="m10 9 5 3-5 3z" />
            </svg>
        </div>
        <div>
            <h2 class="modulo-titulo">Presentación de La Única</h2>
            <p class="video-panel-descripcion">Reproduce, pausa, ajusta el volumen o cambia a pantalla completa con los controles del reproductor.</p>
        </div>
    </div>

    <?php if ($videoDisponible): ?>
        <div class="video-reproductor-marco">
            <video
                class="video-reproductor"
                controls
                playsinline
                preload="metadata"
                aria-label="Video explicativo del aplicativo La Única">
                <source src="<?= h($videoUrl) ?>" type="video/mp4">
                Tu navegador no puede reproducir este video. Comprueba que uses una versión actualizada de Chrome, Firefox, Edge o Safari.
            </video>
        </div>
    <?php else: ?>
        <div class="mensaje error alerta alerta-error" role="alert">
            <svg class="alerta-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            <div class="alerta-contenido">
                <strong>No se encontró el video.</strong>
                <p>Coloca el archivo <code>1.mp4</code> en la carpeta <code>assets/video/</code> del proyecto y vuelve a cargar esta página.</p>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/pie.php'; ?>
