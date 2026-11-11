<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="privada">Configuración privada</h2>
<p>Copia php/config/conexion.local.example.php como php/config/conexion.local.php. El archivo devuelve un arreglo con host, puerto, base_datos, usuario y contrasena. Usa una base propia para cada copia del proyecto y un usuario con los permisos necesarios. No publiques el archivo privado.</p>
<h2 id="prioridad">Prioridad de valores</h2>
<p>Las variables DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME y DB_PASSWORD prevalecen sobre el archivo local; después se aplican los valores predeterminados. No se carga un archivo .env automáticamente. El puerto debe ser un entero entre 1 y 65535.</p>
<p>configuracion.php lee y valida los valores; conexion.php abre mysqli, usa utf8mb4 y fija SQL en UTC−05:00. Ante un fallo, la respuesta pública es genérica y el detalle se registra en logs/errores.log. No incluyas contraseñas en capturas o incidencias.</p>
