<?php
// ===================================================================
// cancelar_factura.php
// Paso 1: llega desde la factura -> muestra "¿Seguro?".
// Paso 2: llega con confirmar=si  -> cancela y vuelve a la factura.
// Cancelar = pasar el estado de Pagado a Cancelado y devolver las
// cantidades al stock. Todo en una transacción, y solo una vez.
// ===================================================================
require __DIR__ . '/../config/conexion.php';

function volver_a_factura($id, $texto, $tipo = 'exito') {
    header('Location: ver_factura.php?id=' . $id . '&tipo=' . $tipo . '&mensaje=' . urlencode($texto));
    exit;
}

// Solo se aceptan peticiones POST con un ID válido.
$id = id_valido($_POST['id_factura'] ?? '');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id === null) {
    header('Location: ver_facturas.php?tipo=error&mensaje=' . urlencode('Solicitud no válida.'));
    exit;
}

$consulta = $conexion->prepare('SELECT estado_factura FROM facturas WHERE id_factura = ?');
$consulta->bind_param('i', $id);
$consulta->execute();
$consulta->bind_result($estado);
$existe = $consulta->fetch();
$consulta->close();

if (!$existe) {
    header('Location: ver_facturas.php?tipo=error&mensaje=' . urlencode('No se encontró la factura.'));
    exit;
}
if ($estado !== 'Pagado') {
    volver_a_factura($id, 'Solo se pueden cancelar facturas en estado Pagado.', 'error');
}

// Paso 2: el usuario ya confirmó.
if (($_POST['confirmar'] ?? '') === 'si') {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // los errores de MySQL lanzan excepciones
    try {
        $conexion->begin_transaction();

        // Bloquear la factura y volver a revisar su estado: si dos personas
        // cancelan a la vez, la segunda ve "Cancelado" y se rechaza.
        $bloqueo = $conexion->prepare('SELECT estado_factura FROM facturas WHERE id_factura = ? FOR UPDATE');
        $bloqueo->bind_param('i', $id);
        $bloqueo->execute();
        $estadoActual = $bloqueo->get_result()->fetch_row()[0] ?? '';
        $bloqueo->close();
        if ($estadoActual !== 'Pagado') {
            throw new RuntimeException('La factura ya no está en estado Pagado.');
        }

        // Cantidades a devolver (sumadas por producto, en orden para evitar bloqueos cruzados).
        $detalles = $conexion->prepare('SELECT id_producto, SUM(cantidad) FROM detalle_facturas WHERE id_factura = ? GROUP BY id_producto ORDER BY id_producto');
        $detalles->bind_param('i', $id);
        $detalles->execute();
        $devolver = $detalles->get_result()->fetch_all(MYSQLI_NUM);
        $detalles->close();

        $leer = $conexion->prepare('SELECT nombre, stock FROM productos WHERE id_producto = ? FOR UPDATE');
        $sumar = $conexion->prepare('UPDATE productos SET stock = stock + ? WHERE id_producto = ?');
        foreach ($devolver as [$idProducto, $cantidad]) {
            $leer->bind_param('i', $idProducto);
            $leer->execute();
            $producto = $leer->get_result()->fetch_assoc();
            // El stock es INT: si devolver la cantidad lo pasa del máximo, se rechaza TODA la cancelación.
            if ($producto['stock'] + $cantidad > 2147483647) {
                throw new RuntimeException('No se puede cancelar: el stock de «' . $producto['nombre'] . '» superaría el máximo permitido.');
            }
            $sumar->bind_param('ii', $cantidad, $idProducto);
            $sumar->execute();
        }

        $cancelar = $conexion->prepare("UPDATE facturas SET estado_factura = 'Cancelado' WHERE id_factura = ?");
        $cancelar->bind_param('i', $id);
        $cancelar->execute();

        $conexion->commit();
        mysqli_report(MYSQLI_REPORT_OFF);
        volver_a_factura($id, 'Factura cancelada. Las cantidades volvieron al stock.');
    } catch (RuntimeException $e) {
        $conexion->rollback();
        mysqli_report(MYSQLI_REPORT_OFF);
        volver_a_factura($id, $e->getMessage(), 'error');
    } catch (Throwable $e) {
        $conexion->rollback();
        mysqli_report(MYSQLI_REPORT_OFF);
        volver_a_factura($id, 'No se pudo cancelar la factura. No se hizo ningún cambio.', 'error');
    }
}

// Paso 1: mostrar la confirmación.
$titulo = 'Cancelar factura';
$seccionActiva = 'facturas';
require __DIR__ . '/../includes/menu.php';
?>
<div class="tarjeta confirmacion aparecer">
    <span class="insignia peligro"><?= icono('close') ?></span>
    <h1 class="h4 mt-3">¿Cancelar la factura #<?= h($id) ?>?</h1>
    <p class="text-secondary">Las cantidades vuelven al stock. La factura queda en estado Cancelado y no se puede reactivar.</p>

    <form method="post" class="d-flex gap-2 justify-content-center">
        <input type="hidden" name="id_factura" value="<?= h($id) ?>">
        <input type="hidden" name="confirmar" value="si">
        <a class="btn btn-light" href="ver_factura.php?id=<?= h($id) ?>">No, volver</a>
        <button class="btn btn-danger" type="submit">Sí, cancelar</button>
    </form>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
