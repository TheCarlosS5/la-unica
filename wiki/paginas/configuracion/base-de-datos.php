<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>La conexión se abre en <code>php/config/conexion.php</code>. <strong>No edites ese archivo</strong> para tu equipo: cada valor se toma, en este orden, de</p>
<ol>
<li>una <strong>variable de entorno</strong> (<code>DB_HOST</code>, <code>DB_PORT</code>, <code>DB_DATABASE</code>, <code>DB_USERNAME</code>, <code>DB_PASSWORD</code>);</li>
<li>el archivo <code>php/config/conexion.local.php</code>, que Git ignora;</li>
<li>el valor por defecto de la tabla.</li>
</ol>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Clave en conexion.local.php</th>
<th>Valor por defecto</th>
<th>Significado</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>host</code></td>
<td><code>'127.0.0.1'</code></td>
<td>Equipo donde corre MySQL. Déjalo en local (ver <a href="index.php?pagina=tecnico/requisitos#requisitos-no-funcionales">RNF-03</a>).</td>
</tr>
<tr>
<td><code>puerto</code></td>
<td><code>3306</code></td>
<td><strong>Puerto</strong> de MySQL (el estándar de Laragon y XAMPP).</td>
</tr>
<tr>
<td><code>base_datos</code></td>
<td><code>'la_unica'</code></td>
<td>Nombre de la base que crea <code>db.sql</code>.</td>
</tr>
<tr>
<td><code>usuario</code></td>
<td><code>'root'</code></td>
<td>Usuario de MySQL. Recomendado: <code>la_unica_app</code> (ver abajo).</td>
</tr>
<tr>
<td><code>contrasena</code></td>
<td><code>''</code></td>
<td>Contraseña (vacía en Laragon para <code>root</code>).</td>
</tr>
</tbody>
</table></div>
<h2 id="cambiar-el-puerto">Cambiar el puerto u otros valores</h2>
<div class="pasos">
<ol>
<li>Averigua en qué puerto escucha tu MySQL. En Laragon: <em>Menú → MySQL → my.ini</em> y busca <code>port=</code>. Si es <code>3306</code>, no tienes que hacer nada.</li>
<li>
<p>Si es otro, copia <code>php/config/conexion.local.example.php</code> como <code>php/config/conexion.local.php</code> y ajusta los valores, por ejemplo:</p>
<pre class="highlight"><code class="language-php">return [
    'puerto' =&gt; 5000,
];</code></pre>
</li>
<li>
<p>Recarga cualquier página. Si ves la lista de clientes (aunque esté vacía), funciona.</p>
</li>
</ol>
</div>
<div class="aviso aviso-note">
<p class="aviso-titulo">¿No era 5000?</p>
<p>Hasta la versión anterior el puerto <code>5000</code> estaba fijo en el código, y la app no arrancaba en una instalación estándar (reporte de QA <em>BUG-001</em>). Ahora el valor por defecto es <code>3306</code> y cada equipo ajusta el suyo en <code>conexion.local.php</code> sin tocar el código compartido.</p>
</div>
<h2 id="usar-un-usuario-con-contraseña">Usar un usuario con contraseña (recomendado)</h2>
<p>No uses <code>root</code> para la aplicación: si alguien llega a la app, tendría control total de MySQL. El proyecto trae <code>referencias/usuario_app.sql</code>, que crea <code>la_unica_app</code> con permisos solo sobre <code>la_unica</code>:</p>
<pre class="highlight"><code class="language-sql">CREATE USER 'la_unica_app'@'localhost' IDENTIFIED BY 'cambia-esta-contrasena';
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON la_unica.* TO 'la_unica_app'@'localhost';</code></pre>
<p>Cambia la contraseña en el script, ejecútalo como <code>root</code> y pon el mismo usuario y contraseña en <code>conexion.local.php</code>:</p>
<pre class="highlight"><code class="language-php">return [
    'usuario' =&gt; 'la_unica_app',
    'contrasena' =&gt; 'cambia-esta-contrasena',
];</code></pre>
<p><code>EXECUTE</code> es necesario porque los reportes llaman <strong>procedimientos almacenados</strong>.</p>
<h2 id="juego-de-caracteres-y-zona-horaria">Juego de caracteres y zona horaria</h2>
<p>La conexión fija:</p>
<ul>
<li><code>utf8mb4</code> — tildes, eñes y emojis se guardan bien.</li>
<li><code>SET time_zone = '-05:00'</code> — MySQL guarda <code>fecha_factura</code> y <code>fecha_registro</code> en hora de Colombia, igual que PHP (<code>America/Bogota</code>).</li>
</ul>
<p>Si tu negocio está en otra zona horaria, cambia <strong>ambas</strong> líneas para que coincidan. Más en <a href="index.php?pagina=configuracion/servidor">Servidor y entorno</a>.</p>
<h2 id="qué-pasa-si-falla">Qué pasa si falla</h2>
<p>Si MySQL está apagado, el puerto es incorrecto o la base no existe, todas las páginas que usan datos muestran solo:</p>
<pre class="highlight"><code class="language-text">Error de conexión a la base de datos.</code></pre>
<p>con código HTTP 500. El detalle técnico <strong>no</strong> se muestra al usuario a propósito. La sección <strong>Sobre nosotros</strong> sí sigue funcionando sin base de datos, porque usa <code>inicializar.php</code> en vez de <code>conexion.php</code>. Ver <a href="index.php?pagina=guia/problemas#error-de-conexión-a-la-base-de-datos">Solución de problemas</a>.</p>
