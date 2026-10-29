<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<div class="aviso aviso-danger">
<p class="aviso-titulo">Modelo de amenaza</p>
<p>La Única está pensada para <strong>un equipo local de confianza</strong>. No tiene autenticación ni roles (fuera del alcance del SRS). Las protecciones de abajo evitan errores, datos corruptos y ataques web comunes, pero <strong>no</strong> sustituyen un inicio de sesión: no la publiques en red.</p>
</div>
<h2 id="inyección-sql--sentencias-preparadas">Inyección SQL → sentencias preparadas</h2>
<p>Todo dato que viene del usuario llega a MySQL como <strong>parámetro</strong>, nunca concatenado:</p>
<pre class="highlight"><code class="language-php">$sentencia = $conexion-&gt;prepare('UPDATE clientes SET nombre = ? WHERE id_cliente = ?');
$sentencia-&gt;bind_param('si', $nombreNuevo, $idCliente);
$sentencia-&gt;execute();</code></pre>
<p>Incluso las búsquedas con <code>LIKE</code> pasan el patrón (<code>'%' . $termino . '%'</code>) como parámetro. Las únicas consultas sin parámetros son las de reportes, que no reciben entrada del usuario.</p>
<h2 id="xss--escape-de-salida">XSS → escape de salida</h2>
<p>Cada valor dinámico impreso en HTML pasa por <code>h()</code> (alias de <code>escaparHtml()</code>):</p>
<pre class="highlight"><code class="language-php">function escaparHtml($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}</code></pre>
<p>Un cliente llamado <code>&lt;script&gt;alert(1)&lt;/script&gt;</code> se muestra como texto.</p>
<h2 id="csrf--token-por-sesión">CSRF → token por sesión</h2>
<p>Todo formulario que <strong>modifica</strong> datos (crear, editar, eliminar, vender, cancelar) incluye un campo oculto <code>csrf_token</code>:</p>
<ul>
<li><code>obtenerTokenCsrf()</code> genera 32 bytes aleatorios (<code>random_bytes</code>) y los guarda en <code>$_SESSION</code>.</li>
<li><code>validarTokenCsrf()</code> compara con <code>hash_equals()</code> (tiempo constante).</li>
<li>Si falla: HTTP 403 o mensaje <em>«No se pudo validar la solicitud…»</em>.</li>
</ul>
<p>Así, una página maliciosa abierta en otra pestaña no puede enviar un formulario a La Única en tu nombre.</p>
<h2 id="métodos-http">Métodos HTTP</h2>
<ul>
<li>Las acciones destructivas (eliminar, cancelar) <strong>solo</strong> aceptan POST. <code>cancelar_factura.php</code> responde <code>405 Método no permitido</code> con <code>Allow: POST</code> a cualquier otro método.</li>
<li>Las consultas usan GET, por lo que se pueden guardar en marcadores.</li>
</ul>
<h2 id="validación-de-entrada">Validación de entrada</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Función</th>
<th>Regla</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>validarId()</code></td>
<td>Entero entre 1 y 2.147.483.647 (rango de <code>INT</code>). Rechaza decimales, signos y textos.</td>
</tr>
<tr>
<td><code>validarFecha()</code></td>
<td>Formato exacto <code>YYYY-MM-DD</code> y fecha real (<code>2026-02-30</code> no pasa).</td>
</tr>
<tr>
<td><code>longitudTexto()</code></td>
<td>Cuenta <strong>caracteres</strong> UTF-8, no bytes (<code>ñ</code> cuenta 1).</td>
</tr>
<tr>
<td>Precio</td>
<td>Regex <code>^\d{1,8}(\.\d{1,2})?$</code> → máximo 99.999.999,99 con 2 decimales.</td>
</tr>
<tr>
<td>Tipos</td>
<td>Se verifica <code>is_string()</code> para evitar arreglos inyectados (<code>nombre[]=x</code>).</td>
</tr>
</tbody>
</table></div>
<p>Las mismas reglas existen como <code>CHECK</code>/<code>NOT NULL</code>/FK en MySQL: defensa en profundidad.</p>
<h2 id="confirmaciones">Confirmaciones</h2>
<p>Eliminar requiere <strong>dos pasos</strong>: <code>confirm()</code> del navegador + página de confirmación del servidor con un segundo POST. Cancelar requiere <code>confirm()</code>.</p>
<h2 id="información-de-errores">Información de errores</h2>
<p>Los mensajes al usuario nunca incluyen trazas, rutas ni SQL. El detalle va al log con <code>registrarErrorServidor()</code>, que además elimina saltos de línea para evitar inyección en el log.</p>
<h2 id="exposición-de-red-rnf-03">Exposición de red (RNF-03)</h2>
<ul>
<li><code>conexion.php</code> se conecta a <code>127.0.0.1</code>.</li>
<li>MySQL y Apache deben escuchar solo en <em>loopback</em>.</li>
<li>La etiqueta <strong>«Administración local»</strong> en la barra superior recuerda el contexto.</li>
</ul>
<h2 id="límites-conocidos">Límites conocidos</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Límite</th>
<th>Riesgo</th>
<th>Mitigación sugerida</th>
</tr>
</thead>
<tbody>
<tr>
<td>Sin autenticación</td>
<td>Cualquiera con acceso al equipo opera la app</td>
<td>Cuenta de Windows con contraseña; no exponer en red.</td>
</tr>
<tr>
<td>Credenciales <code>root</code> sin clave</td>
<td>Si MySQL se expone, acceso total</td>
<td>Usuario dedicado con permisos mínimos (<a href="index.php?pagina=configuracion/base-de-datos#usar-un-usuario-con-contraseña">ver</a>).</td>
</tr>
<tr>
<td>Mensajes en <code>?mensaje=</code></td>
<td>Texto arbitrario mostrado (escapado)</td>
<td>Solo afecta presentación; no ejecuta código.</td>
</tr>
<tr>
<td>Sin cabeceras CSP / <code>X-Frame-Options</code></td>
<td><em>Clickjacking</em> teórico</td>
<td>Añadirlas en Apache si algún día se expone.</td>
</tr>
</tbody>
</table></div>
