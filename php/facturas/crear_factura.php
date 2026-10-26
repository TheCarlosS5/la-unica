<?php
// ===================================================================
// crear_factura.php  (la "C" de CRUD: Create / Crear)
// El operador elige un cliente, agrega productos con cantidades enteras
// y al guardar:
//   1. se revisa de nuevo el stock en el servidor,
//   2. se guarda la factura (estado Pagado) y sus detalles,
//   3. se descuenta el stock.
// Los tres pasos van dentro de una TRANSACCIÓN: o se guardan todos o ninguno.
// ===================================================================
require __DIR__ . '/../config/conexion.php';
$titulo = 'Nueva factura';
$seccionActiva = 'facturas';

$error = '';
$idCliente = '';
$lineas = [['id_producto' => '', 'cantidad' => '']]; // líneas del formulario

// Datos para llenar las listas desplegables.
$clientes = $conexion->query('SELECT id_cliente, nombre FROM clientes ORDER BY nombre')->fetch_all(MYSQLI_ASSOC);
$productos = $conexion->query('SELECT id_producto, nombre, unidad_medida, stock, precio_unitario FROM productos ORDER BY nombre')->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idCliente = trim($_POST['id_cliente'] ?? '');
    $ids = (array)($_POST['id_producto'] ?? []);
    $cantidades = (array)($_POST['cantidad'] ?? []);

    // 1. Armar las líneas. Si un producto se repite, se suman sus cantidades.
    $pedido = [];     // id_producto => cantidad total
    $lineas = [];     // para volver a mostrar el formulario si hay error
    foreach ($ids as $i => $idProducto) {
        $idProducto = trim((string)$idProducto);
        $cantidadTexto = trim((string)($cantidades[$i] ?? ''));
        if ($idProducto === '' && $cantidadTexto === '') {
            continue; // línea vacía: se ignora
        }
        $lineas[] = ['id_producto' => $idProducto, 'cantidad' => $cantidadTexto];

        $idValido = id_valido($idProducto);
        $cantidad = filter_var($cantidadTexto, FILTER_VALIDATE_INT);
        if ($idValido === null) {
            $error = 'Elige un producto en cada línea.';
        } elseif ($cantidad === false || $cantidad < 1 || $cantidad > 2147483647) {
            $error = 'La cantidad debe ser un número entero mayor que 0.';
        } else {
            $pedido[$idValido] = ($pedido[$idValido] ?? 0) + $cantidad;
        }
    }
    if ($lineas === []) {
        $lineas = [['id_producto' => '', 'cantidad' => '']];
    }

    if ($error === '' && id_valido($idCliente) === null) {
        $error = 'Elige un cliente.';
    } elseif ($error === '' && $pedido === []) {
        $error = 'La factura necesita al menos un producto.';
    }

    // 2. Guardar todo dentro de una transacción.
    if ($error === '') {
        $idCliente = (int)$idCliente;
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // ahora los errores de MySQL lanzan excepciones
        try {
            $conexion->begin_transaction();

            // El cliente debe existir.
            $consulta = $conexion->prepare('SELECT 1 FROM clientes WHERE id_cliente = ?');
            $consulta->bind_param('i', $idCliente);
            $consulta->execute();
            if (!$consulta->get_result()->fetch_row()) {
                throw new RuntimeException('El cliente elegido no existe.');
            }
            $consulta->close();

            // Bloquear los productos (FOR UPDATE) para que nadie más cambie su stock
            // mientras guardamos. Siempre en el mismo orden para evitar bloqueos cruzados.
            ksort($pedido);
            $consulta = $conexion->prepare('SELECT nombre, stock FROM productos WHERE id_producto = ? FOR UPDATE');
            foreach ($pedido as $idProducto => $cantidad) {
                $consulta->bind_param('i', $idProducto);
                $consulta->execute();
                $producto = $consulta->get_result()->fetch_assoc();
                if (!$producto) {
                    throw new RuntimeException('Uno de los productos ya no existe.');
                }
                if ((int)$producto['stock'] === 0) {
                    throw new RuntimeException('«' . $producto['nombre'] . '» está agotado.');
                }
                if ($cantidad > (int)$producto['stock']) {
                    throw new RuntimeException('No hay stock suficiente de «' . $producto['nombre'] . '»: quedan ' . $producto['stock'] . '.');
                }
            }
            $consulta->close();

            // La factura se guarda como Pagado (el valor por defecto del esquema es Pendiente).
            $insertar = $conexion->prepare("INSERT INTO facturas (id_cliente, estado_factura) VALUES (?, 'Pagado')");
            $insertar->bind_param('i', $idCliente);
            $insertar->execute();
            $idFactura = $conexion->insert_id;
            $insertar->close();

            // Una fila de detalle por producto y se descuenta el stock.
            $detalle = $conexion->prepare('INSERT INTO detalle_facturas (id_factura, id_producto, cantidad) VALUES (?, ?, ?)');
            $descontar = $conexion->prepare('UPDATE productos SET stock = stock - ? WHERE id_producto = ?');
            foreach ($pedido as $idProducto => $cantidad) {
                $detalle->bind_param('iii', $idFactura, $idProducto, $cantidad);
                $detalle->execute();
                $descontar->bind_param('ii', $cantidad, $idProducto);
                $descontar->execute();
            }

            $conexion->commit();
            mysqli_report(MYSQLI_REPORT_OFF);
            header('Location: ver_factura.php?id=' . $idFactura . '&mensaje=' . urlencode('Factura creada correctamente.'));
            exit;
        } catch (RuntimeException $e) {
            $conexion->rollback();
            $error = $e->getMessage(); // error de regla de negocio: se muestra tal cual
        } catch (Throwable $e) {
            $conexion->rollback();
            $error = 'No se pudo guardar la factura. No se hizo ningún cambio.';
        }
        mysqli_report(MYSQLI_REPORT_OFF);
    }
}

