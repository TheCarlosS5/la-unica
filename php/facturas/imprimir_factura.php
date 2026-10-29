<?php
/**
 * Archivo: imprimir_factura.php
 * Módulo: Facturas — consulta y comprobante.
 * Propósito: presentar el detalle de una factura y generar una vista imprimible del comprobante interno.
 * Funcionamiento: valida el ID, carga factura, cliente, detalles y total mediante consultas preparadas;
 * el total se recalcula con el precio vigente de cada producto.
 * Puntos clave: la impresión se inicia desde el navegador; solo una factura Pagado muestra la acción
 * de cancelación y esta requiere CSRF. El comprobante no es factura fiscal ni electrónica.
 */

require __DIR__ . '/../config/conexion.php';

$idFactura = validarId($_GET['id'] ?? null);
$factura = null;
$detalles = [];
$totalFactura = '0.00';
$errorLectura = false;
$mensajeError = '';
$tokenCsrf = obtenerTokenCsrf();
$mensajeCancelacion = '';
$cancelacionExitosa = false;

if (sesionDisponible() && isset($_SESSION['resultado_cancelacion_factura']) && is_array($_SESSION['resultado_cancelacion_factura'])) {
    $resultadoCancelacion = $_SESSION['resultado_cancelacion_factura'];
    unset($_SESSION['resultado_cancelacion_factura']);
    if ((int)($resultadoCancelacion['id'] ?? 0) === (int)$idFactura) {
        $mensajeCancelacion = is_string($resultadoCancelacion['mensaje'] ?? null) ? $resultadoCancelacion['mensaje'] : '';
        $cancelacionExitosa = ($resultadoCancelacion['exito'] ?? false) === true;
    }
}

if ($idFactura === null) {
    http_response_code(400);
    $mensajeError = 'El identificador de factura no es válido.';
} else {
    try {
        $consultaFactura = $conexion->prepare('SELECT f.id_factura, f.id_cliente, c.nombre AS cliente, f.estado_factura, f.fecha_factura FROM facturas f INNER JOIN clientes c ON c.id_cliente = f.id_cliente WHERE f.id_factura = ?');
        $consultaFactura->bind_param('i', $idFactura);
        $consultaFactura->execute();
        $factura = $consultaFactura->get_result()->fetch_assoc();
        $consultaFactura->close();

        if ($factura === null) {
            http_response_code(404);
            $mensajeError = 'No existe una factura con ese identificador.';
        } else {
            $consultaDetalles = $conexion->prepare('SELECT d.id_detalle_factura, d.id_producto, p.nombre AS producto, p.unidad_medida, d.cantidad, p.precio_unitario, d.cantidad * p.precio_unitario AS subtotal FROM detalle_facturas d INNER JOIN productos p ON p.id_producto = d.id_producto WHERE d.id_factura = ? ORDER BY d.id_detalle_factura');
            $consultaDetalles->bind_param('i', $idFactura);
            $consultaDetalles->execute();
            $resultadoDetalles = $consultaDetalles->get_result();
            $detalles = $resultadoDetalles->fetch_all(MYSQLI_ASSOC);
            $resultadoDetalles->free();
            $consultaDetalles->close();

            $consultaTotal = $conexion->prepare('SELECT COALESCE(SUM(d.cantidad * p.precio_unitario), 0.00) AS total_factura FROM detalle_facturas d INNER JOIN productos p ON p.id_producto = d.id_producto WHERE d.id_factura = ?');
            $consultaTotal->bind_param('i', $idFactura);
            $consultaTotal->execute();
            $filaTotal = $consultaTotal->get_result()->fetch_assoc();
            $consultaTotal->close();
            if ($filaTotal !== null) {
                $totalFactura = (string)$filaTotal['total_factura'];
            }
        }
    } catch (Throwable $e) {
        registrarErrorServidor('consultar_factura', $e);
        $errorLectura = true;
        $mensajeError = 'No se pudo cargar el comprobante. Inténtalo de nuevo más tarde.';
        $factura = null;
        $detalles = [];
        $totalFactura = '0.00';
        if (isset($consultaFactura) && $consultaFactura instanceof mysqli_stmt) {
            $consultaFactura->close();
        }
        if (isset($consultaDetalles) && $consultaDetalles instanceof mysqli_stmt) {
            $consultaDetalles->close();
        }
        if (isset($consultaTotal) && $consultaTotal instanceof mysqli_stmt) {
            $consultaTotal->close();
        }
    }
}

