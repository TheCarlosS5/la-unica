<?php
// ===================================================================
// editar_cliente.php  (la "U" de CRUD: Update / Actualizar)
// Carga el cliente por su ID (?id=5), muestra el formulario con sus
// datos y guarda los cambios.
// Si el cliente ya tiene facturas, el tipo y número de documento no cambian.
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

// 2. Traer los datos actuales y cuántas facturas tiene.
$stmt = $conexion->prepare('SELECT tipo_documento, numero_documento, nombre, telefono, correo, direccion,
                                   (SELECT COUNT(*) FROM factura WHERE id_cliente = clientes.id_cliente) AS facturas
                            FROM clientes WHERE id_cliente = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$c = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$c) {
    http_response_code(404);
    exit('No se encontró el cliente.');
}
$bloquearDoc = (int)$c['facturas'] > 0;

// 3. Si enviaron el formulario, validar y guardar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entrada = $_POST;
    if ($bloquearDoc) {
        // El documento no se puede cambiar: usamos el que ya estaba guardado.
        $entrada['tipo_documento'] = $c['tipo_documento'];
        $entrada['numero_documento'] = $c['numero_documento'];
    }
    [$nuevo, $error] = validar_cliente($entrada);
    $c = array_merge($c, $nuevo);

    if ($error === '') {
        $up = $conexion->prepare('UPDATE clientes SET tipo_documento = ?, numero_documento = ?, nombre = ?, telefono = ?, correo = ?, direccion = ? WHERE id_cliente = ?');
        $up->bind_param('ssssssi', $c['tipo_documento'], $c['numero_documento'], $c['nombre'], $c['telefono'], $c['correo'], $c['direccion'], $id);

        if ($up->execute()) {
            $mensaje = 'Cliente actualizado correctamente.';
        } elseif ($conexion->errno === 1062) {
            $error = 'Ya existe otro cliente con ese tipo y número de documento.';
        } else {
            $error = 'No se pudo actualizar el cliente.';
        }
    }
}

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
