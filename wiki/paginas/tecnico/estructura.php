<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>La estructura corresponde a <code>main</code>. Desde la raíz del repositorio puedes ejecutar <code>php tests/seguridad.php</code> sin SQL. Los resultados de ciclos anteriores se conservan en <code>referencias/QA/</code>.</p><h2 id="árbol">Estructura de carpetas</h2>
<pre class="highlight"><code class="language-text">index.php → main.php
terminos.php y procesar_terminos.php
README.md
assets/
  css/estilos.css
  fonts/ y LICENCIAS.txt
  img/logo.png
  js/facturas.js y js/formularios.js
  video/1.mp4
php/
  config/ conexion, entorno, configuracion, utilidades, seguridad, consultas, errores, paginacion
  includes/ menu.php y pie.php
  inicio/ clientes/ productos/ facturas/ reportes/ historial/ sobre_nosotros/
wiki/ index.php, includes/, paginas/, css/, js/, img/, herramientas/
referencias/ db.sql, dos scripts de datos ficticios, cuatro documentos Word/PDF y QA/
tests/seguridad.php
logs/ diagnóstico local</code></pre>

<h2 id="privados">Archivos privados</h2>
<p>conexion.local.php, credenciales y archivos .log no se publican. Las carpetas ignoradas que existan en un equipo no forman parte de la distribución. Las rutas de instalación son ejemplos y no obligan a usar la misma base de otro proyecto.</p>

<h2 id="portal-inicial">Portal inicial</h2>
<p>index.php, main.php y las rutas operativas comprueban la aceptación antes de consultar SQL. terminos.php presenta cinco pasos y procesar_terminos.php valida POST y CSRF para aceptar, rechazar o revocar. La aceptación queda en la sesión o en una cookie de 30 días con la ruta de la instalación. El operador entra sin cuenta.</p>
