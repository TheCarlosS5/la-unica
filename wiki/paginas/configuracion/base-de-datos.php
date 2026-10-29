<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Toda la conexión vive en <strong>una sola línea</strong> de <code>php/config/conexion.php</code>:</p>
<p class="codigo-titulo">php/config/conexion.php</p>

<pre class="highlight"><code class="language-php">mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set('America/Bogota');

try {
    $conexion = new mysqli('127.0.0.1', 'root', '', 'la_unica', 5000);
    $conexion-&gt;set_charset('utf8mb4');
    $conexion-&gt;query("SET time_zone = '-05:00'");
} catch (Throwable $e) {
    http_response_code(500);
    exit('Error de conexión a la base de datos.');
}</code></pre>
<p>Los parámetros de <code>new mysqli(...)</code> son, en orden:</p>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>#</th>
<th>Valor por defecto</th>
<th>Significado</th>
</tr>
</thead>
<tbody>
<tr>
<td>1</td>
<td><code>'127.0.0.1'</code></td>
<td>Equipo donde corre MySQL. Déjalo en local (ver <a href="index.php?pagina=tecnico/requisitos#requisitos-no-funcionales">RNF-03</a>).</td>
</tr>
<tr>
<td>2</td>
<td><code>'root'</code></td>
<td>Usuario de MySQL.</td>
</tr>
<tr>
<td>3</td>
<td><code>''</code></td>
<td>Contraseña (vacía en Laragon).</td>
</tr>
<tr>
<td>4</td>
<td><code>'la_unica'</code></td>
<td>Nombre de la base que crea <code>db.sql</code>.</td>
</tr>
<tr>
<td>5</td>
<td><code>5000</code></td>
<td><strong>Puerto</strong> de MySQL.</td>
</tr>
</tbody>
</table></div>
<h2 id="cambiar-el-puerto">Cambiar el puerto</h2>
<div class="pasos">
<ol>
<li>Averigua en qué puerto escucha tu MySQL. En Laragon: <em>Menú → MySQL → my.ini</em> y busca <code>port=</code>. Por defecto es <code>3306</code>.</li>
<li>
<p>Cambia el último número de la línea de conexión, por ejemplo:</p>
<pre class="highlight"><code class="language-php">$conexion = new mysqli('127.0.0.1', 'root', '', 'la_unica', 3306);</code></pre>
</li>
<li>
<p>Recarga cualquier página. Si ves la lista de clientes (aunque esté vacía), funciona.</p>
</li>
</ol>
</div>
<div class="aviso aviso-note">
<p class="aviso-titulo">¿Por qué 5000?</p>
<p>El equipo eligió <code>5000</code> para no chocar con otros MySQL instalados en el mismo equipo (el <code>3306</code> de siempre). El README lo aclara: <em>el puerto depende del entorno local</em>. En el historial verás que antes se usó <code>8000</code>; la rama <code>revision-samuel</code> lo dejó definitivamente en <code>5000</code>.</p>
</div>
<h2 id="usar-un-usuario-con-contraseña">Usar un usuario con contraseña</h2>
<p>Es buena idea no usar <code>root</code>. En MySQL:</p>
<pre class="highlight"><code class="language-sql">CREATE USER 'la_unica'@'localhost' IDENTIFIED BY 'una-clave-segura';
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON la_unica.* TO 'la_unica'@'localhost';
FLUSH PRIVILEGES;</code></pre>
<p>y en <code>conexion.php</code>:</p>
<pre class="highlight"><code class="language-php">$conexion = new mysqli('127.0.0.1', 'la_unica', 'una-clave-segura', 'la_unica', 5000);</code></pre>
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
