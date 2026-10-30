<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Un <strong>producto</strong> es lo que vendes. Tiene una unidad de medida libre, existencias enteras y un precio de venta.</p>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Campo</th>
<th>Tipo</th>
<th>Reglas</th>
</tr>
</thead>
<tbody>
<tr>
<td>ID</td>
<td>número entero</td>
<td>Lo asigna la base automáticamente (<code>#1</code>, <code>#2</code>…).</td>
</tr>
<tr>
<td>Nombre</td>
<td>texto</td>
<td>Obligatorio, hasta 100 caracteres.</td>
</tr>
<tr>
<td>Unidad de medida</td>
<td>texto</td>
<td>Obligatoria, hasta 20 caracteres: <code>kg</code>, <code>litro</code>, <code>cubeta</code>, <code>unidad</code>, <code>libra</code>… No hay lista cerrada.</td>
</tr>
<tr>
<td>Stock</td>
<td>número entero</td>
<td>0 o más. No admite decimales.</td>
</tr>
<tr>
<td>Precio unitario</td>
<td>decimal</td>
<td>0 o más, <strong>máximo 2 decimales</strong>, hasta 99.999.999,99. Se escribe con punto: <code>4800.50</code>.</td>
</tr>
</tbody>
</table></div>
<h2 id="abrir-el-módulo">Abrir el módulo</h2>
<figure class="captura"><img src="img/productos-menu.webp" alt="Productos en el menú" loading="lazy"><figcaption>Productos en el menú</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Productos</strong> abre el catálogo.</li>
<li><span class="marca-num">2</span> <strong>Nuevo producto</strong> va directo al formulario.</li>
</ul>
<h2 id="leer-el-catálogo">Leer el catálogo</h2>
<figure class="captura"><img src="img/productos-lista-marcas.webp" alt="Marcas importantes del catálogo" loading="lazy"><figcaption>Marcas importantes del catálogo</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>0 (Agotado)</strong>: el stock llegó a cero. El producto <strong>no se puede vender</strong> hasta que repongas existencias.</li>
<li><span class="marca-num">2</span> <strong>Detalles</strong>: en cuántas líneas de factura aparece el producto.</li>
<li><span class="marca-num">3</span> <strong>En facturas</strong>: candado. El producto tiene historial y no se puede eliminar.</li>
<li><span class="marca-num">4</span> <strong>Eliminar</strong>: solo aparece en productos que nunca se han facturado.</li>
</ul>
<p>El buscador funciona igual que en <a href="index.php?pagina=guia/clientes#buscar-un-cliente">clientes</a>: ID o parte del nombre.</p>
<h2 id="crear-un-producto">Crear un producto</h2>
<div class="pasos">
<ol>
<li>Menú → <em>Productos → <strong>Nuevo producto</strong></em>.</li>
<li>
<p>Llena el formulario:</p>
<figure class="captura"><img src="img/productos-crear-formulario.webp" alt="Formulario de nuevo producto" loading="lazy"><figcaption>Formulario de nuevo producto</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Nombre del producto</strong>.</li>
<li><span class="marca-num">2</span> <strong>Unidad de medida</strong>: cómo lo vendes (<code>kg</code>, <code>litro</code>, <code>pastilla</code>…). Piénsala bien: se bloquea después de la primera venta.</li>
<li><span class="marca-num">3</span> <strong>Stock inicial</strong>: lo que tienes hoy en bodega. Cada venta lo descuenta y cada cancelación lo repone.</li>
<li><span class="marca-num">4</span> <strong>Precio unitario</strong>: con punto para los decimales (<code>2900.50</code>).</li>
<li><span class="marca-num">5</span> Pulsa <strong>Crear producto</strong>.</li>
</ul>
</li>
<li>
<p>Verás el aviso <em>«Producto creado correctamente.»</em> y el formulario listo para otro.</p>
</li>
</ol>
</div>
<h2 id="editar-un-producto">Editar un producto</h2>
<div class="pasos">
<ol>
<li>En el catálogo, pulsa <strong>Editar</strong> en la fila del producto.</li>
<li>
<p>Cambia lo que necesites:</p>
<figure class="captura"><img src="img/productos-editar-formulario.webp" alt="Edición de un producto que ya se vendió" loading="lazy"><figcaption>Edición de un producto que ya se vendió</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Unidad de medida</strong>: se puede cambiar <strong>solo si el producto nunca se ha facturado</strong>. La nota debajo del campo te lo recuerda.</li>
<li><span class="marca-num">2</span> <strong>Stock disponible</strong>: escribe la <strong>cantidad total</strong> que tienes ahora, no lo que vas a sumar. Si contaste 95 kg en bodega, escribe <code>95</code>.</li>
<li><span class="marca-num">3</span> <strong>Precio unitario</strong>: el nuevo precio de venta.</li>
</ul>
</li>
<li>
<p>Pulsa <strong>Guardar cambios</strong>.</p>
</li>
</ol>
</div>
<p>Si intentas cambiar la unidad de un producto que ya se vendió, verás este error y no se guardará nada:</p>
<figure class="captura"><img src="img/productos-editar-error.webp" alt="Error al cambiar la unidad" loading="lazy"><figcaption>Error al cambiar la unidad</figcaption></figure>
<div class="aviso aviso-note">
<p class="aviso-titulo">Cambiar el precio no altera facturas anteriores</p>
<p>Cada línea de factura guarda el precio <strong>del momento de la venta</strong>. Si subes el arroz de $4.800 a $5.000, las ventas nuevas usan $5.000 y la factura #1 conserva su total con $4.800.</p>
</div>
<h2 id="eliminar-un-producto">Eliminar un producto</h2>
<div class="pasos">
<ol>
<li>Pulsa <strong>Eliminar</strong> en la fila (<span class="marca-num">4</span> en la imagen del catálogo). Solo aparece en productos <strong>sin facturas</strong>.</li>
<li>Acepta la pregunta del navegador.</li>
<li>
<p>En la confirmación:</p>
<figure class="captura"><img src="img/productos-eliminar-confirmar.webp" alt="Confirmación de eliminación de producto" loading="lazy"><figcaption>Confirmación de eliminación de producto</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Cancelar</strong> vuelve al catálogo sin borrar.</li>
<li><span class="marca-num">2</span> <strong>Confirmar eliminación</strong> lo borra definitivamente.</li>
</ul>
</li>
</ol>
</div>
<p>¿Ya no vendes un producto que tiene historial? No lo puedes borrar, pero puedes dejar su stock en 0 para que no aparezca disponible en las ventas.</p>
