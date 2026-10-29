<?php
/**
 * Archivo: inicializar.php
 * Módulo: Recursos de información y ayuda.
 * Propósito: definir la URL base y el escape HTML sin abrir una conexión a MySQL.
 * Funcionamiento: permite que el manual y las páginas informativas se consulten aunque la base de
 * datos no esté disponible, y proporciona lo necesario para reutilizar la plantilla común.
 */

$scriptDir = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/')));
$baseUrl = preg_replace('#/php/(clientes|productos|facturas|reportes|inicio|sobre_nosotros)$#', '', $scriptDir);
if ($baseUrl === '/' || $baseUrl === '.') {
    $baseUrl = '';
}
$baseUrl = rtrim($baseUrl, '/');

if (!function_exists('h')) {
    function h($valor): string
    {
        return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
