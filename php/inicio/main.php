<?php
require __DIR__ . '/../config/conexion.php';
$titulo = 'Inicio';
$seccionActiva = 'inicio';
require __DIR__ . '/../includes/menu.php';
?>
<section class="hero" aria-labelledby="titulo-hero">
    <div class="hero-insignia">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <path d="M16 10a4 4 0 0 1-8 0" />
        </svg>
        <span>Caja y tienda</span>
    </div>
    <h1 id="titulo-hero">La Única</h1>
    <p>Cobra, revisa tus facturas y cuida tu inventario desde un solo lugar.</p>
</section>

<section class="tarjetas modulos-grid" aria-label="Módulos del sistema">
    <article class="modulo-card destacado">
        <div class="modulo-cabecera">
            <div class="modulo-icono" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1" />
                    <circle cx="20" cy="21" r="1" />
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                </svg>
            </div>
            <div>
                <h2 class="modulo-titulo">Nueva venta</h2>
            </div>
        </div>
        <p class="modulo-descripcion">Toca los productos que lleva el cliente, mira el total y calcula el cambio al instante.</p>
        <div class="modulo-acciones">
            <a class="boton" href="<?= h($baseUrl) ?>/php/facturas/nueva_factura.php">
                <span>Empezar a vender</span>
                <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </a>
        </div>
        <div class="modulo-detalles">
            <span class="modulo-detalle-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <span>Los productos agotados aparecen bloqueados</span>
            </span>
            <span class="modulo-detalle-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <span>No deja vender más de lo que hay en stock</span>
            </span>
        </div>
    </article>

    <article class="modulo-card">
        <div class="modulo-cabecera">
            <div class="modulo-icono" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                </svg>
            </div>
            <div>
                <h2 class="modulo-titulo">Facturas</h2>
            </div>
        </div>
        <p class="modulo-descripcion">Revisa las ventas ya hechas, quién compró, cuánto fue y si está pagada, pendiente o cancelada.</p>
        <div class="modulo-acciones">
            <a class="boton" href="<?= h($baseUrl) ?>/php/facturas/ver_facturas.php">
                <span>Abrir facturas</span>
                <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="9 18 15 12 9 6" />
                </svg>
            </a>
        </div>
        <div class="modulo-detalles">
            <span class="modulo-detalle-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="20 6 9 17 4 12" />
                </svg>
                <span>Total calculado con los precios de cada producto</span>
            </span>
        </div>
    </article>

    <article class="modulo-card">
        <div class="modulo-cabecera">
            <div class="modulo-icono" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
