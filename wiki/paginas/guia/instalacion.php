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
<td>8.2 o posterior</td>
<td>Extensiones <code>mysqli</code>, <code>mysqlnd</code>, <code>session</code> y <code>mbstring</code>, todas necesarias.</td>
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
<p><strong>Crea una base nueva y exclusiva.</strong> En una copia de <code>referencias/db.sql</code> sustituye <code>CREATE DATABASE la_unica</code> y <code>USE la_unica</code> por el mismo nombre nuevo. Importa esa copia una sola vez. Con HeidiSQL (<em>Laragon → Database</em>): <strong>Archivo → Ejecutar archivo SQL…</strong>. Con la consola, sustituye el puerto y usuario del ejemplo por los de tu equipo:</p>
<pre class="highlight"><code class="language-powershell">cd C:\laragon\www\la-unica
mysql -u USUARIO_INSTALACION -h 127.0.0.1 -P 3306 --default-character-set=utf8mb4 --password</code></pre>
<p>Dentro del cliente SQL, importa la copia preparada con el nombre exclusivo:</p>
<pre class="highlight"><code class="language-sql">SOURCE C:/ruta/privada/esquema_preparado.sql;</code></pre>
<p>Configura ese mismo nombre de base en <code>conexion.local.php</code>. No importes sobre una base con datos de otro proyecto.</p>
</li>
<li>
<p><strong>Recomendado: crea el usuario de la aplicación.</strong> El administrador crea una cuenta con SELECT, INSERT, UPDATE, DELETE y EXECUTE solo sobre esta base y configura sus valores privados en <code>php/config/conexion.local.php</code>. No hay un script usuario_app.sql en esta entrega. Así la app no trabaja con el administrador de MySQL.</p>
</li>
<li>
<p><strong>Limita Apache a este equipo.</strong> La app no tiene usuarios ni contraseñas, así que nadie más en la red debe poder abrirla. En <em>Laragon → Menú → Apache → httpd.conf</em> cambia <code>Listen 80</code> por <code>Listen 127.0.0.1:80</code> y reinicia Apache. El archivo <code>.htaccess</code> del proyecto, además, oculta <code>.git</code>, los scripts SQL y la configuración.</p>
</li>
<li>
<p><strong>Abre la aplicación</strong> en el navegador. Con Laragon suele ser <code>http://la-unica.test/</code> o <code>http://localhost/la-unica/</code>. La raíz (<code>index.php</code>) muestra el portal de términos si no hay aceptación guardada. Avanza y pulsa Aceptar y entrar para abrir el inicio. La sesión o una cookie de 30 días recuerdan la aceptación; no hay cuentas de usuario.</p>
</li>
</ol>
</div>
<div class="aviso aviso-caution">
<p class="aviso-titulo">Precaución</p>
<p>El script <code>db.sql</code> empieza con <code>CREATE DATABASE la_unica</code>. Si la base ya existe, fallará. No es un script de actualización ni de reinicio, y <strong>no trae datos de ejemplo</strong>.</p>
</div>
<p>En una base nueva de demostración, ajusta las instrucciones USE al mismo nombre exclusivo antes de importar. ¿Quieres datos para probar? Ejecuta además <a href="index.php?pagina=tecnico/wiki#datos-de-ejemplo"><code>wiki/herramientas/datos_ejemplo.sql</code></a> y tendrás clientes, productos y facturas como los de las capturas.</p>
<p>La wiki queda disponible en la misma instalación, en <code>…/la-unica/wiki/</code> (desde <em>Sobre nosotros → Wiki</em>). No necesita configuración ni base de datos.</p>
<h2 id="instalar-sin-laragon-avanzado">Instalar sin Laragon (avanzado)</h2>
<p>Si ya tienes PHP y MySQL sueltos, puedes usar el servidor embebido de PHP:</p>
<pre class="highlight"><code class="language-powershell">cd C:\ruta\a\la-unica
php -S 127.0.0.1:8080 -t .</code></pre>
<p>y abrir <code>http://127.0.0.1:8080/</code>. Asegúrate de que <code>php -m</code> liste las extensiones requeridas. El servidor integrado no aplica las protecciones .htaccess; úsalo solo en pruebas independientes sobre 127.0.0.1.</p>
<h2 id="estructura-que-debes-tener">Estructura que debes tener</h2>
<div class="arbol">
<ul>
<li>la-unica/<ul>
<li>index.php comprueba aceptación y redirige a terminos.php o main.php</li><li>terminos.php y procesar_terminos.php aviso y aceptación</li>
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
<li>SRS.docx y SRS.pdf</li><li>manual_tecnico.docx y manual_tecnico.pdf</li><li>PAC.docx y PAC.pdf</li><li>QA/ documentación y evidencias fechadas</li>
</ul>
</li>
</ul>
</li>
</ul>
</div>
<h2 id="actualizar">Actualizar</h2>
<div class="pasos">
<ol>
<li>Haz un respaldo de la base exclusiva con HeidiSQL → <em>Exportar base de datos como SQL</em>. Incluye datos y los cuatro procedimientos. Comprueba usuario, puerto y nombre reales, guarda fuera de la carpeta pública y ensaya la restauración en otra base nueva. El manual técnico incluye un patrón de consola para el entorno comprobado.</li>
<li>Tu configuración está en <code>php/config/conexion.local.php</code>, que Git no toca. Si en una versión anterior editaste <code>conexion.php</code> directamente, pasa tus valores a <code>conexion.local.php</code>.</li>
<li>
<p>Descarga la versión nueva:</p>
<pre class="highlight"><code class="language-powershell">cd C:\laragon\www\la-unica
git pull</code></pre>
</li>
<li>
<p>Esta entrega no incluye migraciones automáticas. Si vienes de otro modelo, respalda y revisa un traslado en una base independiente antes de usar los datos. No apliques scripts de versiones anteriores sin revisar sus diferencias.</p>
</li>
<li><strong>No</strong> vuelvas a importar <code>db.sql</code> sobre una base existente: es solo para instalaciones nuevas.</li>
</ol>
</div>
<h2 id="desinstalar">Retirar una copia</h2><p>Identifica la carpeta y la base exclusivas de la copia; respalda y comprueba su recuperación antes de retirarla. No borres una base compartida ni la de otro proyecto. La desinstalación es una decisión del responsable; el aplicativo y esta revisión no eliminan datos automáticamente.</p>
