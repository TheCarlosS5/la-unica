<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>La wiki vive en la carpeta <code>wiki/</code> del mismo repositorio y la sirve el mismo Apache que la aplicación. Está hecha en <strong>PHP plano</strong>, sin Composer ni frameworks, y <strong>no se conecta a MySQL</strong>: funciona aunque la base de datos esté apagada.</p>
<div class="arbol">
<ul>
<li>wiki/<ul>
<li>index.php enrutador: valida la página pedida y arma la plantilla</li>
<li>includes/<ul>
<li>paginas.php <strong>catálogo</strong>: secciones, títulos y descripciones</li>
<li>funciones.php h(), índice «En esta página», búsqueda y carga por catálogo</li>
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
<h2 id="datos-de-ejemplo">Datos de ejemplo actuales</h2>
<p>wiki/herramientas/datos_ejemplo.sql contiene ocho clientes, diez productos y ocho facturas ficticias en Pagado y Cancelado, con los nombres, unidades y precios históricos requeridos por las cinco tablas. Las fechas de factura se calculan respecto del día de importación para demostrar la regla de cancelación. El stock del catálogo representa una existencia actual de ejemplo.</p>
<p>Importa primero una copia de db.sql en una base nueva con prefijo la_unica_test_. Ajusta CREATE DATABASE y USE al mismo nombre en ambas copias. No es una migración ni un conjunto para una base de trabajo ya poblada.</p>
<h2 id="regenerar-las-capturas">Regenerar las capturas numeradas</h2>
<p>capturas.mjs conserva los recuadros y números naranjas de esta wiki y toma imágenes de la aplicación real. Requiere Node.js 20 o posterior, Playwright, Sharp, Brave/Chromium y PHP. Las dependencias de esta herramienta no son necesarias para ejecutar el aplicativo.</p>
<ol><li>Prepara la base exclusiva nueva con el esquema y los datos ficticios.</li><li>Define DB_TEST_HOST, DB_TEST_PORT, DB_TEST_DATABASE, DB_TEST_USERNAME y DB_TEST_PASSWORD; conserva la clave fuera del repositorio. PHP_BINARY puede señalar el ejecutable de PHP.</li><li>En wiki/herramientas ejecuta npm install y después node capturas.mjs. Opcionalmente pasa la ruta del navegador como único argumento.</li><li>El script inicia su propio PHP en un puerto libre de 127.0.0.1 y lo cierra al terminar. Nunca usa conexion.local.php como destino: los valores DB_TEST_* prevalecen para la conexión de ese servidor.</li><li>Revisa imágenes, texto, numeración, datos ficticios y visores antes de publicar. Si una captura queda obsoleta, actualízala sin retirar los pasos de la guía.</li></ol>
<p>Los nombres de bases de pruebas se validan por prefijo. Usa una base nueva en cada ejecución: el recorrido registra un cliente y una venta y confirma su cancelación. CAPTURAS_NODE_MODULES permite utilizar dependencias ya instaladas en un entorno de automatización.</p>
<h2 id="marcas">Marcas y recortes</h2>
<p>foto() acepta marcas con selector, número, posición y margen, y puede recortar una tarjeta o el conjunto de controles. La capa de anotaciones existe solo en el navegador de captura y no modifica el HTML del aplicativo ni su base. Conserva los números de los pies y del texto al actualizar una imagen.</p>
