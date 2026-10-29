<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>El esquema completo está en <code>referencias/db.sql</code>: <strong>4 tablas, 3 vistas y 5 procedimientos almacenados</strong>. No define motor, <em>charset</em>, índices únicos de negocio ni <em>triggers</em>, y <strong>no inserta datos</strong>.</p>
<figure class="diagrama-er">
<svg viewBox="0 0 660 390" role="img" aria-labelledby="er-titulo">
<title id="er-titulo">Relaciones: un cliente tiene muchas facturas; una factura tiene muchos detalles; un producto aparece en muchos detalles.</title>
<defs>
<marker id="muchos" viewBox="0 0 12 12" refX="11" refY="6" markerWidth="12" markerHeight="12" orient="auto-start-reverse">
<path d="M0 6 L11 0 M0 6 L11 6 M0 6 L11 12" class="trazo" fill="none">
</path>
</marker>
<marker id="uno" viewBox="0 0 12 12" refX="1" refY="6" markerWidth="12" markerHeight="12" orient="auto-start-reverse">
<path d="M4 0 L4 12 M8 0 L8 12" class="trazo" fill="none">
</path>
</marker>
</defs>
<g>
<rect x="10" y="10" width="250" height="118" rx="8" class="caja">
</rect>
<rect x="10" y="10" width="250" height="34" rx="8" class="cabecera">
</rect>
<rect x="10" y="36" width="250" height="8" class="cabecera">
</rect>
<text x="22" y="33" class="titulo">clientes</text>
<g>
<text x="22" y="63" class="campo clave"><tspan class="etiqueta">PK </tspan>id_cliente</text>
<text x="240" y="63" class="tipo" text-anchor="end">INT</text>
</g>
<g>
<text x="22" y="91" class="campo">nombre</text>
<text x="240" y="91" class="tipo" text-anchor="end">VARCHAR(100)</text>
</g>
<g>
<text x="22" y="119" class="campo">fecha_registro</text>
<text x="240" y="119" class="tipo" text-anchor="end">DATE</text>
</g>
</g>
<g>
<rect x="340" y="10" width="250" height="146" rx="8" class="caja">
</rect>
<rect x="340" y="10" width="250" height="34" rx="8" class="cabecera">
</rect>
<rect x="340" y="36" width="250" height="8" class="cabecera">
</rect>
<text x="352" y="33" class="titulo">facturas</text>
<g>
<text x="352" y="63" class="campo clave"><tspan class="etiqueta">PK </tspan>id_factura</text>
<text x="570" y="63" class="tipo" text-anchor="end">INT AI</text>
</g>
<g>
<text x="352" y="91" class="campo clave"><tspan class="etiqueta">FK </tspan>id_cliente</text>
<text x="570" y="91" class="tipo" text-anchor="end">INT</text>
</g>
<g>
<text x="352" y="119" class="campo">estado_factura</text>
<text x="570" y="119" class="tipo" text-anchor="end">ENUM</text>
</g>
<g>
<text x="352" y="147" class="campo">fecha_factura</text>
<text x="570" y="147" class="tipo" text-anchor="end">DATETIME</text>
</g>
</g>
<g>
<rect x="340" y="200" width="250" height="146" rx="8" class="caja">
</rect>
<rect x="340" y="200" width="250" height="34" rx="8" class="cabecera">
</rect>
<rect x="340" y="226" width="250" height="8" class="cabecera">
</rect>
<text x="352" y="223" class="titulo">detalle_facturas</text>
<g>
<text x="352" y="253" class="campo clave"><tspan class="etiqueta">PK </tspan>id_detalle_factura</text>
<text x="570" y="253" class="tipo" text-anchor="end">INT AI</text>
</g>
<g>
<text x="352" y="281" class="campo clave"><tspan class="etiqueta">FK </tspan>id_factura</text>
<text x="570" y="281" class="tipo" text-anchor="end">INT</text>
</g>
<g>
<text x="352" y="309" class="campo clave"><tspan class="etiqueta">FK </tspan>id_producto</text>
<text x="570" y="309" class="tipo" text-anchor="end">INT</text>
</g>
<g>
<text x="352" y="337" class="campo">cantidad</text>
<text x="570" y="337" class="tipo" text-anchor="end">INT &gt; 0</text>
</g>
</g>
<g>
<rect x="10" y="200" width="250" height="174" rx="8" class="caja">
</rect>
<rect x="10" y="200" width="250" height="34" rx="8" class="cabecera">
</rect>
<rect x="10" y="226" width="250" height="8" class="cabecera">
</rect>
<text x="22" y="223" class="titulo">productos</text>
<g>
<text x="22" y="253" class="campo clave"><tspan class="etiqueta">PK </tspan>id_producto</text>
<text x="240" y="253" class="tipo" text-anchor="end">INT AI</text>
</g>
<g>
<text x="22" y="281" class="campo">nombre</text>
<text x="240" y="281" class="tipo" text-anchor="end">VARCHAR(100)</text>
</g>
<g>
<text x="22" y="309" class="campo">unidad_medida</text>
<text x="240" y="309" class="tipo" text-anchor="end">VARCHAR(20)</text>
</g>
<g>
<text x="22" y="337" class="campo">stock</text>
<text x="240" y="337" class="tipo" text-anchor="end">INT ≥ 0</text>
</g>
<g>
<text x="22" y="365" class="campo">precio_unitario</text>
<text x="240" y="365" class="tipo" text-anchor="end">DECIMAL(10,2)</text>
</g>
</g>
<!-- clientes.id_cliente 1 ── N facturas.id_cliente -->
<path d="M260 58 C 300 58, 300 86, 340 86" class="relacion" marker-start="url(#uno)" marker-end="url(#muchos)">
</path>
<!-- facturas.id_factura 1 ── N detalle_facturas.id_factura -->
<path d="M590 58 C 645 58, 645 276, 590 276" class="relacion" marker-start="url(#uno)" marker-end="url(#muchos)">
</path>
<!-- productos.id_producto 1 ── N detalle_facturas.id_producto -->
<path d="M260 248 C 300 248, 300 304, 340 304" class="relacion" marker-start="url(#uno)" marker-end="url(#muchos)">
</path>
</svg>
<figcaption>Modelo de datos de <code>referencias/db.sql</code>: 1 cliente → N facturas → N detalles ← 1 producto.</figcaption>
</figure>

