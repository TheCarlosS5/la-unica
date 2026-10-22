<?php
require __DIR__ . '/../config/conexion.php';
$titulo = 'Inicio';
$seccionActiva = 'inicio';
require __DIR__ . '/../includes/menu.php';
?>
<section class="hero" aria-labelledby="titulo-hero">
    <div class="hero-insignia">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 2L2 7l10 5 10-5-10-5z" />
            <path d="M2 17l10 5 10-5" />
            <path d="M2 12l10 5 10-5" />
        </svg>
        <span>Gestión de clientes y productos</span>
    </div>
    <h1 id="titulo-hero">La Única</h1>
    <p>Panel para administrar clientes, productos, existencias y precios, con protección de registros asociados a facturas.</p>
</section>

<section class="tarjetas modulos-grid" aria-label="Módulos del sistema">
    <article class="modulo-card">
        <div class="modulo-cabecera">
            <div class="modulo-icono" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
            </div>
            <div>
                <h2 class="modulo-titulo">Clientes</h2>
            </div>
        </div>
        <p class="modulo-descripcion">Crear, consultar, editar y eliminar clientes con protección de registros asociados a facturas.</p>
        <div class="modulo-acciones">
            <a class="boton" href="<?= h($baseUrl) ?>/php/clientes/ver_clientes.php">
                <span>Abrir clientes</span>
                <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </a>
            <a class="boton secundario" href="<?= h($baseUrl) ?>/php/clientes/crear_cliente.php">
                <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                <span>Crear cliente</span>
            </a>
        </div>
        <div class="modulo-detalles">
            <span class="modulo-detalle-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <span>Protección contra eliminación de clientes con facturas</span>
            </span>
            <span class="modulo-detalle-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <span>Confirmación de baja en dos pasos</span>
            </span>
        </div>
    </article>

    <article class="modulo-card">
        <div class="modulo-cabecera">
            <div class="modulo-icono" aria-hidden="true">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                    <line x1="12" y1="22.08" x2="12" y2="12" />
                </svg>
            </div>
            <div>
                <h2 class="modulo-titulo">Productos</h2>
            </div>
        </div>
        <p class="modulo-descripcion">Crear, consultar, editar y eliminar artículos del inventario comercial con supervisión de existencias y precios.</p>
        <div class="modulo-acciones">
            <a class="boton" href="<?= h($baseUrl) ?>/php/productos/ver_productos.php">
                <span>Abrir productos</span>
                <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </a>
            <a class="boton secundario" href="<?= h($baseUrl) ?>/php/productos/crear_producto.php">
                <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                </svg>
                <span>Crear producto</span>
            </a>
        </div>
        <div class="modulo-detalles">
            <span class="modulo-detalle-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <span>Restricción de eliminación en productos facturados</span>
            </span>
            <span class="modulo-detalle-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <span>Protección de unidad de medida para registros con historial</span>
            </span>
        </div>
    </article>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>