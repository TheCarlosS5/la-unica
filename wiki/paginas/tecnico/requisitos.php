<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Resumen de <code>referencias/SRS.md</code> <strong>versión 1.4</strong> (27/10/2026, vigente). Las tres copias (<code>.docx</code>, <code>.pdf</code>, <code>.md</code>) deben mantenerse sincronizadas.</p>
<h2 id="resumen-ejecutivo">Resumen ejecutivo</h2>
<blockquote>
<p>La Única será una aplicación web local para registrar clientes y productos, gestionar facturas y consultar reportes. La base de datos <code>la_unica</code> se crea con cuatro tablas, tres vistas y cinco procedimientos almacenados definidos en <code>db.sql</code>. […] El esquema no guarda el precio histórico en cada detalle: los subtotales y totales se calculan con el precio actual del producto.</p>
</blockquote>
<p>Es un <strong>prototipo formativo</strong>: la factura es un comprobante interno sin IVA ni otros impuestos.</p>
<p><strong>Fuera del alcance:</strong> usuarios y roles, autenticación, recuperación de contraseñas, categorías, tabla de tienda, contabilidad avanzada, descuentos, pasarelas de pago, facturación electrónica y apps móviles nativas.</p>
<h2 id="historias-de-usuario">Historias de usuario</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>HU</th>
<th>Como operador, quiero…</th>
</tr>
</thead>
<tbody>
<tr>
<td>3.2.1 Clientes</td>
<td>registrar, buscar y actualizar clientes por <code>id_cliente</code> y nombre para seleccionar al comprador de una venta.</td>
</tr>
<tr>
<td>3.2.2 Productos</td>
<td>registrar y actualizar productos con nombre, unidad, precio y stock entero; ver los agotados y eliminar solo los que no tengan detalles.</td>
</tr>
<tr>
<td>3.2.3 Facturas</td>
<td>seleccionar un cliente y agregar productos con cantidades enteras positivas para generar una factura con total a precio actual y existencias actualizadas.</td>
</tr>
<tr>
<td>3.2.4 Consulta y cancelación</td>
<td>consultar e imprimir facturas por id, cliente o fecha; cancelar solo facturas <em>Pagado</em>.</td>
</tr>
<tr>
<td>3.2.5 Reportes</td>
<td>consultar reportes de clientes, ventas, existencias y estados.</td>
</tr>
</tbody>
</table></div>
<h2 id="requisitos-funcionales--trazabilidad">Requisitos funcionales — trazabilidad</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>ID</th>
<th>Requisito</th>
<th>Implementación</th>
<th>Estado</th>
</tr>
</thead>
<tbody>
<tr>
<td>RF-01</td>
<td>Registrar clientes</td>
<td><code>clientes/crear_cliente.php</code></td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-02</td>
<td>Consultar y actualizar clientes</td>
<td><code>clientes/ver_clientes.php</code>, <code>editar_cliente.php</code> (solo nombre)</td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-03</td>
<td>Registrar productos</td>
<td><code>productos/crear_producto.php</code></td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-04</td>
<td>Consultar y actualizar productos</td>
<td><code>productos/ver_productos.php</code>, <code>editar_producto.php</code> (unidad bloqueada si hay facturas)</td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-05</td>
<td>Preparar una factura</td>
<td><code>facturas/crear_factura.php</code> + <code>assets/js/facturas.js</code></td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-06</td>
<td>Generar la factura (estado <em>Pagado</em> explícito)</td>
<td><code>crear_factura.php</code> — <code>INSERT … 'Pagado'</code></td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-07</td>
<td>Calcular importes con precio vigente</td>
<td>Vistas + <code>imprimir_factura.php</code>, <code>ver_facturas.php</code></td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-08</td>
<td>Controlar existencias</td>
<td>Transacción con <code>FOR UPDATE</code> y <code>UPDATE … WHERE stock &gt;= ?</code></td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-09</td>
<td>Consultar e imprimir facturas</td>
<td><code>ver_facturas.php</code> (id / cliente / fecha), <code>imprimir_factura.php</code></td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-10</td>
<td>Cancelar facturas (Pagado → Cancelado, una vez)</td>
<td><code>facturas/cancelar_factura.php</code></td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-11</td>
<td>Validar los registros</td>
<td><code>validarId</code>, <code>validarFecha</code>, <code>longitudTexto</code>, regex de precio + <code>CHECK</code></td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-12</td>
<td>Eliminar registros sin historial, con confirmación</td>
<td><code>eliminar_cliente.php</code>, <code>eliminar_producto.php</code></td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
<tr>
<td>RF-13</td>
<td>Resumen + 7 reportes</td>
<td><code>reportes/ver_reportes.php</code> + 7 rutas</td>
<td><span class="insignia insignia-success">Cumple</span></td>
</tr>
</tbody>
</table></div>
<h2 id="requisitos-no-funcionales">Requisitos no funcionales</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>ID</th>
<th>Tema</th>
<th>Exigencia</th>
<th>Cómo se atiende</th>
</tr>
</thead>
<tbody>
<tr>
<td>RNF-01</td>
<td>Usabilidad</td>
<td>Acciones en 4 módulos; errores que identifiquen campo y causa; agotados y bloqueos visibles.</td>
<td>Menú por grupos, mensajes junto al formulario, insignias <em>Agotado</em> / <em>En facturas</em>.</td>
</tr>
<tr>
<td>RNF-02</td>
<td>Rendimiento</td>
<td>P95 ≤ 2 s en consultas por id con 1.000 clientes, 1.000 productos y 1.000 facturas (≤ 10 detalles c/u), 100 ejecuciones.</td>
<td>Consultas por clave primaria; sin medición automatizada en el repo. <span class="insignia insignia-caution">Por medir</span></td>
</tr>
<tr>
<td>RNF-03</td>
<td>Exposición del servicio</td>
<td>App y MySQL solo en <em>loopback</em>.</td>
<td>Conexión a <code>127.0.0.1</code>; responsabilidad del despliegue.</td>
</tr>
<tr>
<td>RNF-04</td>
<td>Integridad</td>
<td>Ventas y cancelaciones en transacciones; ajustes atómicos coordinados.</td>
<td>Ver <a href="index.php?pagina=tecnico/flujo-de-ventas">Flujo de ventas</a>.</td>
</tr>
<tr>
<td>RNF-05</td>
<td>Compatibilidad</td>
<td>Navegadores modernos; impresión del comprobante.</td>
<td>HTML/CSS estándar + <code>@media print</code>.</td>
</tr>
<tr>
<td>RNF-06</td>
<td>Mantenibilidad</td>
<td>PHP organizado; esquema reproducible con <code>db.sql</code>.</td>
<td>Estructura por módulos, cabeceras de documentación.</td>
</tr>
</tbody>
</table></div>
<h2 id="reglas-de-negocio-clave-352">Reglas de negocio clave (3.5.2)</h2>
<ul>
<li>Toda factura referencia un cliente; todo detalle, una factura y un producto existentes (FK).</li>
<li>Al menos <strong>un detalle por venta</strong> — lo valida PHP (el esquema no lo impone).</li>
<li><code>unidad_medida</code> es texto libre; <code>stock</code> y <code>cantidad</code> son enteros (<code>stock ≥ 0</code>, <code>cantidad &gt; 0</code>).</li>
<li>No se copian nombre ni precio al detalle: se muestran los <strong>vigentes</strong>.</li>
<li>Cambiar el precio cambia los importes de ventas anteriores.</li>
<li>Cancelar suma al stock <strong>vigente</strong> las cantidades del detalle, una sola vez.</li>
<li>Solo transición <strong>Pagado → Cancelado</strong>; <em>Pendiente</em> no tiene flujo en esta versión.</li>
<li>No se modifican ni eliminan facturas ni detalles.</li>
<li>Un producto con stock 0 sigue visible pero no se puede agregar a una venta.</li>
</ul>
<h2 id="criterios-de-aceptación-destacados-354">Criterios de aceptación destacados (3.5.4)</h2>
<div class="aviso aviso-note">
<p class="aviso-titulo">Nota</p>
<ul>
<li>Se permiten <strong>clientes con nombres repetidos</strong>.</li>
<li>Cantidades fraccionarias, cero o negativas se <strong>rechazan</strong>; stock insuficiente rechaza la venta <strong>sin cambios parciales</strong>.</li>
<li>Venta de 3 → ajuste manual a 1 → cancelar = <strong>4</strong>; una segunda cancelación no cambia nada.</li>
<li>Producto con stock 0 no se puede agregar por la interfaz, y el servidor rechaza intentos de saltarse la regla.</li>
<li>El precio acepta hasta dos decimales y un máximo de <strong>99.999.999,99</strong>.</li>
</ul>
</div>
<h2 id="historial-de-versiones-del-srs">Historial de versiones del SRS</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Versión</th>
<th>Fecha</th>
<th>Estado</th>
<th>Cambio</th>
</tr>
</thead>
<tbody>
<tr>
<td>1.0</td>
<td>05/10/2026</td>
<td>Reemplazada</td>
<td>Creación: RF y RNF.</td>
</tr>
<tr>
<td>1.1</td>
<td>06/10/2026</td>
<td>Reemplazada</td>
<td>Modelo de datos sin redundancias; añadía documento, correo, dirección, decimales.</td>
</tr>
<tr>
<td>1.2</td>
<td>26/10/2026</td>
<td>Reemplazada</td>
<td><strong>Alineación con <code>db.sql</code></strong> (se eliminan los campos que el esquema no tiene).</td>
</tr>
<tr>
<td>1.3</td>
<td>26/10/2026</td>
<td>Reemplazada</td>
<td>Stack: PHP + MySQL.</td>
</tr>
<tr>
<td>1.4</td>
<td>27/10/2026</td>
<td><strong>Vigente</strong></td>
<td>Ajustes a reportes, reglas, dependencias y aceptación.</td>
</tr>
</tbody>
</table></div>
