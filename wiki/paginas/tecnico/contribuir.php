<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="ramas">Ramas</h2>
<p>El repositorio usa una rama por integrante o tarea y <em>pull requests</em> hacia <code>main</code>:</p>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Rama</th>
<th>Uso</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>main</code></td>
<td>Versión integrada del equipo.</td>
</tr>
<tr>
<td><code>revision-samuel</code></td>
<td>Versión funcional revisada (la que documenta esta wiki).</td>
</tr>
<tr>
<td><code>samuel</code>, <code>camilo-trabajo</code>, <code>carlos-gutierrez</code>, <code>Santiago</code>, <code>fernando</code></td>
<td>Trabajo individual.</td>
</tr>
<tr>
<td><code>rediseno-crud</code>, <code>documentacion</code>, <code>santiago-qa-docs</code></td>
<td>Tareas concretas.</td>
</tr>
</tbody>
</table></div>
<h2 id="flujo-recomendado">Flujo recomendado</h2>
<div class="pasos">
<ol>
<li>
<p>Actualiza tu copia y crea una rama:</p>
<pre class="highlight"><code class="language-powershell">git switch main
git pull --ff-only
git switch -c mi-cambio</code></pre>
</li>
<li>
<p>Haz el cambio siguiendo el estilo (abajo).</p>
</li>
<li>Prueba a mano con la <a href="#lista-de-verificación-manual">lista de verificación</a>.</li>
<li>
<p>Commit con mensaje descriptivo, preferiblemente con prefijo: <code>feat:</code>, <code>fix:</code>, <code>docs:</code>, <code>chore:</code>.</p>
<pre class="highlight"><code class="language-powershell">git add -A
git commit -m "fix: corregir puerto MySQL en conexion.php"
git push -u origin mi-cambio</code></pre>
</li>
<li>
<p>Abre un <em>pull request</em> hacia <code>main</code> y pide revisión a otro integrante.</p>
</li>
</ol>
</div>
<div class="aviso aviso-caution">
<p class="aviso-titulo">Precaución</p>
<p>Evita subir carpetas completas con <em>Add files via upload</em> sobre archivos existentes: en el historial se ve que eso borra y recrea todo, y hace muy difícil revisar qué cambió. Usa Git localmente.</p>
</div>
<h2 id="estilo-de-código">Estilo de código</h2>
<ul>
<li><strong>PHP plano, explícito y comentado</strong>. El equipo prefiere claridad a brevedad (se revirtió una versión compacta).</li>
<li>Nombres <strong>en español</strong>, variables en <code>camelCase</code> (<code>$mensajeError</code>), archivos en <code>snake_case</code> (<code>crear_cliente.php</code>).</li>
<li>Cada archivo empieza con el bloque de documentación:</li>
</ul>
<pre class="highlight"><code class="language-php">/**
  * Archivo: nombre.php
  * Módulo: …
  * Propósito: …
  * Funcionamiento: …
  * Puntos clave: …
  */</code></pre>
<ul>
<li><strong>Siempre</strong> sentencias preparadas, <code>h()</code> al imprimir, token CSRF en formularios POST, <code>registrarErrorServidor()</code> en los <code>catch</code>.</li>
<li>Toda escritura que toque varias tablas o el stock va en <strong>transacción</strong>.</li>
<li>Post/Redirect/Get (<code>303</code>) tras una escritura exitosa.</li>
<li>Sin dependencias externas: nada de CDN; fuentes e íconos locales.</li>
</ul>
<h2 id="lista-de-verificación-manual">Lista de verificación manual</h2>
<p>No hay pruebas automatizadas. Antes de un PR, en una base <strong>nueva</strong> con <code>db.sql</code>:</p>
<ul>
<li>[ ] Crear, buscar (por ID y por nombre), editar y eliminar un cliente.</li>
<li>[ ] Intentar crear un cliente con ID repetido → error claro.</li>
<li>[ ] Crear, buscar, editar y eliminar un producto.</li>
<li>[ ] Intentar cambiar la unidad de un producto facturado → bloqueado.</li>
<li>[ ] Vender varios productos; comprobar descuento de stock y total.</li>
<li>[ ] Vender más que el stock (desde otra pestaña) → rechazo sin cambios.</li>
<li>[ ] Imprimir un comprobante (vista previa limpia).</li>
<li>[ ] Cancelar una factura → stock repuesto; segundo intento → bloqueado.</li>
<li>[ ] Intentar eliminar cliente/producto con facturas → bloqueado.</li>
<li>[ ] Resumen y los 7 reportes cargan y cuadran con los datos.</li>
<li>[ ] Sobre nosotros: manual visible; video si existe <code>1.mp4</code>.</li>
<li>[ ] Menú en móvil (abrir, cerrar con ✕, fondo y <kbd>Esc</kbd>).</li>
<li>[ ] Con MySQL apagado: mensaje de conexión controlado.</li>
</ul>
<h2 id="ideas-para-próximas-versiones">Ideas para próximas versiones</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Idea</th>
<th>Beneficio</th>
</tr>
</thead>
<tbody>
<tr>
<td>Inicio de sesión simple</td>
<td>Poder usarla con más de una persona.</td>
</tr>
<tr>
<td>Exportar reportes a CSV</td>
<td>Análisis en hoja de cálculo.</td>
</tr>
<tr>
<td>Configuración de conexión en un archivo aparte (<code>config.local.php</code>)</td>
<td>No editar código al instalar.</td>
</tr>
<tr>
<td>Pruebas automatizadas (PHPUnit)</td>
<td>Detectar regresiones.</td>
</tr>
</tbody>
</table></div>
