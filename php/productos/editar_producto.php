<?php
// ===================================================================
// editar_producto.php  (la "U" de CRUD: Update / Actualizar)
// Carga el producto por su ID (?id=5), muestra el formulario
// con sus datos y guarda los cambios.
// ===================================================================
require __DIR__ . '/../config/conexion.php';
$titulo = 'Editar producto';
$seccionActiva = 'productos';

$mensaje = '';
$error = '';

// 1. Leer el ID de la URL y validarlo.
$id = id_valido($_GET['id'] ?? '');
if ($id === null) {
    http_response_code(400);
    exit('ID de producto no válido.');
}

// 2. Traer los datos actuales del producto.
$stmt = $conexion->prepare('SELECT nombre, unidad_medida, stock, precio_unitario FROM productos WHERE id_producto = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$p) {
    http_response_code(404);
    exit('No se encontró el producto.');
}

// 3. Si enviaron el formulario, validar y guardar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    [$p, $error] = validar_producto($_POST);

    if ($error === '') {
        $stock = (int)$p['stock'];
        $up = $conexion->prepare('UPDATE productos SET nombre = ?, unidad_medida = ?, stock = ?, precio_unitario = ? WHERE id_producto = ?');
        $up->bind_param('ssisi', $p['nombre'], $p['unidad_medida'], $stock, $p['precio_unitario'], $id);

        if ($up->execute()) {
            $mensaje = 'Producto actualizado correctamente.';
        } else {
            $error = 'No se pudo actualizar el producto.';
        }
    }
}

$textoBoton = 'Guardar cambios';
require __DIR__ . '/../includes/menu.php';
?>
<a class="volver" href="ver_productos.php">← Productos</a>

<div class="tarjeta formulario aparecer">
    <div class="formulario-cabecera">
        <img class="miniatura" src="<?= h(foto_producto($p['nombre'])) ?>" alt="">
        <div>
            <h1 class="h4 mb-1">Editar producto</h1>
            <p class="text-secondary mb-0">Producto #<?= h($id) ?></p>
        </div>
    </div>

    <?= alerta($mensaje) ?>
    <?= alerta($error, 'error') ?>

    <?php require __DIR__ . '/../includes/form_producto.php'; ?>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
