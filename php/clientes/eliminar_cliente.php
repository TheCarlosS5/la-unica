<?php
// ===================================================================
// eliminar_cliente.php  (la "D" de CRUD: Delete / Eliminar)
// Paso 1: llega desde el listado -> muestra "¿Seguro?".
// Paso 2: llega con confirmar=si  -> elimina y vuelve al listado.
// ===================================================================
require __DIR__ . '/../config/conexion.php';

// Función corta para volver al listado con un mensaje.
function volver_al_listado($texto, $tipo = 'exito') {
    header('Location: ver_clientes.php?tipo=' . $tipo . '&mensaje=' . urlencode($texto));
    exit;
}

// Solo se aceptan peticiones POST con un ID válido.
$id = id_valido($_POST['id_cliente'] ?? '');
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $id === null) {
    volver_al_listado('Solicitud no válida.', 'error');
}

// Buscar el cliente para mostrar su nombre en la confirmación.
$consulta = $conexion->prepare('SELECT nombre FROM clientes WHERE id_cliente = ?');
$consulta->bind_param('i', $id);
$consulta->execute();
$consulta->bind_result($nombre);
$existe = $consulta->fetch();
$consulta->close();

if (!$existe) {
    volver_al_listado('No se encontró el cliente.', 'error');
}

// Paso 2: el usuario ya confirmó.
if (($_POST['confirmar'] ?? '') === 'si') {
    // No se puede borrar un cliente que tenga facturas.
    $relaciones = $conexion->prepare('SELECT COUNT(*) FROM facturas WHERE id_cliente = ?');
    $relaciones->bind_param('i', $id);
    $relaciones->execute();
    $relaciones->bind_result($totalFacturas);
    $relaciones->fetch();
    $relaciones->close();

    if ($totalFacturas > 0) {
        volver_al_listado('No se puede eliminar: el cliente tiene facturas.', 'error');
    }

    $eliminar = $conexion->prepare('DELETE FROM clientes WHERE id_cliente = ?');
    $eliminar->bind_param('i', $id);
    if ($eliminar->execute() && $eliminar->affected_rows === 1) {
        volver_al_listado('Cliente eliminado correctamente.');
    }
    volver_al_listado('No se pudo eliminar el cliente.', 'error');
}

// Paso 1: mostrar la confirmación.
$titulo = 'Eliminar cliente';
$seccionActiva = 'clientes';
require __DIR__ . '/../includes/menu.php';
?>
<div class="tarjeta confirmacion aparecer">
    <span class="insignia peligro"><?= icono('trash') ?></span>
    <h1 class="h4 mt-3">¿Eliminar este cliente?</h1>
    <p class="text-secondary">Esta acción no se puede deshacer.</p>

    <div class="resumen">
        <span class="avatar"><?= h(iniciales($nombre)) ?></span>
        <div class="text-start">
            <strong><?= h($nombre) ?></strong><br>
            <span class="text-secondary"><?= 'Id ' . h($id) ?></span>
        </div>
    </div>

    <form method="post" class="d-flex gap-2 justify-content-center">
        <input type="hidden" name="id_cliente" value="<?= h($id) ?>">
        <input type="hidden" name="confirmar" value="si">
        <a class="btn btn-light" href="ver_clientes.php">Cancelar</a>
        <button class="btn btn-danger" type="submit">Sí, eliminar</button>
    </form>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
