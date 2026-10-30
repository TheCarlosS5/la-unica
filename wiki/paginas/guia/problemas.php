<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<div class="aviso aviso-tip">
<p class="aviso-titulo">Consejo</p>
<p>Antes que nada, revisa el <strong>log de errores de PHP</strong>: ahí La Única escribe el detalle técnico que no muestra en pantalla (contexto, excepción y <code>SQLSTATE</code>). En Laragon suele estar en <code>C:\laragon\tmp\php_errors.log</code>.</p>
</div>
<h2 id="error-de-conexión-a-la-base-de-datos">«Error de conexión a la base de datos.»</h2>
<p>Todas las páginas muestran solo esa frase (y HTTP 500).</p>
<div class="pasos">
<ol>
<li><strong>¿MySQL está encendido?</strong> En Laragon, <em>Start All</em> y comprueba que MySQL esté en verde.</li>
<li><strong>¿Puerto correcto?</strong> Por defecto la app usa el <strong>3306</strong>. Mira el puerto real en <code>my.ini</code> y, si es otro, ponlo en <code>php/config/conexion.local.php</code> (ver <a href="index.php?pagina=configuracion/base-de-datos#cambiar-el-puerto">Conexión a la base de datos</a>).</li>
<li><strong>¿Existe la base <code>la_unica</code>?</strong> En HeidiSQL o con <code>mysql -u root -P 3306 -e "SHOW DATABASES"</code>. Si no está, importa <code>referencias/db.sql</code>.</li>
<li><strong>¿Usuario y contraseña?</strong> Por defecto <code>root</code> sin contraseña. Si tu MySQL tiene clave (o usas <code>la_unica_app</code>), ponla en <code>conexion.local.php</code>.</li>
</ol>
</div>
<p>Mientras tanto, <em>Sobre nosotros → Manual del Usuario</em> sigue funcionando sin base de datos.</p>
<h2 id="error-al-importar-dbsql-database-exists">Error al importar <code>db.sql</code>: «database exists»</h2>
<pre class="highlight"><code class="language-text">ERROR 1007 (HY000): Can't create database 'la_unica'; database exists</code></pre>
<p>Ya la importaste antes. El script <strong>no es de reinicio</strong>. Si quieres empezar de cero (perderás los datos):</p>
<pre class="highlight"><code class="language-sql">DROP DATABASE la_unica;</code></pre>
<p>y vuelve a importar.</p>
<h2 id="error-al-importar-check-o-default-current_date">Error al importar: <code>CHECK</code> o <code>DEFAULT (CURRENT_DATE)</code></h2>
<pre class="highlight"><code class="language-text">ERROR 1064 (42000): You have an error in your SQL syntax ... near '(CURRENT_DATE)'</code></pre>
<p>Tu MySQL es demasiado viejo (o es MariaDB antiguo). Necesitas <strong>MySQL 8.0.16 o superior</strong>. Laragon reciente ya lo trae.</p>
<h2 id="las-tildes-y-eñes-se-ven-raras-ã-âœ">Las tildes y eñes se ven raras (<code>Ã±</code>, <code>â”œ</code>)</h2>
<p>Los datos se insertaron con una codificación distinta de UTF-8 (pasa al importar con la consola de Windows). La app siempre usa <code>utf8mb4</code>; el problema está en cómo entraron los datos. Al importar desde consola usa:</p>
<pre class="highlight"><code class="language-powershell">mysql -u root -P 3306 --default-character-set=utf8mb4 la_unica &lt; archivo.sql</code></pre>
<h2 id="call-to-undefined-function-mysqli-o-class-mysqli-not-found">«Call to undefined function mysqli…» o «Class "mysqli" not found»</h2>
<p>PHP no tiene la extensión <code>mysqli</code>. En Laragon: <em>Menú → PHP → Extensions → mysqli</em>. Fuera de Laragon, descomenta <code>extension=mysqli</code> en <code>php.ini</code> y reinicia Apache.</p>
<h2 id="no-se-pudo-validar-la-solicitud-actualiza-la-página-e-inténtalo-de-nuevo">«No se pudo validar la solicitud. Actualiza la página e inténtalo de nuevo.»</h2>
<p>El <strong>token CSRF</strong> del formulario no coincide con el de tu sesión. Ocurre si:</p>
<ul>
<li>dejaste el formulario abierto mucho tiempo y la sesión expiró,</li>
<li>reiniciaste Apache o borraste cookies,</li>
<li>enviaste el formulario desde otra pestaña antigua.</li>
</ul>
<p>Solución: recarga la página (F5) y vuelve a enviar.</p>
<h2 id="no-fue-posible-iniciar-la-sesión-de-seguridad">«No fue posible iniciar la sesión de seguridad…»</h2>
<p>PHP no puede guardar sesiones. Revisa <code>session.save_path</code> en <code>php.ini</code> (debe existir y tener permisos de escritura) y que el navegador acepte cookies de <code>localhost</code>.</p>
<h2 id="la-página-se-ve-sin-estilos">La página se ve sin estilos</h2>
<ul>
<li>Abriste el PHP directamente desde el disco (<code>file:///…</code>). Debe abrirse <strong>por el servidor</strong> (<code>http://…</code>).</li>
<li>La carpeta <code>assets/</code> no está junto a <code>php/</code>. Revisa la <a href="index.php?pagina=guia/instalacion#estructura-que-debes-tener">estructura</a>.</li>
</ul>
<h2 id="no-puedo-eliminar-un-cliente-o-producto">No puedo eliminar un cliente o producto</h2>
<p>Es intencional: tiene <strong>historial de facturas</strong> (verás el candado <em>En facturas</em>). Las facturas nunca se borran, así que sus clientes y productos tampoco. Para un producto que ya no vendes, deja el stock en 0.</p>
<h2 id="un-producto-no-aparece-al-hacer-una-factura">Un producto no aparece al hacer una factura</h2>
<p>Está <strong>agotado</strong> (stock 0) o ya lo elegiste en otra línea. Repón stock en <em>Productos → Editar</em> o ajusta la cantidad de la línea existente.</p>
<h2 id="las-existencias-cambiaron-y-no-alcanzan-para-completar-la-venta">«Las existencias cambiaron y no alcanzan para completar la venta»</h2>
<p>Entre que abriste el formulario y guardaste, el stock bajó (otra venta en otra pestaña, o alguien editó el producto). No se guardó nada. Revisa las cantidades y vuelve a guardar.</p>
<h2 id="el-total-de-una-factura-antigua-cambió">El total de una factura antigua cambió</h2>
<p>No es un error: el esquema no guarda precio histórico. Ver la <a href="index.php?pagina=guia/faq#por-qué-cambió-el-total-de-una-factura-vieja">FAQ</a>.</p>
<h2 id="el-video-no-aparece">El video no aparece</h2>
<p>Falta <code>assets/video/1.mp4</code>, o el códec no es compatible (usa H.264/AAC). Ver <a href="index.php?pagina=configuracion/personalizar#video-explicativo">Personalizar</a>.</p>
<h2 id="el-manual-sale-en-blanco">El manual sale en blanco</h2>
<p>Tu navegador tiene desactivado el visor de PDF integrado. Actívalo (en Chrome/Brave: <em>Configuración → Privacidad → Configuración de sitios → Documentos PDF → Abrir en Chrome</em>) o descarga <code>referencias/manual.pdf</code>.</p>
