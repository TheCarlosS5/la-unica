<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="contenido">Actualizar artículos</h2>
<p>wiki/index.php acepta únicamente claves de includes/paginas.php. Cada clave corresponde a un archivo de paginas con la protección LA_UNICA_WIKI. Añade títulos h2 o h3 con id para que el índice local funcione y ajusta la descripción del catálogo al contenido.</p>
<h2 id="calidad">Mantener una sola guía vigente</h2>
<p>La wiki actual no incorpora el texto antiguo dentro de secciones desplegables. El historial Git conserva ese material. Las guías incluyen capturas reales actualizadas en img/actuales, con ejemplos ficticios y pies de imagen. Los archivos anteriores de img se conservan como recursos históricos; no se usan para describir pantallas de otra versión.</p>
<p>Revisa los enlaces de los artículos modificados y prueba el buscador. La wiki debe funcionar sin SQL y rechazar rutas ajenas al catálogo.</p>

<h2 id="capturas">Actualizar las capturas</h2><p>Usa una instalación aislada con datos ficticios. En el repositorio, <a href="https://github.com/TheCarlosS5/la-unica/tree/main/wiki/herramientas">wiki/herramientas</a> documenta cómo generar las imágenes. Revisa las referencias, textos alternativos y pies en los artículos correspondientes.</p>
