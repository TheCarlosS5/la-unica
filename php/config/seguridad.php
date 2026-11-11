<?php
/** Sesión y tokens CSRF compartidos por los formularios. */
/**
 * Inicia la sesión PHP si todavía no está activa.
 * @return bool Indica si la sesión quedó activa.
 */
function sesionDisponible() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
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
