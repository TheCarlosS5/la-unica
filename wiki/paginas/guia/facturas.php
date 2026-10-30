<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Una <strong>factura</strong> es una venta: un cliente, una fecha, un estado y una o más líneas (producto + cantidad).</p>
<h2 id="estados-de-una-factura">Estados de una factura</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Estado</th>
<th>Cómo se llega</th>
<th>¿Descontó stock?</th>
<th>¿Se puede cancelar?</th>
</tr>
</thead>
<tbody>
<tr>
<td><span class="insignia insignia-success">Pagado</span></td>
<td>Venta <em>de contado</em>, o al registrar el pago de una <em>Pendiente</em></td>
<td>Sí</td>
<td><strong>Sí</strong>, una sola vez</td>
</tr>
<tr>
<td><span class="insignia insignia-danger">Cancelado</span></td>
<td>Al cancelar una factura <em>Pagado</em> o <em>Pendiente</em></td>
<td>No (se repuso)</td>
<td>No</td>
</tr>
<tr>
<td><span class="insignia insignia-caution">Pendiente</span></td>
<td>Venta <em>por cobrar</em> (fiado): el cliente se lleva la mercancía y paga después</td>
<td>Sí</td>
<td><strong>Sí</strong>, una sola vez</td>
</tr>
</tbody>
</table></div>
<div class="aviso aviso-note">
<p class="aviso-titulo">Nota</p>
<p>Al guardar una venta eliges la <strong>forma de pago</strong>: <em>De contado</em> la guarda como <em>Pagado</em>; <em>Por cobrar</em> la guarda como <em>Pendiente</em>. Cuando el cliente pague, abre el comprobante y pulsa <strong>Registrar pago</strong>: pasa a <em>Pagado</em> sin mover el inventario.</p>
</div>
<h2 id="abrir-el-módulo">Abrir el módulo</h2>
<figure class="captura"><img src="img/facturas-menu.webp" alt="Facturas en el menú" loading="lazy"><figcaption>Facturas en el menú</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Facturas</strong> abre la búsqueda de ventas.</li>
<li><span class="marca-num">2</span> <strong>Nueva factura</strong> abre el formulario para vender.</li>
</ul>
<h2 id="registrar-una-venta">Registrar una venta</h2>
<div class="pasos">
<ol>
<li>
<p>Menú → <em>Ventas → <strong>Nueva factura</strong></em>.</p>
</li>
<li>
<p><span class="marca-num">1</span> <strong>Elige el cliente.</strong> La lista muestra el nombre y el ID.</p>
<figure class="captura"><img src="img/facturas-paso-cliente.webp" alt="Selección del cliente" loading="lazy"><figcaption>Selección del cliente</figcaption></figure>
</li>
<li>
<p><strong>Llena la primera línea.</strong></p>
<figure class="captura"><img src="img/facturas-paso-linea.webp" alt="Primera línea de la factura" loading="lazy"><figcaption>Primera línea de la factura</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Producto</strong>: cada opción muestra unidad, precio y stock.</li>
<li><span class="marca-num">2</span> <strong>Cantidad</strong>: número entero mayor que 0. No deja pasar del stock disponible.</li>
<li><span class="marca-num">3</span> Debajo verás el <strong>precio unitario</strong> y las <strong>existencias disponibles</strong> del producto elegido.</li>
</ul>
</li>
<li>
<p><strong>Agrega más productos si los necesitas.</strong></p>
<figure class="captura"><img src="img/facturas-paso-agregar.webp" alt="Varias líneas en la factura" loading="lazy"><figcaption>Varias líneas en la factura</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Agregar producto</strong> añade otra línea (máximo 100).</li>
<li><span class="marca-num">2</span> <strong>Quitar</strong> elimina una línea. Siempre queda al menos una.</li>
</ul>
</li>
<li>
<p><strong>Revisa el total y guarda.</strong></p>
<figure class="captura"><img src="img/facturas-paso-total.webp" alt="Total estimado y botón Guardar venta" loading="lazy"><figcaption>Total estimado y botón Guardar venta</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> El <strong>Total estimado</strong> se recalcula al instante con los precios actuales. Al guardar, cada línea conserva ese precio aunque después cambie el catálogo.</li>
<li><span class="marca-num">2</span> Pulsa <strong>Guardar venta</strong>. El botón cambia a <em>Guardando…</em> para evitar que se guarde dos veces.</li>
</ul>
</li>
<li>
<p><strong>Listo.</strong> Verás el número de la factura y su total. El stock de cada producto ya se descontó.</p>
<figure class="captura"><img src="img/facturas-paso-exito.webp" alt="Factura registrada" loading="lazy"><figcaption>Factura registrada</figcaption></figure>
</li>
</ol>
</div>
<h3 id="reglas-del-formulario">Reglas del formulario</h3>
<ul>
<li><strong>Cada producto una sola vez</strong>: si un producto ya está en una línea, aparece deshabilitado en las demás. Para vender más, sube la cantidad.</li>
<li>Los productos <strong>agotados</strong> (stock 0) aparecen deshabilitados.</li>
<li>Cuando ya elegiste todos los productos con stock, <strong>Agregar producto</strong> se desactiva.</li>
</ul>
<p>Al guardar, el servidor vuelve a comprobar <strong>todo</strong> dentro de una transacción. Si entre que abriste el formulario y guardaste alguien vendió y ya no alcanza el stock, verás:</p>
<blockquote>
<p>Las existencias cambiaron y no alcanzan para completar la venta. Revisa las cantidades e inténtalo de nuevo.</p>
</blockquote>
<p>y <strong>no se guarda nada</strong>: ni la factura ni los descuentos.</p>
<h2 id="buscar-facturas">Buscar facturas</h2>
<div class="pasos">
<ol>
<li>Menú → <em>Ventas → <strong>Facturas</strong></em>.</li>
<li>
<p>Usa uno o los dos filtros:</p>
<figure class="captura"><img src="img/facturas-buscar.webp" alt="Filtros de búsqueda de facturas" loading="lazy"><figcaption>Filtros de búsqueda de facturas</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Factura, ID de cliente o nombre</strong>: un número busca a la vez el número de factura, el ID del cliente y los nombres que lo contengan; un texto busca por nombre del cliente.</li>
<li><span class="marca-num">2</span> <strong>Fecha de la factura</strong>: todas las facturas de ese día.</li>
<li><span class="marca-num">3</span> Pulsa <strong>Buscar</strong>.</li>
</ul>
</li>
</ol>
</div>
<p>El listado muestra número, fecha y hora, cliente, estado, cantidad de líneas y <strong>total</strong>, de la más reciente a la más antigua.</p>
<figure class="captura"><img src="img/facturas-lista.webp" alt="Listado de facturas" loading="lazy"><figcaption>Listado de facturas</figcaption></figure>
<h2 id="ver-e-imprimir-el-comprobante">Ver e imprimir el comprobante</h2>
<div class="pasos">
<ol>
<li>
<p>En la fila de la factura, fíjate en <span class="marca-num">1</span> su <strong>estado</strong> y pulsa <span class="marca-num">2</span> <strong>Ver / imprimir</strong>.</p>
<figure class="captura"><img src="img/facturas-ver-boton.webp" alt="Abrir el comprobante" loading="lazy"><figcaption>Abrir el comprobante</figcaption></figure>
</li>
<li>
<p>Arriba del comprobante tienes tres botones:</p>
<figure class="captura"><img src="img/facturas-comprobante-botones.webp" alt="Botones del comprobante" loading="lazy"><figcaption>Botones del comprobante</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Imprimir</strong>: abre el diálogo de impresión del navegador. Puedes elegir <em>Guardar como PDF</em>.</li>
<li><span class="marca-num">2</span> <strong>Cancelar factura</strong>: aparece en facturas <em>Pagado</em> y <em>Pendiente</em> (ver abajo). En una <em>Pendiente</em> también verás <strong>Registrar pago</strong>.</li>
<li><span class="marca-num">3</span> <strong>Volver a facturas</strong>.</li>
</ul>
</li>
<li>
<p>El comprobante incluye el cliente, la fecha, <span class="marca-num">1</span> el estado, cada producto con unidad, cantidad, precio de venta y subtotal, y <span class="marca-num">2</span> el total.</p>
<figure class="captura"><img src="img/facturas-comprobante-detalle.webp" alt="Detalle del comprobante" loading="lazy"><figcaption>Detalle del comprobante</figcaption></figure>
</li>
</ol>
</div>
<p>Al imprimir se ocultan el menú y los botones; solo sale el comprobante:</p>
<figure class="captura"><img src="img/facturas-impresion.webp" alt="Vista de impresión" loading="lazy"><figcaption>Vista de impresión</figcaption></figure>
<div class="aviso aviso-caution">
<p class="aviso-titulo">Precaución</p>
<p>Es un <strong>comprobante interno</strong>, no una factura electrónica de la DIAN ni un documento fiscal.</p>
</div>
<h2 id="cancelar-una-factura">Cancelar una factura</h2>
<div class="pasos">
<ol>
<li>Abre el comprobante de una factura <strong>Pagado</strong> o <strong>Pendiente</strong> y pulsa <strong>Cancelar factura</strong> (<span class="marca-num">2</span> en la imagen de los botones).</li>
<li>Confirma: <em>«¿Cancelar esta factura y reponer las existencias?»</em>.</li>
<li>
<p>Listo:</p>
<figure class="captura"><img src="img/facturas-cancelada.webp" alt="Factura cancelada" loading="lazy"><figcaption>Factura cancelada</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> El aviso confirma que <strong>se repuso el inventario</strong>: cada producto recuperó las unidades vendidas.</li>
<li><span class="marca-num">2</span> El estado pasa a <strong>Cancelado</strong> y el botón de cancelar desaparece.</li>
</ul>
</li>
</ol>
</div>
<p><strong>La reposición ocurre una sola vez.</strong> Aunque alguien reenvíe la petición, el servidor responde <em>«Esta factura ya está cancelada; no se repuso el inventario otra vez.»</em></p>
<div class="pestanas">
<div class="pestana" data-titulo="Ejemplo del SRS">
<p>Una venta descontó <strong>3</strong> unidades. Luego se ajustó el stock a mano a <strong>1</strong>. Al cancelar la venta el stock queda en <strong>4</strong> (1 + 3). Una segunda cancelación no cambia nada.</p>
</div>
<div class="pestana" data-titulo="¿Y las facturas Pendiente?">
<p>Se cancelan igual que las <em>Pagado</em>: la mercancía vuelve al inventario y la deuda desaparece del reporte de pendientes.</p>
</div>
</div>
<h2 id="lo-que-no-se-puede-hacer">Lo que no se puede hacer</h2>
<ul>
<li><strong>Editar</strong> una factura guardada (cambiar cliente, productos o cantidades). Cancélala y registra una nueva.</li>
<li><strong>Eliminar</strong> facturas o líneas: el historial se conserva siempre.</li>
</ul>
