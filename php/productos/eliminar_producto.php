<?php
// ===================================================================
// eliminar_producto.php  (la "D" de CRUD: Delete / Eliminar)
// Paso 1: llega desde el listado -> muestra "¿Seguro?".
// Paso 2: llega con confirmar=si  -> elimina y vuelve al listado.
// ===================================================================
require __DIR__ . '/../config/conexion.php';

// Función corta para volver al listado con un mensaje.
function volver_al_listado($texto, $tipo = 'exito') {
    header('Location: ver_productos.php?tipo=' . $tipo . '&mensaje=' . urlencode($texto));
    exit;
}

// Solo se aceptan peticiones POST con un ID válido.
$id = id_valido($_POST['id_producto'] ?? '');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id === null) {
    volver_al_listado('Solicitud no válida.', 'error');
}

// Buscar el producto para mostrar su nombre en la confirmación.
$consulta = $conexion->prepare('SELECT nombre FROM productos WHERE id_producto = ?');
$consulta->bind_param('i', $id);
$consulta->execute();
$consulta->bind_result($nombre);
$existe = $consulta->fetch();
$consulta->close();

if (!$existe) {
    volver_al_listado('No se encontró el producto.', 'error');
}

// Paso 2: el usuario ya confirmó.
if (($_POST['confirmar'] ?? '') === 'si') {
    // No se puede borrar un producto que aparezca en facturas.
    $relaciones = $conexion->prepare('SELECT COUNT(*) FROM detalle_factura WHERE id_producto = ?');
    $relaciones->bind_param('i', $id);
    $relaciones->execute();
    $relaciones->bind_result($enFacturas);
    $relaciones->fetch();
    $relaciones->close();

    if ($enFacturas > 0) {
        volver_al_listado('No se puede eliminar: el producto aparece en facturas.', 'error');
    }

    $eliminar = $conexion->prepare('DELETE FROM productos WHERE id_producto = ?');
    $eliminar->bind_param('i', $id);
    if ($eliminar->execute() && $eliminar->affected_rows === 1) {
        volver_al_listado('Producto eliminado correctamente.');
    }
    volver_al_listado('No se pudo eliminar el producto.', 'error');
}

// Paso 1: mostrar la confirmación.
$titulo = 'Eliminar producto';
$seccionActiva = 'productos';
require __DIR__ . '/../includes/menu.php';
?>
<div class="tarjeta confirmacion aparecer">
    <img class="miniatura grande" src="<?= h(foto_producto($nombre)) ?>" alt="">
    <h1 class="h4 mt-3">¿Eliminar «<?= h($nombre) ?>»?</h1>
    <p class="text-secondary">Esta acción no se puede deshacer.</p>

    <form method="post" class="d-flex gap-2 justify-content-center">
        <input type="hidden" name="id_producto" value="<?= h($id) ?>">
        <input type="hidden" name="confirmar" value="si">
        <a class="btn btn-light" href="ver_productos.php">Cancelar</a>
        <button class="btn btn-danger" type="submit">Sí, eliminar</button>
    </form>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
