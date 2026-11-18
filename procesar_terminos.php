<?php
/**
 * Archivo: procesar_terminos.php
 * Módulo: Procesamiento de aceptación de términos y condiciones.
 * Propósito: Gestionar la acción del usuario (Aceptar o Rechazar) mediante POST,
 *            establecer la sesión y cookie de permanencia, y redirigir con fluidez.
 */

declare(strict_types=1);

require_once __DIR__ . '/php/config/entorno.php';
require_once __DIR__ . '/php/config/seguridad.php';
sesionDisponible();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Allow: POST'); http_response_code(405); exit('Método no permitido.');
}
if (!validarTokenCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403); exit('Solicitud no válida. Actualiza la página de términos.');
}
$acciones = (int)isset($_POST['aceptar']) + (int)isset($_POST['rechazar']);
if ($acciones !== 1) { http_response_code(400); exit('Selecciona una sola acción.'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Caso 1: Aceptación de términos
    if (isset($_POST['aceptar'])) {
        $_SESSION['terminos_aceptados'] = true;
        $_SESSION['fecha_aceptacion_terminos'] = date('Y-m-d H:i:s');

        // Cookie de conveniencia por 30 días para no forzar la re-aceptación continua en el equipo local
        setcookie('la_unica_terminos', 'aceptados', [
            'expires'  => time() + (86400 * 30),
            'path'     => $baseUrl . '/',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        header('Location: main.php');
        exit;
    }

    // Caso 2: Rechazo de términos
    if (isset($_POST['rechazar'])) {
        unset($_SESSION['terminos_aceptados']);
        unset($_SESSION['fecha_aceptacion_terminos']);

        // Eliminar cookie persistente
        setcookie('la_unica_terminos', '', [
            'expires'  => time() - 3600,
            'path'     => $baseUrl . '/',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        header('Location: terminos.php?rechazado=1');
        exit;
    }
}

// Acceso directo por GET no permitido: redirige a la pantalla de términos
header('Location: terminos.php');
exit;
