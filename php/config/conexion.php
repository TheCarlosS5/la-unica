<?php
mysqli_report(MYSQLI_REPORT_OFF);
$conexion = new mysqli('127.0.0.1', 'root', 'root', 'la_unica', 3306);
if ($conexion->connect_errno) {
    http_response_code(500);
    exit('Error de conexión a MySQL. Revisa que esté iniciado y que use el puerto 8000.');
}
$conexion->set_charset('utf8mb4');

$scriptDir = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/')));
$baseUrl = preg_replace('#/php/(clientes|productos|inicio)$#', '', $scriptDir);
if ($baseUrl === '/' || $baseUrl === '.') $baseUrl = '';
$baseUrl = rtrim($baseUrl, '/');

function h($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function id_valido($valor) {
    $id = filter_var($valor, FILTER_VALIDATE_INT);
    return $id !== false && $id > 0 && $id <= 2147483647 ? $id : null;
}
function fecha_valida($valor) {
    $fecha = DateTime::createFromFormat('!Y-m-d', (string)$valor);
    return $fecha !== false && $fecha->format('Y-m-d') === $valor;
}
function texto_largo($valor) {
    return function_exists('mb_strlen') ? mb_strlen($valor, 'UTF-8') : strlen($valor);
}