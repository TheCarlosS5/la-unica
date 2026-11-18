<?php
require __DIR__ . '/php/config/entorno.php';
require __DIR__ . '/php/config/utilidades.php';
require __DIR__ . '/php/config/seguridad.php';
require __DIR__ . '/php/config/autenticacion.php';
$error = '';
if (usuarioActual()) { header('Location: main.php', true, 303); exit; }
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (!validarTokenCsrf($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        $error = 'La sesión del formulario venció. Actualiza la página e inténtalo de nuevo.';
    } elseif (intentarIngreso(leerTexto($_POST, 'usuario'), is_string($_POST['clave'] ?? null) ? $_POST['clave'] : '')) {
        header('Location: main.php', true, 303); exit;
    } else {
        $error = 'No se pudo iniciar sesión. Comprueba tus credenciales o espera cinco minutos si agotaste los intentos.';
    }
}
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Iniciar sesión · La Única</title><link rel="stylesheet" href="assets/css/estilos.css"></head>
<body><main class="acceso-panel formulario tarjeta-formulario">
<h1>Iniciar sesión</h1><p>La Única · Gestión comercial</p>
<?php if (!cargarUsuarios()): ?><p role="status">Solicita al responsable de la instalación que configure tu cuenta antes de ingresar.</p><?php endif; ?>
<?php if ($error !== ''): ?><p class="mensaje error" role="alert"><?= h($error) ?></p><?php endif; ?>
<form method="post">
<input type="hidden" name="csrf_token" value="<?= h(obtenerTokenCsrf()) ?>">
<div class="campo"><label for="usuario">Usuario</label><input id="usuario" name="usuario" maxlength="50" autocomplete="username" required autofocus></div>
<div class="campo"><label for="clave">Contraseña</label><input id="clave" name="clave" type="password" maxlength="72" autocomplete="current-password" required></div>
<button type="submit">Iniciar sesión</button>
</form></main></body></html>
