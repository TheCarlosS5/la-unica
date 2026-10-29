<?php
/**
 * Archivo: editar_producto.php
 * Módulo: Productos — edición.
 * Propósito: cargar y actualizar los datos vigentes de un producto de forma segura y coordinada.
 * Funcionamiento: valida el ID de GET, carga el artículo y procesa el POST con CSRF y validación
 * de nombre, unidad, existencias y precio. La modificación se realiza dentro de una transacción MySQL
 * con bloqueo de fila (FOR UPDATE) para serializar operaciones con ventas y cancelaciones concurrentes.
 * Detecta si el stock enviado desde el formulario está desactualizado ante movimientos recientes del inventario,
 * preserva las existencias vigentes cuando únicamente se actualizan nombre o precio, y coordina la validación
 * del historial de facturación con los cambios en la unidad de medida para mantener la integridad de los datos.
 * Puntos clave:
 * - Uso de begin_transaction() y SELECT ... FOR UPDATE sobre el producto para evitar condiciones de carrera.
 * - Coordinación de la comprobación en detalle_facturas antes de modificar unidad_medida.
 * - Detección de stock desactualizado y preservación atómica de existencias modificadas por ventas/cancelaciones.
 */

require __DIR__ . '/../config/conexion.php';

$titulo = 'Editar producto';
$seccionActiva = 'productos';
$mensajeExito = '';
$mensajeError = '';
$tokenCsrf = obtenerTokenCsrf();

$idRecibido = $_GET['id'] ?? null;
$idProducto = validarId($idRecibido);

if ($idRecibido === null || $idProducto === null) {
    http_response_code(400);
    exit('ID de producto no válido.');
}

try {
    $sentencia = $conexion->prepare('SELECT nombre, unidad_medida, stock, precio_unitario FROM productos WHERE id_producto = ?');
    $sentencia->bind_param('i', $idProducto);
    $sentencia->execute();
    $sentencia->bind_result($nombreProducto, $unidadMedida, $stockActual, $precioUnitario);
    $existe = $sentencia->fetch();
    $sentencia->close();
} catch (Throwable $e) {
    registrarErrorServidor('leer_producto_editar', $e);
    http_response_code(500);
    exit('No se pudo cargar la información del producto. Inténtalo de nuevo más tarde.');
}

if (!$existe) {
    http_response_code(404);
    exit('No se encontró el producto.');
}

