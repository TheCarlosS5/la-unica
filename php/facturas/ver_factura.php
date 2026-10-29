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
<a class="boton-enlace volver" href="ver_facturas.php">← Volver a facturas</a>

<?= alerta($mensaje, $tipo) ?>

<article class="comprobante-factura aparecer" aria-labelledby="titulo-comprobante">
    <header class="comprobante-cabecera">
        <div>
            <p class="comprobante-marca">La Única</p>
            <h2 id="titulo-comprobante">Comprobante interno de venta</h2>
        </div>
        <div class="comprobante-identificador">
            <span>Número de factura</span>
            <strong>#<?= h($factura['id_factura']) ?></strong>
        </div>
    </header>

    <dl class="comprobante-datos">
        <div>
            <dt>Cliente</dt>
            <dd><?= h($factura['cliente']) ?></dd>
        </div>
        <div>
            <dt>ID del cliente</dt>
            <dd><?= h($factura['id_cliente']) ?></dd>
        </div>
        <div>
            <dt>Fecha</dt>
            <dd><?= h(fecha_hora($factura['fecha_factura'])) ?></dd>
        </div>
        <div>
            <dt>Estado</dt>
            <dd><?= insignia_estado($factura['estado_factura']) ?></dd>
        </div>
    </dl>

    <div class="tabla-wrap tabla-contenedor comprobante-tabla">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Producto</th>
                    <th scope="col">Unidad</th>
                    <th scope="col" class="texto-derecha">Cantidad</th>
                    <th scope="col" class="texto-derecha">Precio vigente</th>
                    <th scope="col" class="texto-derecha">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lineas as $linea): ?>
                    <tr>
                        <td><span class="celda-destacada"><?= h($linea['nombre']) ?></span></td>
                        <td><?= h($linea['unidad_medida']) ?></td>
                        <td class="texto-derecha"><?= h($linea['cantidad']) ?></td>
                        <td class="texto-derecha"><?= pesos2($linea['precio_unitario']) ?></td>
                        <td class="texto-derecha"><?= pesos2($linea['subtotal']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" scope="row" class="texto-derecha">Total</th>
                    <th class="texto-derecha"><?= pesos2($total) ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <p class="comprobante-nota">Los importes se calculan con el precio vigente del producto. Este documento es un comprobante interno; no es una factura fiscal o electrónica.</p>
</article>

<div class="fila-botones solo-pantalla factura-acciones-inferiores acciones-factura-vista">
    <?php if ($factura['estado_factura'] === 'Pagado'): ?>
        <form class="form-cancelar-factura" method="post" action="cancelar_factura.php">
            <input type="hidden" name="id_factura" value="<?= h($factura['id_factura']) ?>">
            <input type="hidden" name="csrf_token" value="<?= h(obtenerTokenCsrf()) ?>">
            <button class="boton secundario" type="submit">Cancelar factura</button>
        </form>
    <?php else: ?>
        <button class="boton secundario" type="button" disabled
                title="Solo se pueden cancelar facturas en estado Pagado">Cancelar factura</button>
    <?php endif; ?>
    <button class="boton" type="button" onclick="window.print()">
        <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="6 9 6 2 18 2 18 9" />
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
            <rect x="6" y="14" width="12" height="8" />
        </svg>
        Imprimir
    </button>
    <a class="boton secundario" href="ver_facturas.php">Volver al listado</a>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
