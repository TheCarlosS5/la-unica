<?php
require __DIR__ . '/../config/conexion.php';

$titulo = 'Reportes';
$seccionActiva = 'reportes';
$reportes = [
    'clientes' => [
        'titulo' => 'Clientes y cantidad de facturas',
        'descripcion' => 'Cuenta las facturas de cada cliente e incluye a quienes todavía no tienen ventas.',
        'fuente' => 'Vista vista_clientes_total_facturas y procedimiento reporte_facturas_clientes()',
        'nota' => 'El conteo incluye facturas Pagado, Cancelado y Pendiente. La vista y el procedimiento se comparan; si sus resultados difieren, el reporte no se muestra.',
        'vacio' => 'Todavía no hay clientes registrados.',
        'columnas' => [
            ['campo' => 'id_cliente', 'titulo' => 'ID cliente', 'tipo' => 'id'],
            ['campo' => 'nombre', 'titulo' => 'Cliente', 'tipo' => 'texto'],
            ['campo' => 'total_facturas', 'titulo' => 'Facturas', 'tipo' => 'entero'],
        ],
        'enlace_factura' => false,
    ],
    'total_facturas' => [
        'titulo' => 'Total vigente por factura',
        'descripcion' => 'Calcula cantidad por precio unitario vigente para cada factura que tiene detalles.',
        'fuente' => 'Vista vista_total_facturas',
        'nota' => 'El total usa los precios actuales del catálogo y no filtra por estado; una factura cancelada también aparece. Las facturas sin detalles no están en esta vista.',
        'vacio' => 'No hay facturas con detalles para calcular.',
        'columnas' => [
            ['campo' => 'id_factura', 'titulo' => 'Factura', 'tipo' => 'id'],
            ['campo' => 'cliente', 'titulo' => 'Cliente', 'tipo' => 'texto'],
            ['campo' => 'total_factura', 'titulo' => 'Total vigente', 'tipo' => 'moneda'],
        ],
        'enlace_factura' => true,
    ],
    'unidades_productos' => [
        'titulo' => 'Unidades facturadas por producto',
        'descripcion' => 'Suma las cantidades de detalle asociadas a cada producto.',
        'fuente' => 'Vista vista_unidades_productos',
        'nota' => 'La suma incluye los detalles de facturas canceladas y pendientes, tal como está definido en la vista. Solo aparecen productos con al menos un detalle.',
        'vacio' => 'Todavía no hay productos incluidos en detalles de factura.',
        'columnas' => [
            ['campo' => 'id_producto', 'titulo' => 'ID producto', 'tipo' => 'id'],
            ['campo' => 'producto', 'titulo' => 'Producto', 'tipo' => 'texto'],
            ['campo' => 'unidades_facturadas', 'titulo' => 'Unidades', 'tipo' => 'entero'],
        ],
        'enlace_factura' => false,
    ],
    'detalle_facturas' => [
        'titulo' => 'Detalle de facturas',
        'descripcion' => 'Consulta cada producto, cantidad, precio vigente y subtotal de sus facturas.',
        'fuente' => 'Procedimiento reporte_detalle_facturas()',
        'nota' => 'Incluye todos los estados y ordena por ID de factura. Los subtotales usan el precio actual del producto; no representan un precio histórico.',
        'vacio' => 'Todavía no hay detalles de factura.',
        'columnas' => [
            ['campo' => 'id_factura', 'titulo' => 'Factura', 'tipo' => 'id'],
            ['campo' => 'cliente', 'titulo' => 'Cliente', 'tipo' => 'texto'],
            ['campo' => 'estado_factura', 'titulo' => 'Estado', 'tipo' => 'estado'],
            ['campo' => 'fecha_factura', 'titulo' => 'Fecha', 'tipo' => 'fecha'],
            ['campo' => 'producto', 'titulo' => 'Producto', 'tipo' => 'texto'],
            ['campo' => 'cantidad', 'titulo' => 'Cantidad', 'tipo' => 'entero'],
            ['campo' => 'precio_unitario', 'titulo' => 'Precio vigente', 'tipo' => 'moneda'],
            ['campo' => 'subtotal', 'titulo' => 'Subtotal vigente', 'tipo' => 'moneda'],
        ],
        'enlace_factura' => true,
    ],
    'stock_productos' => [
        'titulo' => 'Existencias de productos',
        'descripcion' => 'Muestra stock y precio unitario vigente, desde la menor existencia.',
        'fuente' => 'Procedimiento reporte_stock_productos()',
        'nota' => 'El orden ascendente permite revisar primero las existencias más bajas. El precio mostrado es el precio vigente del catálogo.',
        'vacio' => 'Todavía no hay productos registrados.',
        'columnas' => [
            ['campo' => 'id_producto', 'titulo' => 'ID producto', 'tipo' => 'id'],
            ['campo' => 'nombre', 'titulo' => 'Producto', 'tipo' => 'texto'],
            ['campo' => 'stock', 'titulo' => 'Existencias', 'tipo' => 'entero'],
            ['campo' => 'precio_unitario', 'titulo' => 'Precio vigente', 'tipo' => 'moneda'],
        ],
        'enlace_factura' => false,
    ],
    'por_estado' => [
        'titulo' => 'Facturas por estado',
        'descripcion' => 'Cuenta las facturas agrupadas por su estado actual.',
        'fuente' => 'Procedimiento reporte_facturas_por_estado()',
        'nota' => 'El reporte incluye todos los estados que tengan facturas registradas; un estado sin registros no aparece como fila.',
        'vacio' => 'Todavía no hay facturas registradas.',
        'columnas' => [
            ['campo' => 'estado_factura', 'titulo' => 'Estado', 'tipo' => 'estado'],
            ['campo' => 'total_facturas', 'titulo' => 'Facturas', 'tipo' => 'entero'],
        ],
        'enlace_factura' => false,
    ],
    'pendientes' => [
        'titulo' => 'Facturas pendientes',
        'descripcion' => 'Lista las facturas cuyo estado actual es Pendiente, de la más antigua a la más reciente.',
        'fuente' => 'Procedimiento reporte_facturas_pendientes()',
        'nota' => 'Este reporte aplica el filtro Pendiente definido en la base de datos. No cambia el estado ni las existencias.',
        'vacio' => 'No hay facturas pendientes.',
        'columnas' => [
            ['campo' => 'id_factura', 'titulo' => 'Factura', 'tipo' => 'id'],
            ['campo' => 'cliente', 'titulo' => 'Cliente', 'tipo' => 'texto'],
            ['campo' => 'fecha_factura', 'titulo' => 'Fecha', 'tipo' => 'fecha'],
            ['campo' => 'estado_factura', 'titulo' => 'Estado', 'tipo' => 'estado'],
        ],
        'enlace_factura' => true,
    ],
];

