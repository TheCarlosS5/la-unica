<?php
/*
 * LISTADO DE FACTURAS (solo lectura).
 * El total de cada factura = suma de cantidad * precio_unitario de sus detalles
 * (la misma cuenta de la vista vista_total_facturas de referencias/db.sql).
 * Se usa LEFT JOIN para que una factura sin detalles también aparezca (total 0).
 */
require __DIR__ . '/../config/conexion.php';
$titulo = 'Facturas';
$seccionActiva = 'facturas';
$sql = 'SELECT f.id_factura,c.nombre AS cliente,f.estado_factura,f.fecha_factura,COALESCE(SUM(d.cantidad*p.precio_unitario),0) AS total FROM facturas f INNER JOIN clientes c ON c.id_cliente=f.id_cliente LEFT JOIN detalle_facturas d ON d.id_factura=f.id_factura LEFT JOIN productos p ON p.id_producto=d.id_producto GROUP BY f.id_factura,c.nombre,f.estado_factura,f.fecha_factura ORDER BY f.fecha_factura DESC';
$resultado = $conexion->query($sql);
require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista acciones-superiores">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Facturas</span>
        </nav>
        <h1 class="encabezado-titulo">Facturas</h1>
        <p class="encabezado-descripcion">Las ventas más recientes primero, con su cliente, estado y total.</p>
    </div>
    <div class="encabezado-acciones">
        <a class="boton" href="nueva_factura.php">
            <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Nueva venta</span>
        </a>
    </div>
</div>

<?php if (!$resultado || $resultado->num_rows === 0): ?>
    <div class="sin-datos estado-vacio">
        <svg class="estado-vacio-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
        </svg>
        <h2 class="estado-vacio-titulo">Todavía no hay facturas</h2>
        <p class="estado-vacio-texto">Cuando hagas tu primera venta aparecerá aquí.</p>
        <a class="boton" href="nueva_factura.php">Hacer la primera venta</a>
    </div>
<?php else: ?>
    <div class="tabla-wrap tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">N.º</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Estado</th>
                    <th scope="col" class="texto-derecha">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($f = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <span class="chip-id">#<?= h($f['id_factura']) ?></span>
                        </td>
                        <td>
                            <span class="celda-destacada"><?= h($f['cliente']) ?></span>
                        </td>
                        <td>
                            <span class="celda-fecha"><?= h($f['fecha_factura']) ?></span>
                        </td>
                        <td>
                            <span class="insignia-estado <?= h(strtolower($f['estado_factura'])) ?>"><?= h($f['estado_factura']) ?></span>
                        </td>
                        <td class="celda-precio">
                            $ <?= number_format((float)$f['total'], 2, ',', '.') ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
