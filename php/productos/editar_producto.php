<?php
// ===================================================================
// editar_producto.php  (la "U" de CRUD: Update / Actualizar)
// Carga el producto por su ID (?id=5), muestra el formulario
// con sus datos y guarda los cambios.
// Si el producto ya está en facturas, el código y la unidad no cambian
// (las facturas viejas seguirían apuntando a otro artículo).
// El precio sí puede cambiar: las facturas viejas guardan su propio precio.
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

// 2. Traer los datos actuales y en cuántas facturas aparece.
$stmt = $conexion->prepare('SELECT codigo, nombre, descripcion, unidad_medida, precio, stock,
                                   (SELECT COUNT(*) FROM detalle_factura WHERE id_producto = productos.id_producto) AS ventas
                            FROM productos WHERE id_producto = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$p = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$p) {
    http_response_code(404);
    exit('No se encontró el producto.');
}
// La base devuelve el stock con 3 decimales ("5.000"); lo mostramos como "5".
$p['stock'] = rtrim(rtrim($p['stock'], '0'), '.');
$bloquearClave = (int)$p['ventas'] > 0;

// 3. Si enviaron el formulario, validar y guardar.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entrada = $_POST;
    if ($bloquearClave) {
        // Usamos el código y la unidad que ya estaban guardados.
        $entrada['codigo'] = $p['codigo'];
        $entrada['unidad_medida'] = $p['unidad_medida'];
    }
    [$nuevo, $error] = validar_producto($entrada);
    $p = array_merge($p, $nuevo);

    if ($error === '') {
        $up = $conexion->prepare('UPDATE productos SET codigo = ?, nombre = ?, descripcion = ?, unidad_medida = ?, precio = ?, stock = ? WHERE id_producto = ?');
        $up->bind_param('ssssssi', $p['codigo'], $p['nombre'], $p['descripcion'], $p['unidad_medida'], $p['precio'], $p['stock'], $id);

        if ($up->execute()) {
            $mensaje = 'Producto actualizado correctamente.';
        } elseif ($conexion->errno === 1062) {
            $error = 'Ya existe otro producto con ese código.';
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