$tipoReporte = $_GET['reporte'] ?? 'clientes';
$tipoReporte = is_string($tipoReporte) ? $tipoReporte : '';
$errorEntrada = '';
$errorLectura = false;
$filasReporte = [];
$reporteSeleccionado = $reportes[$tipoReporte] ?? null;

function consultarFilasReporte(mysqli $conexion, string $consulta, bool $esProcedimiento = false): array
{
    $resultado = $conexion->query($consulta);
    $filas = [];

    if ($resultado instanceof mysqli_result) {
        try {
            $filas = $resultado->fetch_all(MYSQLI_ASSOC);
        } finally {
            $resultado->free();
        }
    }

    if ($esProcedimiento) {
        while ($conexion->more_results()) {
            if (!$conexion->next_result()) {
                break;
            }

            $resultadoAdicional = $conexion->store_result();
            if ($resultadoAdicional instanceof mysqli_result) {
                $resultadoAdicional->free();
            }
        }
    }

    return $filas;
}

if ($reporteSeleccionado === null) {
    http_response_code(400);
    $errorEntrada = 'Selecciona uno de los reportes disponibles.';
} else {
    try {
        switch ($tipoReporte) {
            case 'clientes':
                $filasVista = consultarFilasReporte(
                    $conexion,
                    'SELECT id_cliente, nombre, total_facturas FROM vista_clientes_total_facturas ORDER BY id_cliente'
                );
                $filasProcedimiento = consultarFilasReporte($conexion, 'CALL reporte_facturas_clientes()', true);

                $normalizarClientes = static function (array $filas): array {
                    $normalizadas = [];
                    foreach ($filas as $fila) {
                        $normalizadas[] = [
                            'id_cliente' => (int)$fila['id_cliente'],
                            'nombre' => (string)$fila['nombre'],
                            'total_facturas' => (int)$fila['total_facturas'],
                        ];
                    }
                    usort($normalizadas, static fn(array $a, array $b): int => $a['id_cliente'] <=> $b['id_cliente']);
                    return $normalizadas;
                };

                if ($normalizarClientes($filasVista) !== $normalizarClientes($filasProcedimiento)) {
                    throw new UnexpectedValueException('La vista y el procedimiento de clientes devolvieron valores diferentes.');
                }
                $filasReporte = $filasProcedimiento;
                break;

            case 'total_facturas':
                $filasReporte = consultarFilasReporte(
                    $conexion,
                    'SELECT id_factura, cliente, total_factura FROM vista_total_facturas ORDER BY id_factura DESC'
                );
                break;

            case 'unidades_productos':
                $filasReporte = consultarFilasReporte(
                    $conexion,
                    'SELECT id_producto, producto, unidades_facturadas FROM vista_unidades_productos ORDER BY unidades_facturadas DESC, id_producto'
                );
                break;

            case 'detalle_facturas':
                $filasReporte = consultarFilasReporte($conexion, 'CALL reporte_detalle_facturas()', true);
                break;

            case 'stock_productos':
                $filasReporte = consultarFilasReporte($conexion, 'CALL reporte_stock_productos()', true);
                break;

            case 'por_estado':
                $filasReporte = consultarFilasReporte($conexion, 'CALL reporte_facturas_por_estado()', true);
                break;

            case 'pendientes':
                $filasReporte = consultarFilasReporte($conexion, 'CALL reporte_facturas_pendientes()', true);
                break;
        }
    } catch (Throwable $e) {
        registrarErrorServidor('leer_reporte_' . $tipoReporte, $e);
        http_response_code(500);
        $errorLectura = true;
        $filasReporte = [];
    }
}

