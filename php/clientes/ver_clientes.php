<?php
// ===================================================================
// ver_clientes.php  (la "R" de CRUD: Read / Leer)
// Lista todos los clientes y cuántas facturas tiene cada uno.
//
// Un cliente con facturas no se puede eliminar (la base de datos lo impide).
// ===================================================================
require __DIR__ . '/../config/conexion.php';
/** @var mysqli $conexion */
$titulo = 'Clientes';
$seccionActiva = 'clientes';

// LEFT JOIN para que aparezcan también los clientes sin facturas (0).
$sql = 'SELECT c.id_cliente, c.nombre, c.fecha_registro, COUNT(f.id_factura) AS facturas
        FROM clientes c
        LEFT JOIN facturas f ON f.id_cliente = c.id_cliente
        GROUP BY c.id_cliente, c.nombre, c.fecha_registro
        ORDER BY c.nombre';
$resultado = $conexion->query($sql);
$clientes = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];

// Cifra del resumen: clientes que todavía no han comprado
$sinFacturas = 0;
foreach ($clientes as $c) {
    if ((int)$c['facturas'] === 0) $sinFacturas++;
}

// Mensaje que llega desde eliminar_cliente.php
$mensaje = $_GET['mensaje'] ?? '';
$tipo = $_GET['tipo'] ?? 'exito';

require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado aparecer">
    <div>
        <h1>Clientes <span class="contador"><?= count($clientes) ?></span></h1>
        <p class="text-secondary mb-0">Personas registradas en la tienda.</p>
    </div>
    <div class="acciones-encabezado">
        <label class="buscador">
            <?= icono('search') ?>
            <input type="search" data-buscador placeholder="Buscar por id o nombre…" aria-label="Buscar cliente">
        </label>
        <a class="btn btn-primary btn-con-icono" href="crear_cliente.php">
            <?= icono('plus') ?> Nuevo cliente
        </a>
    </div>
</div>

<?= alerta($mensaje, $tipo) ?>

<?php if (count($clientes) > 0): ?>
    <div class="resumen-cifras aparecer">
        <div class="cifra"><b><?= count($clientes) ?></b><span>Clientes</span></div>
        <div class="cifra"><b><?= $sinFacturas ?></b><span>Sin facturas</span></div>
    </div>
<?php endif; ?>

<?php if (count($clientes) === 0): ?>
    <div class="vacio aparecer">
        <span class="insignia grande"><?= icono('users') ?></span>
        <h2 class="h5 mt-3">Todavía no hay clientes</h2>
        <a class="btn btn-primary mt-2" href="crear_cliente.php">Crear el primero</a>
    </div>
<?php else: ?>
    <div class="tarjeta aparecer">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Id</th>
                        <th>Registro</th>
                        <th>Facturas</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $i => $c): ?>
                        <tr class="fila" style="--i: <?= $i ?>" data-buscar="<?= h($c['id_cliente'] . ' ' . $c['nombre']) ?>">
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="avatar"><?= h(iniciales($c['nombre'])) ?></span>
                                    <strong><?= h($c['nombre']) ?></strong>
                                </div>
                            </td>
                            <td class="text-secondary"><?= h($c['id_cliente']) ?></td>
                            <td class="text-secondary"><?= h($c['fecha_registro']) ?></td>
                            <td><span class="etiqueta"><?= (int)$c['facturas'] ?></span></td>
                            <td class="text-end text-nowrap">
                                <a class="btn-icono" href="editar_cliente.php?id=<?= h($c['id_cliente']) ?>" title="Editar">
                                    <?= icono('pencil') ?>
                                </a>
                                <?php if ((int)$c['facturas'] === 0): ?>
                                    <form class="d-inline" method="post" action="eliminar_cliente.php">
                                        <input type="hidden" name="id_cliente" value="<?= h($c['id_cliente']) ?>">
                                        <button class="btn-icono" type="submit" title="Eliminar">
                                            <?= icono('trash') ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="sin-resultados" hidden>No hay clientes con ese id o nombre.</p>
    </div>
    <p class="nota">Los clientes con facturas no se pueden eliminar.</p>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
