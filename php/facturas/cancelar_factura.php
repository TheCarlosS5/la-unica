<?php
/**
 * Archivo: cancelar_factura.php
 * Módulo: Facturas — cancelación.
 * Propósito: cancelar una factura Pagado y reponer sus cantidades de inventario una sola vez.
 * Funcionamiento: exige POST y token CSRF. Primero muestra una pantalla de confirmación con los datos
 * de la factura; al confirmar (confirmar=si), dentro de una transacción bloquea la factura y productos,
 * agrupa cantidades por producto, repone stock, cambia el estado a Cancelado y confirma.
 * Puntos clave: solo se cancelan facturas Pagado del mismo día de la venta (para no alterar cierres
 * pasados); no admite una segunda cancelación. Cualquier
 * fallo provoca rollback, registra detalles técnicos en el log y devuelve el resultado por redirección 303.
 */

require __DIR__ . '/../config/conexion.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

$idFactura = validarId($_POST['id_factura'] ?? null);
if ($idFactura === null) {
    http_response_code(400);
    exit('El identificador de factura no es válido.');
}

if (!validarTokenCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('No se pudo validar la solicitud. Actualiza la página e inténtalo de nuevo.');
}

// Primer paso: sin confirmar=si se muestra la pantalla de confirmación con los datos de la factura.
if (($_POST['confirmar'] ?? '') !== 'si') {
    try {
        $consulta = $conexion->prepare(
            'SELECT f.nombre_cliente, f.fecha_factura, f.estado_factura, COUNT(d.id_detalle_factura) AS lineas, '
            . 'COALESCE(SUM(d.cantidad), 0) AS unidades, COALESCE(SUM(d.cantidad * d.precio_unitario), 0) AS total '
            . 'FROM facturas f LEFT JOIN detalle_facturas d ON d.id_factura = f.id_factura '
            . 'WHERE f.id_factura = ? GROUP BY f.id_factura, f.nombre_cliente, f.fecha_factura, f.estado_factura'
        );
        $consulta->bind_param('i', $idFactura);
        $consulta->execute();
        $factura = $consulta->get_result()->fetch_assoc();
        $consulta->close();
    } catch (Throwable $e) {
        registrarErrorServidor('confirmar_cancelacion_factura', $e);
        $factura = null;
    }

    // Si la factura no existe, ya no está pagada o no es de hoy, se vuelve al comprobante con el aviso.
    $avisoNoCancelable = '';
    if ($factura === null) {
        $avisoNoCancelable = 'No se pudo cargar la factura.';
    } elseif ($factura['estado_factura'] !== 'Pagado') {
        $avisoNoCancelable = 'Esta factura ya está cancelada.';
    } elseif (!esFacturaDeHoy($factura['fecha_factura'])) {
        $avisoNoCancelable = 'Solo se pueden cancelar facturas el mismo día de la venta.';
    }
    if ($avisoNoCancelable !== '') {
        if (sesionDisponible()) {
            $_SESSION['resultado_cancelacion_factura'] = [
                'id' => $idFactura,
                'exito' => false,
                'mensaje' => $avisoNoCancelable,
            ];
        }
        header('Location: imprimir_factura.php?id=' . $idFactura, true, 303);
        exit;
    }

    $titulo = 'Cancelar factura';
    $seccionActiva = 'facturas';
    $tokenCsrf = obtenerTokenCsrf();
    require __DIR__ . '/../includes/menu.php';
    ?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <a href="ver_facturas.php">Facturas</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Cancelar factura #<?= (int)$idFactura ?></span>
        </nav>
        <h1 class="encabezado-titulo">Confirmar cancelación</h1>
        <p class="encabezado-descripcion">La factura quedará como Cancelado y su total dejará de sumar en las ventas.</p>
    </div>
</div>

<section class="panel-confirmacion" aria-labelledby="titulo-confirmacion">
    <div class="panel-confirmacion-cabecera">
        <div class="panel-confirmacion-icono" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" />
                <line x1="12" y1="9" x2="12" y2="13" />
                <line x1="12" y1="17" x2="12.01" y2="17" />
            </svg>
        </div>
        <div>
            <h2 id="titulo-confirmacion" style="font-size: 1.15rem; margin: 0 0 4px; color: var(--color-peligro);">Cancelar factura</h2>
            <p style="font-size: 0.88rem; margin: 0; color: var(--color-texto-secundario);">Una factura cancelada no se puede volver a activar.</p>
        </div>
    </div>

    <div class="panel-confirmacion-cuerpo">
        <p style="margin: 0; font-size: 0.94rem; color: var(--color-texto);">¿Está seguro de que desea cancelar la siguiente factura?</p>

        <dl class="tarjeta-registro-eliminar">
            <dt>Factura</dt>
            <dd><span class="chip-id">#<?= (int)$idFactura ?></span></dd>
            <dt>Cliente</dt>
            <dd><?= h($factura['nombre_cliente']) ?></dd>
            <dt>Fecha</dt>
            <dd><?= h(date('d/m/Y H:i', strtotime($factura['fecha_factura']))) ?></dd>
            <dt>Total</dt>
            <dd><?= h(formatearMonedaCop($factura['total'])) ?></dd>
        </dl>

        <div class="nota nota-informativa" style="margin: 0;">
            <svg class="nota-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="1 4 1 10 7 10" />
                <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10" />
            </svg>
            <span>Se repondrán al inventario las <?= (int)$factura['unidades'] ?> unidades de sus <?= (int)$factura['lineas'] ?> <?= (int)$factura['lineas'] === 1 ? 'línea' : 'líneas' ?> de producto.</span>
        </div>
    </div>

    <form method="post" action="cancelar_factura.php">
        <?php if ($tokenCsrf !== null): ?>
            <input type="hidden" name="csrf_token" value="<?= h($tokenCsrf) ?>">
        <?php endif; ?>
        <input type="hidden" name="id_factura" value="<?= (int)$idFactura ?>">
        <input type="hidden" name="confirmar" value="si">
        <div class="panel-confirmacion-pie">
            <a class="boton secundario" href="imprimir_factura.php?id=<?= (int)$idFactura ?>">Volver</a>
            <button type="submit" class="boton-peligro-solido">
                <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="15" y1="9" x2="9" y2="15" />
                    <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
                <span>Confirmar cancelación</span>
            </button>
        </div>
    </form>
</section>
<?php
    require __DIR__ . '/../includes/pie.php';
    exit;
}

