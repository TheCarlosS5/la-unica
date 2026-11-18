<?php
/** Cuentas locales con hashes; la aceptación de términos nunca autentica. */
const ARCHIVO_USUARIOS = __DIR__ . '/usuarios.local.php';

function cargarUsuarios(): array {
    $usuarios = is_file(ARCHIVO_USUARIOS) ? require ARCHIVO_USUARIOS : [];
    return is_array($usuarios) ? $usuarios : [];
}

function usuarioActual(): ?array {
    sesionDisponible();
    $sesion = $_SESSION['usuario'] ?? null;
    if (!is_array($sesion)) { return null; }
    $cuenta = cargarUsuarios()[$sesion['nombre'] ?? ''] ?? null;
    // Cambiar contraseña/rol o retirar una cuenta invalida sus sesiones existentes.
    if (!is_array($cuenta) || !hash_equals(hash('sha256', serialize($cuenta)), (string)($sesion['version'] ?? ''))
        || time() - (int)($_SESSION['ultima_actividad'] ?? 0) > 1800) {
        unset($_SESSION['usuario']);
        return null;
    }
    $_SESSION['ultima_actividad'] = time();
    return ['nombre' => $sesion['nombre'], 'rol' => $cuenta['rol']];
}

function puedeAcceder(string $ruta, ?array $usuario): bool {
    if (!$usuario) { return false; }
    $rol = $usuario['rol'] ?? '';
    if ($rol === 'administrador') { return true; }
    if (!in_array($rol, ['operador', 'consulta'], true)) { return false; }
    if (str_contains($ruta, '/historial/')) { return false; }
    $accion = basename($ruta);
    if (preg_match('/^(crear|editar|eliminar|cancelar)_/', $accion)) {
        return $rol === 'operador' && preg_match('#/php/(clientes/(crear|editar)_cliente|facturas/crear_factura)\.php$#D', $ruta) === 1;
    }
    return true;
}

function exigirAcceso(): void {
    global $baseUrl;
    $usuario = usuarioActual();
    if (!$usuario) {
        header('Location: ' . $baseUrl . '/login.php', true, 303);
        exit;
    }
    if (empty($_SESSION['terminos_aceptados']) && ($_COOKIE['la_unica_terminos'] ?? '') !== 'aceptados') {
        header('Location: ' . $baseUrl . '/terminos.php', true, 303);
        exit;
    }
    if (!puedeAcceder((string)($_SERVER['SCRIPT_NAME'] ?? ''), $usuario)) {
        http_response_code(403);
        exit('Tu cuenta no tiene permiso para esta operación. Vuelve al inicio.');
    }
}

/** Límite persistente por cuenta e IP; no se evade renovando la cookie. */
function intentarIngreso(string $nombre, string $clave): bool {
    $directorio = __DIR__ . '/../../logs';
    if (!is_dir($directorio) && !@mkdir($directorio, 0700, true)) { return false; }
    $ruta = $directorio . '/acceso-' . hash('sha256', $nombre . '|' . ($_SERVER['REMOTE_ADDR'] ?? 'local')) . '.php';
    $archivo = @fopen($ruta, 'c+');
    if (!$archivo) { return false; }
    if (!flock($archivo, LOCK_EX)) { fclose($archivo); return false; }
    $contenido = stream_get_contents($archivo);
    $estado = json_decode(substr($contenido, strlen("<?php exit; ?>\n")), true) ?: ['inicio' => time(), 'fallos' => 0];
    if (time() - $estado['inicio'] >= 300) { $estado = ['inicio' => time(), 'fallos' => 0]; }
    $cuenta = cargarUsuarios()[$nombre] ?? null;
    $hash = $cuenta['hash'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
    $valida = strlen($clave) <= 72 && password_verify($clave, $hash);
    $aceptado = $estado['fallos'] < 5 && $valida && is_array($cuenta)
        && in_array($cuenta['rol'] ?? '', ['administrador', 'operador', 'consulta'], true);
    $estado['fallos'] = $aceptado ? 0 : $estado['fallos'] + 1;
    rewind($archivo);
    ftruncate($archivo, 0);
    fwrite($archivo, "<?php exit; ?>\n" . json_encode($estado));
    fflush($archivo);
    flock($archivo, LOCK_UN);
    fclose($archivo);
    if ($aceptado) {
        session_regenerate_id(true);
        $_SESSION['usuario'] = ['nombre' => $nombre, 'version' => hash('sha256', serialize($cuenta))];
        $_SESSION['ultima_actividad'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $aceptado;
}
