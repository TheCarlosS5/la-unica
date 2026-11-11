<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="entorno">Entorno requerido</h2>
<p>PHP 8.2 o posterior con mysqli, mysqlnd y mbstring. Apache debe permitir .htaccess mediante AllowOverride. MySQL 8.0.16 o posterior o MariaDB compatible debe aplicar las restricciones CHECK del esquema. Las pruebas automatizadas usan PHP 8.3 y MariaDB 11.4 en CI.</p>
<h2 id="hora">Sesiones y hora</h2>
<p>entorno.php fija America/Bogota y calcula la URL base según la ruta servida. Las conexiones SQL usan UTC−05:00 para mantener coherentes la fecha de venta y la cancelación del mismo día. PHP necesita sesiones operativas para los tokens CSRF y mensajes de confirmación.</p>
<h2 id="apache">Protección de archivos</h2>
<p>.htaccess bloquea configuración, Git, pruebas, logs y scripts internos. El servidor integrado de PHP usado por las pruebas no interpreta .htaccess y no debe emplearse como servidor público. Permite escritura a la carpeta logs y limita Apache y SQL al equipo local.</p>
