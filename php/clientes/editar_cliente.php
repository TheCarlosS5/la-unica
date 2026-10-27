<?php
require __DIR__ . '/../config/conexion.php';

$titulo = 'Editar cliente';
$seccionActiva = 'clientes';
$mensajeExito = '';
$mensajeError = '';
$tokenCsrf = obtenerTokenCsrf();

$idRecibido = $_GET['id'] ?? null;
$idCliente = validarId($idRecibido);

if ($idRecibido === null || $idCliente === null) {
    http_response_code(400);
    exit('ID de cliente no válido.');
}

try {
    $sentencia = $conexion->prepare('SELECT nombre, fecha_registro FROM clientes WHERE id_cliente = ?');
    $sentencia->bind_param('i', $idCliente);
    $sentencia->execute();
    $sentencia->bind_result($nombreCliente, $fechaRegistro);
    $existe = $sentencia->fetch();
    $sentencia->close();
} catch (Throwable $e) {
    registrarErrorServidor('leer_cliente_editar', $e);
    http_response_code(500);
    exit('No se pudo cargar la información del cliente. Inténtalo de nuevo más tarde.');
}

if (!$existe) {
    http_response_code(404);
    exit('No se encontró el cliente.');
}

$nombreFormulario = $nombreCliente;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreRecibido = $_POST['nombre'] ?? null;
    $datosPostValidos = is_string($nombreRecibido);
    $nombreFormulario = is_string($nombreRecibido) ? $nombreRecibido : '';
    $nombreLimpio = is_string($nombreRecibido) ? trim($nombreRecibido) : '';

    if (!validarTokenCsrf($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $mensajeError = 'No se pudo validar la solicitud. Actualiza la página e inténtalo de nuevo.';
    } elseif (!$datosPostValidos) {
        $mensajeError = 'Los datos enviados no tienen el formato esperado.';
    } elseif ($nombreLimpio === '' || longitudTexto($nombreLimpio) > 100) {
        $mensajeError = 'El nombre es obligatorio y admite hasta 100 caracteres.';
    } else {
        try {
            $actualizar = $conexion->prepare('UPDATE clientes SET nombre = ? WHERE id_cliente = ?');
            $actualizar->bind_param('si', $nombreLimpio, $idCliente);
            if ($actualizar->execute()) {
                $nombreCliente = $nombreLimpio;
                $nombreFormulario = $nombreLimpio;
                $mensajeExito = 'Cliente actualizado correctamente.';
            } else {
                $mensajeError = 'No se pudo actualizar el cliente.';
            }
            $actualizar->close();
        } catch (Throwable $e) {
            registrarErrorServidor('editar_cliente', $e);
            $mensajeError = 'No se pudo actualizar el cliente.';
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
            <a href="ver_clientes.php">Clientes</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Editar cliente #<?= h($idCliente) ?></span>
        </nav>
        <h1 class="encabezado-titulo">Editar cliente</h1>
        <p class="encabezado-descripcion">Actualiza el nombre del cliente y consulta su fecha de registro.</p>
    </div>
</div>

<div class="panel-formulario-wrap">
    <div class="formulario tarjeta-formulario">
        <div class="tarjeta-id-fijo">
            <span class="etiqueta">Identificador fijo</span>
            <span class="valor">#<?= h($idCliente) ?></span>
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
            <div class="campo">
                <label for="nombre">
                    <span>Nombre del cliente</span>
                    <span class="ayuda" style="margin:0;">Obligatorio</span>
                </label>
                <input id="nombre" name="nombre" type="text" maxlength="100" value="<?= h($nombreFormulario) ?>" required>
            </div>

            <div class="campo">
                <label for="fecha_registro">
                    <span>Fecha de registro</span>
                    <span class="ayuda" style="margin:0;">Solo consulta</span>
                </label>
                <input id="fecha_registro" name="fecha_registro" type="date" value="<?= h($fechaRegistro) ?>" readonly aria-readonly="true">
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
                <a class="boton secundario" href="ver_clientes.php">
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
