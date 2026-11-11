<?php
/*
 * ============================================================================
 * MODULO  HISTORIAL Y DIAGNOSTICO
 * ============================================================================
 * Permite consultar las acciones realizadas y los errores registrados. El historial
 * de acciones usa SQL; el de errores lee un archivo para seguir disponible cuando
 * falla la conexión a la base.
 *
 * RESPONSABILIDAD DE ESTE ARCHIVO
 * Lee las líneas JSON del archivo de errores, filtra por fechas y presenta primero
 * las más recientes; no necesita conexión SQL.
 * ============================================================================
 */

/**
 * Archivo: errores.php
 * Módulo: Historial — log de errores.
 * Propósito: consultar los errores técnicos que registró registrarErrorServidor() en logs/errores.log.
 * Funcionamiento: lee el archivo (una línea JSON por error), filtra por rango de fechas (por defecto,
 * hoy) y muestra primero los más recientes. No se conecta a MySQL, para que se pueda consultar
 * incluso cuando la base de datos está caída. Solo consulta; no borra.
 */

define('SIN_BASE_DE_DATOS', true);
require __DIR__ . '/../config/conexion.php';

$titulo = 'Log de errores';
$seccionActiva = 'historial';
$hoy = date('Y-m-d');
$desde = trim((string)($_GET['desde'] ?? $hoy));
$hasta = trim((string)($_GET['hasta'] ?? $hoy));
$errorFiltro = '';
$errores = [];

if (!validarFecha($desde) || !validarFecha($hasta)) {
    $errorFiltro = 'Ingresa fechas válidas.';
} elseif ($desde > $hasta) {
    $errorFiltro = 'La fecha "desde" no puede ser posterior a la fecha "hasta".';
} elseif (is_file(RUTA_LOG_ERRORES)) {
    foreach (file(RUTA_LOG_ERRORES, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linea) {
        $registro = json_decode($linea, true);
        if (!is_array($registro) || !isset($registro['fecha'])) {
            continue; // Línea dañada o con otro formato.
        }
        $dia = substr($registro['fecha'], 0, 10);
        if ($dia >= $desde && $dia <= $hasta) {
            $errores[] = $registro;
        }
    }
    $errores = array_reverse($errores); // Más recientes primero.
}

/*
 * ============================================================================
 * PRESENTACION DEL MODULO
 * Con los datos y mensajes preparados, carga la navegación compartida y presenta el contenido de esta pantalla.
 * ============================================================================
 */
require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Historial</span>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Errores</span>
        </nav>
        <h1 class="encabezado-titulo">Log de errores</h1>
        <p class="encabezado-descripcion">Errores técnicos registrados por el aplicativo. Al operador se le muestra un mensaje amable; aquí queda el detalle para corregirlos.</p>
    </div>
    <div class="encabezado-acciones">
        <a class="boton secundario" href="acciones.php">Ver historial de acciones</a>
    </div>
</div>

<form class="filtros-facturas" method="get" action="errores.php" aria-label="Filtrar errores">
    <div class="campo">
        <label for="desde">Desde</label>
        <input id="desde" name="desde" type="date" value="<?= h($desde) ?>" required>
    </div>
    <div class="campo">
        <label for="hasta">Hasta</label>
        <input id="hasta" name="hasta" type="date" value="<?= h($hasta) ?>" required>
    </div>
    <div class="filtro-acciones">
        <button type="submit">Consultar</button>
        <a class="boton secundario" href="errores.php">Hoy</a>
    </div>
</form>

<?php if ($errorFiltro !== ''): ?>
    <div class="mensaje error alerta alerta-error" role="alert"><?= h($errorFiltro) ?></div>
<?php elseif ($errores === []): ?>
    <div class="sin-datos estado-vacio" role="status">
        <h2 class="estado-vacio-titulo">No hay errores registrados en este período</h2>
        <p class="estado-vacio-texto">Los errores se guardan en <code>logs/errores.log</code>.</p>
    </div>
<?php else: ?>
    <p class="facturas-conteo" role="status">Errores encontrados: <?= count($errores) ?></p>
    <div class="tabla-wrap tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Fecha y hora</th>
                    <th scope="col">Operación</th>
                    <th scope="col">Mensaje</th>
                    <th scope="col">Tipo y código</th>
                    <th scope="col">Ubicación</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($errores as $registro): ?>
                    <tr>
                        <td><span class="celda-fecha"><?= h(date('d/m/Y H:i:s', strtotime($registro['fecha']))) ?></span></td>
                        <td><?= h($registro['operacion'] ?? '') ?></td>
                        <td><?= h($registro['mensaje'] ?? '') ?></td>
                        <td><?= h(($registro['tipo'] ?? '') . ' (' . ($registro['codigo'] ?? '') . ')') ?></td>
                        <td><span class="celda-fecha"><?= h($registro['ubicacion'] ?? '') ?><br><?= h($registro['pagina'] ?? '') ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
