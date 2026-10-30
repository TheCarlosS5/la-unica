<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>La wiki vive en la carpeta <code>wiki/</code> del mismo repositorio y la sirve el mismo Apache que la aplicación. Está hecha en <strong>PHP plano</strong>, sin Composer ni frameworks, y <strong>no se conecta a MySQL</strong>: funciona aunque la base de datos esté apagada.</p>
<div class="arbol">
<ul>
<li>wiki/<ul>
<li>index.php enrutador: valida la página pedida y arma la plantilla</li>
<li>includes/<ul>
<li>paginas.php <strong>catálogo</strong>: secciones, títulos y descripciones</li>
<li>funciones.php URL base, h(), índice «En esta página», búsqueda</li>
<li>cabecera.php barra superior y menú lateral</li>
<li>pie.php navegación anterior/siguiente y cierre</li>
</ul>
</li>
<li>paginas/<ul>
<li>guia/clientes.php <strong>contenido</strong> (HTML)</li>
<li>…</li>
</ul>
</li>
<li>css/wiki.css estilos con la paleta del aplicativo</li>
<li>js/wiki.js menú en celular, pestañas, copiar código, ampliar imágenes</li>
<li>img/ capturas numeradas en .webp</li>
<li>herramientas/<ul>
<li>capturas.mjs genera todas las imágenes</li>
<li>datos_ejemplo.sql datos usados en las capturas</li>
<li>package.json</li>
</ul>
</li>
</ul>
</li>
</ul>
</div>
<h2 id="cómo-se-abre-una-página">Cómo se abre una página</h2>
<pre class="highlight"><code class="language-text">wiki/index.php?pagina=guia/clientes
         │
         ├─ includes/paginas.php  ¿existe «guia/clientes» en el catálogo?  (si no → 404)
         ├─ paginas/guia/clientes.php  se carga el HTML de la página
         ├─ funciones.php  se arma el índice «En esta página» con los &lt;h2&gt;/&lt;h3&gt;
         └─ cabecera.php + contenido + pie.php</code></pre>
