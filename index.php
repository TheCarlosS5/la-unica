<?php
/**
 * Archivo: index.php
 * Módulo: Entrada de la aplicación.
 * Propósito: controlar el acceso inicial a La Única.
 * Funcionamiento:
 * - Si el usuario ya aceptó los términos (sesión o cookie persistente), lo dirige a main.php.
 * - Si todavía no los aceptó, lo dirige a la pantalla de bienvenida y términos (terminos.php).
 */

declare(strict_types=1);

require_once __DIR__ . '/php/config/entorno.php';
require_once __DIR__ . '/php/config/seguridad.php';
header('Location: ' . (terminosAceptados() ? 'main.php' : 'terminos.php'), true, 303);
exit;
