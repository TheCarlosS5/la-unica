<?php
require __DIR__ . '/../config/conexion.php';

$titulo = 'Crear cliente';
$seccionActiva = 'clientes';
$mensajeExito = '';
$mensajeError = '';

$idCliente = '';
$nombreCliente = '';
$fechaRegistro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idRecibido = $_POST['id_cliente'] ?? null;
    $nombreRecibido = $_POST['nombre'] ?? null;
    $fechaRecibida = $_POST['fecha_registro'] ?? null;

    $idClienteValido = validarId($idRecibido);
    $nombreLimpio = ($nombreRecibido !== null) ? trim((string)$nombreRecibido) : '';
    $fechaLimpia = ($fechaRecibida !== null) ? trim((string)$fechaRecibida) : '';

    $idCliente = ($idRecibido !== null) ? trim((string)$idRecibido) : '';
    $nombreCliente = $nombreLimpio;
    $fechaRegistro = $fechaLimpia;

    if ($idRecibido === null || trim((string)$idRecibido) === '' || $idClienteValido === null) {
        $mensajeError = 'El ID debe ser un entero entre 1 y 2.147.483.647.';
    } elseif ($nombreLimpio === '' || longitudTexto($nombreLimpio) > 100) {
        $mensajeError = 'El nombre es obligatorio y admite hasta 100 caracteres.';
    } elseif ($fechaLimpia !== '' && !validarFecha($fechaLimpia)) {
        $mensajeError = 'La fecha no es válida.';
    } else {
        try {
            if ($fechaLimpia === '') {
                $sentencia = $conexion->prepare('INSERT INTO clientes (id_cliente, nombre) VALUES (?, ?)');
                $sentencia->bind_param('is', $idClienteValido, $nombreLimpio);
            } else {
                $sentencia = $conexion->prepare('INSERT INTO clientes (id_cliente, nombre, fecha_registro) VALUES (?, ?, ?)');
                $sentencia->bind_param('iss', $idClienteValido, $nombreLimpio, $fechaLimpia);
            }

            if ($sentencia && $sentencia->execute()) {
                $mensajeExito = 'Cliente creado correctamente.';
                $idCliente = '';
                $nombreCliente = '';
                $fechaRegistro = '';
            } else {
                $mensajeError = 'No se pudo crear. Revisa que ese ID no esté registrado.';
            }
        } catch (Throwable $e) {
            $mensajeError = 'No se pudo crear. Revisa que ese ID no esté registrado.';
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
            <span>Nuevo cliente</span>
        </nav>
        <h1 class="encabezado-titulo">Crear cliente</h1>
        <p class="encabezado-descripcion">Registra un nuevo comprador asignando su identificador numérico y fecha de alta comercial.</p>
    </div>
</div>

<div class="panel-formulario-wrap">
    <div class="formulario tarjeta-formulario">
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
            <div class="campo">
                <label for="id_cliente">
                    <span>ID del cliente</span>
                    <span class="ayuda" style="margin:0;">Requerido</span>
                </label>
                <input id="id_cliente" name="id_cliente" type="number" min="1" max="2147483647" value="<?= h($idCliente) ?>" placeholder="Ej. 101" required>
                <span class="ayuda texto-ayuda">Se ingresa manualmente, según el esquema de la base de datos (clave primaria única).</span>
            </div>

            <div class="campo">
                <label for="nombre">
                    <span>Nombre completo</span>
                    <span class="ayuda" style="margin:0;">Hasta 100 caracteres</span>
                </label>
                <input id="nombre" name="nombre" type="text" maxlength="100" value="<?= h($nombreCliente) ?>" placeholder="Ej. Carlos Ramirez" required>
            </div>

            <div class="campo">
                <label for="fecha_registro">
                    <span>Fecha de registro</span>
                    <span class="ayuda" style="margin:0;">Opcional</span>
                </label>
                <input id="fecha_registro" name="fecha_registro" type="date" value="<?= h($fechaRegistro) ?>">
                <span class="ayuda texto-ayuda">Déjala vacía para asignar automáticamente la fecha actual del sistema.</span>
            </div>

            <div class="fila-botones">
                <button type="submit">
                    <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                        <polyline points="7 3 7 8 15 8" />
                    </svg>
                    <span>Crear cliente</span>
                </button>
                <a class="boton secundario" href="ver_clientes.php">
                    <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="19" y1="12" x2="5" y2="12" />
                        <polyline points="12 19 5 12 12 5" />
                    </svg>
                    <span>Ver clientes</span>
                </a>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
