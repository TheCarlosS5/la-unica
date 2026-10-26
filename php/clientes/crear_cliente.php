<?php
// ===================================================================
// crear_cliente.php  (la "C" de CRUD: Create / Crear)
// Muestra el formulario y, cuando se envía, guarda el cliente.
// El ID lo genera la base de datos (AUTO_INCREMENT).
// ===================================================================
require __DIR__ . '/../config/conexion.php';
$titulo = 'Nuevo cliente';
$seccionActiva = 'clientes';

$mensaje = '';
$error = '';
$vacio = ['tipo_documento' => 'CC', 'numero_documento' => '', 'nombre' => '', 'telefono' => '', 'correo' => '', 'direccion' => ''];
$c = $vacio;

// Solo procesamos cuando el formulario fue enviado (método POST).
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // validar_cliente (en conexion.php) limpia los datos y devuelve el primer error.
    [$c, $error] = validar_cliente($_POST);

    if ($error === '') {
        $stmt = $conexion->prepare('INSERT INTO clientes (tipo_documento, numero_documento, nombre, telefono, correo, direccion) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $c['tipo_documento'], $c['numero_documento'], $c['nombre'], $c['telefono'], $c['correo'], $c['direccion']);

        if ($stmt->execute()) {
            $mensaje = 'Cliente creado correctamente.';
            $c = $vacio; // limpiar el formulario
        } elseif ($conexion->errno === 1062) { // 1062 = valor repetido en un campo único
            $error = 'Ya existe un cliente con ese tipo y número de documento.';
        } else {
            $error = 'No se pudo crear el cliente.';
        }
    }
}

$bloquearDoc = false;
$textoBoton = 'Guardar cliente';
require __DIR__ . '/../includes/menu.php';
?>
<a class="volver" href="ver_clientes.php">← Clientes</a>

<div class="tarjeta formulario aparecer">
    <div class="formulario-cabecera">
        <span class="insignia"><?= icono('user-plus') ?></span>
        <div>
            <h1 class="h4 mb-1">Nuevo cliente</h1>
            <p class="text-secondary mb-0">Completa los datos para registrarlo.</p>
        </div>
    </div>

    <?= alerta($mensaje) ?>
    <?= alerta($error, 'error') ?>

    <?php require __DIR__ . '/../includes/form_cliente.php'; ?>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
