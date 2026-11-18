<?php
require __DIR__ . '/php/config/entorno.php';
require __DIR__ . '/php/config/seguridad.php';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { header('Allow: POST'); http_response_code(405); exit; }
if (!validarTokenCsrf($_POST['csrf_token'] ?? null)) { http_response_code(403); exit('Solicitud no válida.'); }
$_SESSION = [];
session_regenerate_id(true);
header('Location: login.php', true, 303);
exit;
