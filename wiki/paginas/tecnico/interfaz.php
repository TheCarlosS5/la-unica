<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="plantillas">Plantillas y estilos</h2>
<p>php/includes/menu.php y pie.php reúnen la navegación y los recursos compartidos. assets/css/estilos.css define el diseño adaptable. Los encabezados de los archivos explican su función y los comentarios cercanos a transacciones o cálculos documentan decisiones importantes.</p>
<h2 id="javascript">Formulario de ventas</h2>
<p>assets/js/facturas.js gestiona líneas, selección de productos y total estimado. El servidor vuelve a validar cantidades, precio y disponibilidad; el navegador no decide el valor persistido de una venta. wiki/js/wiki.js gestiona las interacciones propias de la documentación.</p>
<h2 id="impresion">Impresión</h2>
<p>Los estilos de impresión eliminan controles, repiten cabeceras de tabla y organizan los saltos de página. Comprueba la vista previa y el PDF tras cambiar tablas, márgenes o columnas.</p>

<h2 id="guia-visual">Referencia visual de la versión actual</h2>
<p>Capturas reales con datos ficticios. Haz clic o pulsa Enter sobre una imagen para ampliarla.</p>
<figure class="captura"><img src="img/actuales/facturas-lineas.jpg" alt="Interfaz real — Líneas dinámicas y total estimado del formulario." width="1280" height="1118" loading="lazy" decoding="async"><figcaption>Interfaz real — Líneas dinámicas y total estimado del formulario.</figcaption></figure>
