<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Las operaciones sobre facturas —<strong>vender</strong>, <strong>registrar el pago</strong> y <strong>cancelar</strong>— viven en <code>php/facturas/servicio_facturas.php</code> (<code>registrarVenta()</code>, <code>registrarPagoFactura()</code>, <code>cancelarFactura()</code>). Las páginas solo validan el formulario y llaman a esas funciones; <code>tests/Integration/FacturasServicioTest.php</code> las prueba contra una base real. Cada una corre en una <strong>transacción MySQL</strong> mediante <code>ejecutarEnTransaccion()</code>: o se aplica todo, o no se aplica nada (SRS RNF-04).</p>
<h2 id="crear-una-factura">Crear una factura</h2>
<h3 id="1-validación-previa-sin-tocar-la-base">1. Validación previa (sin tocar la base)</h3>
<ul>
<li>Token CSRF válido.</li>
<li><code>id_cliente</code> es un ID válido (<code>validarId</code>).</li>
<li><code>estado_factura</code> es <em>Pagado</em> (de contado) o <em>Pendiente</em> (por cobrar).</li>
<li>Entre <strong>1 y 100</strong> líneas; cada <code>id_producto</code> válido y cada <code>cantidad</code> entera entre 1 y 2.147.483.647.</li>
<li>Las líneas del mismo producto se <strong>agrupan</strong> sumando cantidades (<code>$cantidadesPorProducto</code>), verificando que la suma no desborde un <code>INT</code>.</li>
</ul>
<h3 id="2-transacción">2. Transacción</h3>
<p class="codigo-titulo">registrarVenta() en php/facturas/servicio_facturas.php (resumen)</p>

<pre class="highlight"><code class="language-php">$conexion-&gt;begin_transaction();

// a) El cliente debe seguir existiendo — y se bloquea
SELECT id_cliente FROM clientes WHERE id_cliente = ? FOR UPDATE;

// b) Bloquear productos SIEMPRE en orden de ID ascendente
sort($idsOrdenados, SORT_NUMERIC);
foreach ($idsOrdenados as $id) {
    SELECT stock, precio_unitario FROM productos WHERE id_producto = ? FOR UPDATE;
    // stock &lt; cantidad  →  DomainException("Las existencias cambiaron y no alcanzan…")
}

// c) Cabecera con el estado elegido (Pagado o Pendiente)
INSERT INTO facturas (id_cliente, estado_factura) VALUES (?, ?);

// d) Líneas con el precio leído bajo bloqueo, y descuento atómico
foreach ($idsOrdenados as $id) {
    INSERT INTO detalle_facturas (id_factura, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?);
    UPDATE productos SET stock = stock - ? WHERE id_producto = ? AND stock &gt;= ?;
    // affected_rows !== 1  →  DomainException
}

// e) Total con los precios guardados
SELECT SUM(cantidad * precio_unitario) FROM detalle_facturas WHERE id_factura = ?;

$conexion-&gt;commit();
// crear_factura.php guarda el resultado en sesión y redirige con 303</code></pre>
<h3 id="3-si-algo-falla">3. Si algo falla</h3>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Excepción</th>
<th>HTTP</th>
<th>Mensaje al usuario</th>
<th>Log</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>DomainException</code> (regla de negocio)</td>
<td>409</td>
<td>El texto de la excepción</td>
<td>No</td>
</tr>
<tr>
<td>Cualquier otra (<code>mysqli_sql_exception</code>…)</td>
<td>200</td>
<td>«No se pudo registrar la factura. No se aplicaron cambios…»</td>
<td>Sí</td>
</tr>
</tbody>
</table></div>
<p>En ambos casos se hace <code>rollback()</code> (y si el propio rollback falla, también se registra).</p>
<h3 id="por-qué-bloquear-en-orden">¿Por qué bloquear en orden?</h3>
<p>Si la venta A bloquea el producto 3 y luego el 7, y la venta B bloquea el 7 y luego el 3, cada una esperaría a la otra para siempre (<em>deadlock</em>). Ordenando por ID, todas las ventas piden los candados en la misma secuencia y el interbloqueo no puede ocurrir entre ellas.</p>
<h3 id="doble-protección-del-stock">Doble protección del stock</h3>
<ol>
<li><code>SELECT … FOR UPDATE</code> + comparación en PHP → mensaje claro.</li>
<li><code>UPDATE … WHERE stock &gt;= ?</code> + <code>affected_rows</code> → aunque algo se escapara, nunca resta de más.</li>
<li><code>CHECK (stock &gt;= 0)</code> en la tabla → última barrera en MySQL.</li>
</ol>
<h2 id="cancelar-una-factura">Cancelar una factura</h2>
<div class="pasos">
<ol>
<li>Solo por <strong>POST</strong> (otro método → <code>405</code> con cabecera <code>Allow: POST</code>) y con token CSRF.</li>
<li><code>cancelarFactura()</code>: <code>begin_transaction()</code> y <code>SELECT estado_factura … FOR UPDATE</code>. El bloqueo de la fila hace que dos cancelaciones simultáneas se atiendan una después de la otra.</li>
<li>Si no existe → error. Si es <em>Cancelado</em> → «ya está cancelada; no se repuso el inventario otra vez». <em>Pagado</em> y <em>Pendiente</em> se pueden cancelar.</li>
<li>Agrupar el detalle: <code>SELECT id_producto, SUM(cantidad) … GROUP BY id_producto ORDER BY id_producto</code>.</li>
<li>Por cada producto (en orden): <code>UPDATE productos SET stock = stock + ? WHERE id_producto = ?</code>. Si el resultado desbordara el <code>INT</code>, MySQL rechaza la operación y todo se revierte.</li>
<li><code>UPDATE facturas SET estado_factura = 'Cancelado' WHERE id_factura = ?</code>.</li>
<li><code>commit()</code> y redirección <code>303</code> al comprobante con el mensaje.</li>
</ol>
</div>
<div class="aviso aviso-tip">
<p class="aviso-titulo">Ejemplo de aceptación del SRS</p>
<p>Venta de 3 unidades → stock ajustado a mano a 1 → cancelación → stock 4. Una segunda cancelación no cambia nada. La reposición suma sobre el stock <strong>actual</strong>, no restaura un valor guardado.</p>
</div>
<h2 id="diagrama-de-estados">Diagrama de estados</h2>
<pre class="highlight"><code class="language-text">    venta de contado ───────────────►  Pagado ──────────────┐
                                         ▲                    │ cancelar_factura.php
                                         │ registrar_pago_    ▼
                                         │ factura.php      Cancelado   (final)
                                         │                    ▲
    venta por cobrar ───────────────►  Pendiente ────────────┘</code></pre>
