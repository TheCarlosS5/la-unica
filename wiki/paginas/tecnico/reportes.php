<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>El módulo de reportes está diseñado para que <strong>agregar un reporte sea añadir datos, no copiar HTML</strong>.</p>
<h2 id="piezas">Piezas</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Archivo</th>
<th>Rol</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>datos_reportes.php</code></td>
<td><code>catalogoReportes()</code> (metadatos), <code>obtenerFilasReporte()</code> (qué SQL ejecutar) y <code>consultarFilasReporte()</code> (ejecutar y limpiar).</td>
</tr>
<tr>
<td><code>reporte_individual.php</code></td>
<td>Plantilla única: valida el tipo, consulta, y pinta error, estado vacío o tabla.</td>
</tr>
<tr>
<td><code>clientes_y_facturas.php</code> … (7 archivos)</td>
<td>Rutas públicas de ~12 líneas: <code>$tipoReporte = 'clientes'; require 'reporte_individual.php';</code></td>
</tr>
<tr>
<td><code>ver_reportes.php</code></td>
<td>Resumen: consulta los 7 y calcula indicadores.</td>
</tr>
<tr>
<td><code>includes/menu.php</code></td>
<td>Construye los enlaces del menú recorriendo el catálogo.</td>
</tr>
</tbody>
</table></div>
<h2 id="el-catálogo">El catálogo</h2>
<p>Cada entrada describe un reporte:</p>
<p class="codigo-titulo">php/reportes/datos_reportes.php</p>

<pre class="highlight"><code class="language-php">'total_facturas' =&gt; [
    'archivo'     =&gt; 'total_vigente_por_factura.php',
    'titulo'      =&gt; 'Total por factura',
    'descripcion' =&gt; 'Suma cantidad por precio de venta de cada factura Pagado o Pendiente.',
    'fuente'      =&gt; 'Vista vista_total_facturas',
    'columnas'    =&gt; [
        ['campo' =&gt; 'id_factura',     'titulo' =&gt; 'Factura', 'tipo' =&gt; 'id'],
        ['campo' =&gt; 'cliente',        'titulo' =&gt; 'Cliente', 'tipo' =&gt; 'texto'],
        ['campo' =&gt; 'estado_factura', 'titulo' =&gt; 'Estado',  'tipo' =&gt; 'estado'],
        ['campo' =&gt; 'total_factura',  'titulo' =&gt; 'Total',   'tipo' =&gt; 'moneda'],
    ],
    'enlace_factura' =&gt; true,   // la fila enlaza al comprobante
],</code></pre>
<p>Los <strong>tipos de columna</strong> deciden el formato: <code>id</code> (insignia <code>#N</code>), <code>texto</code>, <code>entero</code> (miles con punto), <code>moneda</code> (<code>formatearMonedaCop</code>), <code>fecha</code> y <code>estado</code> (insignia de color).</p>
<h2 id="fuentes-sql-por-reporte">Fuentes SQL por reporte</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Clave</th>
<th>SQL ejecutado</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>clientes</code></td>
<td><code>CALL reporte_facturas_clientes()</code></td>
</tr>
<tr>
<td><code>total_facturas</code></td>
<td><code>SELECT … FROM vista_total_facturas ORDER BY id_factura DESC</code></td>
</tr>
<tr>
<td><code>unidades_productos</code></td>
<td><code>SELECT … FROM vista_unidades_productos ORDER BY unidades_facturadas DESC, id_producto</code></td>
</tr>
<tr>
<td><code>detalle_facturas</code></td>
<td><code>CALL reporte_detalle_facturas()</code></td>
</tr>
<tr>
<td><code>stock_productos</code></td>
<td><code>CALL reporte_stock_productos()</code></td>
</tr>
<tr>
<td><code>por_estado</code></td>
<td><code>CALL reporte_facturas_por_estado()</code></td>
</tr>
<tr>
<td><code>pendientes</code></td>
<td><code>CALL reporte_facturas_pendientes()</code></td>
</tr>
</tbody>
</table></div>
<h3 id="control-de-coherencia-en-clientes">Control de coherencia en «clientes»</h3>
<p>El reporte de clientes lee <strong>dos</strong> fuentes que deberían dar lo mismo (la vista y el procedimiento), las normaliza (tipos y orden por ID) y si difieren lanza <code>UnexpectedValueException</code>. Así se detecta una base creada con un <code>db.sql</code> modificado.</p>
<h3 id="resultados-extra-de-call">Resultados extra de <code>CALL</code></h3>
<p>Un <code>CALL</code> en MySQLi deja un conjunto de resultados adicional pendiente. Si no se consume, la siguiente consulta falla con <em>«Commands out of sync»</em>. <code>consultarFilasReporte(…, true)</code> recorre <code>more_results()</code> / <code>next_result()</code> y libera todo.</p>
<h2 id="agregar-un-reporte-nuevo">Agregar un reporte nuevo</h2>
<div class="pasos">
<ol>
<li>Crea la vista o procedimiento en MySQL (y añádelo a <code>referencias/db.sql</code>).</li>
<li>Añade una entrada a <code>catalogoReportes()</code> con <code>archivo</code>, <code>titulo</code>, <code>descripcion</code>, <code>fuente</code> y <code>columnas</code>.</li>
<li>Añade un <code>case</code> en <code>obtenerFilasReporte()</code> con el SQL.</li>
<li>
<p>Crea la ruta pública, por ejemplo <code>php/reportes/ventas_por_mes.php</code>:</p>
<pre class="highlight"><code class="language-php">&lt;?php
$tipoReporte = 'ventas_mes';
require __DIR__ . '/reporte_individual.php';</code></pre>
</li>
<li>
<p>El enlace aparece solo en el menú. Si quieres una tarjeta en el resumen, añade su indicador en <code>ver_reportes.php</code>.</p>
</li>
</ol>
</div>
<div class="aviso aviso-caution">
<p class="aviso-titulo">Precaución</p>
<p><code>ver_reportes.php</code> consulta <strong>todas</strong> las fuentes: si una falla, no se muestra un resumen parcial. Prueba tu SQL antes de registrarlo.</p>
</div>
