<?php
// ===================================================================
// ver_facturas.php  (la "R" de CRUD: Read / Leer)
// Lista las facturas con su cliente, fecha, estado y total.
// El total NO está guardado: se calcula con el precio actual del producto.
// ===================================================================
require __DIR__ . '/../config/conexion.php';
$titulo = 'Facturas';
$seccionActiva = 'facturas';

$sql = 'SELECT f.id_factura, f.id_cliente, c.nombre AS cliente, f.fecha_factura, f.estado_factura,
               COALESCE(SUM(ROUND(d.cantidad * p.precio_unitario, 2)), 0) AS total
        FROM facturas f
        INNER JOIN clientes c ON c.id_cliente = f.id_cliente
        LEFT JOIN detalle_facturas d ON d.id_factura = f.id_factura
        LEFT JOIN productos p ON p.id_producto = d.id_producto
        GROUP BY f.id_factura, f.id_cliente, c.nombre, f.fecha_factura, f.estado_factura
        ORDER BY f.id_factura DESC';
$resultado = $conexion->query($sql);
$facturas = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];

// Mensaje que llega desde crear_factura.php o cancelar_factura.php
$mensaje = $_GET['mensaje'] ?? '';
$tipo = $_GET['tipo'] ?? 'exito';

require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado aparecer">
    <div>
        <h1>Facturas <span class="contador"><?= count($facturas) ?></span></h1>
        <p class="text-secondary mb-0">Ventas registradas. Busca por número, cliente o fecha.</p>
    </div>
    <div class="acciones-encabezado">
        <label class="buscador">
            <?= icono('search') ?>
            <input type="search" data-buscador placeholder="Buscar por id, cliente o fecha…" aria-label="Buscar factura">
        </label>
        <a class="btn btn-primary btn-con-icono" href="crear_factura.php">
            <?= icono('plus') ?> Nueva factura
        </a>
    </div>
</div>

<?= alerta($mensaje, $tipo) ?>

<?php if (count($facturas) === 0): ?>
    <div class="vacio aparecer">
        <span class="insignia grande"><?= icono('receipt') ?></span>
        <h2 class="h5 mt-3">Todavía no hay facturas</h2>
        <a class="btn btn-primary mt-2" href="crear_factura.php">Crear la primera</a>
    </div>
<?php else: ?>
    <div class="tarjeta aparecer">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Factura</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facturas as $i => $f): ?>
                        <tr class="fila" style="--i: <?= $i ?>"
                            data-buscar="<?= h($f['id_factura'] . ' ' . $f['id_cliente'] . ' ' . $f['cliente'] . ' ' . $f['fecha_factura'] . ' ' . fecha_hora($f['fecha_factura'])) ?>">
                            <td><strong>#<?= h($f['id_factura']) ?></strong></td>
                            <td>
                                <?= h($f['cliente']) ?><br>
                                <span class="text-secondary">Id <?= h($f['id_cliente']) ?></span>
                            </td>
                            <td class="text-secondary"><?= h(fecha_hora($f['fecha_factura'])) ?></td>
                            <td><?= insignia_estado($f['estado_factura']) ?></td>
                            <td class="num"><strong><?= pesos2($f['total']) ?></strong></td>
                            <td class="text-end text-nowrap">
                                <a class="btn-icono" href="ver_factura.php?id=<?= h($f['id_factura']) ?>" title="Ver e imprimir">
                                    <?= icono('eye') ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="sin-resultados" hidden>No hay facturas con ese dato.</p>
    </div>
    <p class="nota">Los importes se calculan con el precio actual de cada producto.</p>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
