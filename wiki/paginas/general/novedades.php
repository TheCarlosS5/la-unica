<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Cronología del repositorio hasta la rama <code>revision-samuel</code> (79 commits entre el 22 y el 28 de octubre de 2026). Las fechas y mensajes salen de <code>git log</code>.</p>
<h2 id="30-de-octubre-de-2026--precio-de-venta-y-ventas-por-cobrar">30 de octubre de 2026 — precio de venta, ventas por cobrar y pruebas</h2>
<ul>
<li><code>fix:</code> <code>detalle_facturas</code> guarda el <strong>precio de la venta</strong>; editar el catálogo ya no cambia facturas anteriores. Las bases existentes se actualizan con <code>referencias/migraciones/001_precio_historico.sql</code>.</li>
<li><code>feat:</code> ventas <strong>por cobrar</strong> (estado <em>Pendiente</em>), con <em>Registrar pago</em> y cancelación desde el comprobante.</li>
<li><code>fix:</code> los reportes de ventas (totales y unidades) excluyen las facturas canceladas.</li>
<li><code>fix:</code> el comprobante ya no falla con un error fatal cuando la consulta de detalles falla.</li>
<li><code>fix:</code> puerto MySQL por defecto <code>3306</code> (BUG-001); usuario <code>la_unica_app</code>, cookies de sesión <code>HttpOnly</code>/<code>SameSite</code>, cabeceras de seguridad y <code>.htaccess</code>.</li>
<li><code>docs:</code> <strong>SRS 1.5</strong> en <code>.docx</code>, <code>.pdf</code> y <code>.md</code>: requisitos alineados con el aplicativo, RF-14, RF-15, RNF-07, RNF-08 y nueva sección de arquitectura.</li>
<li><code>docs:</code> <strong>Manual de usuario 1.1</strong> (<code>.docx</code> y <code>.pdf</code>): venta por cobrar, registro de pago (5.4), Sobre nosotros (2.4), reportes actualizados, capturas nuevas e índice corregido. También se regeneraron las capturas de la wiki.</li>
<li><code>fix:</code> el reporte <em>Total por factura</em> ya muestra la columna Estado.</li>
<li><code>test:</code> PHPUnit con 30 pruebas de integración (reportes, ventas, pagos y cancelaciones) que corren en GitHub Actions en cada push.</li>
</ul>
<h2 id="28-de-octubre-de-2026--versión-funcional-revisada">28 de octubre de 2026 — versión funcional revisada</h2>
<ul>
<li><strong>Versión funcional</strong> subida por Samuel con la sección <strong>Sobre nosotros</strong> (manual PDF integrado y página del video explicativo).</li>
<li><code>fix:</code> se corrige el <strong>puerto MySQL</strong> (queda en <code>5000</code>), el menú y el aviso del video en «Sobre nosotros».</li>
<li><code>chore:</code> se quitan archivos huérfanos que ninguna página usaba.</li>
<li>Se eliminan los borradores del manual (<code>Manual_usuario.docx</code>, <code>Manual_de_usuario_La_Unica.docx</code>); quedan <code>manual.docx</code> y <code>manual.pdf</code>.</li>
<li>Se agrega <code>assets/video/1.mp4</code> (video de prueba).</li>
</ul>
<h2 id="27-de-octubre--srs-14-y-manual-de-usuario">27 de octubre — SRS 1.4 y manual de usuario</h2>
<ul>
<li><strong>SRS v1.4</strong>: ajustes a reportes, reglas, dependencias y criterios de aceptación.</li>
<li>Camilo agrega y actualiza el <strong>manual de usuario</strong> con capturas de los módulos.</li>
<li>«Actualización 90 % terminado» y merge del PR #19 (<code>camilo-trabajo</code>).</li>
</ul>
<h2 id="26-de-octubre--todo-el-srs-en-php">26 de octubre — todo el SRS en PHP</h2>
<ul>
<li>Rediseño del CRUD y alineación al <strong>SRS v1.2</strong> (alineado con <code>db.sql</code>): facturas, cancelaciones y reportes.</li>
<li><strong>SRS v1.3</strong>: el stack tecnológico pasa oficialmente a PHP + MySQL.</li>
<li>Se revierte la simplificación en camelCase (PR #13): el equipo prefiere código explícito y comentado, fácil para principiantes.</li>
<li>Backend completo de clientes y productos; <code>conexion.php</code> con utilidades compartidas.</li>
</ul>
<h2 id="22-de-octubre--primeros-pasos">22 de octubre — primeros pasos</h2>
<ul>
<li>Primer commit, front «tipo libreta» y retiro del módulo de caja: el alcance se centra en clientes y productos.</li>
<li>Nuevo front para el CRUD (PR #1), README corregido (PR #5) y el script <code>db.sql</code> definitivo.</li>
<li>Mejoras de accesibilidad WAI-ARIA y validaciones en formularios (PR #10).</li>
</ul>
<h2 id="versiones-del-srs">Versiones del SRS</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Versión</th>
<th>Fecha</th>
<th>Cambio principal</th>
</tr>
</thead>
<tbody>
<tr>
<td>1.0</td>
<td>05/10/2026</td>
<td>Creación del SRS: requisitos funcionales y no funcionales.</td>
</tr>
<tr>
<td>1.1</td>
<td>06/10/2026</td>
<td>Corrección del modelo de datos (sin datos redundantes).</td>
</tr>
<tr>
<td>1.2</td>
<td>26/10/2026</td>
<td>Alineación de entidades, campos, estados y reportes con <code>db.sql</code>.</td>
</tr>
<tr>
<td>1.3</td>
<td>26/10/2026</td>
<td>Stack tecnológico: PHP y MySQL.</td>
</tr>
<tr>
<td>1.4</td>
<td>27/10/2026</td>
<td>Ajustes a reportes, reglas, dependencias y aceptación.</td>
</tr>
<tr>
<td>1.5</td>
<td>30/10/2026</td>
<td><strong>Vigente.</strong> Alineación con el aplicativo, arquitectura, seguridad y pruebas.</td>
</tr>
</tbody>
</table></div>
