<?php
/**
 * Archivo: servicio_facturas.php
 * Módulo: Facturas — reglas de negocio.
 * Propósito: registrar ventas, registrar pagos y cancelar facturas sin depender de la vista HTML,
 * para que las páginas y las pruebas automáticas usen exactamente la misma lógica.
 * Puntos clave:
 * - Cada operación corre en una transacción; cualquier excepción la revierte por completo.
 * - Las reglas incumplidas lanzan DomainException con un mensaje apto para el operador.
 * - El precio de cada línea se copia del catálogo al vender y no cambia después.
 */

const ESTADOS_VENTA = ['Pagado', 'Pendiente'];

/**
 * Ejecuta $operacion dentro de una transacción y la revierte si lanza cualquier excepción.
 * @return mixed Lo que devuelva $operacion.
 */
function ejecutarEnTransaccion(mysqli $conexion, callable $operacion)
{
    $conexion->begin_transaction();
    try {
        $resultado = $operacion($conexion);
        $conexion->commit();
        return $resultado;
    } catch (Throwable $e) {
        try {
            $conexion->rollback();
        } catch (Throwable $errorRollback) {
            error_log('[rollback] ' . $errorRollback->getMessage());
        }
        throw $e;
    }
}

/**
 * Registra una venta: bloquea cliente y productos, valida stock, guarda la factura con el precio
 * vigente de cada producto y descuenta las existencias.
 * @param array<int,int> $cantidadesPorProducto Cantidad total por ID de producto (ya agrupada).
 * @param string $estado Pagado (contado) o Pendiente (por cobrar).
 * @return array{id:int,total:string} ID de la factura y total guardado.
 */
function registrarVenta(mysqli $conexion, int $idCliente, array $cantidadesPorProducto, string $estado = 'Pagado'): array
{
    if (!in_array($estado, ESTADOS_VENTA, true)) {
        throw new DomainException('El estado de la venta debe ser Pagado o Pendiente.');
    }
    if ($cantidadesPorProducto === []) {
        throw new DomainException('Agrega al menos un producto a la venta.');
    }

    return ejecutarEnTransaccion($conexion, static function (mysqli $conexion) use ($idCliente, $cantidadesPorProducto, $estado): array {
        $consultaCliente = $conexion->prepare('SELECT id_cliente FROM clientes WHERE id_cliente = ? FOR UPDATE');
        $consultaCliente->bind_param('i', $idCliente);
        $consultaCliente->execute();
        $clienteExiste = $consultaCliente->get_result()->fetch_assoc() !== null;
        $consultaCliente->close();
        if (!$clienteExiste) {
            throw new DomainException('El cliente seleccionado ya no existe. Actualiza la página y vuelve a seleccionarlo.');
        }

        // Bloquear productos siempre en orden de ID reduce interbloqueos entre ventas concurrentes.
        $idsOrdenados = array_keys($cantidadesPorProducto);
        sort($idsOrdenados, SORT_NUMERIC);
        $precios = [];
        $consultaProducto = $conexion->prepare('SELECT stock, precio_unitario FROM productos WHERE id_producto = ? FOR UPDATE');
        foreach ($idsOrdenados as $idProducto) {
            $consultaProducto->bind_param('i', $idProducto);
            $consultaProducto->execute();
            $producto = $consultaProducto->get_result()->fetch_assoc();
            if ($producto === null) {
                throw new DomainException('Uno de los productos seleccionados ya no existe. Actualiza la página.');
            }
            if ((int)$producto['stock'] < $cantidadesPorProducto[$idProducto]) {
                throw new DomainException('Las existencias cambiaron y no alcanzan para completar la venta. Revisa las cantidades e inténtalo de nuevo.');
            }
            $precios[$idProducto] = (string)$producto['precio_unitario'];
        }
        $consultaProducto->close();

        $crearFactura = $conexion->prepare('INSERT INTO facturas (id_cliente, estado_factura) VALUES (?, ?)');
        $crearFactura->bind_param('is', $idCliente, $estado);
        $crearFactura->execute();
        $idFactura = (int)$conexion->insert_id;
        $crearFactura->close();

        $insertarDetalle = $conexion->prepare('INSERT INTO detalle_facturas (id_factura, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)');
        $descontarStock = $conexion->prepare('UPDATE productos SET stock = stock - ? WHERE id_producto = ? AND stock >= ?');
        foreach ($idsOrdenados as $idProducto) {
            $cantidad = $cantidadesPorProducto[$idProducto];
            $insertarDetalle->bind_param('iiis', $idFactura, $idProducto, $cantidad, $precios[$idProducto]);
            $insertarDetalle->execute();

            $descontarStock->bind_param('iii', $cantidad, $idProducto, $cantidad);
            $descontarStock->execute();
            if ($descontarStock->affected_rows !== 1) {
                throw new DomainException('Las existencias cambiaron durante la venta. No se guardó la factura; inténtalo de nuevo.');
            }
        }
        $insertarDetalle->close();
        $descontarStock->close();

        return ['id' => $idFactura, 'total' => totalFactura($conexion, $idFactura)];
    });
}

