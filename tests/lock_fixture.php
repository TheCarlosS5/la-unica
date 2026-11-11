<?php
/** Retiene una fila de la base de pruebas para verificar dos solicitudes HTTP en espera. */
require __DIR__ . '/bootstrap.php';
if (PHP_SAPI !== 'cli' || count($argv) !== 5 || !in_array($argv[1], ['productos', 'facturas'], true)) {
    exit(1);
}
$tabla = $argv[1];
$columna = $tabla === 'productos' ? 'id_producto' : 'id_factura';
$id = filter_var($argv[2], FILTER_VALIDATE_INT);
if ($id === false || $id <= 0) { exit(1); }
$db = conexionPruebas();
try {
    $db->begin_transaction();
    $consulta = $db->prepare("SELECT $columna FROM $tabla WHERE $columna = ? FOR UPDATE");
    $consulta->bind_param('i', $id);
    $consulta->execute();
    if ($consulta->get_result()->num_rows !== 1) { throw new RuntimeException('Fila de prueba ausente'); }
    file_put_contents($argv[3], 'locked');
    $limite = microtime(true) + 10;
    while (!is_file($argv[4]) && microtime(true) < $limite) { usleep(10000); clearstatcache(); }
    if (!is_file($argv[4])) { throw new RuntimeException('No se liberó el bloqueo'); }
} finally {
    $db->rollback();
    $db->close();
}
