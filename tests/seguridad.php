<?php
/** Regresión sin base de datos ni cuentas: php tests/seguridad.php */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../php/config/entorno.php';
require __DIR__ . '/../php/config/utilidades.php';
require __DIR__ . '/../php/config/seguridad.php';
require __DIR__ . '/../php/config/autenticacion.php';
$pruebas = 0;
function verificar(bool $condicion, string $caso): void {
    global $pruebas;
    if (!$condicion) { fwrite(STDERR, 'FALLO: ' . $caso . PHP_EOL); exit(1); }
    $pruebas++;
}
$rutas = [
    '/php/clientes/ver_clientes.php' => ['administrador', 'operador', 'consulta'],
    '/php/productos/ver_productos.php' => ['administrador', 'operador', 'consulta'],
    '/php/reportes/ventas.php' => ['administrador', 'operador', 'consulta'],
    '/php/clientes/crear_cliente.php' => ['administrador', 'operador'],
    '/php/clientes/editar_cliente.php' => ['administrador', 'operador'],
    '/php/facturas/crear_factura.php' => ['administrador', 'operador'],
    '/php/facturas/cancelar_factura.php' => ['administrador'],
    '/php/productos/crear_producto.php' => ['administrador'],
    '/php/productos/editar_producto.php' => ['administrador'],
    '/php/productos/eliminar_producto.php' => ['administrador'],
    '/php/clientes/eliminar_cliente.php' => ['administrador'],
    '/php/historial/errores.php' => ['administrador'],
    '/php/historial/acciones.php' => ['administrador'],
];
foreach ($rutas as $ruta => $permitidos) {
    verificar(!puedeAcceder($ruta, null), 'Sin sesión ' . $ruta);
    foreach (['administrador', 'operador', 'consulta', 'rol-inexistente'] as $rol) {
        verificar(puedeAcceder($ruta, ['rol' => $rol]) === in_array($rol, $permitidos, true), $rol . ' ' . $ruta);
    }
}
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
