<?php
/**
 * Archivo: crear_factura.php
 * Módulo: Facturas — registro de ventas.
 * Propósito: crear una factura con cliente y líneas de productos, y actualizar el inventario.
 * Funcionamiento: valida CSRF, cliente, productos y cantidades; agrega cantidades repetidas por
 * producto; abre una transacción, bloquea las filas implicadas, valida stock, inserta factura/detalles,
 * descuenta existencias, calcula el total vigente y confirma todo en conjunto.
 * Puntos clave:
 * - Se admiten de 1 a 100 líneas y cantidades enteras positivas.
 * - Los productos se bloquean por ID ordenado para reducir conflictos de concurrencia.
 * - Cualquier error revierte factura, detalles e inventario; el éxito usa sesión flash y redirección 303.
 * - La aplicación marca la factura como Pagado; no procesa una transacción bancaria.
 */

require __DIR__ . '/../config/conexion.php';

$titulo = 'Nueva factura';
$seccionActiva = 'facturas';
$tokenCsrf = obtenerTokenCsrf();
$mensajeError = '';
$mensajeExito = '';
$clienteSeleccionado = '';
$lineasFormulario = [['id_producto' => '', 'cantidad' => '1']];
$clientes = [];
$productos = [];

if (sesionDisponible() && isset($_SESSION['factura_creada']) && is_array($_SESSION['factura_creada'])) {
    $facturaCreada = $_SESSION['factura_creada'];
    unset($_SESSION['factura_creada']);
    $mensajeExito = 'Factura #' . (int)($facturaCreada['id'] ?? 0)
        . ' registrada como Pagado. Total: '
        . formatearMonedaCop($facturaCreada['total'] ?? '0.00') . '.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteEntrada = $_POST['id_cliente'] ?? null;
    $productosEntrada = $_POST['id_producto'] ?? null;
    $cantidadesEntrada = $_POST['cantidad'] ?? null;
    $clienteSeleccionado = is_string($clienteEntrada) ? $clienteEntrada : '';
    $lineasEntradaValidas = is_array($productosEntrada)
        && is_array($cantidadesEntrada)
        && count($productosEntrada) === count($cantidadesEntrada)
        && count($productosEntrada) >= 1
        && count($productosEntrada) <= 100;

    $lineasFormulario = [];
    if ($lineasEntradaValidas) {
        foreach ($productosEntrada as $indice => $productoEntrada) {
            $cantidadEntrada = $cantidadesEntrada[$indice] ?? null;
            $lineasFormulario[] = [
                'id_producto' => is_string($productoEntrada) ? $productoEntrada : '',
                'cantidad' => is_string($cantidadEntrada) ? $cantidadEntrada : '',
            ];
        }
    } else {
        $lineasFormulario = [['id_producto' => '', 'cantidad' => '1']];
    }

    $lineasVenta = [];
    $cantidadesPorProducto = [];
    $idClienteValido = validarId($clienteSeleccionado);

    if (!validarTokenCsrf($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $mensajeError = 'No se pudo validar la solicitud. Actualiza la página e inténtalo de nuevo.';
    } elseif (!is_string($clienteEntrada) || $idClienteValido === null) {
        $mensajeError = 'Selecciona un cliente válido.';
    } elseif (!$lineasEntradaValidas) {
        $mensajeError = 'Agrega entre 1 y 100 productos con sus cantidades.';
    } else {
        foreach ($productosEntrada as $indice => $productoEntrada) {
            $cantidadEntrada = $cantidadesEntrada[$indice] ?? null;
            $idProducto = is_string($productoEntrada) ? validarId($productoEntrada) : null;
            $cantidadTexto = is_string($cantidadEntrada) ? $cantidadEntrada : '';

            if ($idProducto === null || !preg_match('/^[1-9][0-9]*$/D', $cantidadTexto)) {
                $mensajeError = 'Cada línea debe tener un producto válido y una cantidad entera mayor que cero.';
                break;
            }

            $cantidad = filter_var($cantidadTexto, FILTER_VALIDATE_INT);
            if ($cantidad === false || $cantidad > 2147483647) {
                $mensajeError = 'La cantidad debe ser un entero entre 1 y 2.147.483.647.';
                break;
            }

            $lineasVenta[] = ['id_producto' => $idProducto, 'cantidad' => $cantidad];
            $cantidadAcumulada = ($cantidadesPorProducto[$idProducto] ?? 0) + $cantidad;
            if ($cantidadAcumulada > 2147483647) {
                $mensajeError = 'La cantidad acumulada de un producto supera el límite permitido.';
                break;
            }
            $cantidadesPorProducto[$idProducto] = $cantidadAcumulada;
        }
    }

    if ($mensajeError === '' && $tokenCsrf === null) {
        http_response_code(503);
        $mensajeError = 'No fue posible iniciar la sesión de seguridad. Recarga la página e inténtalo de nuevo.';
    }

    if ($mensajeError === '') {
        $transaccionIniciada = false;
        try {
            // Factura, detalles y descuentos se confirman juntos o se revierten juntos.
            $conexion->begin_transaction();
            $transaccionIniciada = true;

            $consultaCliente = $conexion->prepare('SELECT id_cliente FROM clientes WHERE id_cliente = ? FOR UPDATE');
            $consultaCliente->bind_param('i', $idClienteValido);
            $consultaCliente->execute();
            $clienteExiste = $consultaCliente->get_result()->fetch_assoc() !== null;
            $consultaCliente->close();
            if (!$clienteExiste) {
                throw new DomainException('El cliente seleccionado ya no existe. Actualiza la página y vuelve a seleccionarlo.');
            }

            // Bloquear productos siempre en orden de ID reduce interbloqueos entre ventas concurrentes.
            $idsOrdenados = array_keys($cantidadesPorProducto);
            sort($idsOrdenados, SORT_NUMERIC);
            $consultaProducto = $conexion->prepare('SELECT stock FROM productos WHERE id_producto = ? FOR UPDATE');
            foreach ($idsOrdenados as $idProducto) {
                $consultaProducto->bind_param('i', $idProducto);
                $consultaProducto->execute();
                $productoEncontrado = $consultaProducto->get_result()->fetch_assoc();
                if ($productoEncontrado === null) {
                    throw new DomainException('Uno de los productos seleccionados ya no existe. Actualiza la página.');
                }
                if ((int)$productoEncontrado['stock'] < $cantidadesPorProducto[$idProducto]) {
                    throw new DomainException('Las existencias cambiaron y no alcanzan para completar la venta. Revisa las cantidades e inténtalo de nuevo.');
                }
            }
            $consultaProducto->close();

            $crearFactura = $conexion->prepare("INSERT INTO facturas (id_cliente, estado_factura) VALUES (?, 'Pagado')");
            $crearFactura->bind_param('i', $idClienteValido);
            $crearFactura->execute();
            $idFactura = (int)$conexion->insert_id;
            $crearFactura->close();

            $insertarDetalle = $conexion->prepare('INSERT INTO detalle_facturas (id_factura, id_producto, cantidad) VALUES (?, ?, ?)');
            $descontarStock = $conexion->prepare('UPDATE productos SET stock = stock - ? WHERE id_producto = ? AND stock >= ?');
            foreach ($cantidadesPorProducto as $idProducto => $cantidad) {
                $insertarDetalle->bind_param('iii', $idFactura, $idProducto, $cantidad);
                $insertarDetalle->execute();

                $descontarStock->bind_param('iii', $cantidad, $idProducto, $cantidad);
                $descontarStock->execute();
                if ($descontarStock->affected_rows !== 1) {
                    throw new DomainException('Las existencias cambiaron durante la venta. No se guardó la factura; inténtalo de nuevo.');
                }
            }
            $insertarDetalle->close();
            $descontarStock->close();

            // El total se lee de la vista para mantener la misma regla de precio vigente que los reportes.
            $consultaTotal = $conexion->prepare('SELECT total_factura FROM vista_total_facturas WHERE id_factura = ?');
            $consultaTotal->bind_param('i', $idFactura);
            $consultaTotal->execute();
            $filaTotal = $consultaTotal->get_result()->fetch_assoc();
            $consultaTotal->close();
            if ($filaTotal === null || $filaTotal['total_factura'] === null) {
                throw new RuntimeException('La factura no produjo un total calculable.');
            }

            $totalFactura = (string)$filaTotal['total_factura'];
            $conexion->commit();
            $transaccionIniciada = false;

            $_SESSION['factura_creada'] = [
                'id' => $idFactura,
                'total' => $totalFactura,
                'productos' => count($cantidadesPorProducto),
            ];
            header('Location: crear_factura.php', true, 303);
            exit;
        } catch (DomainException $e) {
            if ($transaccionIniciada) {
                try {
                    $conexion->rollback();
                } catch (Throwable $errorRollback) {
                    registrarErrorServidor('crear_factura_rollback', $errorRollback);
                }
            }
            http_response_code(409);
            $mensajeError = $e->getMessage();
        } catch (Throwable $e) {
            if ($transaccionIniciada) {
                try {
                    $conexion->rollback();
                } catch (Throwable $errorRollback) {
                    registrarErrorServidor('crear_factura_rollback', $errorRollback);
                }
            }
            registrarErrorServidor('crear_factura', $e);
            $mensajeError = 'No se pudo registrar la factura. No se aplicaron cambios; verifica la conexión e inténtalo de nuevo.';
        }
    }
}

try {
    $resultadoClientes = $conexion->query('SELECT id_cliente, nombre FROM clientes ORDER BY nombre, id_cliente');
    $clientes = $resultadoClientes->fetch_all(MYSQLI_ASSOC);
    $resultadoClientes->free();

    $resultadoProductos = $conexion->query('SELECT id_producto, nombre, unidad_medida, stock, precio_unitario FROM productos ORDER BY nombre, id_producto');
    $productos = $resultadoProductos->fetch_all(MYSQLI_ASSOC);
    $resultadoProductos->free();
} catch (Throwable $e) {
    registrarErrorServidor('cargar_catalogos_factura', $e);
    $mensajeError = 'No se pudieron cargar los clientes y productos. Recarga la página o verifica la conexión.';
    $clientes = [];
    $productos = [];
}

$opcionesProductos = '<option value="">Selecciona un producto</option>';
$productosDisponibles = 0;
foreach ($productos as $producto) {
    $idProducto = (int)$producto['id_producto'];
    $stockProducto = (int)$producto['stock'];
    if ($stockProducto > 0) {
        $productosDisponibles++;
    }
    $etiqueta = $producto['nombre'] . ' · ' . $producto['unidad_medida'] . ' · '
        . formatearMonedaCop($producto['precio_unitario']) . ' · stock: ' . $stockProducto;
    $opcionesProductos .= '<option value="' . $idProducto . '" data-stock="' . $stockProducto
        . '" data-precio="' . h((string)$producto['precio_unitario']) . '"'
        . ($stockProducto < 1 ? ' disabled' : '') . '>' . h($etiqueta) . '</option>';
}

require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Nueva factura</span>
        </nav>
        <h1 class="encabezado-titulo">Registrar una venta</h1>
        <p class="encabezado-descripcion">Selecciona el cliente y agrega los productos. El inventario se valida y actualiza al guardar la factura.</p>
    </div>
</div>

<div class="panel-formulario-wrap">
    <section class="formulario tarjeta-formulario factura-formulario" aria-labelledby="titulo-formulario-factura">
        <h2 id="titulo-formulario-factura" class="titulo-seccion">Datos de la venta</h2>

        <?php if ($mensajeExito !== ''): ?>
            <div class="mensaje alerta" role="status">
                <svg class="alerta-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" /><polyline points="22 4 12 14.01 9 11.01" /></svg>
                <div class="alerta-contenido"><?= h($mensajeExito) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($mensajeError !== ''): ?>
            <div class="mensaje error alerta alerta-error" role="alert">
                <svg class="alerta-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></svg>
                <div class="alerta-contenido"><?= h($mensajeError) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($tokenCsrf === null): ?>
            <div class="mensaje aviso alerta alerta-aviso" role="status"><div class="alerta-contenido">La sesión de seguridad no está disponible. Recarga la página antes de registrar una venta.</div></div>
        <?php elseif (count($clientes) === 0): ?>
            <div class="estado-vacio" role="status"><h2>No hay clientes registrados</h2><p>Registra un cliente antes de crear una factura.</p><a class="boton" href="<?= h($baseUrl) ?>/php/clientes/crear_cliente.php">Crear cliente</a></div>
        <?php elseif (count($productos) === 0): ?>
            <div class="estado-vacio" role="status"><h2>No hay productos registrados</h2><p>Registra productos antes de crear una factura.</p><a class="boton" href="<?= h($baseUrl) ?>/php/productos/crear_producto.php">Crear producto</a></div>
        <?php elseif ($productosDisponibles === 0): ?>
            <div class="mensaje aviso alerta alerta-aviso" role="status"><div class="alerta-contenido">No hay productos con existencias disponibles para vender.</div></div>
        <?php else: ?>
            <form method="post" id="formulario-factura" data-moneda="COP">
                <input type="hidden" name="csrf_token" value="<?= h($tokenCsrf) ?>">

                <div class="campo">
                    <label for="id_cliente"><span>Cliente</span><span class="ayuda" style="margin:0">Requerido</span></label>
                    <select id="id_cliente" name="id_cliente" required>
                        <option value="">Selecciona un cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?= (int)$cliente['id_cliente'] ?>" <?= $clienteSeleccionado === (string)$cliente['id_cliente'] ? 'selected' : '' ?>><?= h($cliente['nombre']) ?> · ID <?= (int)$cliente['id_cliente'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="factura-lineas-cabecera">
                    <div><h3>Productos</h3><p class="ayuda">Cada producto puede agregarse una sola vez; ajusta su cantidad.</p></div>
                    <button class="boton secundario" type="button" id="agregar-linea-factura">Agregar producto</button>
                </div>

                <div id="lineas-factura" class="factura-lineas" aria-live="polite">
                    <?php foreach ($lineasFormulario as $indice => $linea): ?>
                        <div class="factura-linea" data-linea-factura>
                            <div class="campo factura-producto-campo">
                                <label for="id_producto_<?= $indice ?>">Producto</label>
                                <select id="id_producto_<?= $indice ?>" name="id_producto[]" data-producto-select required>
                                    <?php
                                    $opcionesLinea = str_replace('<option value="' . h((string)$linea['id_producto']) . '"', '<option value="' . h((string)$linea['id_producto']) . '" selected', $opcionesProductos);
                                    echo $opcionesLinea;
                                    ?>
                                </select>
                                <span class="ayuda" data-detalle-producto>Selecciona un producto para consultar precio y existencias.</span>
                            </div>
                            <div class="campo factura-cantidad-campo">
                                <label for="cantidad_<?= $indice ?>">Cantidad</label>
                                <input id="cantidad_<?= $indice ?>" name="cantidad[]" type="number" min="1" max="2147483647" step="1" inputmode="numeric" value="<?= h($linea['cantidad']) ?>" data-cantidad required>
                            </div>
                            <button class="boton-peligro factura-quitar-linea" type="button" data-quitar-linea aria-label="Quitar este producto">Quitar</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <template id="plantilla-linea-factura">
                    <div class="factura-linea" data-linea-factura>
                        <div class="campo factura-producto-campo">
                            <label>Producto</label>
                            <select name="id_producto[]" data-producto-select required><?= $opcionesProductos ?></select>
                            <span class="ayuda" data-detalle-producto>Selecciona un producto para consultar precio y existencias.</span>
                        </div>
                        <div class="campo factura-cantidad-campo">
                            <label>Cantidad</label>
                            <input name="cantidad[]" type="number" min="1" max="2147483647" step="1" inputmode="numeric" value="1" data-cantidad required>
                        </div>
                        <button class="boton-peligro factura-quitar-linea" type="button" data-quitar-linea aria-label="Quitar este producto">Quitar</button>
                    </div>
                </template>

                <div class="factura-resumen" aria-live="polite">
                    <span>Total estimado</span>
                    <output id="total-factura" for="lineas-factura">$0,00 COP</output>
                    <p>El total se calcula con el precio vigente de cada producto, según el esquema de esta versión.</p>
                </div>

                <div class="fila-botones">
                    <button type="submit">
                        <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11z" /><path d="M17 21v-8H7v8M7 3v5h8" /></svg>
                        <span>Guardar venta</span>
                    </button>
                    <a class="boton secundario" href="<?= h($baseUrl) ?>/main.php">Volver al inicio</a>
                </div>
            </form>
            <script src="<?= h($baseUrl) ?>/assets/js/facturas.js" defer></script>
        <?php endif; ?>
    </section>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