/**
 * Suma cantidad por precio guardado de las líneas de una factura, en SQL para no perder centavos.
 * @return string Total decimal con dos posiciones ("0.00" si no hay líneas).
 */
function totalFactura(mysqli $conexion, int $idFactura): string
{
    $consulta = $conexion->prepare('SELECT COALESCE(SUM(cantidad * precio_unitario), 0.00) AS total FROM detalle_facturas WHERE id_factura = ?');
    try {
        $consulta->bind_param('i', $idFactura);
        $consulta->execute();
        return (string)$consulta->get_result()->fetch_assoc()['total'];
    } finally {
        $consulta->close();
    }
}

/**
 * Bloquea una factura y devuelve su estado actual.
 * @throws DomainException Si la factura no existe.
 */
function bloquearEstadoFactura(mysqli $conexion, int $idFactura): string
{
    $consulta = $conexion->prepare('SELECT estado_factura FROM facturas WHERE id_factura = ? FOR UPDATE');
    $consulta->bind_param('i', $idFactura);
    $consulta->execute();
    $fila = $consulta->get_result()->fetch_assoc();
    $consulta->close();

    if ($fila === null) {
        throw new DomainException('No existe esa factura; no se aplicaron cambios.');
    }
    return (string)$fila['estado_factura'];
}

/**
 * Marca como Pagado una factura Pendiente. No modifica el inventario.
 */
function registrarPagoFactura(mysqli $conexion, int $idFactura): void
{
    ejecutarEnTransaccion($conexion, static function (mysqli $conexion) use ($idFactura): void {
        if (bloquearEstadoFactura($conexion, $idFactura) !== 'Pendiente') {
            throw new DomainException('Solo se puede registrar el pago de una factura Pendiente.');
        }

        $actualizar = $conexion->prepare("UPDATE facturas SET estado_factura = 'Pagado' WHERE id_factura = ?");
        $actualizar->bind_param('i', $idFactura);
        $actualizar->execute();
        $actualizar->close();
    });
}

/**
 * Cancela una factura Pagado o Pendiente y repone sus cantidades en el inventario una sola vez.
 */
function cancelarFactura(mysqli $conexion, int $idFactura): void
{
    ejecutarEnTransaccion($conexion, static function (mysqli $conexion) use ($idFactura): void {
        $estado = bloquearEstadoFactura($conexion, $idFactura);
        if ($estado === 'Cancelado') {
            throw new DomainException('Esta factura ya está cancelada; no se repuso el inventario otra vez.');
        }

        // Agrupar líneas evita actualizar varias veces el mismo producto de una factura.
        $consultaDetalles = $conexion->prepare('SELECT id_producto, SUM(cantidad) AS cantidad FROM detalle_facturas WHERE id_factura = ? GROUP BY id_producto ORDER BY id_producto');
        $consultaDetalles->bind_param('i', $idFactura);
        $consultaDetalles->execute();
        $lineas = $consultaDetalles->get_result()->fetch_all(MYSQLI_ASSOC);
        $consultaDetalles->close();

        if ($lineas === []) {
            throw new DomainException('La factura no tiene detalles; no se puede reponer el inventario.');
        }

        $reponerStock = $conexion->prepare('UPDATE productos SET stock = stock + ? WHERE id_producto = ?');
        foreach ($lineas as $linea) {
            $cantidad = (int)$linea['cantidad'];
            $idProducto = (int)$linea['id_producto'];
            $reponerStock->bind_param('ii', $cantidad, $idProducto);
            $reponerStock->execute();
            if ($reponerStock->affected_rows !== 1) {
                throw new DomainException('No se encontró un producto de la factura; no se modificó el inventario.');
            }
        }
        $reponerStock->close();

        $actualizarFactura = $conexion->prepare("UPDATE facturas SET estado_factura = 'Cancelado' WHERE id_factura = ?");
        $actualizarFactura->bind_param('i', $idFactura);
        $actualizarFactura->execute();
        $actualizarFactura->close();
    });
}
