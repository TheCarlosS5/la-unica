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

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Comprobación de aceptación (en sesión activa o cookie persistente del equipo)
$terminosAceptados = !empty($_SESSION['terminos_aceptados']) || (!empty($_COOKIE['la_unica_terminos']) && $_COOKIE['la_unica_terminos'] === 'aceptados');

if ($terminosAceptados) {
    $_SESSION['terminos_aceptados'] = true;
    header('Location: main.php');
    exit;
}

header('Location: terminos.php');
exit;