require __DIR__ . '/../includes/menu.php';
?>
<a class="volver" href="ver_facturas.php">← Facturas</a>

<div class="tarjeta formulario aparecer" style="max-width: 60rem;">
    <div class="formulario-cabecera">
        <span class="insignia"><?= icono('receipt') ?></span>
        <div>
            <h1 class="h4 mb-1">Nueva factura</h1>
            <p class="text-secondary mb-0">Elige el cliente y agrega los productos.</p>
        </div>
    </div>

    <?= alerta($error, 'error') ?>

    <?php if (count($clientes) === 0 || count($productos) === 0): ?>
        <p class="mb-0">Para facturar necesitas al menos un cliente y un producto registrados.</p>
    <?php else: ?>
    <form method="post" id="form-factura">
        <div class="mb-4">
            <label class="form-label" for="id_cliente">Cliente</label>
            <select class="form-select" id="id_cliente" name="id_cliente" required>
                <option value="">Elige un cliente…</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= h($c['id_cliente']) ?>" <?= (string)$idCliente === (string)$c['id_cliente'] ? 'selected' : '' ?>>
                        <?= h($c['nombre']) ?> (<?= h($c['id_cliente']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <label class="form-label">Productos</label>
        <div id="lineas">
            <?php foreach ($lineas as $linea): ?>
                <div class="linea-producto">
                    <select class="form-select" name="id_producto[]" aria-label="Producto">
                        <option value="">Elige un producto…</option>
                        <?php foreach ($productos as $p): ?>
                            <?php $agotado = (int)$p['stock'] === 0; ?>
                            <option value="<?= h($p['id_producto']) ?>" data-precio="<?= h($p['precio_unitario']) ?>" data-stock="<?= h($p['stock']) ?>"
                                    <?= $agotado ? 'disabled' : '' ?> <?= $linea['id_producto'] === (string)$p['id_producto'] ? 'selected' : '' ?>>
                                <?= h($p['nombre']) ?> — <?= $agotado ? 'Agotado' : h($p['stock']) . ' ' . h($p['unidad_medida']) . ' · ' . pesos($p['precio_unitario']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input class="form-control" name="cantidad[]" type="number" min="1" step="1" placeholder="Cantidad"
                           value="<?= h($linea['cantidad']) ?>" aria-label="Cantidad">
                    <span class="subtotal text-secondary">—</span>
                    <button class="btn-icono" type="button" data-quitar title="Quitar línea"><?= icono('close') ?></button>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="btn btn-light btn-con-icono mt-2" type="button" id="agregar-linea">
            <?= icono('plus') ?> Agregar producto
        </button>

        <div class="total-factura px-0"><span>Total</span> <span id="total">$ 0,00</span></div>
        <p class="nota mt-0">El stock se vuelve a revisar al guardar.</p>

        <div class="d-flex gap-2 justify-content-end">
            <a class="btn btn-light" href="ver_facturas.php">Cancelar</a>
            <button class="btn btn-primary" type="submit">Guardar factura</button>
        </div>
    </form>

    <script>
        // Solo muestra subtotales y total mientras se escribe. El servidor vuelve a calcular todo al guardar.
        (function () {
            var contenedor = document.getElementById('lineas');
            var plantilla = contenedor.firstElementChild.cloneNode(true);
            var formato = new Intl.NumberFormat('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            function recalcular() {
                var total = 0;
                contenedor.querySelectorAll('.linea-producto').forEach(function (linea) {
                    var opcion = linea.querySelector('select').selectedOptions[0];
                    var cantidad = parseInt(linea.querySelector('input').value, 10);
                    var celda = linea.querySelector('.subtotal');
                    if (opcion && opcion.dataset.precio && cantidad > 0) {
                        var subtotal = Math.round(cantidad * parseFloat(opcion.dataset.precio) * 100) / 100;
                        total += subtotal;
                        celda.textContent = '$ ' + formato.format(subtotal);
                    } else {
                        celda.textContent = '—';
                    }
                });
                document.getElementById('total').textContent = '$ ' + formato.format(total);
            }

            document.getElementById('agregar-linea').addEventListener('click', function () {
                var nueva = plantilla.cloneNode(true);
                nueva.querySelector('select').value = '';
                nueva.querySelector('input').value = '';
                contenedor.appendChild(nueva);
                recalcular();
            });
            contenedor.addEventListener('click', function (e) {
                var boton = e.target.closest('[data-quitar]');
                if (!boton) return;
                if (contenedor.children.length > 1) boton.closest('.linea-producto').remove();
                recalcular();
            });
            contenedor.addEventListener('input', recalcular);
            contenedor.addEventListener('change', recalcular);
            recalcular();
        })();
    </script>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
