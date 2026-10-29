<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="apache-o-el-servidor-embebido">Apache o el servidor embebido</h2>
<p>La Única no necesita reglas de reescritura (<code>.htaccess</code>) ni Composer: cada pantalla es un archivo PHP que se abre directamente, por ejemplo <code>/php/clientes/ver_clientes.php</code>. Funciona igual en:</p>
<ul>
<li><strong>Laragon / Apache</strong>, con la carpeta en <code>www/</code> (<code>http://la-unica.test/</code> o <code>http://localhost/la-unica/</code>).</li>
<li><strong>Servidor embebido de PHP</strong> (<code>php -S 127.0.0.1:8080 -t .</code>), útil para pruebas.</li>
</ul>
<h2 id="url-base-automática">URL base automática</h2>
<p>No hay que configurar la ruta de instalación. <code>conexion.php</code> la calcula a partir de la página solicitada:</p>
<pre class="highlight"><code class="language-php">$scriptDir = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/')));
$baseUrl = preg_replace('#/php/(clientes|productos|facturas|reportes|inicio)$#', '', $scriptDir);</code></pre>
<p>Así, si la app está en <code>http://localhost/la-unica/</code>, <code>$baseUrl</code> vale <code>/la-unica</code>, y si está en la raíz, vale <code>''</code>. Todos los enlaces del menú, el CSS y las fuentes se construyen con esa base.</p>
<h2 id="extensiones-de-php">Extensiones de PHP</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Extensión</th>
<th>Para qué</th>
<th>¿Obligatoria?</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>mysqli</code> + <code>mysqlnd</code></td>
<td>Conexión y <code>get_result()</code> en consultas preparadas</td>
<td>Sí</td>
</tr>
<tr>
<td><code>session</code></td>
<td>Token CSRF y mensajes después de redirigir</td>
<td>Sí (sin ella no se puede guardar nada)</td>
</tr>
<tr>
<td><code>mbstring</code></td>
<td>Contar caracteres UTF-8 en los límites de 100/20</td>
<td>No: hay un respaldo con expresiones regulares</td>
</tr>
</tbody>
</table></div>
<p>Compruébalo con <code>php -m</code>.</p>
<h2 id="sesiones">Sesiones</h2>
<p>La app usa la sesión de PHP solo para dos cosas: el <strong>token CSRF</strong> y el mensaje de «Factura creada» tras la redirección. No hay usuarios. Si PHP no puede iniciar la sesión (carpeta temporal sin permisos, por ejemplo), los formularios muestran:</p>
<blockquote>
<p>No fue posible iniciar la sesión de seguridad. Recarga la página e inténtalo de nuevo.</p>
</blockquote>
<h2 id="zona-horaria-y-moneda">Zona horaria y moneda</h2>
<ul>
<li>PHP: <code>date_default_timezone_set('America/Bogota')</code>.</li>
<li>MySQL (por conexión): <code>SET time_zone = '-05:00'</code>.</li>
<li>Moneda: pesos colombianos con el formato <code>$1.234.567,89 COP</code> (punto de miles, coma decimal), generado por <code>formatearMonedaCop()</code> en PHP y por <code>Intl.NumberFormat('es-CO')</code> en el formulario de facturas.</li>
</ul>
<h2 id="registro-de-errores">Registro de errores</h2>
<p>Los errores técnicos se envían al <strong>log de PHP</strong> con <code>error_log()</code> mediante <code>registrarErrorServidor()</code>, con el contexto, la clase de excepción y el <code>SQLSTATE</code>. Por ejemplo:</p>
<pre class="highlight"><code class="language-text">[crear_factura] mysqli_sql_exception (1452): Cannot add or update a child row... SQLSTATE=23000</code></pre>
<p>En Laragon el log está en <code>C:\laragon\tmp\php_errors.log</code> (o donde indique <code>error_log</code> en <code>php.ini</code>). Con <code>php -S</code>, aparece en la misma consola.</p>
<div class="aviso aviso-danger">
<p class="aviso-titulo">Solo en local</p>
<p>La app no tiene autenticación. El SRS (RNF-03) exige que Apache y MySQL escuchen <strong>solo en 127.0.0.1</strong>. No abras los puertos en el firewall ni la publiques en Internet.</p>
</div>
