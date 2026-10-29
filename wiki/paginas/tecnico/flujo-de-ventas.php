<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Las dos operaciones que tocan inventario —<strong>vender</strong> (<code>crear_factura.php</code>) y <strong>cancelar</strong> (<code>cancelar_factura.php</code>)— se ejecutan dentro de una <strong>transacción MySQL</strong>. O se aplica todo, o no se aplica nada (SRS RNF-04).</p>
<h2 id="crear-una-factura">Crear una factura</h2>
<h3 id="1-validación-previa-sin-tocar-la-base">1. Validación previa (sin tocar la base)</h3>
<ul>
<li>Token CSRF válido.</li>
<li><code>id_cliente</code> es un ID válido (<code>validarId</code>).</li>
<li>Entre <strong>1 y 100</strong> líneas; cada <code>id_producto</code> válido y cada <code>cantidad</code> entera entre 1 y 2.147.483.647.</li>
<li>Las líneas del mismo producto se <strong>agrupan</strong> sumando cantidades (<code>$cantidadesPorProducto</code>), verificando que la suma no desborde un <code>INT</code>.</li>
</ul>
<h3 id="2-transacción">2. Transacción</h3>
<p class="codigo-titulo">php/facturas/crear_factura.php (resumen)</p>

<pre class="highlight"><code class="language-php">$conexion-&gt;begin_transaction();

// a) El cliente debe seguir existiendo — y se bloquea
SELECT id_cliente FROM clientes WHERE id_cliente = ? FOR UPDATE;

// b) Bloquear productos SIEMPRE en orden de ID ascendente
sort($idsOrdenados, SORT_NUMERIC);
foreach ($idsOrdenados as $id) {
    SELECT stock FROM productos WHERE id_producto = ? FOR UPDATE;
    // stock &lt; cantidad  →  DomainException("Las existencias cambiaron y no alcanzan…")
}

// c) Cabecera con estado explícito
INSERT INTO facturas (id_cliente, estado_factura) VALUES (?, 'Pagado');

// d) Líneas y descuento atómico
foreach ($cantidadesPorProducto as $id =&gt; $cantidad) {
    INSERT INTO detalle_facturas (id_factura, id_producto, cantidad) VALUES (?, ?, ?);
    UPDATE productos SET stock = stock - ? WHERE id_producto = ? AND stock &gt;= ?;
    // affected_rows !== 1  →  DomainException
}

// e) Total con la misma regla que los reportes
SELECT total_factura FROM vista_total_facturas WHERE id_factura = ?;

$conexion-&gt;commit();
$_SESSION['factura_creada'] = [...];
header('Location: crear_factura.php', true, 303);</code></pre>
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
<li><code>begin_transaction()</code> y <code>SELECT estado_factura … FOR UPDATE</code>.</li>
<li>Si no existe → error. Si es <em>Cancelado</em> → «ya está cancelada; no se repuso el inventario otra vez». Si es <em>Pendiente</em> → «Solo se pueden cancelar facturas con estado Pagado».</li>
<li>Agrupar el detalle: <code>SELECT id_producto, SUM(cantidad) … GROUP BY id_producto ORDER BY id_producto</code>.</li>
<li>Por cada producto (en orden): bloquearlo, comprobar que <code>stock + cantidad</code> no supere 2.147.483.647, y <code>UPDATE productos SET stock = stock + ? WHERE id_producto = ? AND stock &lt;= ?</code>.</li>
<li><code>UPDATE facturas SET estado_factura = 'Cancelado' WHERE id_factura = ? AND estado_factura = 'Pagado'</code> — la condición sobre el estado evita una doble cancelación concurrente.</li>
<li><code>commit()</code> y redirección <code>303</code> al comprobante con el mensaje.</li>
</ol>
</div>
<div class="aviso aviso-tip">
<p class="aviso-titulo">Ejemplo de aceptación del SRS</p>
<p>Venta de 3 unidades → stock ajustado a mano a 1 → cancelación → stock 4. Una segunda cancelación no cambia nada. La reposición suma sobre el stock <strong>actual</strong>, no restaura un valor guardado.</p>
</div>
<h2 id="diagrama-de-estados">Diagrama de estados</h2>
<pre class="highlight"><code class="language-text">                 crear_factura.php
    (venta)  ───────────────────────►  Pagado
                                         │
                                         │ cancelar_factura.php
                                         ▼
                                     Cancelado   (final)

    INSERT directo en MySQL sin estado ─►  Pendiente   (sin transiciones en esta versión)</code></pre>