$titulo = $idFactura === null ? 'Comprobante' : 'Factura #' . $idFactura;
$seccionActiva = 'facturas';
require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista acciones-superiores solo-pantalla">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <a href="ver_facturas.php">Facturas</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Comprobante</span>
        </nav>
        <h1 class="encabezado-titulo"><?= $factura !== null ? 'Factura #' . (int)$factura['id_factura'] : 'Comprobante' ?></h1>
        <p class="encabezado-descripcion">Consulta e imprime el detalle con los precios vigentes del catálogo.</p>
    </div>
    <div class="encabezado-acciones factura-acciones">
        <?php if ($factura !== null): ?>
            <button class="boton" type="button" onclick="window.print()">Imprimir</button>
            <?php if ($factura['estado_factura'] === 'Pagado' && $tokenCsrf !== null): ?>
                <form class="form-cancelar-factura" method="post" action="cancelar_factura.php" onsubmit="return confirm('¿Cancelar esta factura y reponer las existencias?');">
                    <input type="hidden" name="csrf_token" value="<?= h($tokenCsrf) ?>">
                    <input type="hidden" name="id_factura" value="<?= (int)$factura['id_factura'] ?>">
                    <button class="boton-peligro" type="submit">Cancelar factura</button>
                </form>
            <?php elseif ($factura['estado_factura'] === 'Cancelado'): ?>
                <button class="boton-peligro" type="button" disabled>Factura cancelada</button>
            <?php elseif ($factura['estado_factura'] === 'Pagado'): ?>
                <button class="boton secundario" type="button" disabled>Sesión segura no disponible</button>
            <?php else: ?>
                <button class="boton secundario" type="button" disabled>No disponible para este estado</button>
            <?php endif; ?>
        <?php endif; ?>
        <a class="boton secundario" href="ver_facturas.php">Volver a facturas</a>
    </div>
</div>

<?php if ($mensajeCancelacion !== ''): ?>
    <div class="mensaje <?= $cancelacionExitosa ? '' : 'error ' ?>alerta <?= $cancelacionExitosa ? '' : 'alerta-error' ?>" role="<?= $cancelacionExitosa ? 'status' : 'alert' ?>"><?= h($mensajeCancelacion) ?></div>
<?php endif; ?>

<?php if ($mensajeError !== ''): ?>
    <div class="mensaje error alerta alerta-error" role="alert"><?= h($mensajeError) ?></div>
    <?php if ($factura === null): ?><p class="solo-pantalla"><a class="boton secundario" href="ver_facturas.php">Volver a facturas</a></p><?php endif; ?>
<?php endif; ?>

<?php if ($factura !== null): ?>
    <?php
    $hayFechaFactura = is_string($factura['fecha_factura']) && $factura['fecha_factura'] !== '';
    $fechaFactura = $hayFechaFactura ? (new DateTimeImmutable($factura['fecha_factura']))->format('d/m/Y H:i') : 'Sin fecha registrada';
    ?>
    <article class="comprobante-factura" aria-labelledby="titulo-comprobante">
        <header class="comprobante-cabecera">
            <div>
                <p class="comprobante-marca">La Única</p>
                <h2 id="titulo-comprobante">Comprobante interno de venta</h2>
            </div>
            <div class="comprobante-identificador">
                <span>Número de factura</span>
                <strong>#<?= (int)$factura['id_factura'] ?></strong>
            </div>
        </header>

        <dl class="comprobante-datos">
            <div><dt>Cliente</dt><dd><?= h($factura['cliente']) ?></dd></div>
            <div><dt>ID del cliente</dt><dd><?= (int)$factura['id_cliente'] ?></dd></div>
            <div><dt>Fecha</dt><dd><?= h($fechaFactura) ?><?= $hayFechaFactura ? ' (Bogotá)' : '' ?></dd></div>
            <div><dt>Estado</dt><dd><span class="estado-factura <?= strtolower(h($factura['estado_factura'])) ?>"><?= h($factura['estado_factura']) ?></span></dd></div>
        </dl>

        <?php if ($detalles === []): ?>
            <div class="mensaje aviso alerta alerta-aviso" role="status">Esta factura no tiene líneas de detalle registradas.</div>
        <?php else: ?>
            <div class="tabla-wrap tabla-contenedor comprobante-tabla">
                <table class="tabla">
                    <thead>
                        <tr>
                            <th scope="col">Producto</th>
                            <th scope="col">Unidad</th>
                            <th scope="col" class="texto-centro">Cantidad</th>
                            <th scope="col" class="texto-derecha">Precio vigente</th>
                            <th scope="col" class="texto-derecha">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $detalle): ?>
                            <tr>
                                <td><span class="celda-destacada"><?= h($detalle['producto']) ?></span></td>
                                <td><?= h($detalle['unidad_medida']) ?></td>
                                <td class="texto-centro"><?= (int)$detalle['cantidad'] ?></td>
                                <td class="texto-derecha"><?= h(formatearMonedaCop($detalle['precio_unitario'])) ?></td>
                                <td class="texto-derecha"><?= h(formatearMonedaCop($detalle['subtotal'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" scope="row" class="texto-derecha">Total</th>
                            <th class="texto-derecha"><?= h(formatearMonedaCop($totalFactura)) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>

        <p class="comprobante-nota">Los importes se calculan con el precio vigente del producto. Este documento es un comprobante interno; no es una factura fiscal o electrónica.</p>
    </article>

    <div class="fila-botones solo-pantalla factura-acciones-inferiores">
        <button type="button" onclick="window.print()">Imprimir comprobante</button>
        <a class="boton secundario" href="ver_facturas.php">Volver al listado</a>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