$nombreFormulario = $nombreProducto;
$unidadFormulario = $unidadMedida;
$stockFormulario = $stockActual;
$precioFormulario = $precioUnitario;
$stockOriginalFormulario = (string)$stockActual;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    if (sesionDisponible()) {
        if (!isset($_SESSION['edicion_productos']) || !is_array($_SESSION['edicion_productos'])) {
            $_SESSION['edicion_productos'] = [];
        }
        $_SESSION['edicion_productos'][$idProducto] = [
            'stock' => (int)$stockActual,
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreRecibido = $_POST['nombre'] ?? null;
    $unidadRecibida = $_POST['unidad_medida'] ?? null;
    $stockRecibido = $_POST['stock'] ?? null;
    $precioRecibido = $_POST['precio_unitario'] ?? null;
    $stockOriginalRecibido = $_POST['stock_original'] ?? $_POST['stock_previo'] ?? $_POST['stock_anterior'] ?? null;

    $datosPostValidos = is_string($nombreRecibido)
        && is_string($unidadRecibida)
        && is_string($stockRecibido)
        && is_string($precioRecibido)
        && ($stockOriginalRecibido === null || is_string($stockOriginalRecibido));

    $nombreFormulario = is_string($nombreRecibido) ? $nombreRecibido : '';
    $unidadFormulario = is_string($unidadRecibida) ? $unidadRecibida : '';
    $stockFormulario = is_string($stockRecibido) ? $stockRecibido : '';
    $precioFormulario = is_string($precioRecibido) ? $precioRecibido : '';
    $stockOriginalFormulario = is_string($stockOriginalRecibido) ? $stockOriginalRecibido : (string)$stockActual;

    $nombreNuevo = is_string($nombreRecibido) ? trim($nombreRecibido) : '';
    $unidadNueva = is_string($unidadRecibida) ? trim($unidadRecibida) : '';
    $stockTexto = is_string($stockRecibido) ? trim($stockRecibido) : '';
    $precioNuevo = is_string($precioRecibido) ? trim($precioRecibido) : '';

    $stockNuevo = filter_var($stockTexto, FILTER_VALIDATE_INT);

    $stockOriginal = null;
    if (is_string($stockOriginalRecibido) && trim($stockOriginalRecibido) !== '') {
        $stockOriginalValidado = filter_var(trim($stockOriginalRecibido), FILTER_VALIDATE_INT);
        if ($stockOriginalValidado !== false && $stockOriginalValidado >= 0 && $stockOriginalValidado <= 2147483647) {
            $stockOriginal = $stockOriginalValidado;
        }
    } elseif (sesionDisponible() && isset($_SESSION['edicion_productos'][$idProducto]['stock'])) {
        $stockOriginalEnSesion = filter_var($_SESSION['edicion_productos'][$idProducto]['stock'], FILTER_VALIDATE_INT);
        if ($stockOriginalEnSesion !== false && $stockOriginalEnSesion >= 0 && $stockOriginalEnSesion <= 2147483647) {
            $stockOriginal = $stockOriginalEnSesion;
        }
    }

    if (!validarTokenCsrf($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $mensajeError = 'No se pudo validar la solicitud. Actualiza la página e inténtalo de nuevo.';
    } elseif (!$datosPostValidos) {
        $mensajeError = 'Los datos enviados no tienen el formato esperado.';
    } elseif ($nombreNuevo === '' || longitudTexto($nombreNuevo) > 100) {
        $mensajeError = 'El nombre es obligatorio y admite hasta 100 caracteres.';
    } elseif ($unidadNueva === '' || longitudTexto($unidadNueva) > 20) {
        $mensajeError = 'La unidad es obligatoria y admite hasta 20 caracteres.';
    } elseif ($stockTexto === '' || $stockNuevo === false || $stockNuevo < 0 || $stockNuevo > 2147483647) {
        $mensajeError = 'El stock debe ser un entero no negativo.';
    } elseif ($precioNuevo === '' || !preg_match('/^\d{1,8}(?:\.\d{1,2})?$/', $precioNuevo)) {
        $mensajeError = 'El precio debe ser no negativo y tener máximo dos decimales.';
    } else {
        $transaccionIniciada = false;
        try {
            $conexion->begin_transaction();
            $transaccionIniciada = true;

            // Bloquear la fila del producto para coordinar ventas, cancelaciones y ediciones simultáneas
            $consultaProducto = $conexion->prepare('SELECT nombre, unidad_medida, stock, precio_unitario FROM productos WHERE id_producto = ? FOR UPDATE');
            $consultaProducto->bind_param('i', $idProducto);
            $consultaProducto->execute();
            $consultaProducto->bind_result($nombreActualBd, $unidadActualBd, $stockActualBd, $precioActualBd);
            $existeProducto = $consultaProducto->fetch();
            $consultaProducto->close();

            if (!$existeProducto) {
                throw new DomainException('No se encontró el producto.');
            }

            // Coordinar validación del historial de facturación con la modificación de unidad de medida
            if ($unidadNueva !== $unidadActualBd) {
                $checkDetalles = $conexion->prepare('SELECT COUNT(*) FROM detalle_facturas WHERE id_producto = ? FOR UPDATE');
                $checkDetalles->bind_param('i', $idProducto);
                $checkDetalles->execute();
                $checkDetalles->bind_result($totalDetalles);
                $checkDetalles->fetch();
                $checkDetalles->close();

                if ($totalDetalles > 0) {
                    throw new DomainException('No se puede cambiar la unidad: el producto ya aparece en facturas.');
                }
            }

            // Coordinar gestión de inventario: detectar stock desactualizado y preservar movimientos recientes
            $cambioDatosNoStock = ($nombreNuevo !== $nombreActualBd
                || number_format((float)$precioNuevo, 2, '.', '') !== number_format((float)$precioActualBd, 2, '.', '')
                || $unidadNueva !== $unidadActualBd);

            if ($stockOriginal !== null) {
                $usuarioModificoStock = ($stockNuevo !== $stockOriginal);
                $inventarioCambioEnBd = ($stockActualBd !== $stockOriginal);

                if ($usuarioModificoStock && $inventarioCambioEnBd && $stockNuevo !== $stockActualBd) {
                    throw new DomainException('El stock enviado está desactualizado debido a movimientos recientes de inventario. Actualiza la página para consultar las existencias vigentes.');
                }

                if (!$usuarioModificoStock) {
                    if ($inventarioCambioEnBd && !$cambioDatosNoStock) {
                        // El stock del formulario difiere del vigente en BD y no se modificaron otros datos
                        throw new DomainException('El stock enviado está desactualizado debido a movimientos recientes de inventario. Actualiza la página para consultar las existencias vigentes.');
                    }
                    // Si el usuario no modificó el stock pero sí nombre, precio o unidad,
                    // se conserva el stock actual de la BD para no sobrescribir ventas o cancelaciones simultáneas.
                    $stockFinal = $stockActualBd;
                } else {
                    $stockFinal = $stockNuevo;
                }
            } else {
                $stockFinal = $stockNuevo;
            }

            $actualizar = $conexion->prepare('UPDATE productos SET nombre = ?, unidad_medida = ?, stock = ?, precio_unitario = ? WHERE id_producto = ?');
            $actualizar->bind_param('ssisi', $nombreNuevo, $unidadNueva, $stockFinal, $precioNuevo, $idProducto);
            $actualizar->execute();
            $actualizar->close();

            $conexion->commit();
            $transaccionIniciada = false;

            $nombreProducto = $nombreNuevo;
            $unidadMedida = $unidadNueva;
            $stockActual = $stockFinal;
            $precioUnitario = $precioNuevo;

            $nombreFormulario = $nombreNuevo;
            $unidadFormulario = $unidadNueva;
            $stockFormulario = $stockFinal;
            $precioFormulario = $precioNuevo;
            $stockOriginalFormulario = (string)$stockFinal;

            if (sesionDisponible()) {
                if (!isset($_SESSION['edicion_productos']) || !is_array($_SESSION['edicion_productos'])) {
                    $_SESSION['edicion_productos'] = [];
                }
                $_SESSION['edicion_productos'][$idProducto] = [
                    'stock' => (int)$stockFinal,
                ];
            }

            $mensajeExito = 'Producto actualizado correctamente.';
        } catch (DomainException $e) {
            if ($transaccionIniciada) {
                try {
                    $conexion->rollback();
                } catch (Throwable $errorRollback) {
                    registrarErrorServidor('editar_producto_rollback', $errorRollback);
                }
            }
            $mensajeError = $e->getMessage();

            try {
                $consultaReleer = $conexion->prepare('SELECT nombre, unidad_medida, stock, precio_unitario FROM productos WHERE id_producto = ?');
                $consultaReleer->bind_param('i', $idProducto);
                $consultaReleer->execute();
                $consultaReleer->bind_result($nombreReleido, $unidadReleida, $stockReleido, $precioReleido);
                if ($consultaReleer->fetch()) {
                    $stockActual = $stockReleido;
                    $stockOriginalFormulario = (string)$stockReleido;
                    $stockFormulario = (string)$stockReleido;
                    if (sesionDisponible()) {
                        if (!isset($_SESSION['edicion_productos']) || !is_array($_SESSION['edicion_productos'])) {
                            $_SESSION['edicion_productos'] = [];
                        }
                        $_SESSION['edicion_productos'][$idProducto] = [
                            'stock' => (int)$stockReleido,
                        ];
                    }
                }
                $consultaReleer->close();
            } catch (Throwable $eReleer) {
            }
        } catch (Throwable $e) {
            if ($transaccionIniciada) {
                try {
                    $conexion->rollback();
                } catch (Throwable $errorRollback) {
                    registrarErrorServidor('editar_producto_rollback', $errorRollback);
                }
            }
            registrarErrorServidor('editar_producto', $e);
            $mensajeError = 'No se pudo actualizar el producto.';
        }
    }
}

require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <a href="ver_productos.php">Productos</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Editar producto #<?= h($idProducto) ?></span>
        </nav>
        <h1 class="encabezado-titulo">Editar producto</h1>
        <p class="encabezado-descripcion">Modifica el catálogo de artículos, existencias disponibles y precio unitario.</p>
    </div>
</div>

<div class="panel-formulario-wrap">
    <div class="formulario tarjeta-formulario">
        <div class="tarjeta-id-fijo">
            <span class="etiqueta">Identificador fijo</span>
            <span class="valor">#<?= h($idProducto) ?></span>
        </div>

        <?php if ($mensajeExito !== ''): ?>
            <div class="mensaje alerta" role="status">
                <svg class="alerta-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                <div class="alerta-contenido"><?= h($mensajeExito) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($mensajeError !== ''): ?>
            <div class="mensaje error alerta alerta-error" role="alert">
                <svg class="alerta-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <div class="alerta-contenido"><?= h($mensajeError) ?></div>
            </div>
        <?php endif; ?>

        <form method="post">
            <?php if ($tokenCsrf !== null): ?>
                <input type="hidden" name="csrf_token" value="<?= h($tokenCsrf) ?>">
            <?php endif; ?>
            <input type="hidden" name="stock_original" value="<?= h($stockOriginalFormulario) ?>">
            <div class="formulario-grid">
                <div class="campo col-completa">
                    <label for="nombre">
                        <span>Nombre del producto</span>
                        <span class="ayuda" style="margin:0;">Hasta 100 caracteres</span>
                    </label>
                    <input id="nombre" name="nombre" type="text" maxlength="100" value="<?= h($nombreFormulario) ?>" required>
                </div>

                <div class="campo">
                    <label for="unidad_medida">
                        <span>Unidad de medida</span>
                        <span class="ayuda" style="margin:0;">Hasta 20 car.</span>
                    </label>
                    <input id="unidad_medida" name="unidad_medida" type="text" maxlength="20" value="<?= h($unidadFormulario) ?>" required>
                    <span class="ayuda texto-ayuda">Nota: No se puede modificar si el producto ya cuenta con facturas registradas.</span>
                </div>

                <div class="campo">
                    <label for="stock">
                        <span>Stock disponible</span>
                        <span class="ayuda" style="margin:0;">Entero ≥ 0</span>
                    </label>
                    <input id="stock" name="stock" type="number" min="0" max="2147483647" step="1" value="<?= h($stockFormulario) ?>" required>
                    <span class="ayuda texto-ayuda">Unidades en inventario.</span>
                </div>

                <div class="campo col-completa">
                    <label for="precio_unitario">
                        <span>Precio unitario ($)</span>
                        <span class="ayuda" style="margin:0;">Hasta 2 decimales</span>
                    </label>
                    <input id="precio_unitario" name="precio_unitario" type="number" min="0" max="99999999.99" step="0.01" value="<?= h($precioFormulario) ?>" required>
                    <span class="ayuda texto-ayuda">Valor unitario de venta.</span>
                </div>
            </div>

            <div class="fila-botones">
                <button type="submit">
                    <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                        <polyline points="7 3 7 8 15 8" />
                    </svg>
                    <span>Guardar cambios</span>
                </button>
                <a class="boton secundario" href="ver_productos.php">
                    <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="19" y1="12" x2="5" y2="12" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                    <span>Volver</span>
                </a>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
