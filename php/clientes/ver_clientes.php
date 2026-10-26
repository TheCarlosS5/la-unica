<?php
require __DIR__ . '/../config/conexion.php';
$titulo = 'Clientes';
$seccionActiva = 'clientes';
$sql = 'SELECT c.id_cliente,c.nombre,c.fecha_registro,COUNT(f.id_factura) AS facturas FROM clientes c LEFT JOIN facturas f ON f.id_cliente=c.id_cliente GROUP BY c.id_cliente,c.nombre,c.fecha_registro ORDER BY c.id_cliente';
$resultado = $conexion->query($sql);
require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista acciones-superiores">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Clientes</span>
        </nav>
        <h1 class="encabezado-titulo">Listado de clientes</h1>
        <p class="encabezado-descripcion">Consulta los clientes registrados y su relación con facturas existentes.</p>
    </div>
    <div class="encabezado-acciones">
        <a class="boton" href="crear_cliente.php">
            <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>s
            <span>Crear cliente</span>
        </a>
    </div>
</div>

<?php if (isset($_GET['mensaje'])): ?>
    <div class="mensaje alerta" role="status">
        <svg class="alerta-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
        <div class="alerta-contenido"><?= h($_GET['mensaje']) ?></div>
    </div>
<?php endif; ?>

<?php if (!$resultado || $resultado->num_rows === 0): ?>
    <div class="sin-datos estado-vacio">
        <svg class="estado-vacio-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
        <h2 class="estado-vacio-titulo">Todavía no hay clientes registrados</h2>
        <p class="estado-vacio-texto">Registra el primer cliente para comenzar a organizar la información comercial.</p>
        <a class="boton" href="crear_cliente.php">
            <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Crear primer cliente</span>
        </a>
    </div>
<?php else: ?>
    <div class="tabla-wrap tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Fecha de registro</th>
                    <th scope="col">Facturas</th>
                    <th scope="col" class="texto-derecha">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($c = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <span class="chip-id">#<?= h($c['id_cliente']) ?></span>
                        </td>
                        <td>
                            <span class="celda-destacada"><?= h($c['nombre']) ?></span>
                        </td>
                        <td>
                            <span class="celda-fecha"><?= h($c['fecha_registro']) ?></span>
                        </td>
                        <td>
                            <span class="insignia-facturas <?= (int)$c['facturas'] > 0 ? 'con-facturas' : 'sin-facturas' ?>">
                                <?= h($c['facturas']) ?> <?= (int)$c['facturas'] === 1 ? 'factura' : 'facturas' ?>
                            </span>
                        </td>
                        <td>
                            <div class="acciones" style="justify-content: flex-end;">
                                <a class="editar" href="editar_cliente.php?id=<?= h($c['id_cliente']) ?>" title="Editar cliente #<?= h($c['id_cliente']) ?>">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    <span>Editar</span>
                                </a>
                                <?php if ((int)$c['facturas'] === 0): ?>
                                    <form method="post" action="eliminar_cliente.php" onsubmit="return confirm('¿Eliminar este cliente?')">
                                        <input type="hidden" name="id_cliente" value="<?= h($c['id_cliente']) ?>">
                                        <button type="submit" class="boton-peligro" title="Eliminar cliente #<?= h($c['id_cliente']) ?>">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                            </svg>
                                            <span>Eliminar</span>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="bloqueado etiqueta-bloqueado" title="No se puede eliminar: el cliente tiene facturas asociadas">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                        </svg>
                                        <span>Tiene facturas</span>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="nota nota-informativa">
        <svg class="nota-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
        <span>Control de integridad: No se permite eliminar clientes que tengan facturas asociadas en el sistema.</span>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
