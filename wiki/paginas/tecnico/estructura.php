<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Rama <code>revision-samuel</code>. Unas <strong>6.300 líneas</strong> entre PHP, CSS y JS.</p>
<div class="arbol">
<ul>
<li>index.php redirige a main.php</li>
<li>main.php incluye php/inicio/main.php</li>
<li>README.md</li>
<li>RELEASE.md guía para publicar versiones</li>
<li>assets/<ul>
<li>css/<ul>
<li>estilos.css <strong>≈2.200 líneas</strong>: variables, layout, componentes, impresión</li>
</ul>
</li>
<li>fonts/<ul>
<li>architects-daughter-latin.woff2 títulos</li>
<li>patrick-hand-latin.woff2 texto</li>
<li>inter-*.woff2, inter-tight-*.woff2, pixelify-sans-*.woff2</li>
<li>LICENCIAS.txt</li>
</ul>
</li>
<li>img/<ul>
<li>logo.png favicon y marca</li>
</ul>
</li>
<li>js/<ul>
<li>facturas.js líneas dinámicas y total en vivo</li>
</ul>
</li>
<li>video/<ul>
<li>1.mp4 video explicativo</li>
</ul>
</li>
</ul>
</li>
<li>php/<ul>
<li>config/<ul>
<li>conexion.php conexión MySQLi + utilidades compartidas</li>
</ul>
</li>
<li>includes/<ul>
<li>menu.php <code>&lt;head&gt;</code>, menú lateral, barra superior, abre <code>&lt;main&gt;</code></li>
<li>pie.php cierra <code>&lt;main&gt;</code>, pie y JS del menú adaptable</li>
</ul>
</li>
<li>inicio/<ul>
<li>main.php panel de inicio</li>
</ul>
</li>
<li>clientes/<ul>
<li>ver_clientes.php listado + búsqueda</li>
<li>crear_cliente.php</li>
<li>editar_cliente.php</li>
<li>eliminar_cliente.php confirmación + DELETE</li>
</ul>
</li>
<li>productos/<ul>
<li>ver_productos.php</li>
<li>crear_producto.php</li>
<li>editar_producto.php</li>
<li>eliminar_producto.php</li>
</ul>
</li>
<li>facturas/<ul>
<li>ver_facturas.php búsqueda</li>
<li>crear_factura.php venta con transacción</li>
<li>imprimir_factura.php comprobante</li>
<li>cancelar_factura.php cancelación con transacción</li>
</ul>
</li>
<li>reportes/<ul>
<li>ver_reportes.php resumen</li>
<li>datos_reportes.php catálogo + acceso a datos</li>
<li>reporte_individual.php plantilla común</li>
<li>clientes_y_facturas.php</li>
<li>total_vigente_por_factura.php</li>
<li>unidades_facturadas_por_producto.php</li>
<li>detalle_facturas.php</li>
<li>existencias_productos.php</li>
<li>facturas_por_estado.php</li>
<li>facturas_pendientes.php</li>
</ul>
</li>
<li>sobre_nosotros/<ul>
<li>index.php</li>
<li>inicializar.php base URL + h() sin conectar a MySQL</li>
<li>manual_usuario.php visor del PDF</li>
<li>video_explicativo.php</li>
</ul>
</li>
</ul>
</li>
<li>referencias/<ul>
<li>db.sql esquema</li>
<li>SRS.docx, SRS.pdf, SRS.md v1.4</li>
<li>manual.docx, manual.pdf</li>
</ul>
</li>
<li>wiki/ <strong>esta wiki</strong> (no usa la base de datos)<ul>
<li>index.php enrutador: <code>index.php?pagina=guia/clientes</code></li>
<li>includes/ catálogo de páginas, funciones y plantilla</li>
<li>paginas/ una página = un archivo .php con HTML</li>
<li>css/wiki.css</li>
<li>js/wiki.js</li>
<li>img/ capturas numeradas (.webp)</li>
<li>herramientas/ script de capturas y datos de ejemplo</li>
</ul>
</li>
</ul>
</div>
<h2 id="tamaño-por-archivo-líneas">Tamaño por archivo (líneas)</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Archivo</th>
<th style="text-align: right;">Líneas</th>
<th></th>
<th>Archivo</th>
<th style="text-align: right;">Líneas</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>facturas/crear_factura.php</code></td>
<td style="text-align: right;">346</td>
<td></td>
<td><code>clientes/ver_clientes.php</code></td>
<td style="text-align: right;">213</td>
</tr>
<tr>
<td><code>config/conexion.php</code></td>
<td style="text-align: right;">243</td>
<td></td>
<td><code>clientes/eliminar_cliente.php</code></td>
<td style="text-align: right;">195</td>
</tr>
<tr>
<td><code>includes/menu.php</code></td>
<td style="text-align: right;">239</td>
<td></td>
<td><code>productos/eliminar_producto.php</code></td>
<td style="text-align: right;">195</td>
</tr>
<tr>
<td><code>productos/editar_producto.php</code></td>
<td style="text-align: right;">229</td>
<td></td>
<td><code>facturas/imprimir_factura.php</code></td>
<td style="text-align: right;">197</td>
</tr>
<tr>
<td><code>reportes/datos_reportes.php</code></td>
<td style="text-align: right;">226</td>
<td></td>
<td><code>facturas/ver_facturas.php</code></td>
<td style="text-align: right;">186</td>
</tr>
<tr>
<td><code>productos/ver_productos.php</code></td>
<td style="text-align: right;">224</td>
<td></td>
<td><code>clientes/crear_cliente.php</code></td>
<td style="text-align: right;">184</td>
</tr>
</tbody>
</table></div>
<p>Las 7 rutas de reportes tienen ~12 líneas cada una: solo asignan <code>$tipoReporte</code> y cargan la plantilla.</p>
<h2 id="convenciones">Convenciones</h2>
<ul>
<li><strong>Nombres en español</strong>: variables (<code>$mensajeError</code>, <code>$idCliente</code>), funciones (<code>validarId</code>, <code>formatearMonedaCop</code>), clases CSS (<code>.boton-peligro</code>, <code>.barra-lateral</code>).</li>
<li><strong>Archivos en <code>snake_case</code></strong> con verbo + entidad: <code>crear_cliente.php</code>, <code>ver_facturas.php</code>.</li>
<li><strong>Cabecera de documentación</strong> obligatoria en cada archivo: <em>Archivo, Módulo, Propósito, Funcionamiento, Puntos clave</em>.</li>
<li><strong>Funciones compartidas</strong> envueltas en <code>if (!function_exists(...))</code> para poder incluir <code>conexion.php</code> más de una vez sin errores.</li>
<li><strong>Alias de compatibilidad</strong> (<code>id_valido</code>, <code>fecha_valida</code>, <code>texto_largo</code>) para código antiguo que use los nombres en snake_case.</li>
</ul>
