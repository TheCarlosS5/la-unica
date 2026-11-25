<?php
/**
 * Archivo: ver_facturas.php
 * Módulo: Facturas — consulta.
 * Propósito: buscar ventas y mostrar su estado, fecha, líneas y total.
 * Funcionamiento: combina filtros opcionales por ID de factura, ID/nombre de cliente y fecha; construye
 * condiciones parametrizadas, agrupa detalles/productos y ordena los resultados más recientes primero.
 * Puntos clave: el importe y el nombre del cliente son los guardados en la venta; los parámetros usan sentencias
 * preparadas y los enlaces abren el comprobante de cada factura.
 */

require __DIR__ . '/../config/conexion.php';

$titulo = 'Facturas';
$seccionActiva = 'facturas';
$terminoBusqueda = leerTexto($_GET, 'buscar');
$fechaBusqueda = leerTexto($_GET, 'fecha');
$errorFiltro = '';
$errorLectura = false;
$facturas = [];
$paginacion = null;

if (longitudTexto($terminoBusqueda) > 100) {
    $errorFiltro = 'La búsqueda admite hasta 100 caracteres.';
} elseif ($fechaBusqueda !== '' && !validarFecha($fechaBusqueda)) {
    $errorFiltro = 'Ingresa una fecha válida en formato año-mes-día.';
}

$condiciones = [];
$tipos = '';
$parametros = [];
// Si lo escrito es un número válido, se busca también como número de factura o documento del cliente.
$idBusqueda = validarId($terminoBusqueda);

if ($terminoBusqueda !== '') {
    if ($idBusqueda !== null) {
        $condiciones[] = '(f.id_factura = ? OR f.id_cliente = ? OR f.nombre_cliente LIKE ?)';
        $tipos .= 'iis';
        $parametros[] = $idBusqueda;
        $parametros[] = $idBusqueda;
        $parametros[] = '%' . $terminoBusqueda . '%';
    } else {
        $condiciones[] = 'f.nombre_cliente LIKE ?';
        $tipos .= 's';
        $parametros[] = '%' . $terminoBusqueda . '%';
    }
}

if ($fechaBusqueda !== '') {
    $condiciones[] = 'f.fecha_factura >= ? AND f.fecha_factura < DATE_ADD(?, INTERVAL 1 DAY)';
    $tipos .= 'ss';
    $inicioFecha = $fechaBusqueda . ' 00:00:00';
    $parametros[] = $inicioFecha;
    $parametros[] = $inicioFecha;
}

if ($errorFiltro === '') {
    $sqlFacturas = 'SELECT f.id_factura, f.id_cliente, f.nombre_cliente AS cliente, f.estado_factura, f.fecha_factura, '
        . 'COUNT(d.id_detalle_factura) AS cantidad_lineas, '
        . 'COALESCE(SUM(d.cantidad * d.precio_unitario), 0.00) AS total_factura '
        . 'FROM facturas f '
        . 'LEFT JOIN detalle_facturas d ON d.id_factura = f.id_factura';
    if ($condiciones !== []) {
        $sqlFacturas .= ' WHERE ' . implode(' AND ', $condiciones);
    }
    $sqlFacturas .= ' GROUP BY f.id_factura, f.id_cliente, f.nombre_cliente, f.estado_factura, f.fecha_factura '
        . 'ORDER BY f.fecha_factura DESC, f.id_factura DESC';

    try {
        $paginacion = consultarPagina($conexion, $sqlFacturas, $tipos, $parametros);
        $facturas = $paginacion['filas'];
    } catch (Throwable $e) {
        registrarErrorServidor('leer_facturas', $e);
        $errorLectura = true;
        if (isset($consultaFacturas) && $consultaFacturas instanceof mysqli_stmt) {
            $consultaFacturas->close();
        }
    }
}

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
        <p class="encabezado-descripcion">Busca ventas por número, cliente o fecha y consulta su comprobante interno.</p>
    </div>
    <div class="encabezado-acciones">
        <a class="boton" href="crear_factura.php">Crear factura</a>
    </div>
</div>

