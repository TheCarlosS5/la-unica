<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Esta wiki corresponde a <strong>La Única 2</strong>. Consulta los <a href="../php/sobre_nosotros/index.php">manuales vigentes, el PAC y el video</a> o <a href="../main.php">vuelve al aplicativo</a>.</p>
<h2 id="version-la-unica-2">Indicaciones vigentes de La Única 2</h2>
<p>La wiki se incorporó después de la revisión documental 1.1 y de la ampliación de reportes. Los cambios locales no equivalen a una publicación en GitHub ni a una versión aprobada del SRS.</p>
<p>Los manuales 1.1 preceden las últimas ampliaciones de reportes y wiki; el README local registra esas incorporaciones. La reproducción, impresión y revisión visual en navegador siguen pendientes de verificación.</p>
<details><summary>Referencia histórica del proyecto original y sus capturas</summary>
<p><strong>Este material describe otra versión.</strong> Sus ejemplos de conexión, ramas, tablas, pagos y cancelaciones no deben aplicarse directamente a La Única 2. Para esta copia prevalecen las indicaciones de arriba y sus manuales.</p>

<p>Resumen de <code>RELEASE.md</code>. Las notas de cada versión viven en <strong>GitHub Releases</strong>, no en un archivo del repositorio.</p>
<h2 id="versionado">Versionado</h2>
<p>Etiquetas <code>vMAYOR.MENOR.PARCHE</code>:</p>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Cambio</th>
<th>Ejemplo</th>
</tr>
</thead>
<tbody>
<tr>
<td>Primera versión estable</td>
<td><code>v1.0.0</code></td>
</tr>
<tr>
<td>Corrección compatible</td>
<td><code>v1.0.1</code></td>
</tr>
<tr>
<td>Función nueva compatible</td>
<td><code>v1.1.0</code></td>
</tr>
<tr>
<td>Cambio incompatible</td>
<td><code>v2.0.0</code></td>
</tr>
</tbody>
</table></div>
<p>Una etiqueta publicada <strong>no se mueve ni se reutiliza</strong>: si hay un problema, se publica una versión nueva. La versión del SRS (1.5) y la del software son independientes.</p>
<h2 id="antes-de-publicar">Antes de publicar</h2>
<ul>
<li>[ ] Cambios revisados y subidos a <code>main</code>.</li>
<li>[ ] Sin configuraciones personales, contraseñas reales ni datos de clientes.</li>
<li>[ ] README y RELEASE coinciden con lo que hay.</li>
<li>[ ] <code>SRS.docx</code>, <code>SRS.pdf</code> y <code>SRS.md</code> en la misma versión; manual al día.</li>
<li>[ ] Instalación desde cero en base vacía.</li>
<li>[ ] CRUD de clientes y productos, bloqueos y confirmaciones.</li>
<li>[ ] Resumen + 7 reportes contra las vistas/procedimientos.</li>
<li>[ ] Sobre nosotros (manual y video).</li>
<li>[ ] Estilos y fuentes cargan sin Bootstrap ni <code>assets/vendor</code>.</li>
<li>[ ] Notas de versión preparadas.</li>
</ul>
<h2 id="etiquetar">Etiquetar</h2>
<div class="pasos">
<ol>
<li>
<p>Sincroniza <code>main</code> y comprueba que no hay cambios pendientes:</p>
<pre class="highlight"><code class="language-powershell">git switch main
git pull --ff-only origin main
git status --short</code></pre>
</li>
<li>
<p>Crea y sube la etiqueta anotada:</p>
<pre class="highlight"><code class="language-powershell">git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0</code></pre>
</li>
<li>
<p>En GitHub: <strong>Releases → Draft a new release</strong>, elige la etiqueta, título <code>La Única v1.0.0</code>, escribe las notas y publica (sin marcar <em>pre-release</em> si es estable).</p>
</li>
</ol>
</div>
<div class="aviso aviso-tip">
<p class="aviso-titulo">Consejo</p>
<p>Registra como verificación solo las pruebas manuales que de verdad hiciste: el proyecto no tiene pruebas automatizadas ni proceso de compilación.</p>
</div>

</details>
