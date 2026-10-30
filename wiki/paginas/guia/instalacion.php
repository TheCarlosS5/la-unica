<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="requisitos">Requisitos</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Componente</th>
<th>Versión</th>
<th>Notas</th>
</tr>
</thead>
<tbody>
<tr>
<td>Apache</td>
<td>cualquiera reciente</td>
<td>Viene con Laragon.</td>
</tr>
<tr>
<td>PHP</td>
<td>8.x recomendado</td>
<td>Extensiones <code>mysqli</code>, <code>mysqlnd</code>, <code>session</code> y preferiblemente <code>mbstring</code>.</td>
</tr>
<tr>
<td>MySQL</td>
<td><strong>8.0.16 o superior</strong></td>
<td>Necesario para los <code>CHECK</code> y <code>DEFAULT (CURRENT_DATE)</code> del esquema.</td>
</tr>
<tr>
<td>Navegador</td>
<td>moderno</td>
<td>Chrome, Edge, Firefox, Brave…</td>
</tr>
</tbody>
</table></div>
<p><a href="https://laragon.org" target="_blank" rel="noopener">Laragon</a> es el entorno documentado por el equipo: trae Apache, PHP y MySQL listos.</p>
<h2 id="instalar">Instalar</h2>
<div class="pasos">
<ol>
<li>
<p><strong>Descarga el código.</strong> Con Git:</p>
<pre class="highlight"><code class="language-powershell">cd C:\laragon\www
git clone https://github.com/TheCarlosS5/la-unica.git</code></pre>
<p>O sin Git: en GitHub pulsa <strong>Code → Download ZIP</strong> y descomprime la carpeta como <code>C:\laragon\www\la-unica</code>.</p>
</li>
<li>
<p><strong>Inicia Laragon</strong> y pulsa <strong>Start All</strong> para encender Apache y MySQL.</p>
</li>
<li>
<p><strong>Revisa la conexión a MySQL.</strong> Por defecto la aplicación usa <code>127.0.0.1</code>, puerto <strong><code>3306</code></strong> (el de Laragon), usuario <code>root</code> sin contraseña. Si tu MySQL es distinto, copia <code>php/config/conexion.local.example.php</code> como <code>conexion.local.php</code> y ajústalo (ver <a href="index.php?pagina=configuracion/base-de-datos">Base de datos</a>).</p>
</li>
<li>
<p><strong>Crea la base de datos</strong> importando <code>referencias/db.sql</code> una sola vez. Con HeidiSQL (<em>Laragon → Database</em>): <strong>Archivo → Ejecutar archivo SQL…</strong> y elige <code>referencias\db.sql</code>. Con la consola:</p>
<pre class="highlight"><code class="language-powershell">cd C:\laragon\www\la-unica
mysql -u root -h 127.0.0.1 -P 3306 --default-character-set=utf8mb4 &lt; referencias\db.sql</code></pre>
</li>
<li>
<p><strong>Recomendado: crea el usuario de la aplicación.</strong> Cambia la contraseña dentro de <code>referencias/usuario_app.sql</code>, ejecútalo como <code>root</code> y escribe ese usuario y contraseña en <code>php/config/conexion.local.php</code>. Así la app no trabaja con el administrador de MySQL.</p>
</li>
<li>
<p><strong>Limita Apache a este equipo.</strong> La app no tiene usuarios ni contraseñas, así que nadie más en la red debe poder abrirla. En <em>Laragon → Menú → Apache → httpd.conf</em> cambia <code>Listen 80</code> por <code>Listen 127.0.0.1:80</code> y reinicia Apache. El archivo <code>.htaccess</code> del proyecto, además, oculta <code>.git</code>, los scripts SQL y la configuración.</p>
</li>
<li>
<p><strong>Abre la aplicación</strong> en el navegador. Con Laragon suele ser <code>http://la-unica.test/</code> o <code>http://localhost/la-unica/</code>. La raíz (<code>index.php</code>) te lleva al panel de inicio.</p>
</li>
</ol>
</div>
<div class="aviso aviso-caution">
<p class="aviso-titulo">Precaución</p>
<p>El script <code>db.sql</code> empieza con <code>CREATE DATABASE la_unica</code>. Si la base ya existe, fallará. No es un script de actualización ni de reinicio, y <strong>no trae datos de ejemplo</strong>.</p>
</div>
<p>¿Quieres datos para probar? Ejecuta además <a href="index.php?pagina=tecnico/wiki#datos-de-ejemplo"><code>wiki/herramientas/datos_ejemplo.sql</code></a> y tendrás clientes, productos y facturas como los de las capturas.</p>
<p>La wiki queda disponible en la misma instalación, en <code>…/la-unica/wiki/</code> (o <em>Menú → Información → Wiki</em>). No necesita configuración ni base de datos.</p>
<h2 id="instalar-sin-laragon-avanzado">Instalar sin Laragon (avanzado)</h2>
<p>Si ya tienes PHP y MySQL sueltos, puedes usar el servidor embebido de PHP:</p>
<pre class="highlight"><code class="language-powershell">cd C:\ruta\a\la-unica
php -S 127.0.0.1:8080 -t .</code></pre>
<p>y abrir <code>http://127.0.0.1:8080/</code>. Asegúrate de que <code>php -m</code> liste <code>mysqli</code>.</p>
<h2 id="estructura-que-debes-tener">Estructura que debes tener</h2>
<div class="arbol">
<ul>
<li>la-unica/<ul>
<li>index.php redirige a main.php</li>
<li>main.php</li>
<li>assets/<ul>
<li>css/estilos.css</li>
<li>fonts/</li>
<li>img/logo.png</li>
<li>js/facturas.js</li>
<li>video/1.mp4 opcional</li>
</ul>
</li>
<li>php/<ul>
<li>config/conexion.php valores por defecto (no lo edites)</li>
<li>config/conexion.local.php <strong>tu configuración</strong> (cópialo de conexion.local.example.php)</li>
<li>clientes/</li>
<li>productos/</li>
<li>facturas/</li>
<li>reportes/</li>
<li>sobre_nosotros/</li>
<li>…</li>
</ul>
</li>
<li>referencias/<ul>
<li>db.sql</li>
<li>manual.pdf</li>
<li>SRS.md</li>
</ul>
</li>
</ul>
</li>
</ul>
</div>
<h2 id="actualizar">Actualizar</h2>
<div class="pasos">
<ol>
<li>Haz una copia de tu base de datos (HeidiSQL → <em>Exportar base de datos como SQL</em>, o <code>mysqldump -u root -P 3306 --routines la_unica &gt; respaldo.sql</code>).</li>
<li>Tu configuración está en <code>php/config/conexion.local.php</code>, que Git no toca. Si en una versión anterior editaste <code>conexion.php</code> directamente, pasa tus valores a <code>conexion.local.php</code>.</li>
<li>
<p>Descarga la versión nueva:</p>
<pre class="highlight"><code class="language-powershell">cd C:\laragon\www\la-unica
git pull</code></pre>
</li>
<li>
<p>Aplica las migraciones nuevas de <code>referencias/migraciones/</code>, en orden y una sola vez. Si tu base se creó antes de que existiera el precio de venta en <code>detalle_facturas</code>, ejecuta <code>001_precio_historico.sql</code>.</p>
</li>
<li><strong>No</strong> vuelvas a importar <code>db.sql</code> sobre una base existente: es solo para instalaciones nuevas.</li>
</ol>
</div>
<h2 id="desinstalar">Desinstalar</h2>
<div class="pasos">
<ol>
<li>
<p>Borra la base de datos (esto elimina <strong>todos</strong> los datos):</p>
<pre class="highlight"><code class="language-sql">DROP DATABASE la_unica;</code></pre>
</li>
<li>
<p>Elimina la carpeta <code>C:\laragon\www\la-unica</code>.</p>
</li>
<li>Si creaste el usuario <code>la_unica_app</code>, bórralo con <code>DROP USER 'la_unica_app'@'localhost', 'la_unica_app'@'127.0.0.1';</code>.</li>
</ol>
</div>
