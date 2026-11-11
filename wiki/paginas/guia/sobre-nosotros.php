<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>La sección <strong>Sobre nosotros</strong> reúne la ayuda del aplicativo. Está en <em>Menú → Información → Sobre nosotros</em>.</p>
<figure class="captura"><img src="img/sobre-nosotros.webp" alt="Tarjetas de Sobre nosotros" loading="lazy"><figcaption>Tarjetas de Sobre nosotros</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Manual del Usuario</strong>: abre el manual en PDF dentro de la aplicación.</li>
<li><span class="marca-num">2</span> <strong>Video explicativo</strong>: abre el video de presentación.</li>
<li><span class="marca-num">3</span> <strong>Wiki</strong>: abre esta wiki. También está en <em>Menú → Información → Wiki</em>.</li>
</ul>
<div class="aviso aviso-tip">
<p class="aviso-titulo">Consejo</p>
<p>Esta sección y la wiki <strong>funcionan aunque MySQL esté apagado</strong>: no abren conexión a la base de datos. Útil para leer la ayuda mientras arreglas un problema de conexión.</p>
</div>
<h2 id="manual-del-usuario">Manual del Usuario</h2>
<p>Muestra <code>referencias/manual.pdf</code> (35 páginas, con capturas de los módulos) con el visor de PDF del navegador: puedes cambiar de página, buscar texto, hacer zoom, descargar o imprimir sin salir de La Única.</p>
<figure class="captura"><img src="img/sobre-nosotros-manual.webp" alt="Manual de usuario integrado" loading="lazy"><figcaption>Manual de usuario integrado</figcaption></figure>
<h2 id="video-explicativo">Video explicativo</h2>
<p>Reproduce <code>assets/video/1.mp4</code> con el reproductor del navegador. No se reproduce solo ni abre pestañas externas.</p>
<figure class="captura"><img src="img/sobre-nosotros-video.webp" alt="Página del video explicativo" loading="lazy"><figcaption>Página del video explicativo</figcaption></figure>
<p>Si el archivo no existe, la página muestra: <em>«Coloca el archivo 1.mp4 en la carpeta assets/video/ del proyecto y vuelve a cargar esta página.»</em> Ver <a href="index.php?pagina=configuracion/personalizar#video-explicativo">Personalizar → Video</a>.</p>
<h2 id="esta-wiki">Esta wiki</h2>
<p>La wiki vive en la carpeta <code>wiki/</code> del mismo proyecto y se abre desde <em>Menú → Información → Wiki</em> o en la dirección <code>…/la-unica/wiki/</code>. Está hecha en PHP plano, igual que la aplicación. Ver <a href="index.php?pagina=tecnico/wiki">Mantener esta wiki</a>.</p>
<h2 id="otros-documentos-del-proyecto">Otros documentos del proyecto</h2>
<p>En la carpeta <code>referencias/</code> del repositorio:</p>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Archivo</th>
<th>Contenido</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>SRS.docx</code> / <code>SRS.pdf</code> / <code>SRS.md</code></td>
<td>Especificación de requisitos <strong>v1.5</strong> (vigente). Resumida en <a href="index.php?pagina=tecnico/requisitos">Requisitos</a>.</td>
</tr>
<tr>
<td><code>manual.docx</code> / <code>manual.pdf</code></td>
<td>Manual de usuario.</td>
</tr>
<tr>
<td><code>db.sql</code></td>
<td>Esquema de la base de datos. Explicado en <a href="index.php?pagina=tecnico/base-de-datos">Base de datos</a>.</td>
</tr>
</tbody>
</table></div>
