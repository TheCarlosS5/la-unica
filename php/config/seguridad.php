<?php
/** Sesión y tokens CSRF compartidos por los formularios. */
/**
 * Inicia la sesión PHP si todavía no está activa.
 * @return bool Indica si la sesión quedó activa.
 */
function sesionDisponible() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        ini_set('session.use_strict_mode', '1');
        session_name('la_unica_' . substr(hash('sha256', dirname(__DIR__, 2)), 0, 12));
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => ($GLOBALS['baseUrl'] ?? '') . '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
    return session_status() === PHP_SESSION_ACTIVE;
}
/**
 * Recupera o crea el token CSRF de la sesión para los formularios con cambios.
 * @return string|null Token hexadecimal o null si la sesión no está disponible.
 */
function obtenerTokenCsrf() {
    if (!sesionDisponible()) {
        return null;
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
/**
 * Compara el token recibido con el de la sesión.
 * @param mixed $tokenRecibido Token enviado por el formulario.
 * @return bool Resultado de la validación.
 */
function validarTokenCsrf($tokenRecibido) {
    $tokenEsperado = obtenerTokenCsrf();
    return is_string($tokenRecibido) && $tokenEsperado !== null && hash_equals($tokenEsperado, $tokenRecibido);
}

// Se conservan los scripts/estilos inline existentes; recursos externos y framing quedan restringidos.
if (PHP_SAPI !== 'cli' && !headers_sent()) {
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: same-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; media-src 'self'; object-src 'self'; frame-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'");
    header('Cache-Control: no-store');
}
