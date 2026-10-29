<?php
/**
 * Archivo: index.php
 * Módulo: Entrada de la aplicación.
 * Propósito: recibir las visitas a la raíz del proyecto y dirigirlas a la página principal.
 * Funcionamiento: responde con una redirección HTTP hacia main.php y termina la petición.
 * Punto clave: no abre la base de datos ni renderiza módulos; centraliza la URL de entrada.
 */

header('Location: main.php');
exit;