<h2 id="tablas">Tablas</h2>
<h3 id="clientes"><code>clientes</code></h3>
<pre class="highlight"><code class="language-sql">CREATE TABLE clientes (
    id_cliente INT PRIMARY KEY,                 -- manual, no AUTO_INCREMENT
    nombre VARCHAR(100) NOT NULL,               -- puede repetirse
    fecha_registro DATE DEFAULT (CURRENT_DATE)  -- hoy si no se envía
);</code></pre>
<h3 id="productos"><code>productos</code></h3>
<pre class="highlight"><code class="language-sql">CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    unidad_medida VARCHAR(20) NOT NULL,                         -- texto libre
    stock INT NOT NULL CHECK (stock &gt;= 0),
    precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario &gt;= 0)
);</code></pre>
<h3 id="facturas"><code>facturas</code></h3>
<pre class="highlight"><code class="language-sql">CREATE TABLE facturas (
    id_factura INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    estado_factura ENUM('Pagado', 'Cancelado', 'Pendiente') DEFAULT 'Pendiente',
    fecha_factura DATETIME DEFAULT (CURRENT_TIMESTAMP),
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente)
);</code></pre>
<h3 id="detalle_facturas"><code>detalle_facturas</code></h3>
<pre class="highlight"><code class="language-sql">CREATE TABLE detalle_facturas (
    id_detalle_factura INT AUTO_INCREMENT PRIMARY KEY,
    id_factura INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL CHECK (cantidad &gt; 0),
    FOREIGN KEY (id_factura) REFERENCES facturas(id_factura),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);</code></pre>
<h2 id="relaciones-y-reglas-que-impone-la-base">Relaciones y reglas que impone la base</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Regla</th>
<th>Mecanismo</th>
<th>Efecto en la app</th>
</tr>
</thead>
<tbody>
<tr>
<td>Una factura pertenece a un cliente existente</td>
<td>FK <code>facturas.id_cliente</code></td>
<td>No se puede borrar un cliente con facturas.</td>
</tr>
<tr>
<td>Cada línea apunta a factura y producto existentes</td>
<td>FK en <code>detalle_facturas</code></td>
<td>No se puede borrar un producto facturado.</td>
</tr>
<tr>
<td>Sin borrado en cascada</td>
<td>No hay <code>ON DELETE CASCADE</code></td>
<td>El historial nunca desaparece por accidente.</td>
</tr>
<tr>
<td>Stock nunca negativo</td>
<td><code>CHECK (stock &gt;= 0)</code></td>
<td>Última barrera si PHP fallara.</td>
</tr>
<tr>
<td>Precio nunca negativo</td>
<td><code>CHECK (precio_unitario &gt;= 0)</code></td>
<td></td>
</tr>
<tr>
<td>Cantidad siempre positiva</td>
<td><code>CHECK (cantidad &gt; 0)</code></td>
<td></td>
</tr>
<tr>
<td>Estado válido</td>
<td><code>ENUM</code></td>
<td>Solo <em>Pagado</em>, <em>Cancelado</em> o <em>Pendiente</em>.</td>
</tr>
</tbody>
</table></div>
<div class="aviso aviso-caution">
<p class="aviso-titulo">Sin precio histórico</p>
<p><code>detalle_facturas</code> no guarda el precio de venta. Todo importe se calcula como <code>cantidad × productos.precio_unitario</code> <strong>vigente</strong>. Es la limitación más importante del modelo (SRS RF-07). Una evolución natural sería añadir <code>precio_unitario</code> a <code>detalle_facturas</code> y llenarlo al vender.</p>
</div>
<p>Tampoco hay restricción <code>UNIQUE (id_factura, id_producto)</code>: la base permitiría dos líneas del mismo producto en una factura. La aplicación lo evita <strong>agrupando</strong> cantidades por producto antes de insertar.</p>
<h2 id="vistas">Vistas</h2>
<div class="pestanas">
<div class="pestana" data-titulo="vista_clientes_total_facturas">
<pre class="highlight"><code class="language-sql">-- Conserva clientes sin facturas y cuenta las asociadas, cualquiera sea su estado.
SELECT clientes.id_cliente, clientes.nombre,
       COUNT(facturas.id_factura) AS total_facturas
