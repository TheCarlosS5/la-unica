<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>La Única es una aplicación web <strong>local</strong> hecha en <strong>PHP + MySQL</strong> para el día a día de un negocio pequeño: registrar clientes y productos, vender con facturas que descuentan el inventario y consultar reportes. Es el proyecto del grupo de la ficha <strong>3407799</strong> del programa <em>Análisis y Desarrollo de Software</em> del SENA (Centro de la Industria, la Empresa y los Servicios, Regional Huila).</p>
<figure class="captura"><img src="img/inicio.webp" alt="Pantalla de inicio de La Única" loading="lazy"><figcaption>Pantalla de inicio de La Única</figcaption></figure>
<h2 id="qué-puedes-hacer">Qué puedes hacer</h2>
<div class="tarjetas-wiki">
<div class="tarjeta-wiki">
<p class="tarjeta-titulo">Clientes</p>
<p>Registrar con un <strong>ID propio</strong> (cédula, NIT o un consecutivo), buscar por ID o nombre, cambiar el nombre y eliminar los que <strong>no</strong> tengan facturas.</p>
</div>
<div class="tarjeta-wiki">
<p class="tarjeta-titulo">Productos</p>
<p>Catálogo con <strong>unidad de medida</strong> libre (kg, litro, cubeta…), existencias enteras y precio. Marca de <strong>Agotado</strong> cuando el stock llega a 0.</p>
</div>
<div class="tarjeta-wiki">
<p class="tarjeta-titulo">Facturas</p>
<p>Ventas de 1 a 100 productos con <strong>total en vivo</strong>, descuento automático del stock, comprobante imprimible y <strong>cancelación</strong> que repone el inventario.</p>
</div>
<div class="tarjeta-wiki">
<p class="tarjeta-titulo">Reportes</p>
<p>Un resumen con indicadores y <strong>siete reportes</strong>: clientes, totales, unidades, detalle, existencias, estados y pendientes.</p>
</div>
</div>
<h2 id="cómo-usar-esta-wiki">Cómo usar esta wiki</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Si quieres…</th>
<th>Ve a…</th>
</tr>
</thead>
<tbody>
<tr>
<td>Instalar La Única en un computador</td>
<td><a href="index.php?pagina=guia/instalacion">Instalar / Actualizar / Desinstalar</a></td>
</tr>
<tr>
<td>Hacer tu primera venta de principio a fin</td>
<td><a href="index.php?pagina=guia/primeros-pasos">Primeros pasos</a></td>
</tr>
<tr>
<td>Aprender un módulo paso a paso</td>
<td><a href="index.php?pagina=guia/clientes">Clientes</a>, <a href="index.php?pagina=guia/productos">Productos</a>, <a href="index.php?pagina=guia/facturas">Facturas</a>, <a href="index.php?pagina=guia/reportes">Reportes</a></td>
</tr>
<tr>
<td>Resolver un error</td>
<td><a href="index.php?pagina=guia/problemas">Solución de problemas</a> o el <a href="index.php?pagina=guia/mensajes">Catálogo de mensajes</a></td>
</tr>
<tr>
<td>Entender cómo está hecho por dentro</td>
<td><a href="index.php?pagina=tecnico/arquitectura">Notas técnicas</a></td>
</tr>
</tbody>
</table></div>
<p>En las guías, los números naranjas como <span class="marca-num">1</span> <span class="marca-num">2</span> <span class="marca-num">3</span> del texto corresponden a los recuadros numerados de cada imagen. Haz clic en cualquier imagen para verla en grande.</p>
<h2 id="cómo-funciona-en-una-frase">Cómo funciona en una frase</h2>
<p>El navegador habla con <strong>Apache + PHP</strong> en tu propio equipo, y PHP guarda y consulta todo en <strong>MySQL</strong>. Nada sale a Internet.</p>
<pre class="highlight"><code class="language-text">Navegador ──► Apache + PHP (php/…) ──► MySQL (base la_unica)
    ▲                                         │
    └────────── páginas HTML ◄────────────────┘</code></pre>
<h2 id="fuera-del-alcance">Fuera del alcance</h2>
<div class="aviso aviso-caution">
<p class="aviso-titulo">Lo que La Única no hace (a propósito)</p>
<ul>
<li><strong>No hay inicio de sesión, usuarios ni roles.</strong> Cualquiera con acceso al equipo puede usarla.</li>
<li><strong>No es facturación electrónica</strong> ni factura fiscal: el comprobante es interno.</li>
<li><strong>No guarda precio histórico</strong>: si cambias el precio de un producto, cambian los importes mostrados en facturas anteriores.</li>
<li>No procesa pagos: una venta registrada se marca como <em>Pagado</em> directamente.</li>
<li>No está pensada para publicarse en Internet.</li>
</ul>
</div>
