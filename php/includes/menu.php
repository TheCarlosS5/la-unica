<?php
/**
 * Archivo: menu.php
 * Módulo: Plantilla compartida de navegación.
 * Propósito: construir la cabecera, el menú lateral y el contenedor principal que usan las vistas.
 * Funcionamiento: consume las variables de título/sección preparadas por cada módulo, identifica
 * la página actual y renderiza enlaces con estado activo. Obtiene los enlaces de reportes del catálogo.
 * Puntos clave:
 * - Se incluye después de preparar los datos de la página.
 * - Escapa valores dinámicos con h() y define el inicio de <main>; pie.php lo cierra.
 * - Carga CSS, fuentes locales, favicon y controles accesibles del menú.
 */

require_once __DIR__ . '/../reportes/datos_reportes.php';
$catalogoReportes = catalogoReportes();
$baseUrl = $baseUrl ?? '';
$titulo = $titulo ?? 'La Única';
$seccionActual = $seccionActiva ?? '';
$paginaActual = basename(str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '')));

if ($seccionActual === '') {
    $scriptActual = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    if (strpos($scriptActual, '/clientes/') !== false) {
        $seccionActual = 'clientes';
    } elseif (strpos($scriptActual, '/productos/') !== false) {
        $seccionActual = 'productos';
    } elseif (strpos($scriptActual, '/facturas/') !== false) {
        $seccionActual = 'facturas';
    } elseif (strpos($scriptActual, '/reportes/') !== false) {
        $seccionActual = 'reportes';
    } elseif (strpos($scriptActual, '/sobre_nosotros/') !== false) {
        $seccionActual = 'sobre_nosotros';
    } else {
        $seccionActual = 'inicio';
    }
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#fbf7ec">
    <title><?= h($titulo) ?> · La Única - Administración</title>
    <link rel="icon" type="image/png" href="<?= h($baseUrl) ?>/assets/img/logo.png">
    <link rel="preload" href="<?= h($baseUrl) ?>/assets/fonts/patrick-hand-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= h($baseUrl) ?>/assets/fonts/architects-daughter-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/assets/css/estilos.css">
</head>

<body class="shell-layout">
    <a class="salto-contenido" href="#contenido-principal">Saltar al contenido principal</a>

    <div id="drawer-backdrop" class="drawer-backdrop" aria-hidden="true"></div>

    <div class="layout-app">
        <aside id="barra-lateral" class="barra-lateral" aria-label="Menú principal de La Única">
            <div class="sidebar-cabecera">
                <a class="sidebar-marca" href="<?= h($baseUrl) ?>/main.php" title="Panel La Única">
                    <span class="marca-emblema" aria-hidden="true">
                        <img src="<?= h($baseUrl) ?>/assets/img/logo.png" alt="">
                    </span>
                    <span class="marca-texto">
                        <span class="marca-nombre">La Única</span>
                        <span class="marca-subtexto">Gestión comercial</span>
                    </span>
                </a>
                <button id="btn-cerrar-drawer" class="btn-cerrar-drawer" type="button" aria-label="Cerrar menú lateral">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="18" y1="6" x2="6" y2="18" />
                        <line x1="6" y1="6" x2="18" y2="18" />
                    </svg>
                </button>
            </div>

            <div class="sidebar-cuerpo">
                <div class="sidebar-grupo">
                    <nav class="sidebar-nav" aria-label="Navegación del sistema">
                        <a class="nav-enlace<?= $seccionActual === 'inicio' ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/main.php" <?= $paginaActual === 'main.php' ? ' aria-current="page"' : '' ?>>
                            <svg class="nav-icono" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                            <span>Inicio</span>
                        </a>
                    </nav>
                </div>

                <div class="sidebar-grupo">
                    <span class="sidebar-rotulo">Ventas</span>
                    <nav class="sidebar-nav" aria-label="Facturas">
                        <div class="nav-item-complejo">
                            <a class="nav-enlace<?= $seccionActual === 'facturas' ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/php/facturas/ver_facturas.php" <?= $paginaActual === 'ver_facturas.php' ? ' aria-current="page"' : '' ?>>
                                <svg class="nav-icono" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="4" width="18" height="16" rx="2" />
                                    <path d="M7 9h10M7 13h3m3 0h4m-10 3h10" />
                                </svg>
                                <span>Facturas</span>
                            </a>
                            <a class="nav-subenlace<?= $paginaActual === 'crear_factura.php' ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/php/facturas/crear_factura.php" title="Registrar una nueva factura" <?= $paginaActual === 'crear_factura.php' ? ' aria-current="page"' : '' ?>>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                <span>Nueva factura</span>
                            </a>
                        </div>
                    </nav>
                </div>

                <div class="sidebar-grupo">
                    <span class="sidebar-rotulo">Catálogo</span>
                    <nav class="sidebar-nav" aria-label="Clientes y productos">
                        <div class="nav-item-complejo">
                            <a class="nav-enlace<?= $seccionActual === 'clientes' ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/php/clientes/ver_clientes.php" <?= $paginaActual === 'ver_clientes.php' ? ' aria-current="page"' : '' ?>>
                                <svg class="nav-icono" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                                <span>Clientes</span>
                            </a>
                            <a class="nav-subenlace<?= $paginaActual === 'crear_cliente.php' ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/php/clientes/crear_cliente.php" title="Crear nuevo cliente" <?= $paginaActual === 'crear_cliente.php' ? ' aria-current="page"' : '' ?>>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                <span>Nuevo cliente</span>
                            </a>
                        </div>

                        <div class="nav-item-complejo">
                            <a class="nav-enlace<?= $seccionActual === 'productos' ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/php/productos/ver_productos.php" <?= $paginaActual === 'ver_productos.php' ? ' aria-current="page"' : '' ?>>
                                <svg class="nav-icono" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                                    <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                                    <line x1="12" y1="22.08" x2="12" y2="12" />
                                </svg>
                                <span>Productos</span>
                            </a>
                            <a class="nav-subenlace<?= $paginaActual === 'crear_producto.php' ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/php/productos/crear_producto.php" title="Crear nuevo producto" <?= $paginaActual === 'crear_producto.php' ? ' aria-current="page"' : '' ?>>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                <span>Nuevo producto</span>
                            </a>
                        </div>
                    </nav>
                </div>

                <div class="sidebar-grupo">
                    <span class="sidebar-rotulo">Información</span>
                    <nav class="sidebar-nav" aria-label="Consultas de información">
                        <div class="nav-item-complejo">
                            <a class="nav-enlace<?= $seccionActual === 'reportes' ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/php/reportes/ver_reportes.php" <?= $paginaActual === 'ver_reportes.php' ? ' aria-current="page"' : '' ?>>
                                <svg class="nav-icono" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M3 3v18h18" />
                                    <path d="M18 17V9M13 17V5M8 17v-3" />
                                </svg>
                                <span>Reportes</span>
                            </a>
                            <?php foreach ($catalogoReportes as $reporteNav): ?>
                                <a class="nav-subenlace<?= $paginaActual === $reporteNav['archivo'] ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/php/reportes/<?= h($reporteNav['archivo']) ?>" title="Ver reporte: <?= h($reporteNav['titulo']) ?>" <?= $paginaActual === $reporteNav['archivo'] ? ' aria-current="page"' : '' ?>>
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M3 3v18h18" />
                                        <path d="M18 17V9M13 17V5M8 17v-3" />
                                    </svg>
                                    <span><?= h($reporteNav['titulo']) ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>

                        <div class="nav-item-complejo">
                            <a class="nav-enlace<?= $seccionActual === 'sobre_nosotros' ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/php/sobre_nosotros/index.php" <?= $paginaActual === 'index.php' && $seccionActual === 'sobre_nosotros' ? ' aria-current="page"' : '' ?>>
                                <svg class="nav-icono" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 16v-4" />
                                    <path d="M12 8h.01" />
                                </svg>
                                <span>Sobre nosotros</span>
                            </a>
                            <a class="nav-subenlace<?= $paginaActual === 'manual_usuario.php' ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/php/sobre_nosotros/manual_usuario.php" title="Consultar el manual del usuario" <?= $paginaActual === 'manual_usuario.php' ? ' aria-current="page"' : '' ?>>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                                </svg>
                                <span>Manual del Usuario</span>
                            </a>
                            <a class="nav-subenlace<?= $paginaActual === 'video_explicativo.php' ? ' activo' : '' ?>" href="<?= h($baseUrl) ?>/php/sobre_nosotros/video_explicativo.php" title="Ver el video explicativo" <?= $paginaActual === 'video_explicativo.php' ? ' aria-current="page"' : '' ?>>
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                    <path d="m10 9 5 3-5 3z" />
                                </svg>
                                <span>Video explicativo</span>
                            </a>
                        </div>
                    </nav>
                </div>
            </div>

            <div class="sidebar-pie">
                <div class="terminal-badge" title="Panel local de administración">
                    <span class="punto-estado" aria-hidden="true"></span>
                    <div class="terminal-badge-info">
                        <span class="terminal-badge-titulo">La Única</span>
                        <span class="terminal-badge-sub">Gestión local</span>
                    </div>
                </div>
            </div>
        </aside>

        <div class="area-principal">
            <header class="topbar-principal" role="banner">
                <div class="topbar-izquierda">
                    <button id="btn-abrir-drawer" class="btn-abrir-drawer" type="button" aria-label="Abrir menú principal" aria-expanded="false" aria-controls="barra-lateral">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <line x1="3" y1="12" x2="21" y2="12" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <line x1="3" y1="18" x2="21" y2="18" />
                        </svg>
                    </button>
                    <div class="topbar-contexto">
                        <span class="topbar-sistema">La Única</span>
                        <span class="topbar-separador" aria-hidden="true">/</span>
                        <span class="topbar-modulo"><?= h($titulo) ?></span>
                    </div>
                </div>
                <div class="topbar-meta">
                    <span class="insignia-facturadora-estado">
                        <span class="punto-estado" aria-hidden="true"></span>
                        <span>Administración local</span>
                    </span>
                </div>
            </header>

            <main id="contenido-principal" class="contenedor contenedor-contenido">
