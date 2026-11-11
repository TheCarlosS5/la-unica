<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>La estructura corresponde a main. No se publican tests, composer.json, workflows, AUTORES.md ni RELEASE.md en esta entrega; los resultados de validaciones anteriores se conservan como evidencia histórica.</p><h2 id="árbol">Estructura de carpetas</h2>
<pre class="highlight"><code class="language-text">index.php → main.php
README.md
assets/
  css/estilos.css
  fonts/ y LICENCIAS.txt
  img/logo.png
  js/facturas.js
  video/1.mp4
php/
  config/ conexion, entorno, configuracion, utilidades, seguridad, consultas, errores
  includes/ menu.php y pie.php
  inicio/ clientes/ productos/ facturas/ reportes/ historial/ sobre_nosotros/
wiki/ index.php, includes/, paginas/, css/, js/, img/, herramientas/
referencias/ db.sql, dos scripts de datos ficticios, cuatro documentos Word/PDF y QA/
logs/ diagnóstico local no publicado</code></pre>

<h2 id="privados">Archivos privados</h2>
<p>conexion.local.php, credenciales y archivos .log no se publican. Las carpetas ignoradas que existan en un equipo no forman parte de la distribución. Las rutas de instalación son ejemplos y no obligan a usar la misma base de otro proyecto.</p>

<h2 id="portal-inicial">Portal inicial</h2>
<p>index.php y main.php comprueban la aceptación. terminos.php muestra cinco pasos; procesar_terminos.php guarda o revoca sesión/cookie. Esta capa no autentica y los módulos mantienen rutas directas. La cookie dura 30 días, tiene ruta / y puede compartirse entre carpetas del mismo host.</p>
