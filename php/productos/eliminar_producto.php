<?php
/**
 * Archivo: eliminar_producto.php
 * Módulo: Productos — eliminación protegida.
 * Propósito: confirmar la baja de un artículo que no tenga historial de facturación.
 * Funcionamiento: exige POST, valida ID y CSRF, muestra una confirmación y, al confirmarse, consulta
 * detalle_facturas antes de ejecutar DELETE con una sentencia preparada.
 * Puntos clave: los productos referenciados por facturas no se eliminan; se informa el resultado
 * sin exponer errores técnicos al navegador.
 */

require __DIR__ . '/../config/conexion.php';

$titulo = 'Eliminar producto';
$seccionActiva = 'productos';

$mensajeResultado = '';
$esError = false;
$mostrarConfirmacion = false;
$nombreProducto = '';
$tokenCsrf = obtenerTokenCsrf();

$idRecibido = $_POST['id_producto'] ?? null;
$confirmarRecibido = $_POST['confirmar'] ?? null;
$datosPostValidos = is_string($idRecibido)
    && ($confirmarRecibido === null || is_string($confirmarRecibido));
$idProducto = validarId($idRecibido);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(400);
    $mensajeResultado = 'Solicitud no válida.';
    $esError = true;
} elseif (!validarTokenCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    $mensajeResultado = 'No se pudo validar la solicitud. Actualiza la página e inténtalo de nuevo.';
    $esError = true;
} elseif (!$datosPostValidos || $idProducto === null) {
    http_response_code(400);
    $mensajeResultado = 'Solicitud no válida.';
    $esError = true;
} else {
    try {
        $consulta = $conexion->prepare('SELECT nombre FROM productos WHERE id_producto = ?');
        $consulta->bind_param('i', $idProducto);
        $consulta->execute();
        $consulta->bind_result($nombreProducto);
        $existe = $consulta->fetch();
        $consulta->close();

        if (!$existe) {
            http_response_code(404);
            $mensajeResultado = 'No se encontró el producto.';
            $esError = true;
        } else {
            $esConfirmado = ($confirmarRecibido === 'si');

            if (!$esConfirmado) {
                $mostrarConfirmacion = true;
            } else {
                $checkDetalles = $conexion->prepare('SELECT COUNT(*) FROM detalle_facturas WHERE id_producto = ?');
                $checkDetalles->bind_param('i', $idProducto);
                $checkDetalles->execute();
                $checkDetalles->bind_result($totalDetalles);
                $checkDetalles->fetch();
                $checkDetalles->close();

                if ($totalDetalles > 0) {
                    $mensajeResultado = 'No se puede eliminar: el producto aparece en detalles de factura.';
                    $esError = true;
                } else {
                    $eliminar = $conexion->prepare('DELETE FROM productos WHERE id_producto = ?');
                    $eliminar->bind_param('i', $idProducto);
                    $ejecutado = $eliminar->execute();

                    if ($ejecutado && $eliminar->affected_rows === 1) {
                        $mensajeResultado = 'Producto eliminado correctamente.';
                        $esError = false;
                    } else {
                        $mensajeResultado = 'No se encontró el producto o no se pudo eliminar.';
                        $esError = true;
                    }
                    $eliminar->close();
                }
            }
        }
    } catch (Throwable $e) {
        registrarErrorServidor('eliminar_producto', $e);
        $mensajeResultado = 'Error en el servidor al procesar la eliminación.';
        $esError = true;
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
            <span>Baja de producto</span>
        </nav>
        <h1 class="encabezado-titulo"><?= $mostrarConfirmacion ? 'Confirmar eliminación' : 'Estado de la operación' ?></h1>
        <p class="encabezado-descripcion">Protocolo de seguridad para preservación de registros comerciales e integridad referencial.</p>
    </div>
</div>

<?php if ($mostrarConfirmacion): ?>
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
                <h2 id="titulo-confirmacion" style="font-size: 1.15rem; margin: 0 0 4px; color: var(--color-peligro);">Confirmar baja permanente</h2>
                <p style="font-size: 0.88rem; margin: 0; color: var(--color-texto-secundario);">Esta acción no se puede deshacer una vez ejecutada.</p>
            </div>
        </div>

        <div class="panel-confirmacion-cuerpo">
            <p style="margin: 0; font-size: 0.94rem; color: var(--color-texto);">
                ¿Está seguro de que desea eliminar al siguiente producto del inventario?
            </p>

            <dl class="tarjeta-registro-eliminar">
                <dt>ID Producto</dt>
                <dd><span class="chip-id">#<?= h($idProducto) ?></span></dd>
                <dt>Nombre</dt>
                <dd><?= h($nombreProducto) ?></dd>
            </dl>

            <div class="nota nota-informativa" style="margin: 0;">
                <svg class="nota-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
                <span>Validación de relaciones: Si el producto aparece en detalles de factura en la base de datos, la eliminación será rechazada automáticamente.</span>
            </div>
        </div>

        <form method="post" action="eliminar_producto.php">
            <?php if ($tokenCsrf !== null): ?>
                <input type="hidden" name="csrf_token" value="<?= h($tokenCsrf) ?>">
            <?php endif; ?>
            <input type="hidden" name="id_producto" value="<?= h($idProducto) ?>">
            <input type="hidden" name="confirmar" value="si">
            <div class="panel-confirmacion-pie">
                <a class="boton secundario" href="ver_productos.php">Cancelar</a>
                <button type="submit" class="boton-peligro-solido">
                    <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                    </svg>
                    <span>Confirmar eliminación</span>
                </button>
            </div>
        </form>
    </section>
<?php else: ?>
    <div class="panel-formulario-wrap">
        <div class="tarjeta-formulario" style="max-width: 580px; text-align: center;">
            <div class="alerta <?= $esError ? 'mensaje error alerta-error' : 'mensaje alerta' ?>" style="text-align: left;" role="<?= $esError ? 'alert' : 'status' ?>">
                <svg class="alerta-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <?php if ($esError): ?>
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    <?php else: ?>
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    <?php endif; ?>
                </svg>
                <div class="alerta-contenido">
                    <strong><?= $esError ? 'Operación bloqueada' : 'Operación exitosa' ?>:</strong> <?= h($mensajeResultado) ?>
                </div>
            </div>

            <div style="margin-top: 24px;">
                <a class="boton" href="ver_productos.php">
                    <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="19" y1="12" x2="5" y2="12" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                    <span>Volver a productos</span>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
