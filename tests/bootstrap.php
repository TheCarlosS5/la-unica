<?php
/** Conexión exclusiva de pruebas: nunca acepta la base de la aplicación. */
$testDatabase = getenv('DB_TEST_DATABASE') ?: '';
if (!preg_match('/^la_unica_test(?:_[a-zA-Z0-9_]+)?$/D', $testDatabase)) {
    throw new RuntimeException('DB_TEST_DATABASE debe ser una base exclusiva con prefijo la_unica_test.');
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set('America/Bogota');
function conexionPruebas(): mysqli {
    $db = new mysqli(getenv('DB_TEST_HOST') ?: '127.0.0.1', getenv('DB_TEST_USERNAME') ?: 'root', getenv('DB_TEST_PASSWORD') ?: '', getenv('DB_TEST_DATABASE'), (int)(getenv('DB_TEST_PORT') ?: 3306));
    $db->set_charset('utf8mb4');
    $db->query("SET time_zone = '-05:00'");
    return $db;
}
require_once __DIR__ . '/../php/reportes/datos_reportes.php';
