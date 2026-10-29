<?php
/**
 * Archivo: index.php
 * Módulo: Sobre nosotros.
 * Propósito: presentar los recursos de ayuda y documentación de La Única.
 * Funcionamiento: muestra tarjetas de acceso al manual integrado y al futuro video explicativo.
 */

require __DIR__ . '/inicializar.php';

$titulo = 'Sobre nosotros';
$seccionActiva = 'sobre_nosotros';
require __DIR__ . '/../includes/menu.php';
?>

<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span aria-current="page">Sobre nosotros</span>
        </nav>
        <h1 class="encabezado-titulo">Sobre nosotros</h1>
        <p class="encabezado-descripcion">Encuentra recursos para conocer y utilizar La Única. Consulta el manual directamente aquí o accede al espacio del video explicativo.</p>
    </div>
</div>

<section class="tarjetas reportes-resumen-grid" aria-label="Recursos de ayuda">
    <article class="reportes-resumen-card">
        <div class="modulo-cabecera">
            <div class="modulo-icono" aria-hidden="true">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                </svg>
            </div>
            <h2 class="modulo-titulo">Manual del Usuario</h2>
        </div>
        <p class="reportes-resumen-descripcion">Consulta la guía de uso con las instrucciones y capturas de las principales funciones del aplicativo.</p>
        <p class="reportes-resumen-detalle">El documento PDF se muestra integrado en la página, sin abrir una pestaña externa.</p>
        <div class="modulo-acciones">
            <a class="boton" href="<?= h($baseUrl) ?>/php/sobre_nosotros/manual_usuario.php">
                <span>Consultar manual</span>
                <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6" /></svg>
            </a>
        </div>
    </article>

    <article class="reportes-resumen-card">
        <div class="modulo-cabecera">
            <div class="modulo-icono" aria-hidden="true">
                <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="5" width="18" height="14" rx="2" />
                    <path d="m10 9 5 3-5 3z" />
                </svg>
            </div>
            <h2 class="modulo-titulo">Video explicativo</h2>
        </div>
        <p class="reportes-resumen-descripcion">Reproduce la presentación audiovisual para conocer las principales funciones de la aplicación.</p>
        <p class="reportes-resumen-detalle">El video se reproduce directamente dentro del aplicativo.</p>
        <div class="modulo-acciones">
            <a class="boton secundario" href="<?= h($baseUrl) ?>/php/sobre_nosotros/video_explicativo.php">
                <span>Ver video</span>
                <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6" /></svg>
            </a>
        </div>
    </article>
</section>

<?php require __DIR__ . '/../includes/pie.php'; ?>
