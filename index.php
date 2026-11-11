<?php
/*
 * ============================================================================
 * MODULO  INICIO DEL APLICATIVO
 * ============================================================================
 * Presenta los accesos principales y orienta al operador hacia clientes, productos,
 * facturas, reportes e información del proyecto.
 *
 * RESPONSABILIDAD DE ESTE ARCHIVO
 * Punto de entrada: redirige hacia la pantalla principal sin registrar operaciones.
 * ============================================================================
 */

/**
 * Archivo: index.php
 * Módulo: Entrada de la aplicación.
 * Propósito: recibir las visitas a la raíz del proyecto y dirigirlas a la página principal.
 * Funcionamiento: responde con una redirección HTTP hacia main.php y termina la petición.
 * Punto clave: no abre la base de datos ni renderiza módulos; centraliza la URL de entrada.
 */

header('Location: main.php');
exit;