<?php
/**
 * Archivo: cancelar_factura.php
 * Módulo: Facturas — cancelación.
 * Propósito: cancelar una factura Pagado y reponer sus cantidades de inventario una sola vez.
 * Funcionamiento: exige POST y token CSRF; dentro de una transacción bloquea la factura y productos,
 * agrupa cantidades por producto, repone stock, cambia el estado a Cancelado y confirma.
 * Puntos clave: no admite otros estados ni una segunda cancelación; valida límites de stock. Cualquier
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

$transaccionIniciada = false;
$mensajeResultado = '';
$cancelacionExitosa = false;

try {
    // El cambio de estado y la reposición de todas las existencias forman una sola operación.
    $conexion->begin_transaction();
    $transaccionIniciada = true;

    $consultaFactura = $conexion->prepare('SELECT estado_factura FROM facturas WHERE id_factura = ? FOR UPDATE');
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

    $bloquearProducto = $conexion->prepare('SELECT stock FROM productos WHERE id_producto = ? FOR UPDATE');
    $actualizarStock = $conexion->prepare('UPDATE productos SET stock = stock + ? WHERE id_producto = ? AND stock <= ?');
    foreach ($productosFactura as $productoFactura) {
        $idProducto = (int)$productoFactura['id_producto'];
        $cantidadTexto = (string)$productoFactura['cantidad_total'];
        if (!ctype_digit($cantidadTexto) || strlen($cantidadTexto) > 10 || (float)$cantidadTexto > 2147483647) {
            throw new DomainException('La cantidad acumulada supera el límite de existencias permitido.');
        }
        $cantidad = (int)$cantidadTexto;
        if ($cantidad < 1) {
            throw new DomainException('La factura contiene una cantidad inválida; no se modificó el inventario.');
        }

        $bloquearProducto->bind_param('i', $idProducto);
        $bloquearProducto->execute();
        $filaProducto = $bloquearProducto->get_result()->fetch_assoc();
        if ($filaProducto === null) {
            throw new DomainException('No se encontró un producto de la factura; no se modificó el inventario.');
        }

        $stockActual = (int)$filaProducto['stock'];
        if ($stockActual > 2147483647 - $cantidad) {
            throw new DomainException('La reposición superaría el máximo de stock permitido; no se modificó el inventario.');
        }

        $maximoAntesDeSumar = 2147483647 - $cantidad;
        $actualizarStock->bind_param('iii', $cantidad, $idProducto, $maximoAntesDeSumar);
        $actualizarStock->execute();
        if ($actualizarStock->affected_rows !== 1) {
            throw new DomainException('El inventario cambió durante la cancelación; no se aplicaron cambios.');
        }
    }
    $bloquearProducto->close();
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
