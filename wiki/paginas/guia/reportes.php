<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Los reportes son <strong>solo de consulta</strong>: no cambian datos. Leen las <strong>vistas</strong> y <strong>procedimientos almacenados</strong> de <code>db.sql</code>, así que muestran lo que hay en la base en ese momento.</p>
<h2 id="abrir-los-reportes">Abrir los reportes</h2>
<figure class="captura"><img src="img/reportes-menu.webp" alt="Reportes en el menú" loading="lazy"><figcaption>Reportes en el menú</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Reportes</strong> abre el resumen general.</li>
<li><span class="marca-num">2</span> Debajo están los siete reportes individuales; puedes abrir cualquiera directamente.</li>
</ul>
<h2 id="el-resumen-general">El resumen general</h2>
<p>Siete tarjetas, una por reporte:</p>
<figure class="captura"><img src="img/reportes-resumen.webp" alt="Resumen de reportes" loading="lazy"><figcaption>Resumen de reportes</figcaption></figure>
<p>Cada tarjeta se lee igual:</p>
<figure class="captura"><img src="img/reportes-tarjeta.webp" alt="Partes de una tarjeta del resumen" loading="lazy"><figcaption>Partes de una tarjeta del resumen</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Indicador principal</strong>: la cifra más importante del reporte.</li>
<li><span class="marca-num">2</span> <strong>Detalle</strong>: contexto del indicador (en este caso, el desglose por estado).</li>
<li><span class="marca-num">3</span> <strong>Abrir reporte</strong>: muestra la tabla completa.</li>
</ul>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Tarjeta</th>
<th>Indicador</th>
<th>Detalle</th>
</tr>
</thead>
<tbody>
<tr>
<td>Clientes y cantidad de facturas</td>
<td>Nº de clientes</td>
<td>Facturas entre todos los clientes (todos los estados)</td>
</tr>
<tr>
<td>Total por factura</td>
<td>Suma en COP</td>
<td>Cuántas ventas Pagado o Pendiente</td>
</tr>
<tr>
<td>Unidades vendidas por producto</td>
<td>Unidades</td>
<td>Cuántos productos se han vendido</td>
</tr>
<tr>
<td>Detalle de facturas</td>
<td>Líneas de detalle</td>
<td>En cuántas facturas distintas</td>
</tr>
<tr>
<td>Existencias de productos</td>
<td>Stock total</td>
<td>En cuántos productos</td>
</tr>
<tr>
<td>Facturas por estado</td>
<td>Nº de facturas</td>
<td><em>Pagado · Cancelado · Pendiente</em></td>
</tr>
<tr>
<td>Facturas pendientes</td>
<td>Nº pendientes</td>
<td>—</td>
</tr>
</tbody>
</table></div>
<div class="aviso aviso-note">
<p class="aviso-titulo">Qué incluye el total</p>
<p>«Total por factura» suma las ventas <em>Pagado</em> y <em>Pendiente</em> con el precio registrado en cada venta; las <em>Cancelado</em> no cuentan. Lo cobrado es la parte <em>Pagado</em>; lo que falta por cobrar está en <em>Facturas pendientes</em>.</p>
</div>
<h2 id="leer-un-reporte-individual">Leer un reporte individual</h2>
<figure class="captura"><img src="img/reportes-individual-partes.webp" alt="Partes de un reporte individual" loading="lazy"><figcaption>Partes de un reporte individual</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Cabecera</strong>: la fuente en MySQL (vista o procedimiento), el título, qué calcula y cuántos registros hay.</li>
<li><span class="marca-num">2</span> <strong>Nota</strong>: cómo interpretar el reporte (orden, estados incluidos, precio usado).</li>
<li><span class="marca-num">3</span> <strong>Filas</strong>: en este reporte, la primera es el producto con <strong>menos existencias</strong>.</li>
</ul>
<p>En los reportes que tienen número de factura, cada fila tiene un botón para abrir el comprobante.</p>
<h2 id="los-siete-reportes">Los siete reportes</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>#</th>
<th>Reporte</th>
<th>Para qué sirve</th>
<th>Orden</th>
<th>Ojo con…</th>
</tr>
</thead>
<tbody>
<tr>
<td>1</td>
<td><strong>Clientes y cantidad de facturas</strong></td>
<td>Ver quién compra más</td>
<td>Más facturas primero</td>
<td>Incluye clientes con 0 facturas.</td>
</tr>
<tr>
<td>2</td>
<td><strong>Total por factura</strong></td>
<td>Importe de cada venta</td>
<td>Factura más reciente primero</td>
<td>Solo facturas con detalles; excluye las canceladas.</td>
</tr>
<tr>
<td>3</td>
<td><strong>Unidades vendidas por producto</strong></td>
<td>Qué se vende más</td>
<td>Más unidades primero</td>
<td>Excluye las canceladas, cuyas unidades volvieron al inventario.</td>
</tr>
<tr>
<td>4</td>
<td><strong>Detalle de facturas</strong></td>
<td>Cada línea vendida, con subtotal</td>
<td>Por factura</td>
<td>Una fila por línea.</td>
</tr>
<tr>
<td>5</td>
<td><strong>Existencias de productos</strong></td>
<td>Saber qué pedir al proveedor</td>
<td><strong>Menor stock primero</strong></td>
<td>—</td>
</tr>
<tr>
<td>6</td>
<td><strong>Facturas por estado</strong></td>
<td>Cuántas pagadas, canceladas y pendientes</td>
<td>Alfabético</td>
<td>Un estado sin facturas no aparece.</td>
</tr>
<tr>
<td>7</td>
<td><strong>Facturas pendientes</strong></td>
<td>Ventas por cobrar</td>
<td><strong>Más antigua primero</strong></td>
<td>Solo estado <em>Pendiente</em>.</td>
</tr>
</tbody>
</table></div>
<div class="galeria" style="--columnas: 3">
<figure class="captura"><img src="img/reporte-clientes-y-facturas.webp" alt="Clientes y cantidad de facturas" loading="lazy"><figcaption>Clientes y cantidad de facturas</figcaption></figure>
<figure class="captura"><img src="img/reporte-total-vigente-por-factura.webp" alt="Total por factura" loading="lazy"><figcaption>Total por factura</figcaption></figure>
<figure class="captura"><img src="img/reporte-unidades-facturadas-por-producto.webp" alt="Unidades facturadas por producto" loading="lazy"><figcaption>Unidades facturadas por producto</figcaption></figure>
<figure class="captura"><img src="img/reporte-existencias-productos.webp" alt="Existencias de productos" loading="lazy"><figcaption>Existencias de productos</figcaption></figure>
<figure class="captura"><img src="img/reporte-facturas-por-estado.webp" alt="Facturas por estado" loading="lazy"><figcaption>Facturas por estado</figcaption></figure>
<figure class="captura"><img src="img/reporte-facturas-pendientes.webp" alt="Facturas pendientes" loading="lazy"><figcaption>Facturas pendientes</figcaption></figure>
</div>

<p>¿Quieres saber cómo están hechos por dentro? Ver <a href="index.php?pagina=tecnico/reportes">Cómo funcionan los reportes</a>.</p>