<form class="filtros-facturas" method="get" action="ver_facturas.php" role="search" aria-label="Buscar facturas">
    <div class="campo">
        <label for="buscar-facturas">N.º de factura, documento o nombre del cliente</label>
        <input id="buscar-facturas" name="buscar" type="search" maxlength="100" value="<?= h($terminoBusqueda) ?>" placeholder="Ej. 120 o María Gómez" autocomplete="off">
    </div>
    <div class="campo">
        <label for="fecha-facturas">Fecha de la factura</label>
        <input id="fecha-facturas" name="fecha" type="date" value="<?= h($fechaBusqueda) ?>">
    </div>
    <div class="filtro-acciones">
        <button type="submit">Buscar</button>
        <?php if ($terminoBusqueda !== '' || $fechaBusqueda !== ''): ?>
            <a class="boton secundario" href="ver_facturas.php">Limpiar</a>
        <?php endif; ?>
    </div>
</form>

<?php if ($errorFiltro !== ''): ?>
    <div class="mensaje error alerta alerta-error" role="alert"><?= h($errorFiltro) ?></div>
<?php elseif ($errorLectura): ?>
    <div class="mensaje error alerta alerta-error" role="alert">No se pudieron cargar las facturas. Inténtalo de nuevo más tarde.</div>
<?php elseif ($facturas === []): ?>
    <div class="sin-datos estado-vacio" role="status">
        <h2 class="estado-vacio-titulo"><?= $terminoBusqueda !== '' || $fechaBusqueda !== '' ? 'No se encontraron facturas' : 'Todavía no hay facturas' ?></h2>
        <p class="estado-vacio-texto"><?= $terminoBusqueda !== '' || $fechaBusqueda !== '' ? 'No hay resultados con esos criterios. Prueba con otro número, documento, nombre o fecha.' : 'Las ventas registradas aparecerán aquí para su consulta e impresión.' ?></p>
        <?php if ($terminoBusqueda !== '' || $fechaBusqueda !== ''): ?>
            <a class="boton secundario" href="ver_facturas.php">Limpiar búsqueda</a>
        <?php else: ?>
            <a class="boton" href="crear_factura.php">Crear factura</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <?php mostrarPaginacion($paginacion); ?>
    <div class="tabla-wrap tabla-contenedor">
        <table class="tabla listado-adaptable" role="table">
            <thead role="rowgroup">
                <tr role="row">
                    <th role="columnheader" scope="col">Factura</th>
                    <th role="columnheader" scope="col">Fecha</th>
                    <th role="columnheader" scope="col">Cliente</th>
                    <th role="columnheader" scope="col">Estado</th>
                    <th role="columnheader" scope="col" class="texto-centro">Líneas</th>
                    <th role="columnheader" scope="col" class="texto-derecha">Total</th>
                    <th role="columnheader" scope="col" class="texto-derecha">Acciones</th>
                </tr>
            </thead>
            <tbody role="rowgroup">
                <?php foreach ($facturas as $factura): ?>
                    <?php $fechaMostrar = !empty($factura['fecha_factura']) ? (new DateTimeImmutable($factura['fecha_factura']))->format('d/m/Y H:i') : 'Sin fecha registrada'; ?>
                    <tr role="row">
                        <td role="cell"><span class="listado-etiqueta" aria-hidden="true">Factura</span><span class="chip-id">#<?= (int)$factura['id_factura'] ?></span></td>
                        <td role="cell"><span class="listado-etiqueta" aria-hidden="true">Fecha</span><span class="celda-fecha"><?= h($fechaMostrar) ?></span></td>
                        <td role="cell"><span class="listado-etiqueta" aria-hidden="true">Cliente</span><span class="celda-destacada"><?= h($factura['cliente']) ?></span></td>
                        <td role="cell"><span class="listado-etiqueta" aria-hidden="true">Estado</span><span class="estado-factura <?= strtolower(h($factura['estado_factura'])) ?>"><span aria-hidden="true"><?= $factura['estado_factura'] === 'Pagado' ? '✓' : '×' ?></span> <?= h($factura['estado_factura']) ?></span></td>
                        <td role="cell" class="texto-centro"><span class="listado-etiqueta" aria-hidden="true">Líneas</span><?= (int)$factura['cantidad_lineas'] ?></td>
                        <td role="cell" class="texto-derecha celda-destacada"><span class="listado-etiqueta" aria-hidden="true">Total</span><?= h(formatearMonedaCop($factura['total_factura'])) ?></td>
                        <td role="cell" class="listado-acciones texto-derecha"><span class="listado-etiqueta" aria-hidden="true">Acciones</span><a class="boton boton-secundario boton-factura-detalle" href="imprimir_factura.php?id=<?= (int)$factura['id_factura'] ?>">Ver / imprimir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php mostrarPaginacion($paginacion); ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
