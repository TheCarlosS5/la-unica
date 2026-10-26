<?php
// ===================================================================
// crear_producto.php  (la "C" de CRUD: Create / Crear)
// Muestra el formulario y, cuando se envía, guarda el producto.
// El ID lo genera la base de datos (AUTO_INCREMENT).
// ===================================================================
require __DIR__ . '/../config/conexion.php';
$titulo = 'Nuevo producto';
$seccionActiva = 'productos';

$mensaje = '';
$error = '';
$vacio = ['codigo' => '', 'nombre' => '', 'descripcion' => '', 'unidad_medida' => 'UND', 'precio' => '', 'stock' => ''];
$p = $vacio;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // validar_producto (en conexion.php) limpia los datos y devuelve el primer error.
    [$p, $error] = validar_producto($_POST);

    if ($error === '') {
        $stmt = $conexion->prepare('INSERT INTO productos (codigo, nombre, descripcion, unidad_medida, precio, stock) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('ssssss', $p['codigo'], $p['nombre'], $p['descripcion'], $p['unidad_medida'], $p['precio'], $p['stock']);

        if ($stmt->execute()) {
            $mensaje = 'Producto creado correctamente.';
            $p = $vacio; // limpiar el formulario
        } elseif ($conexion->errno === 1062) { // 1062 = valor repetido en un campo único
            $error = 'Ya existe un producto con ese código.';
        } else {
            $error = 'No se pudo crear el producto.';
        }
    }
}

$bloquearClave = false;
$textoBoton = 'Guardar producto';
require __DIR__ . '/../includes/menu.php';
?>
<a class="volver" href="ver_productos.php">← Productos</a>

<div class="tarjeta formulario aparecer">
    <div class="formulario-cabecera">
        <span class="insignia"><?= icono('package') ?></span>
        <div>
            <h1 class="h4 mb-1">Nuevo producto</h1>
            <p class="text-secondary mb-0">La foto se elige sola según el nombre.</p>
        </div>
    </div>

    <?= alerta($mensaje) ?>
    <?= alerta($error, 'error') ?>

    <?php require __DIR__ . '/../includes/form_producto.php'; ?>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
