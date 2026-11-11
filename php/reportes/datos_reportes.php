<?php
/*
 * ============================================================================
 * MODULO  REPORTES E IMPRESION
 * ============================================================================
 * Transforma los datos de ventas, clientes e inventario en consultas para revisar la
 * tienda. Las consultas conservan las reglas de cada reporte; la presentación ofrece
 * impresión y guardado PDF mediante el navegador.
 *
 * RESPONSABILIDAD DE ESTE ARCHIVO
 * Centraliza columnas y consultas adicionales: los precios de ventas salen del
 * detalle histórico. Las claves de reporte se validan contra una lista fija.
 * ============================================================================
 */

/**
 * Archivo: datos_reportes.php
 * Módulo: Reportes — catálogo y acceso a datos.
 * Propósito: centralizar los reportes adicionales adaptados a la base de la-unica-2.
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
            'fuente' => 'Consulta de las tablas de esta versión',
            'nota' => 'El conteo incluye facturas Pagado y Cancelado.',
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
            'descripcion' => 'Suma cantidad por precio de venta de cada factura Pagado.',
            'fuente' => 'Consulta de las tablas de esta versión',
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
            'descripcion' => 'Suma las cantidades vendidas de cada producto en facturas Pagado.',
            'fuente' => 'Consulta de las tablas de esta versión',
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
            'fuente' => 'Consulta de las tablas de esta versión',
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
            'fuente' => 'Consulta de las tablas de esta versión',
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
            'fuente' => 'Consulta de las tablas de esta versión',
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
            'descripcion' => 'Consulta de compatibilidad con la otra versión del proyecto. La Única 2 no implementa ventas por cobrar.',
            'fuente' => 'Consulta de las tablas de esta versión',
            'nota' => 'Esta versión solo registra Pagado y Cancelado; no permite crear ventas por cobrar ni registrar pagos pendientes.',
            'vacio' => 'No aplicable: esta versión no admite facturas Pendiente.',
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
    $consultas = [
        'clientes' => "SELECT c.id_cliente,c.nombre,COUNT(f.id_factura) AS total_facturas FROM clientes c LEFT JOIN facturas f ON f.id_cliente=c.id_cliente GROUP BY c.id_cliente,c.nombre ORDER BY c.id_cliente",
        'total_facturas' => "SELECT f.id_factura,f.nombre_cliente AS cliente,f.estado_factura,SUM(d.cantidad*d.precio_unitario) AS total_factura FROM facturas f JOIN detalle_facturas d ON d.id_factura=f.id_factura WHERE f.estado_factura='Pagado' GROUP BY f.id_factura,f.nombre_cliente,f.estado_factura ORDER BY f.id_factura DESC",
        'unidades_productos' => "SELECT p.id_producto,p.nombre AS producto,SUM(d.cantidad) AS unidades_facturadas FROM productos p JOIN detalle_facturas d ON d.id_producto=p.id_producto JOIN facturas f ON f.id_factura=d.id_factura WHERE f.estado_factura='Pagado' GROUP BY p.id_producto,p.nombre ORDER BY unidades_facturadas DESC,p.id_producto",
        'detalle_facturas' => "SELECT f.id_factura,f.nombre_cliente AS cliente,f.estado_factura,f.fecha_factura,d.nombre_producto AS producto,d.cantidad,d.precio_unitario,d.cantidad*d.precio_unitario AS subtotal FROM facturas f JOIN detalle_facturas d ON d.id_factura=f.id_factura ORDER BY f.id_factura DESC,d.id_detalle_factura",
        'stock_productos' => "SELECT id_producto,nombre,stock,precio_unitario FROM productos ORDER BY stock,id_producto",
        'por_estado' => "SELECT estado_factura,COUNT(*) AS total_facturas FROM facturas GROUP BY estado_factura ORDER BY estado_factura",
        'pendientes' => "SELECT id_factura,nombre_cliente AS cliente,fecha_factura,estado_factura FROM facturas WHERE estado_factura='Pendiente' ORDER BY fecha_factura,id_factura",
    ];
    if (!isset($consultas[$tipoReporte])) {
        throw new InvalidArgumentException('El reporte solicitado no está disponible.');
    }
    return consultarFilasReporte($conexion, $consultas[$tipoReporte]);
}
