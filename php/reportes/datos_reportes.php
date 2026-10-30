<?php
/**
 * Archivo: datos_reportes.php
 * Módulo: Reportes — catálogo y acceso a datos.
 * Propósito: centralizar definición, columnas, textos, fuentes SQL y consulta de los siete reportes.
 * Funcionamiento: catalogoReportes() entrega metadatos; obtenerFilasReporte() selecciona la vista o
 * procedimiento correspondiente; consultarFilasReporte() lee resultados y limpia resultados adicionales.
 * Puntos clave: los totales y subtotales usan el precio guardado en cada línea de la venta; los reportes
 * de ventas (totales y unidades) excluyen las facturas canceladas.
 */

/**
 * Devuelve títulos, descripción, fuente de datos, columnas y reglas de los siete reportes.
 * @return array Catálogo indexado por la clave interna de cada reporte.
 */
function catalogoReportes(): array
{
    return [
        'clientes' => [
            'archivo' => 'clientes_y_facturas.php',
            'titulo' => 'Clientes y cantidad de facturas',
            'descripcion' => 'Cuenta las facturas de cada cliente e incluye a quienes todavía no tienen ventas.',
            'fuente' => 'Procedimiento reporte_facturas_clientes()',
            'nota' => 'El conteo incluye facturas Pagado, Cancelado y Pendiente.',
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
            'titulo' => 'Total por factura',
            'descripcion' => 'Suma cantidad por precio de venta de cada factura Pagado o Pendiente.',
            'fuente' => 'Vista vista_total_facturas',
            'nota' => 'Usa el precio registrado en cada venta. Las facturas canceladas y las que no tienen detalles no aparecen.',
            'vacio' => 'No hay ventas vigentes para calcular.',
            'columnas' => [
                ['campo' => 'id_factura', 'titulo' => 'Factura', 'tipo' => 'id'],
                ['campo' => 'cliente', 'titulo' => 'Cliente', 'tipo' => 'texto'],
                ['campo' => 'estado_factura', 'titulo' => 'Estado', 'tipo' => 'estado'],
                ['campo' => 'total_factura', 'titulo' => 'Total', 'tipo' => 'moneda'],
            ],
            'enlace_factura' => true,
        ],
        'unidades_productos' => [
            'archivo' => 'unidades_facturadas_por_producto.php',
            'titulo' => 'Unidades vendidas por producto',
            'descripcion' => 'Suma las cantidades vendidas de cada producto en facturas Pagado o Pendiente.',
            'fuente' => 'Vista vista_unidades_productos',
            'nota' => 'Excluye las facturas canceladas, cuyas unidades ya volvieron al inventario. Solo aparecen productos con al menos una venta vigente.',
            'vacio' => 'Todavía no hay productos vendidos.',
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
            'descripcion' => 'Consulta cada producto, cantidad, precio de venta y subtotal de sus facturas.',
            'fuente' => 'Procedimiento reporte_detalle_facturas()',
            'nota' => 'Incluye todos los estados (revisa la columna Estado) y ordena por ID de factura. Precio y subtotal son los registrados en la venta.',
            'vacio' => 'Todavía no hay detalles de factura.',
            'columnas' => [
                ['campo' => 'id_factura', 'titulo' => 'Factura', 'tipo' => 'id'],
                ['campo' => 'cliente', 'titulo' => 'Cliente', 'tipo' => 'texto'],
                ['campo' => 'estado_factura', 'titulo' => 'Estado', 'tipo' => 'estado'],
                ['campo' => 'fecha_factura', 'titulo' => 'Fecha', 'tipo' => 'fecha'],
                ['campo' => 'producto', 'titulo' => 'Producto', 'tipo' => 'texto'],
                ['campo' => 'cantidad', 'titulo' => 'Cantidad', 'tipo' => 'entero'],
                ['campo' => 'precio_unitario', 'titulo' => 'Precio de venta', 'tipo' => 'moneda'],
                ['campo' => 'subtotal', 'titulo' => 'Subtotal', 'tipo' => 'moneda'],
            ],
            'enlace_factura' => true,
        ],
        'stock_productos' => [
            'archivo' => 'existencias_productos.php',
            'titulo' => 'Existencias de productos',
            'descripcion' => 'Muestra stock y precio unitario vigente, desde la menor existencia.',
            'fuente' => 'Procedimiento reporte_stock_productos()',
            'nota' => 'El orden ascendente permite revisar primero las existencias más bajas. El precio mostrado es el precio actual del catálogo.',
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
            'descripcion' => 'Lista las ventas por cobrar (estado Pendiente), de la más antigua a la más reciente.',
            'fuente' => 'Procedimiento reporte_facturas_pendientes()',
            'nota' => 'Una venta queda Pendiente cuando se registra como «por cobrar». Desde su comprobante se puede registrar el pago o cancelarla.',
            'vacio' => 'No hay ventas por cobrar.',
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

/**
 * Ejecuta una consulta, recoge filas asociativas y libera todos sus resultados.
 * Cuando se invoca un procedimiento, drena también los conjuntos adicionales de MySQLi.
 * @param mysqli $conexion Conexión compartida del aplicativo.
 * @param string $consulta SELECT o llamada CALL definida por el reporte.
 * @param bool $esProcedimiento Indica si deben consumirse resultados adicionales.
 * @return array Filas devueltas por la primera consulta.
 */
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

/**
 * Despacha una clave de reporte a su vista o procedimiento almacenado.
 * @param mysqli $conexion Conexión compartida del aplicativo.
 * @param string $tipoReporte Clave definida en catalogoReportes().
 * @return array Filas normalizadas para la vista.
 * @throws InvalidArgumentException Si la clave no está disponible.
 */
function obtenerFilasReporte(mysqli $conexion, string $tipoReporte): array
{
    switch ($tipoReporte) {
        case 'clientes':
            return consultarFilasReporte($conexion, 'CALL reporte_facturas_clientes()', true);

        case 'total_facturas':
            return consultarFilasReporte(
                $conexion,
                'SELECT id_factura, cliente, estado_factura, total_factura FROM vista_total_facturas ORDER BY id_factura DESC'
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
