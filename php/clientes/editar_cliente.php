<?php
// ===================================================================
// editar_cliente.php  (la "U" de CRUD: Update / Actualizar)
// Carga el cliente por su ID (?id=5), muestra el formulario con sus
// datos y guarda el nombre. El id y la fecha de registro no cambian.
// ===================================================================
require __DIR__ . '/../config/conexion.php';
$titulo = 'Editar cliente';
$seccionActiva = 'clientes';

$mensaje = '';
$error = '';

// 1. Leer el ID de la URL y validarlo.
$id = id_valido($_GET['id'] ?? '');
if ($id === null) {
    http_response_code(400);
    exit('ID de cliente no válido.');
}

// 2. Traer los datos actuales del cliente.
$stmt = $conexion->prepare('SELECT id_cliente, nombre, fecha_registro FROM clientes WHERE id_cliente = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$c = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$c) {
    http_response_code(404);
    exit('No se encontró el cliente.');
}

// 3. Si enviaron el formulario, validar y guardar solo el nombre.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entrada = $c;                              // id y fecha: los que ya estaban guardados
    $entrada['nombre'] = $_POST['nombre'] ?? '';
    [$nuevo, $error] = validar_cliente($entrada);
    $c['nombre'] = $nuevo['nombre'];

    if ($error === '') {
        $up = $conexion->prepare('UPDATE clientes SET nombre = ? WHERE id_cliente = ?');
        $up->bind_param('si', $c['nombre'], $id);

        if ($up->execute()) {
            $mensaje = 'Cliente actualizado correctamente.';
        } else {
            $error = 'No se pudo actualizar el cliente.';
        }
    }
}

$esEdicion = true;
$textoBoton = 'Guardar cambios';
require __DIR__ . '/../includes/menu.php';
?>
<a class="volver" href="ver_clientes.php">← Clientes</a>

<div class="tarjeta formulario aparecer">
    <div class="formulario-cabecera">
        <span class="avatar"><?= h(iniciales($c['nombre'])) ?></span>
        <div>
            <h1 class="h4 mb-1">Editar cliente</h1>
            <p class="text-secondary mb-0">Cliente #<?= h($id) ?></p>
        </div>
    </div>

    <?= alerta($mensaje) ?>
    <?= alerta($error, 'error') ?>

    <?php require __DIR__ . '/../includes/form_cliente.php'; ?>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
