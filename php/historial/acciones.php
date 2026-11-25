<?php
/**
 * Archivo: acciones.php
 * Módulo: Historial — log general de acciones.
 * Propósito: consultar lo que se ha hecho en el aplicativo (registros, ediciones, eliminaciones,
 * facturas y cancelaciones), guardado por registrarAccion() en la tabla log_acciones.
 * Funcionamiento: filtra por rango de fechas (por defecto, hoy) y por módulo. Solo consulta; no borra.
 */

require __DIR__ . '/../config/conexion.php';

$titulo = 'Historial de acciones';
$seccionActiva = 'historial';
$modulos = ['Clientes', 'Productos', 'Facturas'];
$hoy = date('Y-m-d');
$desde = trim((string)($_GET['desde'] ?? $hoy));
$hasta = trim((string)($_GET['hasta'] ?? $hoy));
$modulo = (string)($_GET['modulo'] ?? '');
$errorFiltro = '';
$errorLectura = false;
$acciones = [];

if (!validarFecha($desde) || !validarFecha($hasta)) {
    $errorFiltro = 'Ingresa fechas válidas.';
} elseif ($desde > $hasta) {
    $errorFiltro = 'La fecha "desde" no puede ser posterior a la fecha "hasta".';
} elseif ($modulo !== '' && !in_array($modulo, $modulos, true)) {
    $errorFiltro = 'El módulo seleccionado no es válido.';
} else {
    try {
        $consulta = $conexion->prepare(
            'SELECT fecha, modulo, accion, descripcion FROM log_acciones '
            . 'WHERE fecha >= ? AND fecha < ? + INTERVAL 1 DAY AND (? = \'\' OR modulo = ?) '
            . 'ORDER BY fecha DESC, id_log DESC'
        );
        $consulta->bind_param('ssss', $desde, $hasta, $modulo, $modulo);
        $consulta->execute();
        $acciones = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
        $consulta->close();
    } catch (Throwable $e) {
        registrarErrorServidor('leer_historial_acciones', $e);
        $errorLectura = true;
    }
}

require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Historial</span>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Acciones</span>
        </nav>
        <h1 class="encabezado-titulo">Historial de acciones</h1>
        <p class="encabezado-descripcion">Registro de lo que se ha hecho en el aplicativo: clientes, productos, facturas y cancelaciones.</p>
    </div>
    <div class="encabezado-acciones">
        <a class="boton secundario" href="errores.php">Ver log de errores</a>
    </div>
</div>

<form class="filtros-facturas filtros-historial" method="get" action="acciones.php" aria-label="Filtrar historial">
    <div class="campo">
        <label for="desde">Desde</label>
        <input id="desde" name="desde" type="date" value="<?= h($desde) ?>" required>
    </div>
    <div class="campo">
        <label for="hasta">Hasta</label>
        <input id="hasta" name="hasta" type="date" value="<?= h($hasta) ?>" required>
    </div>
    <div class="campo">
        <label for="modulo">Módulo</label>
        <select id="modulo" name="modulo">
            <option value="">Todos</option>
            <?php foreach ($modulos as $opcion): ?>
                <option value="<?= h($opcion) ?>"<?= $opcion === $modulo ? ' selected' : '' ?>><?= h($opcion) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filtro-acciones">
        <button type="submit">Consultar</button>
        <a class="boton secundario" href="acciones.php">Hoy</a>
    </div>
</form>

<?php if ($errorFiltro !== ''): ?>
    <div class="mensaje error alerta alerta-error" role="alert"><?= h($errorFiltro) ?></div>
<?php elseif ($errorLectura): ?>
    <div class="mensaje error alerta alerta-error" role="alert">No se pudo leer el historial. Revisa que la tabla log_acciones exista en la base de datos; el detalle quedó en el log de errores.</div>
<?php elseif ($acciones === []): ?>
    <div class="sin-datos estado-vacio" role="status">
        <h2 class="estado-vacio-titulo">No hay acciones registradas en este período</h2>
        <p class="estado-vacio-texto">Prueba con otro rango de fechas o con otro módulo.</p>
    </div>
<?php else: ?>
    <p class="facturas-conteo" role="status">Acciones encontradas: <?= count($acciones) ?></p>
    <div class="tabla-wrap tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Fecha y hora</th>
                    <th scope="col">Módulo</th>
                    <th scope="col">Acción</th>
                    <th scope="col">Descripción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($acciones as $registro): ?>
                    <tr>
                        <td><span class="celda-fecha"><?= h(date('d/m/Y H:i:s', strtotime($registro['fecha']))) ?></span></td>
                        <td><?= h($registro['modulo']) ?></td>
                        <td><span class="insignia-accion <?= strtolower(h($registro['accion'])) ?>"><?= h($registro['accion']) ?></span></td>
                        <td><?= h($registro['descripcion']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
