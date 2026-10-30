<?php
/**
 * Archivo: inicializar.php
 * Módulo: Recursos de información y ayuda.
 * Propósito: preparar la URL base y el escape HTML sin abrir una conexión a MySQL.
 * Funcionamiento: permite que el manual y las páginas informativas se consulten aunque la base de
 * datos no esté disponible, y proporciona lo necesario para reutilizar la plantilla común.
 */

require __DIR__ . '/../config/entorno.php';
