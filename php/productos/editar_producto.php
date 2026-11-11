<?php
/*
 * ============================================================================
 * MODULO  PRODUCTOS E INVENTARIO
 * ============================================================================
 * Mantiene el catálogo con nombre, unidad, existencias y precio. Las consultas
 * permiten revisar disponibilidad; las operaciones validan los datos y protegen
 * productos que ya forman parte de una venta.
 *
 * RESPONSABILIDAD DE ESTE ARCHIVO
 * Actualiza los datos vigentes del catálogo sin reescribir las ventas históricas.
 * ============================================================================
 */

/**
 * Archivo: editar_producto.php
 * Módulo: Productos — edición.
 * Propósito: cargar y actualizar los datos vigentes de un producto.
 * Funcionamiento: valida el ID de GET, carga el artículo y procesa el POST con CSRF y validación
 * de nombre, unidad, existencias y precio antes de ejecutar UPDATE.
 * Puntos clave: stock representa la nueva cantidad total. Todos los campos se pueden cambiar aunque el
 * producto tenga facturas: cada factura guarda su propia copia del nombre, la unidad y el precio.
 */

require __DIR__ . '/../config/conexion.php';

$titulo = 'Editar producto';
$seccionActiva = 'productos';
$mensajeExito = '';
$mensajeError = '';
$tokenCsrf = obtenerTokenCsrf();

$idProducto = validarId($_GET['id'] ?? '');

if ($idProducto === null) {
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

/*
 * ============================================================================
 * PROCESAMIENTO DEL FORMULARIO
 * Valida la solicitud y sus campos antes de ejecutar la operación; conserva los mensajes y valores que la página necesita presentar.
 * ============================================================================
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreNuevo = leerTexto($_POST, 'nombre');
    $unidadNueva = leerTexto($_POST, 'unidad_medida');
    $stockTexto = leerTexto($_POST, 'stock');
    $precioNuevo = leerTexto($_POST, 'precio_unitario');
    $stockNuevo = validarEntero($stockTexto, 0);

    // Si hay un error, el formulario muestra lo que se escribió.
    $nombreFormulario = $nombreNuevo;
    $unidadFormulario = $unidadNueva;
    $stockFormulario = $stockTexto;
    $precioFormulario = $precioNuevo;

    if (!validarTokenCsrf($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $mensajeError = 'No se pudo validar la solicitud. Actualiza la página e inténtalo de nuevo.';
    } elseif ($nombreNuevo === '' || longitudTexto($nombreNuevo) > 100) {
        $mensajeError = 'El nombre es obligatorio y admite hasta 100 caracteres.';
    } elseif ($unidadNueva === '' || longitudTexto($unidadNueva) > 20) {
        $mensajeError = 'La unidad es obligatoria y admite hasta 20 caracteres.';
    } elseif ($stockNuevo === null) {
        $mensajeError = 'El stock debe ser un entero no negativo.';
    } elseif (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/', $precioNuevo)) {
        $mensajeError = 'El precio debe ser no negativo y tener máximo dos decimales.';
    } else {
        try {
            $actualizar = $conexion->prepare('UPDATE productos SET nombre = ?, unidad_medida = ?, stock = ?, precio_unitario = ? WHERE id_producto = ?');
            $actualizar->bind_param('ssisi', $nombreNuevo, $unidadNueva, $stockNuevo, $precioNuevo, $idProducto);

            if ($actualizar->execute()) {
                $cambios = [];
                if ($nombreNuevo !== $nombreProducto) {
                    $cambios[] = 'nombre «' . $nombreProducto . '» → «' . $nombreNuevo . '»';
                }
                if ($unidadNueva !== $unidadMedida) {
                    $cambios[] = 'unidad ' . $unidadMedida . ' → ' . $unidadNueva;
                }
                if ((int)$stockNuevo !== (int)$stockActual) {
                    $cambios[] = 'stock ' . $stockActual . ' → ' . $stockNuevo;
                }
                if ((float)$precioNuevo !== (float)$precioUnitario) {
                    $cambios[] = 'precio ' . formatearMonedaCop($precioUnitario) . ' → ' . formatearMonedaCop($precioNuevo);
                }
                registrarAccion($conexion, 'Productos', 'Editar', 'Producto #' . $idProducto . ': '
                    . ($cambios === [] ? 'guardado sin cambios' : implode('; ', $cambios)) . '.');
                $nombreProducto = $nombreNuevo;
                $unidadMedida = $unidadNueva;
                $stockActual = $stockNuevo;
                $precioUnitario = $precioNuevo;
                $nombreFormulario = $nombreNuevo;
                $unidadFormulario = $unidadNueva;
                $stockFormulario = $stockNuevo;
                $precioFormulario = $precioNuevo;
                $mensajeExito = 'Producto actualizado correctamente.';
            } else {
                $mensajeError = 'No se pudo actualizar el producto.';
            }
            $actualizar->close();
        } catch (Throwable $e) {
            registrarErrorServidor('editar_producto', $e);
            $mensajeError = 'No se pudo actualizar el producto.';
        }
    }
}

/*
 * ============================================================================
 * PRESENTACION DEL MODULO
 * Con los datos y mensajes preparados, carga la navegación compartida y presenta el contenido de esta pantalla.
 * ============================================================================
 */
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
                    <span class="ayuda texto-ayuda">Las facturas anteriores conservan la unidad con la que se vendieron.</span>
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
