<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="estilo-visual-papel-y-marcador">Estilo visual: «papel y marcador»</h2>
<p>Toda la interfaz sale de <code>assets/css/estilos.css</code> (≈2.200 líneas, sin Bootstrap ni frameworks). La idea es una libreta: fondo de papel cálido, tinta de grafito, un <strong>marcador verde</strong> para las acciones y un <strong>resaltador amarillo</strong> para lo importante (totales, elemento activo).</p>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Token</th>
<th>Valor</th>
<th>Uso</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>--color-primario</code></td>
<td><code>#2d6a4f</code></td>
<td>Botones principales, enlaces activos</td>
</tr>
<tr>
<td><code>--color-resaltador</code></td>
<td><code>#fff1b8</code></td>
<td>Total estimado, identificador fijo</td>
</tr>
<tr>
<td><code>--color-fondo</code></td>
<td><code>#faf7f0</code></td>
<td>Fondo de página</td>
</tr>
<tr>
<td><code>--color-superficie</code></td>
<td><code>#fffdf7</code></td>
<td>Tarjetas</td>
</tr>
<tr>
<td><code>--color-texto</code></td>
<td><code>#2b2b36</code></td>
<td>Texto (grafito)</td>
</tr>
<tr>
<td><code>--color-peligro</code></td>
<td><code>#b3301f</code></td>
<td>Eliminar, cancelar, agotado</td>
</tr>
<tr>
<td><code>--color-aviso</code></td>
<td><code>#7a5200</code></td>
<td>Candados «En facturas», pendiente</td>
</tr>
<tr>
<td><code>--fuente-titulo</code></td>
<td>Architects Daughter</td>
<td>Títulos</td>
</tr>
<tr>
<td><code>--fuente-base</code></td>
<td>Patrick Hand</td>
<td>Texto</td>
</tr>
</tbody>
</table></div>
<p>Otros tokens: radios (<code>--radio-sm/md/lg/pill</code>), sombras (<code>--sombra-sm/md/lg</code>), dimensiones del <em>shell</em> (<code>--ancho-sidebar: 262px</code>, <code>--alto-topbar: 60px</code>) y transiciones.</p>
<h2 id="plantilla-de-página">Plantilla de página</h2>
<p><code>includes/menu.php</code> + <code>includes/pie.php</code> envuelven cada vista:</p>
<pre class="highlight"><code class="language-text">body.shell-layout
├─ a.salto-contenido            «Saltar al contenido principal» (accesibilidad)
├─ #drawer-backdrop             fondo oscuro del menú móvil
└─ .layout-app
    ├─ aside#barra-lateral       marca + grupos Ventas / Catálogo / Información
    └─ (barra superior: botón ☰, migas, «Administración local»)
      └─ main#contenido-principal
         └─ … la vista …
    └─ pie</code></pre>
<p>Los enlaces del menú reciben la clase <code>activo</code> y <code>aria-current="page"</code> según <code>$seccionActiva</code> y el archivo actual. Los 7 subenlaces de Reportes se generan desde <code>catalogoReportes()</code>.</p>
<h2 id="menú-adaptable">Menú adaptable</h2>
<p>El JavaScript de <code>pie.php</code>:</p>
<ul>
<li>abre/cierra el panel con el botón ☰ (<code>#btn-abrir-drawer</code>) y ✕ (<code>#btn-cerrar-drawer</code>),</li>
<li>lo cierra al tocar el fondo o pulsar <kbd>Esc</kbd>,</li>
<li>actualiza <code>aria-expanded</code> y <strong>devuelve el foco</strong> al botón que lo abrió.</li>
</ul>
<h2 id="impresión">Impresión</h2>
<p>Una hoja <code>@media print</code> oculta menú, barra superior, botones y pie, y deja el comprobante a ancho completo en blanco y negro legible. Ver la captura en <a href="index.php?pagina=guia/facturas#ver-e-imprimir-el-comprobante">Facturas</a>.</p>
<h2 id="assetsjsfacturasjs"><code>assets/js/facturas.js</code></h2>
<p>Script autoejecutable (IIFE, <code>'use strict'</code>) que solo actúa si encuentra <code>#formulario-factura</code>.</p>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Función</th>
<th>Qué hace</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>centavos(precio)</code></td>
<td>Convierte <code>"4300.00"</code> a <code>430000n</code> (<strong>BigInt</strong>), sin errores de coma flotante.</td>
</tr>
<tr>
<td><code>monedaDesdeCentavos(valor)</code></td>
<td><code>430000n</code> → <code>"$4.300,00 COP"</code> con <code>Intl.NumberFormat('es-CO')</code>.</td>
</tr>
<tr>
<td><code>asignarIds(fila)</code></td>
<td>Da IDs únicos a cada <code>select</code>/<code>input</code> y enlaza sus <code>&lt;label for&gt;</code>.</td>
</tr>
<tr>
<td><code>actualizarDetalle(fila)</code></td>
<td>Muestra precio y existencias del producto elegido; fija <code>max</code> de la cantidad al stock.</td>
</tr>
<tr>
<td><code>actualizarOpciones()</code></td>
<td>Deshabilita productos agotados y los ya elegidos en otra línea.</td>
</tr>
<tr>
<td><code>actualizarTotal()</code></td>
<td>Suma <code>precio × cantidad</code> de las líneas válidas.</td>
</tr>
<tr>
<td><code>actualizarControles()</code></td>
<td>Desactiva «Agregar producto» si no quedan productos y «Quitar» si solo hay una línea.</td>
</tr>
</tbody>
</table></div>
<p>Las líneas nuevas se clonan de un <code>&lt;template id="plantilla-linea-factura"&gt;</code>. Al enviar, el botón se desactiva y muestra <em>Guardando…</em>.</p>
<div class="aviso aviso-note">
<p class="aviso-titulo">Nota</p>
<p>El total del navegador es <strong>orientativo</strong>. El que vale es el que calcula el servidor con <code>vista_total_facturas</code> dentro de la transacción.</p>
</div>
<h2 id="accesibilidad">Accesibilidad</h2>
<ul>
<li>Enlace «Saltar al contenido principal».</li>
<li>Etiquetas <code>&lt;label&gt;</code> asociadas a cada campo; indicaciones <em>Requerido / Opcional / Hasta 100 caracteres</em>.</li>
<li>Mensajes con <code>role="alert"</code> (errores) y <code>role="status"</code> (éxitos).</li>
<li>Íconos SVG con <code>aria-hidden="true"</code> y botones con <code>aria-label</code>.</li>
<li>Navegación con <code>aria-label</code> por grupo y <code>aria-current</code> en la página actual.</li>
</ul>