FROM clientes
LEFT JOIN facturas ON clientes.id_cliente = facturas.id_cliente
GROUP BY clientes.id_cliente, clientes.nombre;</code></pre>
</div>
<div class="pestana" data-titulo="vista_total_facturas">
<p><pre class="highlight"><code class="language-sql">-- Total por factura con el precio vigente; solo incluye facturas con detalles.
SELECT facturas.id_factura, clientes.nombre AS cliente,
       SUM(detalle_facturas.cantidad * productos.precio_unitario) AS total_factura
FROM facturas
INNER JOIN clientes ON facturas.id_cliente = clientes.id_cliente
INNER JOIN detalle_facturas ON facturas.id_factura = detalle_facturas.id_factura
INNER JOIN productos ON detalle_facturas.id_producto = productos.id_producto
GROUP BY facturas.id_factura, clientes.nombre;</code></pre>
La app la usa también para calcular el total al <strong>crear</strong> una factura, así el aviso y los reportes siguen la misma regla.</p>
</div>
<div class="pestana" data-titulo="vista_unidades_productos">
<pre class="highlight"><code class="language-sql">-- Suma cantidades de detalle por producto, de todos los estados.
SELECT productos.id_producto, productos.nombre AS producto,
       SUM(detalle_facturas.cantidad) AS unidades_facturadas
FROM productos
INNER JOIN detalle_facturas ON productos.id_producto = detalle_facturas.id_producto
GROUP BY productos.id_producto, productos.nombre;</code></pre>
</div>
</div>
<h2 id="procedimientos-almacenados">Procedimientos almacenados</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Procedimiento</th>
<th>Devuelve</th>
<th>Orden</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>reporte_detalle_facturas()</code></td>
<td>Una fila por línea: factura, cliente, estado, fecha, producto, cantidad, precio, subtotal</td>
<td><code>id_factura</code></td>
</tr>
<tr>
<td><code>reporte_facturas_clientes()</code></td>
<td>Igual que la vista de clientes</td>
<td><code>total_facturas DESC</code></td>
</tr>
<tr>
<td><code>reporte_stock_productos()</code></td>
<td>id, nombre, stock, precio</td>
<td><code>stock ASC</code></td>
</tr>
<tr>
<td><code>reporte_facturas_por_estado()</code></td>
<td>estado, total</td>
<td><code>estado_factura ASC</code></td>
</tr>
<tr>
<td><code>reporte_facturas_pendientes()</code></td>
<td>factura, cliente, fecha, estado (solo <em>Pendiente</em>)</td>
<td><code>fecha_factura ASC</code></td>
</tr>
</tbody>
</table></div>
<p>Se invocan con <code>CALL nombre()</code>. Como MySQLi devuelve un resultado extra tras un <code>CALL</code>, <code>consultarFilasReporte()</code> lo limpia con <code>next_result()</code> antes de la siguiente consulta.</p>
<h2 id="consultas-útiles">Consultas útiles</h2>
<pre class="highlight"><code class="language-sql">-- Productos con menos de 5 unidades
SELECT id_producto, nombre, stock FROM productos WHERE stock &lt; 5 ORDER BY stock;

-- Ventas cobradas (solo Pagado) por mes, con precio vigente
SELECT DATE_FORMAT(f.fecha_factura, '%Y-%m') AS mes,
       SUM(d.cantidad * p.precio_unitario)    AS total
FROM facturas f
JOIN detalle_facturas d ON d.id_factura = f.id_factura
JOIN productos p        ON p.id_producto = d.id_producto
WHERE f.estado_factura = 'Pagado'
GROUP BY mes ORDER BY mes;

-- Mejores clientes por importe
SELECT c.nombre, SUM(d.cantidad * p.precio_unitario) AS total
FROM clientes c
JOIN facturas f         ON f.id_cliente = c.id_cliente AND f.estado_factura = 'Pagado'
JOIN detalle_facturas d ON d.id_factura = f.id_factura
JOIN productos p        ON p.id_producto = d.id_producto
GROUP BY c.id_cliente, c.nombre ORDER BY total DESC LIMIT 5;</code></pre>
<h2 id="respaldo-y-restauración">Respaldo y restauración</h2>
<pre class="highlight"><code class="language-powershell"># Respaldo (incluye vistas y procedimientos)
mysqldump -u root -h 127.0.0.1 -P 5000 --routines --databases la_unica &gt; respaldo.sql

# Restaurar en un equipo sin la base
mysql -u root -h 127.0.0.1 -P 5000 --default-character-set=utf8mb4 &lt; respaldo.sql</code></pre>
