<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli('127.0.0.1', 'root', 'root', 'la_unica', 3306);
    $conexion->set_charset('utf8mb4');
} catch (Throwable $e) {
    http_response_code(500);
    exit('Error de conexión a la base de datos.');
}

$scriptDir = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/')));
$baseUrl = preg_replace('#/php/(clientes|productos|inicio)$#', '', $scriptDir);
if ($baseUrl === '/' || $baseUrl === '.') {
    $baseUrl = '';
}
$baseUrl = rtrim($baseUrl, '/');

if (!function_exists('escaparHtml')) {
    function escaparHtml($valor) {
        return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('h')) {
    function h($valor) {
        return escaparHtml($valor);
    }
}

if (!function_exists('validarId')) {
    function validarId($valor) {
        if ($valor === null || $valor === '') {
            return null;
        }
        $id = filter_var($valor, FILTER_VALIDATE_INT);
        return ($id !== false && $id > 0 && $id <= 2147483647) ? $id : null;
    }
}

if (!function_exists('id_valido')) {
    function id_valido($valor) {
        return validarId($valor);
    }
}

if (!function_exists('validarFecha')) {
    function validarFecha($valor) {
        if ($valor === null || $valor === '') {
            return false;
        }
        $fecha = DateTime::createFromFormat('!Y-m-d', (string)$valor);
        return $fecha !== false && $fecha->format('Y-m-d') === (string)$valor;
    }
}

if (!function_exists('fecha_valida')) {
    function fecha_valida($valor) {
        return validarFecha($valor);
    }
}

if (!function_exists('longitudTexto')) {
    function longitudTexto($valor) {
        if ($valor === null) {
            return 0;
        }
        return function_exists('mb_strlen') ? mb_strlen((string)$valor, 'UTF-8') : strlen((string)$valor);
    }
}

if (!function_exists('texto_largo')) {
    function texto_largo($valor) {
        return longitudTexto($valor);
    }
}
