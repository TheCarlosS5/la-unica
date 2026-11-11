<?php
/*
 * ============================================================================
 * MODULO  INICIO DEL APLICATIVO
 * ============================================================================
 * Presenta los accesos principales y orienta al operador hacia clientes, productos,
 * facturas, reportes e información del proyecto.
 *
 * RESPONSABILIDAD DE ESTE ARCHIVO
 * Delega la pantalla de entrada al módulo de Inicio.
 * ============================================================================
 */

/**
 * Archivo: main.php
 * Módulo: Enrutamiento inicial.
 * Propósito: delegar la página principal al módulo de Inicio.
 * Funcionamiento: carga php/inicio/main.php usando una ruta absoluta basada en este archivo.
 * Punto clave: la estructura visual y los datos compartidos se preparan en el módulo incluido.
 */

require __DIR__ . '/php/inicio/main.php';