require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Reportes</span>
        </nav>
        <h1 class="encabezado-titulo">Reportes</h1>
        <p class="encabezado-descripcion">Consulta clientes, ventas, existencias y estados usando las vistas y procedimientos de la base de datos.</p>
    </div>
</div>

<form class="filtros-facturas filtros-reportes" method="get" action="ver_reportes.php" aria-label="Seleccionar reporte">
    <div class="campo">
        <label for="tipo-reporte">Reporte disponible</label>
        <select id="tipo-reporte" name="reporte" required>
            <?php foreach ($reportes as $claveReporte => $reporte): ?>
                <option value="<?= h($claveReporte) ?>" <?= $tipoReporte === $claveReporte ? 'selected' : '' ?>><?= h($reporte['titulo']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filtro-acciones">
        <button type="submit">Ver reporte</button>
    </div>
</form>

<?php if ($errorEntrada !== ''): ?>
    <div class="mensaje error alerta alerta-error" role="alert"><?= h($errorEntrada) ?></div>
<?php elseif ($errorLectura): ?>
    <div class="mensaje error alerta alerta-error" role="alert">No se pudo generar el reporte. Revisa la conexión y que estén disponibles las vistas y procedimientos definidos en <code>referencias/db.sql</code>.</div>
<?php elseif ($reporteSeleccionado !== null && $filasReporte === []): ?>
    <div class="sin-datos estado-vacio" role="status">
        <h2 class="estado-vacio-titulo"><?= h($reporteSeleccionado['vacio']) ?></h2>
        <p class="estado-vacio-texto"><?= h($reporteSeleccionado['descripcion']) ?></p>
    </div>
<?php elseif ($reporteSeleccionado !== null): ?>
    <section class="panel-reporte" aria-labelledby="titulo-reporte">
        <div class="panel-reporte-cabecera">
            <div>
                <p class="reporte-fuente">Fuente: <?= h($reporteSeleccionado['fuente']) ?></p>
                <h2 id="titulo-reporte" class="reporte-titulo"><?= h($reporteSeleccionado['titulo']) ?></h2>
                <p class="reporte-descripcion"><?= h($reporteSeleccionado['descripcion']) ?></p>
            </div>
            <p class="facturas-conteo" role="status">Registros: <?= number_format(count($filasReporte), 0, ',', '.') ?></p>
        </div>
        <p id="nota-reporte" class="nota-reporte"><?= h($reporteSeleccionado['nota']) ?></p>
        <div class="tabla-wrap tabla-contenedor">
            <table class="tabla" aria-describedby="nota-reporte">
                <thead>
                    <tr>
                        <?php foreach ($reporteSeleccionado['columnas'] as $columna): ?>
                            <th scope="col"<?= in_array($columna['tipo'], ['entero', 'moneda'], true) ? ' class="texto-derecha"' : '' ?>><?= h($columna['titulo']) ?></th>
                        <?php endforeach; ?>
                        <?php if ($reporteSeleccionado['enlace_factura']): ?>
                            <th scope="col" class="texto-derecha">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filasReporte as $fila): ?>
                        <tr>
                            <?php foreach ($reporteSeleccionado['columnas'] as $columna): ?>
                                <?php $valor = $fila[$columna['campo']] ?? ''; ?>
                                <td<?= in_array($columna['tipo'], ['entero', 'moneda'], true) ? ' class="texto-derecha"' : '' ?>>
                                    <?php if ($columna['tipo'] === 'id'): ?>
                                        <span class="chip-id">#<?= h($valor) ?></span>
                                    <?php elseif ($columna['tipo'] === 'moneda'): ?>
                                        <span class="celda-destacada"><?= h(formatearMonedaCop($valor)) ?></span>
                                    <?php elseif ($columna['tipo'] === 'entero'): ?>
                                        <?= number_format((int)$valor, 0, ',', '.') ?>
                                    <?php elseif ($columna['tipo'] === 'fecha'): ?>
                                        <?php try { $fechaReporte = (new DateTimeImmutable((string)$valor))->format('d/m/Y H:i'); } catch (Throwable $e) { $fechaReporte = 'Sin fecha disponible'; } ?>
                                        <span class="celda-fecha"><?= h($fechaReporte) ?></span>
                                    <?php elseif ($columna['tipo'] === 'estado'): ?>
                                        <span class="estado-factura <?= strtolower(h($valor)) ?>"><?= h($valor) ?></span>
                                    <?php else: ?>
                                        <?= h($valor) ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <?php if ($reporteSeleccionado['enlace_factura']): ?>
                                <td class="texto-derecha">
                                    <a class="boton secundario boton-factura-detalle" href="<?= h($baseUrl) ?>/php/facturas/imprimir_factura.php?id=<?= (int)$fila['id_factura'] ?>">Ver factura</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php endif; ?>

<?php require __DIR__ . '/../includes/pie.php'; ?>
