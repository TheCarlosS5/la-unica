<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="ciclo">Ciclo de una página</h2>
<p>La aplicación usa PHP por módulos con mysqli, sin framework ni API separada. Cada entrada carga conexion.php, valida los parámetros, ejecuta consultas y presenta HTML mediante las plantillas compartidas. Las operaciones sensibles usan POST y CSRF; una operación completada redirige para evitar reenviar el formulario al recargar.</p>
<h2 id="configuracion">Responsabilidades compartidas</h2>
<p>conexion.php coordina el arranque; entorno.php define hora y URL; configuracion.php lee valores privados; utilidades.php valida y formatea; seguridad.php administra sesiones y CSRF; consultas.php encapsula historial y procedimientos; errores.php registra fallos y prepara respuestas seguras. La interfaz pública de las funciones se conserva para los módulos existentes.</p>
<p>Las transacciones de venta y cancelación se implementan en sus módulos. Las pruebas SQL y HTTP se ejecutan contra una base exclusiva; la wiki no conecta con SQL.</p>

<h2 id="guia-visual">Referencia visual de la versión actual</h2>
<p>Capturas reales con datos ficticios. Haz clic o pulsa Enter sobre una imagen para ampliarla.</p>
<figure class="captura"><img src="img/actuales/historial-acciones.jpg" alt="Trazabilidad — Acciones de los flujos ejecutados en la base ficticia." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Trazabilidad — Acciones de los flujos ejecutados en la base ficticia.</figcaption></figure>
