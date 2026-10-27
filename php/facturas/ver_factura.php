<?php
// ===================================================================
// ver_factura.php
// Muestra una factura completa (?id=5): cliente, productos, subtotales
// y total. Se puede imprimir con el botón (usa la impresión del navegador).
// Si la factura está Pagado, también ofrece cancelarla.
// ===================================================================
require __DIR__ . '/../config/conexion.php';
$titulo = 'Factura';
$seccionActiva = 'facturas';

$id = id_valido($_GET['id'] ?? '');
if ($id === null) {
    http_response_code(400);
    exit('ID de factura no válido.');
}

// Datos de la factura y de su cliente.
$stmt = $conexion->prepare('SELECT f.id_factura, f.id_cliente, c.nombre AS cliente, f.fecha_factura, f.estado_factura
                            FROM facturas f INNER JOIN clientes c ON c.id_cliente = f.id_cliente
                            WHERE f.id_factura = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$factura = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$factura) {
    http_response_code(404);
    exit('No se encontró la factura.');
}

// Líneas de la factura. El precio es el VIGENTE del producto (el esquema no guarda el histórico).
$stmt = $conexion->prepare('SELECT p.id_producto, p.nombre, p.unidad_medida, d.cantidad, p.precio_unitario,
                                   ROUND(d.cantidad * p.precio_unitario, 2) AS subtotal
                            FROM detalle_facturas d INNER JOIN productos p ON p.id_producto = d.id_producto
                            WHERE d.id_factura = ? ORDER BY d.id_detalle_factura');
$stmt->bind_param('i', $id);
$stmt->execute();
$lineas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$total = 0;
foreach ($lineas as $linea) {
    $total += $linea['subtotal'];
}

$mensaje = $_GET['mensaje'] ?? '';
$tipo = $_GET['tipo'] ?? 'exito';

require __DIR__ . '/../includes/menu.php';
?>
<a class="volver" href="ver_facturas.php">← Facturas</a>

<?= alerta($mensaje, $tipo) ?>

<div class="tarjeta aparecer">
    <div class="p-4 d-flex flex-wrap justify-content-between gap-3" style="border-bottom: 2px solid var(--borde);">
        <div>
            <p class="text-secondary mb-1">Comprobante interno de venta</p>
            <h1 class="h4 mb-2">Factura #<?= h($factura['id_factura']) ?></h1>
            <?= insignia_estado($factura['estado_factura']) ?>
        </div>
        <div class="text-md-end">
            <strong><?= h($factura['cliente']) ?></strong><br>
            <span class="text-secondary">Id cliente: <?= h($factura['id_cliente']) ?></span><br>
            <span class="text-secondary"><?= h(fecha_hora($factura['fecha_factura'])) ?></span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Unidad</th>
                    <th class="num">Cantidad</th>
                    <th class="num">Precio</th>
                    <th class="num">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lineas as $linea): ?>
                    <tr>
                        <td><?= h($linea['nombre']) ?></td>
                        <td class="text-secondary"><?= h($linea['unidad_medida']) ?></td>
                        <td class="num"><?= h($linea['cantidad']) ?></td>
                        <td class="num"><?= pesos2($linea['precio_unitario']) ?></td>
                        <td class="num"><?= pesos2($linea['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="total-factura"><span>Total</span> <span><?= pesos2($total) ?></span></div>
</div>

<div class="d-flex gap-2 justify-content-end mt-3 no-imprimir">
    <?php if ($factura['estado_factura'] === 'Pagado'): ?>
        <form method="post" action="cancelar_factura.php">
            <input type="hidden" name="id_factura" value="<?= h($factura['id_factura']) ?>">
            <button class="btn btn-light" type="submit">Cancelar factura</button>
        </form>
    <?php else: ?>
        <button class="btn btn-light" type="button" disabled
                title="Solo se pueden cancelar facturas en estado Pagado">Cancelar factura</button>
    <?php endif; ?>
    <button class="btn btn-primary btn-con-icono" type="button" onclick="window.print()">
        <?= icono('printer') ?> Imprimir
    </button>
</div>
<p class="nota no-imprimir">Los importes usan el precio actual de cada producto.</p>
<?php require __DIR__ . '/../includes/pie.php'; ?>
