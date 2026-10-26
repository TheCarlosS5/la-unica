<?php
require __DIR__ . '/../config/conexion.php';

$titulo = 'Productos';
$seccionActiva = 'productos';
$tokenCsrf = obtenerTokenCsrf();

$sqlProductos = 'SELECT p.id_producto, p.nombre, p.unidad_medida, p.stock, p.precio_unitario, COUNT(d.id_producto) AS total_detalles FROM productos p LEFT JOIN detalle_facturas d ON d.id_producto = p.id_producto GROUP BY p.id_producto, p.nombre, p.unidad_medida, p.stock, p.precio_unitario ORDER BY p.id_producto';
$resultadoProductos = $conexion->query($sqlProductos);

$mensajeUrl = $_GET['mensaje'] ?? null;
$mensajeTexto = ($mensajeUrl !== null) ? trim((string)$mensajeUrl) : '';

require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista acciones-superiores">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Productos</span>
        </nav>
        <h1 class="encabezado-titulo">Listado de productos</h1>
        <p class="encabezado-descripcion">Supervisión de inventario comercial, unidades de venta, existencias actuales y tarifas unitarias.</p>
    </div>
    <div class="encabezado-acciones">
        <a class="boton" href="crear_producto.php">
            <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            <span>Crear producto</span>
        </a>
    </div>
</div>

<?php if ($mensajeTexto !== ''): ?>
    <div class="mensaje alerta" role="status">
        <svg class="alerta-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
            <polyline points="22 4 12 14.01 9 11.01" />
        </svg>
        <div class="alerta-contenido"><?= h($mensajeTexto) ?></div>
    </div>
<?php endif; ?>

<?php if (!$resultadoProductos || $resultadoProductos->num_rows === 0): ?>
    <div class="sin-datos estado-vacio">
        <svg class="estado-vacio-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
            <line x1="12" y1="22" x2="12" y2="12" />
        </svg>
        <h2 class="estado-vacio-titulo">Todavía no hay productos registrados</h2>
        <p class="estado-vacio-texto">Registra los primeros artículos del inventario con sus existencias y precios unitarios para iniciar las ventas.</p>
        <a class="boton" href="crear_producto.php">
            <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            <span>Crear primer producto</span>
        </a>
    </div>
<?php else: ?>
    <div class="tabla-wrap tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Unidad</th>
                    <th scope="col">Stock</th>
                    <th scope="col" class="texto-derecha">Precio unitario</th>
                    <th scope="col">Detalles</th>
                    <th scope="col" class="texto-derecha">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($producto = $resultadoProductos->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <span class="chip-id">#<?= h($producto['id_producto']) ?></span>
                        </td>
                        <td>
                            <span class="celda-destacada"><?= h($producto['nombre']) ?></span>
                        </td>
                        <td>
                            <span class="insignia-unidad"><?= h($producto['unidad_medida']) ?></span>
                        </td>
                        <td>
                            <?php if ((int)$producto['stock'] === 0): ?>
                                <span class="insignia-stock agotado">0 (Agotado)</span>
                            <?php else: ?>
                                <span class="insignia-stock disponible"><?= h($producto['stock']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="celda-precio">
                            $ <?= number_format((float)$producto['precio_unitario'], 2, ',', '.') ?>
                        </td>
                        <td>
                            <span class="insignia-facturas <?= (int)$producto['total_detalles'] > 0 ? 'con-facturas' : 'sin-facturas' ?>">
                                <?= h($producto['total_detalles']) ?> <?= (int)$producto['total_detalles'] === 1 ? 'detalle' : 'detalles' ?>
                            </span>
                        </td>
                        <td>
                            <div class="acciones" style="justify-content: flex-end;">
                                <a class="editar" href="editar_producto.php?id=<?= h($producto['id_producto']) ?>" title="Editar producto #<?= h($producto['id_producto']) ?>">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                    </svg>
                                    <span>Editar</span>
                                </a>
                                <?php if ((int)$producto['total_detalles'] === 0): ?>
                                    <form method="post" action="eliminar_producto.php" onsubmit="return confirm('¿Eliminar este producto?')">
                                        <?php if ($tokenCsrf !== null): ?>
                                            <input type="hidden" name="csrf_token" value="<?= h($tokenCsrf) ?>">
                                        <?php endif; ?>
                                        <input type="hidden" name="id_producto" value="<?= h($producto['id_producto']) ?>">
                                        <button type="submit" class="boton-peligro" title="Eliminar producto #<?= h($producto['id_producto']) ?>">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <polyline points="3 6 5 6 21 6" />
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                            <span>Eliminar</span>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="bloqueado etiqueta-bloqueado" title="No se permite eliminar productos que estén incluidos en detalles de factura">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                        </svg>
                                        <span>En facturas</span>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="nota nota-informativa">
        <svg class="nota-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="16" x2="12" y2="12" />
            <line x1="12" y1="8" x2="12.01" y2="8" />
        </svg>
        <span>No se permite eliminar productos que estén incluidos en detalles de factura.</span>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
