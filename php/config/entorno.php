<?php
/**
 * Archivo: entorno.php
 * Módulo: Configuración compartida sin base de datos.
 * Propósito: calcular la URL base, enviar cabeceras de seguridad y definir el escape HTML.
 * Funcionamiento: lo cargan conexion.php y las páginas informativas que deben abrir aunque MySQL
 * esté apagado (sobre_nosotros/inicializar.php).
 */

date_default_timezone_set('America/Bogota');

if (!headers_sent()) {
    // Impide incrustar la aplicación en otra página (clickjacking) y que el navegador adivine tipos.
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
}

$scriptDir = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/')));
$baseUrl = preg_replace('#/php/[a-z_]+$#', '', $scriptDir);
if ($baseUrl === '/' || $baseUrl === '.') {
    $baseUrl = '';
}
$baseUrl = rtrim($baseUrl, '/');

if (!function_exists('h')) {
    /**
     * Codifica un valor como texto HTML seguro en UTF-8.
     * @param mixed $valor Valor proveniente de datos o entrada.
     * @return string Texto escapado para colocar en HTML.
     */
    function h($valor): string
    {
        return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
