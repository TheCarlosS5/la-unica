<?php
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
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232b2b36' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath fill='%23ffe58a' d='M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z'/%3E%3Cline x1='3' y1='6' x2='21' y2='6'/%3E%3Cpath d='M16 10a4 4 0 0 1-8 0'/%3E%3C/svg%3E">
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
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
                            <line x1="3" y1="6" x2="21" y2="6" />
                            <path d="M16 10a4 4 0 0 1-8 0" />
                        </svg>
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
