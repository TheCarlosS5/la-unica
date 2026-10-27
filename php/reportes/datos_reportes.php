<?php
function catalogoReportes(): array
{
    return [
        'clientes' => [
            'archivo' => 'clientes_y_facturas.php',
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
            'archivo' => 'total_vigente_por_factura.php',
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
            'archivo' => 'unidades_facturadas_por_producto.php',
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
            'archivo' => 'detalle_facturas.php',
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
            'archivo' => 'existencias_productos.php',
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
            'archivo' => 'facturas_por_estado.php',
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
            'archivo' => 'facturas_pendientes.php',
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
}

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

function obtenerFilasReporte(mysqli $conexion, string $tipoReporte): array
{
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
            return $filasProcedimiento;

        case 'total_facturas':
            return consultarFilasReporte(
                $conexion,
                'SELECT id_factura, cliente, total_factura FROM vista_total_facturas ORDER BY id_factura DESC'
            );

        case 'unidades_productos':
            return consultarFilasReporte(
                $conexion,
                'SELECT id_producto, producto, unidades_facturadas FROM vista_unidades_productos ORDER BY unidades_facturadas DESC, id_producto'
            );

        case 'detalle_facturas':
            return consultarFilasReporte($conexion, 'CALL reporte_detalle_facturas()', true);

        case 'stock_productos':
            return consultarFilasReporte($conexion, 'CALL reporte_stock_productos()', true);

        case 'por_estado':
            return consultarFilasReporte($conexion, 'CALL reporte_facturas_por_estado()', true);

        case 'pendientes':
            return consultarFilasReporte($conexion, 'CALL reporte_facturas_pendientes()', true);
    }

    throw new InvalidArgumentException('El reporte solicitado no está disponible.');
}
