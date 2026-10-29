<?php
/**
 * Archivo: php/inicio/main.php
 * Módulo: Inicio.
 * Propósito: mostrar el panel de entrada y los accesos a clientes, productos, facturas y reportes.
 * Funcionamiento: carga la conexión y utilidades compartidas, fija el título y sección activa,
 * incluye menu.php, presenta tarjetas con enlaces a cada módulo y termina con pie.php.
 * Punto clave: esta página es el destino de main.php y no ejecuta operaciones de escritura.
 */

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
        <span>Ventas y catálogo</span>
    </div>
    <h1 id="titulo-hero">La Única</h1>
    <p>Administra clientes, productos, existencias, precios y facturas desde un solo lugar.</p>
</section>

<section class="tarjetas modulos-grid" aria-label="Módulos del sistema">
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

    <article class="modulo-card">
        <div class="modulo-cabecera">
            <div class="modulo-icono" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="16" rx="2" />
                    <path d="M7 9h10M7 13h3m3 0h4m-10 3h10" />
                </svg>
            </div>
            <div><h2 class="modulo-titulo">Facturas</h2></div>
        </div>
        <p class="modulo-descripcion">Registra ventas y consulta o imprime comprobantes con el precio vigente de cada producto.</p>
        <div class="modulo-acciones">
            <a class="boton" href="<?= h($baseUrl) ?>/php/facturas/ver_facturas.php">
                <span>Consultar facturas</span>
                <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" /></svg>
            </a>
            <a class="boton secundario" href="<?= h($baseUrl) ?>/php/facturas/crear_factura.php">Nueva factura</a>
        </div>
        <div class="modulo-detalles">
            <span class="modulo-detalle-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12" /></svg>
                <span>Búsqueda por factura, cliente y fecha</span>
            </span>
            <span class="modulo-detalle-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12" /></svg>
                <span>Comprobante listo para imprimir</span>
            </span>
        </div>
    </article>

    <article class="modulo-card">
        <div class="modulo-cabecera">
            <div class="modulo-icono" aria-hidden="true">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3v18h18" />
                    <path d="M18 17V9M13 17V5M8 17v-3" />
                </svg>
            </div>
            <div><h2 class="modulo-titulo">Reportes</h2></div>
        </div>
        <p class="modulo-descripcion">Consulta ventas, clientes, existencias y facturas pendientes con los datos actuales de la base.</p>
        <div class="modulo-acciones">
            <a class="boton" href="<?= h($baseUrl) ?>/php/reportes/ver_reportes.php">
                <span>Abrir reportes</span>
                <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6" /></svg>
            </a>
        </div>
        <div class="modulo-detalles">
            <span class="modulo-detalle-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12" /></svg>
                <span>Conteo de clientes, ventas y estados</span>
            </span>
            <span class="modulo-detalle-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12" /></svg>
                <span>Existencias, unidades y pendientes</span>
            </span>
        </div>
    </article>
</section>
<?php require __DIR__ . '/../includes/pie.php'; ?>
