<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Los reportes son <strong>solo de consulta</strong>: no cambian datos. La versión actual ofrece cinco reportes y un resumen diario. Utiliza cuatro procedimientos y consultas sobre las tablas actuales; <code>db.sql</code> no define vistas.</p><h2 id="abrir-los-reportes">Abrir los reportes</h2>
<figure class="captura"><img src="img/reportes-menu.webp" alt="Reportes en el menú actual" loading="lazy"><figcaption>Reportes en el menú actual</figcaption></figure>
<p><span class="marca-num">1</span> Reportes abre el resumen. <span class="marca-num">2</span> Debajo se encuentran los accesos a las cinco consultas.</p>
<h2 id="el-resumen-general">El resumen general</h2>
<p>Las tarjetas resumen ventas del día, unidades del día y existencias. Más abajo están Clientes y cantidad de facturas y Detalle de facturas.</p><figure class="captura"><img src="img/reportes-resumen.webp" alt="Resumen diario y cinco consultas actuales" loading="lazy"><figcaption>Resumen diario y cinco consultas actuales</figcaption></figure>
<figure class="captura"><img src="img/reportes-tarjeta.webp" alt="Partes de la tarjeta Ventas de hoy" loading="lazy"><figcaption>Partes de la tarjeta Ventas de hoy</figcaption></figure>
<ul><li><span class="marca-num">1</span> Importe vendido.</li><li><span class="marca-num">2</span> Cantidad de facturas pagadas.</li><li><span class="marca-num">3</span> Botón que abre Ventas.</li></ul>
<h2 id="leer-un-reporte-individual">Leer una consulta</h2>
<figure class="captura"><img src="img/reportes-individual-partes.webp" alt="Encabezado, impresión y filas del reporte de existencias" loading="lazy"><figcaption>Encabezado, impresión y filas del reporte de existencias</figcaption></figure>
<p><span class="marca-num">1</span> identifica la consulta; <span class="marca-num">2</span> permite imprimir o guardar como PDF; <span class="marca-num">3</span> señala una fila del resultado. Los datos y las notas dependen del reporte.</p>
<h2 id="los-siete-reportes-en-detalle">Los cinco reportes en detalle</h2>
<div class="tabla-wiki"><table><thead><tr><th>Reporte</th><th>Qué muestra</th></tr></thead><tbody><tr><td>Ventas</td><td>Lista facturas con su estado o agrupa por día, semana, mes o año. Solo Pagado suma al total vendido.</td></tr><tr><td>Unidades vendidas por producto</td><td>Cantidades e importes por fechas, excluyendo Cancelado.</td></tr><tr><td>Existencias</td><td>Stock y precio actual; Bajo es de 1 a 5 y Agotado es 0.</td></tr><tr><td>Clientes y cantidad de facturas</td><td>Incluye clientes sin facturas y cuenta Pagado y Cancelado.</td></tr><tr><td>Detalle de facturas</td><td>Líneas con precio histórico, fecha, estado y subtotal. Incluye ambos estados.</td></tr></tbody></table></div>

<h2 id="ventas">Ventas</h2>
<p>Selecciona Desde, Hasta y Ver por, y pulsa Consultar. Al abrir se muestra hoy. La vista por factura conserva el estado; las agrupaciones separan ventas pagadas y canceladas.</p><figure class="captura"><img src="img/reporte-total-vigente-por-factura.webp" alt="Ventas en la versión actual" loading="lazy"><figcaption>Ventas en la versión actual</figcaption></figure>

<h2 id="unidades">Unidades vendidas</h2>
<p>Elige el rango de fechas. Se suman cantidades e importes de las facturas Pagado, utilizando el precio guardado al vender.</p><figure class="captura"><img src="img/reporte-unidades-facturadas-por-producto.webp" alt="Unidades vendidas en la versión actual" loading="lazy"><figcaption>Unidades vendidas en la versión actual</figcaption></figure>

<h2 id="existencias">Existencias</h2>
<p>Ordena desde las menores existencias y muestra precios actuales. No representa los precios de ventas anteriores.</p><figure class="captura"><img src="img/reporte-existencias-productos.webp" alt="Existencias en la versión actual" loading="lazy"><figcaption>Existencias en la versión actual</figcaption></figure>

<h2 id="clientes">Clientes y cantidad de facturas</h2>
<p>Incluye los clientes sin facturas. El conteo incluye Pagado y Cancelado porque ambos conservan la relación con el cliente.</p><figure class="captura"><img src="img/reporte-clientes-y-facturas.webp" alt="Clientes y cantidad de facturas en la versión actual" loading="lazy"><figcaption>Clientes y cantidad de facturas en la versión actual</figcaption></figure>

<h2 id="detalle">Detalle de facturas</h2>
<p>Muestra nombre del cliente en la venta, producto, cantidad, fecha, estado, precio histórico y subtotal. Revisa Estado al interpretar las cifras.</p><figure class="captura"><img src="img/reporte-detalle-facturas.webp" alt="Detalle de facturas en la versión actual" loading="lazy"><figcaption>Detalle de facturas en la versión actual</figcaption></figure>

<h2 id="imprimir-y-guardar-pdf">Imprimir y guardar como PDF</h2>
<p>Aplica los filtros antes de pulsar Imprimir reporte o Guardar como PDF. Ambos abren el diálogo del navegador; selecciona Guardar como PDF y la carpeta de destino para exportar. No se genera una descarga automática.</p><p>Usa A4, escala 100 % y desactiva encabezados y pies del navegador. Detalle de facturas utiliza horizontal; los demás, vertical. La página imprime su título, período, fecha y numeración.</p><figure class="captura"><img src="img/reporte-ventas-impresion.webp" alt="Estilos de impresión del reporte de Ventas; no es el diálogo del navegador" loading="lazy"><figcaption>Estilos de impresión del reporte de Ventas; no es el diálogo del navegador</figcaption></figure>