<p>Solo se cargan páginas que estén en el catálogo, así nadie puede pedir archivos arbitrarios por la URL. Sin <code>?pagina=</code> se muestra la página de bienvenida (<code>guia/introduccion</code>).</p>
<p>La búsqueda (<code>index.php?buscar=texto</code>) recorre el texto de todas las páginas del catálogo y muestra las coincidencias con un fragmento.</p>
<h2 id="editar-una-página">Editar una página</h2>
<div class="pasos">
<ol>
<li>Abre el archivo en <code>wiki/paginas/&lt;sección&gt;/&lt;página&gt;.php</code>. Es HTML normal.</li>
<li>
<p>La primera línea protege el archivo para que no se abra suelto:</p>
<pre class="highlight"><code class="language-php">&lt;?php if (!defined('LA_UNICA_WIKI')) { exit; } ?&gt;</code></pre>
<p>No la borres.</p>
</li>
<li>
<p>Edita el HTML y recarga la página en el navegador. No hay que compilar nada.</p>
</li>
</ol>
</div>
<p>Elementos disponibles (con su clase CSS):</p>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Elemento</th>
<th>HTML</th>
</tr>
</thead>
<tbody>
<tr>
<td>Número de paso naranja</td>
<td><code>&lt;span class="marca-num"&gt;1&lt;/span&gt;</code></td>
</tr>
<tr>
<td>Captura</td>
<td><code>&lt;figure class="captura"&gt;&lt;img src="img/nombre.webp" alt="…"&gt;&lt;figcaption&gt;…&lt;/figcaption&gt;&lt;/figure&gt;</code></td>
</tr>
<tr>
<td>Aviso</td>
<td><code>&lt;div class="aviso aviso-tip"&gt;&lt;p class="aviso-titulo"&gt;Consejo&lt;/p&gt;…&lt;/div&gt;</code> (<code>aviso-note</code>, <code>aviso-tip</code>, <code>aviso-caution</code>, <code>aviso-danger</code>)</td>
</tr>
<tr>
<td>Pasos numerados</td>
<td><code>&lt;div class="pasos"&gt;&lt;ol&gt;…&lt;/ol&gt;&lt;/div&gt;</code></td>
</tr>
<tr>
<td>Insignia</td>
<td><code>&lt;span class="insignia insignia-success"&gt;Pagado&lt;/span&gt;</code></td>
</tr>
<tr>
<td>Pestañas</td>
<td><code>&lt;div class="pestanas"&gt;&lt;div class="pestana" data-titulo="A"&gt;…&lt;/div&gt;…&lt;/div&gt;</code></td>
</tr>
</tbody>
</table></div>
<p>Los títulos <code>&lt;h2&gt;</code> y <code>&lt;h3&gt;</code> necesitan un <code>id</code> para aparecer en «En esta página».</p>
<h2 id="agregar-una-página-nueva">Agregar una página nueva</h2>
<div class="pasos">
<ol>
<li>Crea <code>wiki/paginas/guia/mi-pagina.php</code> con la línea de protección y el HTML.</li>
<li>
<p>Regístrala en <code>wiki/includes/paginas.php</code>, dentro de la sección que quieras:</p>
<pre class="highlight"><code class="language-php">'guia/mi-pagina' =&gt; [
   'titulo' =&gt; 'Mi página',
   'descripcion' =&gt; 'Una frase que explica de qué trata.',
],</code></pre>
</li>
<li>
<p>Enlázala desde otras páginas con <code>index.php?pagina=guia/mi-pagina</code>.</p>
</li>
</ol>
</div>
<p>El orden del catálogo es el orden del menú lateral y de los botones «Anterior / Siguiente».</p>
<h2 id="datos-de-ejemplo">Datos de ejemplo</h2>
<p><code>wiki/herramientas/datos_ejemplo.sql</code> crea 8 clientes, 10 productos (uno agotado y uno con stock bajo) y 8 facturas en los tres estados. Se ejecuta <strong>después</strong> de <code>db.sql</code> en una base vacía:</p>
<pre class="highlight"><code class="language-powershell">mysql -u root -h 127.0.0.1 -P 3306 --default-character-set=utf8mb4 &lt; referencias\db.sql
mysql -u root -h 127.0.0.1 -P 3306 --default-character-set=utf8mb4 &lt; wiki\herramientas\datos_ejemplo.sql</code></pre>
<div class="aviso aviso-caution">
<p class="aviso-titulo">Precaución</p>
<p>Usa siempre <code>--default-character-set=utf8mb4</code> al importar desde la consola de Windows; si no, «Frutería Doña Rosa» se guarda como «Fruter├¡a Do├▒a Rosa».</p>
</div>
<h2 id="regenerar-las-capturas">Regenerar las capturas</h2>
<p>Las imágenes con recuadros y números naranjas <strong>no se editan a mano</strong>: las dibuja el script <code>capturas.mjs</code> sobre la aplicación real, marcando los elementos de la página por su selector CSS y recortando solo la zona que importa. Si cambia la interfaz, se vuelven a generar todas en un minuto.</p>
<div class="pasos">
<ol>
<li>Crea una base nueva con <code>db.sql</code> + <code>datos_ejemplo.sql</code> (el script registra un cliente, una venta y cancela esa venta, así que necesita empezar desde cero).</li>
<li>Sirve la aplicación, por ejemplo: <code>php -S 127.0.0.1:8765 -t C:\ruta\la-unica</code>.</li>
<li>
<p>Instala la herramienta (solo la primera vez; necesita <a href="https://nodejs.org" target="_blank" rel="noopener">Node.js</a>):</p>
<pre class="highlight"><code class="language-powershell">cd wiki\herramientas
npm install</code></pre>
</li>
<li>
<p>Ejecuta:</p>
<pre class="highlight"><code class="language-powershell">node capturas.mjs http://127.0.0.1:8765 "C:/Program Files/Google/Chrome/Application/chrome.exe"</code></pre>
<p>Sirve cualquier navegador basado en Chromium (Chrome, Edge, Brave). Las imágenes quedan en <code>wiki/img/</code>.</p>
</li>
</ol>
</div>
<p>Cada captura se declara así dentro del script:</p>
<pre class="highlight"><code class="language-js">await foto('clientes-crear-formulario', {
  recorte: '.tarjeta-formulario',          // zona que se recorta
  marcas: [
    { sel: '#id_cliente', n: 1 },          // recuadro con el número 1
    { sel: '#nombre', n: 2 },
    { sel: '.tarjeta-formulario button[type=submit]', n: 3, num: 'esquina' },
  ],
});</code></pre>
<p>Opciones de cada marca: <code>sel</code> (selector CSS), <code>contiene</code> (filtra por texto), <code>dentro</code> (elemento interno), <code>n</code> (número), <code>num</code> (<code>izquierda</code>, <code>derecha</code> o <code>esquina</code>), <code>texto</code> y <code>lado</code> (etiqueta con flecha de texto), <code>margen</code>.</p>
