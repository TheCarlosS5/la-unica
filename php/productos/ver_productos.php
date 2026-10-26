<?php
// ===================================================================
// ver_productos.php  (la "R" de CRUD: Read / Leer)
// Muestra los productos como tarjetas con foto, stock y precio.
// ===================================================================
require __DIR__ . '/../config/conexion.php';
$titulo = 'Productos';
$seccionActiva = 'productos';

// "detalles" cuenta en cuántas facturas aparece el producto.
$sql = 'SELECT p.id_producto, p.codigo, p.nombre, p.unidad_medida, p.stock, p.precio,
               COUNT(d.id_producto) AS detalles
        FROM productos p
        LEFT JOIN detalle_factura d ON d.id_producto = p.id_producto
        GROUP BY p.id_producto, p.codigo, p.nombre, p.unidad_medida, p.stock, p.precio
        ORDER BY p.nombre';
$resultado = $conexion->query($sql);
$productos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];

// Cifras del resumen de arriba
$pocoStock = 0;
$agotados = 0;
foreach ($productos as $p) {
    if ((float)$p['stock'] == 0) $agotados++;
    elseif ((float)$p['stock'] < 10) $pocoStock++;
}

// Mensaje que llega desde eliminar_producto.php
$mensaje = $_GET['mensaje'] ?? '';
$tipo = $_GET['tipo'] ?? 'exito';

require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado aparecer">
    <div>
        <h1>Productos <span class="contador"><?= count($productos) ?></span></h1>
        <p class="text-secondary mb-0">Catálogo e inventario de la tienda.</p>
    </div>
    <div class="acciones-encabezado">
        <label class="buscador">
            <?= icono('search') ?>
            <input type="search" data-buscador placeholder="Buscar por código o nombre…" aria-label="Buscar producto">
        </label>
        <a class="btn btn-primary btn-con-icono" href="crear_producto.php">
            <?= icono('plus') ?> Nuevo producto
        </a>
    </div>
</div>

<?= alerta($mensaje, $tipo) ?>

<?php if (count($productos) > 0): ?>
    <div class="resumen-cifras aparecer">
        <div class="cifra"><b><?= count($productos) ?></b><span>Productos</span></div>
        <div class="cifra aviso"><b><?= $pocoStock ?></b><span>Con poco stock</span></div>
        <div class="cifra alerta-cifra"><b><?= $agotados ?></b><span>Agotados</span></div>
    </div>
<?php endif; ?>

<?php if (count($productos) === 0): ?>
    <div class="vacio aparecer">
        <span class="insignia grande"><?= icono('package') ?></span>
        <h2 class="h5 mt-3">Todavía no hay productos</h2>
        <a class="btn btn-primary mt-2" href="crear_producto.php">Crear el primero</a>
    </div>
<?php else: ?>
    <div class="row g-3 g-lg-4">
        <?php foreach ($productos as $i => $p): ?>
            <div class="col-6 col-md-4 col-xl-3 fila" style="--i: <?= $i ?>" data-buscar="<?= h($p['codigo'] . ' ' . $p['nombre']) ?>">
                <article class="producto">
                    <div class="producto-foto">
                        <img src="<?= h(foto_producto($p['nombre'])) ?>" alt="" loading="lazy">
                    </div>
                    <div class="producto-info">
                        <h2 class="producto-nombre"><?= h($p['nombre']) ?></h2>
                        <p class="producto-codigo"><?= h($p['codigo']) ?></p>
                        <p class="producto-meta">
                            <?php if ((float)$p['stock'] == 0): ?>
                                <span class="estado agotado">Agotado</span>
                            <?php elseif ((float)$p['stock'] < 10): ?>
                                <span class="estado pocas">Quedan <?= cantidad($p['stock']) ?> <?= h($p['unidad_medida']) ?></span>
                            <?php else: ?>
                                <span class="estado"><?= cantidad($p['stock']) ?> <?= h($p['unidad_medida']) ?> en stock</span>
                            <?php endif; ?>
                        </p>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="precio"><?= pesos($p['precio']) ?><small class="text-secondary"> / <?= h($p['unidad_medida']) ?></small></span>
                            <span class="text-nowrap">
                                <a class="btn-icono" href="editar_producto.php?id=<?= h($p['id_producto']) ?>" title="Editar">
                                    <?= icono('pencil') ?>
                                </a>
                                <?php if ((int)$p['detalles'] === 0): ?>
                                    <form class="d-inline" method="post" action="eliminar_producto.php">
                                        <input type="hidden" name="id_producto" value="<?= h($p['id_producto']) ?>">
                                        <button class="btn-icono" type="submit" title="Eliminar">
                                            <?= icono('trash') ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="sin-resultados" hidden>No hay productos con ese código o nombre.</p>
    <p class="nota">Los productos que ya aparecen en facturas no se pueden eliminar.</p>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
