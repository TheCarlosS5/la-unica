<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="logo">Logo</h2>
<p>Reemplaza <code>assets/img/logo.png</code> por tu imagen (cuadrada, PNG con transparencia, idealmente ≥ 256 px). Se usa como favicon y en la cabecera del menú lateral.</p>
<h2 id="colores">Colores</h2>
<p>Todos los colores son variables CSS en <code>:root</code>, al principio de <code>assets/css/estilos.css</code>. Cambiar una variable cambia toda la interfaz:</p>
<p class="codigo-titulo">assets/css/estilos.css</p>

<pre class="highlight"><code class="language-css">:root {
  /* Colores principales: verde "marcador" */
  --color-primario: #2d6a4f;
  --color-primario-hover: #245641;

  /* Resaltador amarillo: para lo activo, lo importante y los totales */
  --color-resaltador: #fff1b8;

  /* Papel */
  --color-fondo: #faf7f0;
  --color-superficie: #fffdf7;

  /* Tinta de lápiz (grafito) */
  --color-texto: #2b2b36;
}</code></pre>
<p>Hay además familias para estados: <code>--color-exito-*</code>, <code>--color-peligro-*</code> y <code>--color-aviso-*</code>.</p>
<h2 id="fuentes">Fuentes</h2>
<pre class="highlight"><code class="language-css">--fuente-titulo: "Architects Daughter", "Segoe Print", "Comic Sans MS", cursive;
--fuente-base: "Patrick Hand", "Segoe Print", "Comic Sans MS", system-ui, sans-serif;</code></pre>
<p>Para un aspecto más formal puedes usar las fuentes Inter que ya están en <code>assets/fonts/</code>:</p>
<pre class="highlight"><code class="language-css">--fuente-titulo: "Inter Tight", system-ui, sans-serif;
--fuente-base: "Inter", system-ui, sans-serif;</code></pre>
<p>(declarando sus <code>@font-face</code> igual que las existentes).</p>
<h2 id="tamaño-general">Tamaño general</h2>
<p><code>html { font-size: 17px; }</code> controla la escala de toda la interfaz porque las medidas usan <code>rem</code>. Súbelo a <code>18px</code> o <code>19px</code> si la pantalla del negocio está lejos.</p>
<h2 id="video-explicativo">Video explicativo</h2>
<p>Coloca un archivo MP4 en <code>assets/video/1.mp4</code>. La página <em>Sobre nosotros → Video explicativo</em> lo detecta con <code>file_exists()</code>; si falta, muestra un aviso con la instrucción en lugar del reproductor. El video no se reproduce solo.</p>
<div class="aviso aviso-tip">
<p class="aviso-titulo">Consejo</p>
<p>Usa H.264 + AAC para que funcione en cualquier navegador, y mantenlo por debajo de ~100 MB para que GitHub lo acepte.</p>
</div>
<h2 id="manual-de-usuario">Manual de usuario</h2>
<p><em>Sobre nosotros → Manual del Usuario</em> incrusta <code>referencias/manual.pdf</code>. Para actualizarlo, edita <code>referencias/manual.docx</code>, expórtalo a PDF con el mismo nombre y reemplaza el archivo.</p>
<h2 id="textos">Textos</h2>
<p>Los títulos y descripciones de los reportes están centralizados en <code>catalogoReportes()</code> de <code>php/reportes/datos_reportes.php</code>; el resto de textos está directamente en cada vista PHP.</p>
