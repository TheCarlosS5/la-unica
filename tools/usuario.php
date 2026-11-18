<?php
/** Administración local de cuentas; nunca se ejecuta por HTTP. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../php/config/autenticacion.php';
$nombre = $argv[1] ?? '';
$rol = $argv[2] ?? '';
if (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/D', $nombre) || !in_array($rol, ['administrador', 'operador', 'consulta', 'eliminar'], true)) {
    fwrite(STDERR, "Uso: php tools/usuario.php nombre administrador|operador|consulta|eliminar\n");
    exit(1);
}
$cuenta = null;
if ($rol !== 'eliminar') {
    fwrite(STDERR, "Contraseña (12 a 72 bytes; la entrada de consola es visible): ");
    $clave = rtrim((string)fgets(STDIN), "\r\n");
    if (strlen($clave) < 12 || strlen($clave) > 72) { fwrite(STDERR, "Longitud de contraseña no válida.\n"); exit(1); }
    $cuenta = ['rol' => $rol, 'hash' => password_hash($clave, PASSWORD_DEFAULT)];
    unset($clave);
}
$bloqueo = fopen(ARCHIVO_USUARIOS . '.lock', 'c');
if (!$bloqueo || !flock($bloqueo, LOCK_EX)) { throw new RuntimeException('No se pudo bloquear el archivo de cuentas.'); }
$usuarios = cargarUsuarios();
if ($cuenta) { $usuarios[$nombre] = $cuenta; } else { unset($usuarios[$nombre]); }
// Archivo temporal y renombrado: las peticiones nunca leen contenido parcial.
$temporal = tempnam(dirname(ARCHIVO_USUARIOS), 'usuarios-');
file_put_contents($temporal, "<?php\nreturn " . var_export($usuarios, true) . ";\n");
chmod($temporal, 0600);
if (!rename($temporal, ARCHIVO_USUARIOS)) { throw new RuntimeException('No se pudo guardar la cuenta.'); }
flock($bloqueo, LOCK_UN);
fclose($bloqueo);
echo "Cuenta actualizada. Sus sesiones anteriores quedan invalidadas.\n";
