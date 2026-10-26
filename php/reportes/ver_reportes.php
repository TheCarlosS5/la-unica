<?php
// ===================================================================
// ver_reportes.php
// Muestra los reportes que ya trae la base de datos (referencias/db.sql):
//   - 3 vistas      (se consultan con SELECT)
//   - 5 procedimientos almacenados (se ejecutan con CALL)
// El reporte se elige con la URL: ver_reportes.php?r=stock
// ===================================================================
require __DIR__ . '/../config/conexion.php';
$titulo = 'Reportes';
$seccionActiva = 'reportes';

// Catálogo de reportes: nombre en la URL => título, explicación, consulta SQL
// y las columnas que son dinero (se muestran con $ y dos decimales).
$reportes = [
    'clientes' => [
        'titulo' => 'Facturas por cliente',
        'detalle' => 'Cuántas facturas tiene cada cliente (cualquier estado). Incluye clientes sin facturas.',
        'sql' => 'CALL reporte_facturas_clientes()',
        'dinero' => [],
    ],
    'totales' => [
        'titulo' => 'Total por factura',
        'detalle' => 'Suma de cantidad × precio actual de cada factura. No filtra por estado.',
        'sql' => 'SELECT * FROM vista_total_facturas',
        'dinero' => ['total_factura'],
    ],
    'unidades' => [
        'titulo' => 'Unidades por producto',
        'detalle' => 'Unidades facturadas de cada producto. Incluye las de facturas canceladas.',
        'sql' => 'SELECT * FROM vista_unidades_productos',
        'dinero' => [],
    ],
    'detalle' => [
        'titulo' => 'Detalle de facturas',
        'detalle' => 'Cada línea de cada factura con su subtotal calculado con el precio actual.',
        'sql' => 'CALL reporte_detalle_facturas()',
        'dinero' => ['precio_unitario', 'subtotal'],
    ],
    'stock' => [
        'titulo' => 'Existencias',
        'detalle' => 'Stock y precio vigente de cada producto, del que tiene menos al que tiene más.',
        'sql' => 'CALL reporte_stock_productos()',
        'dinero' => ['precio_unitario'],
    ],
    'estados' => [
        'titulo' => 'Facturas por estado',
        'detalle' => 'Cuántas facturas hay en cada estado.',
        'sql' => 'CALL reporte_facturas_por_estado()',
        'dinero' => [],
    ],
    'pendientes' => [
        'titulo' => 'Facturas pendientes',
        'detalle' => 'Facturas con estado Pendiente, de la más antigua a la más reciente.',
        'sql' => 'CALL reporte_facturas_pendientes()',
        'dinero' => [],
    ],
    'vista_clientes' => [
        'titulo' => 'Total de facturas por cliente (vista)',
        'detalle' => 'Lo mismo que «Facturas por cliente», pero leído desde la vista vista_clientes_total_facturas.',
        'sql' => 'SELECT * FROM vista_clientes_total_facturas',
        'dinero' => [],
    ],
];

// Reporte pedido en la URL; si no existe, se muestra el primero.
$clave = $_GET['r'] ?? 'clientes';
if (!isset($reportes[$clave])) {
    $clave = 'clientes';
}
$reporte = $reportes[$clave];

$filas = [];
$columnas = [];
$resultado = $conexion->query($reporte['sql']);
if ($resultado) {
    $columnas = array_map(fn($campo) => $campo->name, $resultado->fetch_fields());
    $filas = $resultado->fetch_all(MYSQLI_ASSOC);
    $resultado->free();
}
// Un CALL deja resultados pendientes; hay que leerlos para poder seguir usando la conexión.
while ($conexion->more_results()) {
    $conexion->next_result();
}

require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado aparecer">
    <div>
        <h1>Reportes</h1>
        <p class="text-secondary mb-0">Consultas listas de la base de datos.</p>
    </div>
</div>

<nav class="pestanas aparecer" aria-label="Reportes">
    <?php foreach ($reportes as $id => $r): ?>
        <a class="pestana <?= $id === $clave ? 'activa' : '' ?>" href="ver_reportes.php?r=<?= h($id) ?>"><?= h($r['titulo']) ?></a>
    <?php endforeach; ?>
</nav>

<div class="tarjeta aparecer">
    <div class="p-3" style="border-bottom: 2px solid var(--borde);">
        <h2 class="h5 mb-1"><?= h($reporte['titulo']) ?> <span class="contador"><?= count($filas) ?></span></h2>
        <p class="text-secondary mb-0"><?= h($reporte['detalle']) ?></p>
    </div>

    <?php if (count($filas) === 0): ?>
        <p class="sin-resultados">No hay datos para este reporte todavía.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <?php foreach ($columnas as $columna): ?>
                            <th><?= h(ucfirst(str_replace('_', ' ', $columna))) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filas as $fila): ?>
                        <tr>
                            <?php foreach ($columnas as $columna): ?>
                                <?php $valor = $fila[$columna]; ?>
                                <?php if (in_array($columna, $reporte['dinero'], true)): ?>
                                    <td class="num"><?= pesos2($valor) ?></td>
                                <?php elseif (in_array($columna, ['estado', 'estado_factura'], true)): ?>
                                    <td><?= insignia_estado($valor) ?></td>
                                <?php elseif ($columna === 'fecha_factura' || $columna === 'fecha'): ?>
                                    <td class="text-secondary"><?= h(fecha_hora($valor)) ?></td>
                                <?php elseif (is_numeric($valor)): ?>
                                    <td class="num"><?= h($valor) ?></td>
                                <?php else: ?>
                                    <td><?= h($valor ?? '—') ?></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
