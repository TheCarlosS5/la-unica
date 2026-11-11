<?php
/**
 * Archivo: main.php
 * Módulo: Enrutamiento inicial hacia el panel de control.
 * Propósito: verificar la aceptación de términos y condiciones antes de
 *            delegar la página principal al módulo de Inicio.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Verificar aceptación de términos y condiciones (en sesión o cookie persistente)
$terminosAceptados = !empty($_SESSION['terminos_aceptados']) || (!empty($_COOKIE['la_unica_terminos']) && $_COOKIE['la_unica_terminos'] === 'aceptados');

if (!$terminosAceptados) {
    header('Location: terminos.php');
    exit;
}

// Mantener activa la marca en la sesión de PHP
$_SESSION['terminos_aceptados'] = true;

// Cargar página principal del sistema
require __DIR__ . '/php/inicio/main.php';
