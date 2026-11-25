<?php
/**
 * Archivo: main.php
 * Módulo: Enrutamiento inicial hacia el panel de control.
 * Propósito: verificar la aceptación de términos y condiciones antes de
 *            delegar la página principal al módulo de Inicio.
 */

declare(strict_types=1);

require_once __DIR__ . '/php/config/entorno.php';
require_once __DIR__ . '/php/config/seguridad.php';
exigirAceptacionTerminos();

// Cargar página principal del sistema
require __DIR__ . '/php/inicio/main.php';
