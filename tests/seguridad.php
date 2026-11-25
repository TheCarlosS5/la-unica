<?php
/** Regresión sin base de datos ni cuentas: php tests/seguridad.php */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../php/config/entorno.php';
require __DIR__ . '/../php/config/utilidades.php';
require __DIR__ . '/../php/config/seguridad.php';
$pruebas = 0;
function verificar(bool $condicion, string $caso): void {
    global $pruebas;
    if (!$condicion) { fwrite(STDERR, 'FALLO: ' . $caso . PHP_EOL); exit(1); }
    $pruebas++;
}
verificar(!terminosAceptados(), 'Entrada sin aceptación');
$_SESSION['terminos_aceptados'] = true;
verificar(terminosAceptados(), 'Aceptación en sesión');
unset($_SESSION['terminos_aceptados']);
$_COOKIE['la_unica_terminos'] = 'aceptados';
verificar(terminosAceptados(), 'Aceptación recordada en el navegador');
unset($_COOKIE['la_unica_terminos']);
$token = obtenerTokenCsrf();
verificar(is_string($token) && strlen($token) === 64, 'Token aleatorio de 32 bytes');
verificar(validarTokenCsrf($token), 'CSRF válido');
foreach ([null, '', 'incorrecto', [], [$token]] as $invalido) {
    verificar(!validarTokenCsrf($invalido), 'CSRF inválido o manipulado');
}
verificar(validarEntero('2147483648') === null, 'Fuera de INT');
verificar(validarEntero('-1') === null, 'Existencias negativas');
verificar(validarEntero('0') === 0, 'Existencias cero');
verificar(validarId('0') === null, 'Documento cero');
verificar(validarFecha('2026-02-30') === false, 'Fecha inexistente');
verificar(leerTexto(['pagina' => ['2']], 'pagina') === '', 'Parámetro anidado');
echo $pruebas . " verificaciones correctas.\n";
