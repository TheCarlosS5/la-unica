<?php
/**
 * Archivo: cabecera.php
 * Módulo: Wiki — plantilla de apertura.
 * Propósito: imprimir el <head>, la barra superior con el buscador y el menú lateral de secciones.
 * Funcionamiento: usa $catalogo, $pagina y $tituloPagina preparados por index.php; marca como activa
 * la página abierta y deja abierto el contenedor principal, que cierra pie.php.
 * Punto clave: todas las rutas son relativas a wiki/, así funciona en cualquier carpeta de instalación.
 */

if (!defined('LA_UNICA_WIKI')) {
    exit;
}
$claveActual = $pagina['clave'] ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#faf7f0">
    <title><?= h($tituloPagina) ?> · Wiki de La Única</title>
    <link rel="icon" type="image/png" href="../assets/img/logo.png">
    <link rel="preload" href="../assets/fonts/patrick-hand-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="../assets/fonts/architects-daughter-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="css/wiki.css">
</head>
<body class="wiki">
    <a class="wiki-salto" href="#contenido">Saltar al contenido</a>

    <header class="wiki-barra">
        <button id="wiki-abrir-menu" class="wiki-boton-menu" type="button" aria-label="Abrir el menú de la wiki" aria-expanded="false" aria-controls="wiki-menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <line x1="4" y1="7" x2="20" y2="7" /><line x1="4" y1="12" x2="20" y2="12" /><line x1="4" y1="17" x2="20" y2="17" />
            </svg>
        </button>
        <a class="wiki-marca" href="index.php">
            <img src="../assets/img/logo.png" alt="" width="34" height="34">
            <span class="wiki-marca-nombre">La Única</span>
            <span class="wiki-marca-etiqueta">Wiki</span>
        </a>
        <form class="wiki-buscador" action="index.php" method="get" role="search">
            <label class="wiki-oculto" for="wiki-buscar">Buscar en la wiki</label>
            <svg class="wiki-buscador-icono" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" />
            </svg>
            <input id="wiki-buscar" name="buscar" type="search" maxlength="100" placeholder="Buscar en la wiki…" value="<?= h($terminoBusqueda ?? '') ?>" autocomplete="off">
        </form>
        <a class="wiki-volver" href="../main.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="19" y1="12" x2="5" y2="12" /><polyline points="12 19 5 12 12 5" />
            </svg>
            <span>Volver a La Única</span>
        </a>
    </header>

    <div id="wiki-fondo-menu" class="wiki-fondo-menu" hidden></div>

    <div class="wiki-cuerpo">
        <nav id="wiki-menu" class="wiki-menu" aria-label="Secciones de la wiki">
            <?php foreach ($catalogo as $seccion => $paginasSeccion): ?>
                <p class="wiki-menu-seccion"><?= h($seccion) ?></p>
                <ul>
                    <?php foreach ($paginasSeccion as $clave => $datos): ?>
                        <li>
                            <a href="index.php?pagina=<?= h($clave) ?>"<?= $clave === $claveActual ? ' class="activo" aria-current="page"' : '' ?>>
                                <?= h($datos['menu'] ?? $datos['titulo']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </nav>

        <main id="contenido" class="wiki-principal">
