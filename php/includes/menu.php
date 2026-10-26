<?php
// ===================================================================
// menu.php
// Parte de arriba de todas las páginas: <head> y barra de navegación.
// Antes de incluirlo, cada página define:
//   $titulo        -> texto de la pestaña del navegador
//   $seccionActiva -> 'clientes' o 'productos' (resalta el enlace)
// ===================================================================
require_once __DIR__ . '/funciones_vista.php';

$titulo = $titulo ?? 'Panel';
$seccionActiva = $seccionActiva ?? '';
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($titulo) ?></title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📦</text></svg>">

    <!-- Antes de pintar la página: aplicar el tema guardado (claro u oscuro) -->
    <script>
        var temaGuardado = null;
        try { temaGuardado = localStorage.getItem('tema'); } catch (e) {}
        var prefiereOscuro = window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.setAttribute('data-bs-theme', temaGuardado || (prefiereOscuro ? 'dark' : 'light'));
    </script>

    <!-- Bootstrap 5, íconos de pixeles y nuestros estilos (todo en copia local) -->
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/assets/vendor/bootstrap.min.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/assets/vendor/pixelarticons/pixelarticons.css">
    <link rel="stylesheet" href="<?= h($baseUrl) ?>/assets/css/estilos.css">
</head>
<body>

<header class="barra-superior">
    <nav class="container d-flex align-items-center gap-2" aria-label="Menú principal">
        <a class="marca" href="<?= h($baseUrl) ?>/php/productos/ver_productos.php"><?= icono('package') ?> La Única</a>
        <a class="enlace-menu <?= $seccionActiva === 'clientes' ? 'activo' : '' ?>"
           href="<?= h($baseUrl) ?>/php/clientes/ver_clientes.php">
            <?= icono('users') ?> Clientes
        </a>
        <a class="enlace-menu <?= $seccionActiva === 'productos' ? 'activo' : '' ?>"
           href="<?= h($baseUrl) ?>/php/productos/ver_productos.php">
            <?= icono('package') ?> Productos
        </a>

        <button id="cambiar-tema" class="btn-tema ms-auto" type="button" title="Cambiar modo claro / oscuro">
            <span class="solo-claro"><?= icono('moon') ?></span>
            <span class="solo-oscuro"><?= icono('sun') ?></span>
        </button>
    </nav>
</header>

<main class="container py-4 py-lg-5">
