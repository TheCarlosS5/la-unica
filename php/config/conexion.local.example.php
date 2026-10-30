<?php
/**
 * Archivo: conexion.local.example.php
 * Propósito: plantilla de configuración local. Cópiala como conexion.local.php (ignorado por Git)
 * y ajusta los valores; las variables de entorno DB_* tienen prioridad sobre este archivo.
 * Recomendado: usar el usuario la_unica_app creado con referencias/usuario_app.sql en lugar de root.
 */

return [
    'host' => '127.0.0.1',
    'puerto' => 3306,
    'base_datos' => 'la_unica',
    'usuario' => 'la_unica_app',
    'contrasena' => 'cambia-esta-contrasena',
];
