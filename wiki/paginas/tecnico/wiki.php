<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="contenido">Actualizar artículos</h2>
<p>wiki/index.php acepta únicamente claves de includes/paginas.php. Cada clave corresponde a un archivo de paginas con la protección LA_UNICA_WIKI. Añade títulos h2 o h3 con id para que el índice local funcione y ajusta la descripción del catálogo al contenido.</p>
<h2 id="calidad">Mantener una sola guía vigente</h2>
<p>La wiki actual no incorpora el texto antiguo dentro de secciones desplegables. El historial Git conserva ese material. Las guías incluyen capturas reales actualizadas en img/actuales, con ejemplos ficticios y pies de imagen. Los archivos anteriores de img se conservan como recursos históricos; no se usan para describir pantallas de otra versión.</p>
<p>Ejecuta la prueba de enlaces y páginas de tests/advanced.py y revisa el buscador. La wiki debe seguir funcionando sin SQL y rechazar rutas ajenas al catálogo.</p>

<h2 id="capturas">Regenerar las capturas de forma segura</h2><p>herramientas/generar_capturas.cjs abre Brave contra un servidor PHP independiente y una base nueva con prefijo la_unica_test_. Registra los flujos de ejemplo y genera imágenes y un manifiesto sin modificar la configuración privada ni la base de trabajo. Sigue herramientas/README.md y revisa cada imagen antes de publicarla.</p>
