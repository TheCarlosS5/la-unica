<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="contenido">Actualizar artículos</h2>
<p>wiki/index.php acepta únicamente claves de includes/paginas.php. Cada clave corresponde a un archivo de paginas con la protección LA_UNICA_WIKI. Añade títulos h2 o h3 con id para que el índice local funcione y ajusta la descripción del catálogo al contenido.</p>
<h2 id="calidad">Mantener una sola guía vigente</h2>
<p>La wiki actual no incorpora el texto antiguo dentro de secciones desplegables. El historial Git conserva ese material. Antes de agregar capturas, verifica que pertenezcan a la interfaz actual y que no incluyan información privada; los archivos antiguos de img quedan como recursos históricos y no ilustran las guías actuales.</p>
<p>Ejecuta la prueba de enlaces y páginas de tests/advanced.py y revisa el buscador. La wiki debe seguir funcionando sin SQL y rechazar rutas ajenas al catálogo.</p>
