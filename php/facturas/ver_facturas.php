<?php
/**
 * Archivo: ver_facturas.php
 * Módulo: Facturas — consulta.
 * Propósito: buscar ventas y mostrar su estado, fecha, líneas y total vigente.
 * Funcionamiento: combina filtros opcionales por ID de factura, ID/nombre de cliente y fecha; construye
 * condiciones parametrizadas, agrupa detalles/productos y ordena los resultados más recientes primero.
 * Puntos clave: el importe se calcula con precios actuales del catálogo; los parámetros usan sentencias
 * preparadas y los enlaces abren el comprobante de cada factura.
 */

require __DIR__ . '/../config/conexion.php';

$titulo = 'Facturas';
$seccionActiva = 'facturas';
$terminoBusqueda = $_GET['buscar'] ?? '';
$terminoBusqueda = is_string($terminoBusqueda) ? trim($terminoBusqueda) : '';
$fechaBusqueda = $_GET['fecha'] ?? '';
$fechaBusqueda = is_string($fechaBusqueda) ? trim($fechaBusqueda) : '';
$errorFiltro = '';
$errorLectura = false;
$facturas = [];

if (longitudTexto($terminoBusqueda) > 100) {
    $errorFiltro = 'La búsqueda admite hasta 100 caracteres.';
} elseif ($fechaBusqueda !== '' && !validarFecha($fechaBusqueda)) {
    $errorFiltro = 'Ingresa una fecha válida en formato año-mes-día.';
}

$condiciones = [];
$tipos = '';
$parametros = [];
$idBusqueda = null;
if ($terminoBusqueda !== '' && ctype_digit($terminoBusqueda)) {
    $normalizado = ltrim($terminoBusqueda, '0');
    $normalizado = $normalizado === '' ? '0' : $normalizado;
    if (strlen($normalizado) <= 10) {
        $idBusqueda = validarId($normalizado);
    }
}

if ($terminoBusqueda !== '') {
    if ($idBusqueda !== null) {
        $condiciones[] = '(f.id_factura = ? OR f.id_cliente = ? OR c.nombre LIKE ?)';
        $tipos .= 'iis';
        $parametros[] = $idBusqueda;
        $parametros[] = $idBusqueda;
        $parametros[] = '%' . $terminoBusqueda . '%';
    } else {
        $condiciones[] = 'c.nombre LIKE ?';
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
    $sqlFacturas = 'SELECT f.id_factura, f.id_cliente, c.nombre AS cliente, f.estado_factura, f.fecha_factura, '
        . 'COUNT(d.id_detalle_factura) AS cantidad_lineas, '
        . 'COALESCE(SUM(d.cantidad * p.precio_unitario), 0.00) AS total_factura '
        . 'FROM facturas f INNER JOIN clientes c ON c.id_cliente = f.id_cliente '
        . 'LEFT JOIN detalle_facturas d ON d.id_factura = f.id_factura '
        . 'LEFT JOIN productos p ON p.id_producto = d.id_producto';
    if ($condiciones !== []) {
        $sqlFacturas .= ' WHERE ' . implode(' AND ', $condiciones);
    }
    $sqlFacturas .= ' GROUP BY f.id_factura, f.id_cliente, c.nombre, f.estado_factura, f.fecha_factura '
        . 'ORDER BY f.fecha_factura DESC, f.id_factura DESC';

    try {
        if ($parametros === []) {
            $resultadoFacturas = $conexion->query($sqlFacturas);
        } else {
            $consultaFacturas = $conexion->prepare($sqlFacturas);
            $argumentos = [$tipos];
            foreach ($parametros as $indice => &$parametro) {
                $argumentos[] = &$parametro;
            }
            unset($parametro);
            call_user_func_array([$consultaFacturas, 'bind_param'], $argumentos);
            $consultaFacturas->execute();
            $resultadoFacturas = $consultaFacturas->get_result();
        }

        $facturas = $resultadoFacturas->fetch_all(MYSQLI_ASSOC);
        $resultadoFacturas->free();
        if (isset($consultaFacturas)) {
            $consultaFacturas->close();
        }
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
        <a class="boton" href="crear_factura.php">Nueva factura</a>
    </div>
</div>

<form class="filtros-facturas" method="get" action="ver_facturas.php" role="search" aria-label="Buscar facturas">
    <div class="campo">
        <label for="buscar-facturas">Factura, ID de cliente o nombre</label>
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
        <p class="estado-vacio-texto"><?= $terminoBusqueda !== '' || $fechaBusqueda !== '' ? 'No hay resultados con esos criterios. Prueba con otro ID, nombre o fecha.' : 'Las ventas registradas aparecerán aquí para su consulta e impresión.' ?></p>
        <?php if ($terminoBusqueda !== '' || $fechaBusqueda !== ''): ?>
            <a class="boton secundario" href="ver_facturas.php">Limpiar búsqueda</a>
        <?php else: ?>
            <a class="boton" href="crear_factura.php">Registrar una venta</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <p class="facturas-conteo" role="status">Facturas encontradas: <?= count($facturas) ?></p>
    <div class="tabla-wrap tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Factura</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Estado</th>
                    <th scope="col" class="texto-centro">Líneas</th>
                    <th scope="col" class="texto-derecha">Total vigente</th>
                    <th scope="col" class="texto-derecha">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturas as $factura): ?>
                    <?php $fechaMostrar = is_string($factura['fecha_factura']) && $factura['fecha_factura'] !== '' ? (new DateTimeImmutable($factura['fecha_factura']))->format('d/m/Y H:i') : 'Sin fecha registrada'; ?>
                    <tr>
                        <td><span class="chip-id">#<?= (int)$factura['id_factura'] ?></span></td>
                        <td><span class="celda-fecha"><?= h($fechaMostrar) ?></span></td>
                        <td><span class="celda-destacada"><?= h($factura['cliente']) ?></span></td>
                        <td><span class="estado-factura <?= strtolower(h($factura['estado_factura'])) ?>"><?= h($factura['estado_factura']) ?></span></td>
                        <td class="texto-centro"><?= (int)$factura['cantidad_lineas'] ?></td>
                        <td class="texto-derecha celda-destacada"><?= h(formatearMonedaCop($factura['total_factura'])) ?></td>
                        <td class="texto-derecha"><a class="boton boton-secundario boton-factura-detalle" href="imprimir_factura.php?id=<?= (int)$factura['id_factura'] ?>">Ver / imprimir</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