$transaccionIniciada = false;
$mensajeResultado = '';
$cancelacionExitosa = false;

try {
    // El cambio de estado y la reposición de todas las existencias forman una sola operación.
    $conexion->begin_transaction();
    $transaccionIniciada = true;

    $consultaFactura = $conexion->prepare('SELECT estado_factura, fecha_factura FROM facturas WHERE id_factura = ? FOR UPDATE');
    $consultaFactura->bind_param('i', $idFactura);
    $consultaFactura->execute();
    $filaFactura = $consultaFactura->get_result()->fetch_assoc();
    $consultaFactura->close();

    if ($filaFactura === null) {
        throw new DomainException('No existe esa factura; no se modificó el inventario.');
    }
    if ($filaFactura['estado_factura'] !== 'Pagado') {
        $mensajeEstado = $filaFactura['estado_factura'] === 'Cancelado'
            ? 'Esta factura ya está cancelada; no se repuso el inventario otra vez.'
            : 'Solo se pueden cancelar facturas con estado Pagado.';
        throw new DomainException($mensajeEstado);
    }
    // Se vuelve a comprobar aquí por si la confirmación quedó abierta hasta pasada la medianoche.
    if (!esFacturaDeHoy($filaFactura['fecha_factura'])) {
        throw new DomainException('Solo se pueden cancelar facturas el mismo día de la venta; no se modificó el inventario.');
    }

    // Agrupar líneas evita actualizar varias veces el mismo producto en una factura.
    $consultaDetalles = $conexion->prepare('SELECT id_producto, SUM(cantidad) AS cantidad_total FROM detalle_facturas WHERE id_factura = ? GROUP BY id_producto ORDER BY id_producto');
    $consultaDetalles->bind_param('i', $idFactura);
    $consultaDetalles->execute();
    $resultadoDetalles = $consultaDetalles->get_result();
    $productosFactura = $resultadoDetalles->fetch_all(MYSQLI_ASSOC);
    $resultadoDetalles->free();
    $consultaDetalles->close();

    if ($productosFactura === []) {
        throw new DomainException('La factura no tiene detalles; no se puede reponer el inventario.');
    }

    // La suma stock = stock + cantidad es atómica en MySQL; la cantidad viene de la base (CHECK cantidad > 0).
    $actualizarStock = $conexion->prepare('UPDATE productos SET stock = stock + ? WHERE id_producto = ?');
    foreach ($productosFactura as $productoFactura) {
        $cantidad = (int)$productoFactura['cantidad_total'];
        $idProducto = (int)$productoFactura['id_producto'];
        $actualizarStock->bind_param('ii', $cantidad, $idProducto);
        $actualizarStock->execute();
        if ($actualizarStock->affected_rows !== 1) {
            throw new DomainException('No se encontró un producto de la factura; no se aplicaron cambios.');
        }
    }
    $actualizarStock->close();

    // La condición de estado también protege frente a una cancelación repetida o concurrente.
    $actualizarFactura = $conexion->prepare("UPDATE facturas SET estado_factura = 'Cancelado' WHERE id_factura = ? AND estado_factura = 'Pagado'");
    $actualizarFactura->bind_param('i', $idFactura);
    $actualizarFactura->execute();
    $facturaActualizada = $actualizarFactura->affected_rows === 1;
    $actualizarFactura->close();
    if (!$facturaActualizada) {
        throw new DomainException('La factura cambió de estado durante la cancelación; no se aplicaron cambios.');
    }

    // El registro del historial va dentro de la transacción: cancelación y registro se confirman juntos.
    registrarAccion($conexion, 'Facturas', 'Cancelar', 'Factura #' . $idFactura . ' cancelada; se repuso el inventario.', true);
    $conexion->commit();
    $transaccionIniciada = false;
    $cancelacionExitosa = true;
    $mensajeResultado = 'Factura cancelada. Se repuso el inventario.';
} catch (DomainException $e) {
    if ($transaccionIniciada) {
        try {
            $conexion->rollback();
        } catch (Throwable $errorRollback) {
            registrarErrorServidor('cancelar_factura_rollback', $errorRollback);
        }
    }
    $mensajeResultado = $e->getMessage();
} catch (Throwable $e) {
    if ($transaccionIniciada) {
        try {
            $conexion->rollback();
        } catch (Throwable $errorRollback) {
            registrarErrorServidor('cancelar_factura_rollback', $errorRollback);
        }
    }
    registrarErrorServidor('cancelar_factura', $e);
    $mensajeResultado = 'No se pudo cancelar la factura. No se aplicaron cambios; inténtalo de nuevo.';
}

if (sesionDisponible()) {
    $_SESSION['resultado_cancelacion_factura'] = [
        'id' => $idFactura,
        'exito' => $cancelacionExitosa,
        'mensaje' => $mensajeResultado,
    ];
}

header('Location: imprimir_factura.php?id=' . $idFactura, true, 303);
exit;
