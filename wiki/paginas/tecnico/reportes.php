<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="fuentes">Fuentes de datos</h2>
<p>Ventas y Unidades consultan procedimientos por fechas. Existencias usa el procedimiento de stock. Clientes y cantidad de facturas y Detalle usan consultas sobre las tablas actuales. datos_reportes.php contiene las definiciones auxiliares y reporte_individual.php presenta las consultas reutilizables. Algunas rutas antiguas redirigen a reportes vigentes por compatibilidad y no se muestran como opciones adicionales.</p>
<h2 id="calculos">Cálculos y presentación</h2>
<p>Los importes de ventas usan el precio histórico del detalle. Las canceladas se conservan para consulta, pero se excluyen de unidades vendidas y del total vendido. acciones_impresion.php prepara filtros, título y estilos para imprimir sin los controles de navegación.</p>
<p>La salida a PDF depende del diálogo del navegador. Los documentos técnicos registran la validación visual realizada y sus límites. La orientación horizontal del detalle evita comprimir sus ocho columnas.</p>

<h2 id="guia-visual">Referencia visual de la versión actual</h2>
<p>Capturas reales con datos ficticios. Haz clic o pulsa Enter sobre una imagen para ampliarla.</p>
<figure class="captura"><img src="img/actuales/reportes-detalle.jpg" alt="Consulta real — Detalles con precios históricos y estado." width="1600" height="1050" loading="lazy" decoding="async"><figcaption>Consulta real — Detalles con precios históricos y estado.</figcaption></figure>
<figure class="captura"><img src="img/actuales/reportes-impresion.jpg" alt="Salida visual — Estilos de impresión del reporte." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Salida visual — Estilos de impresión del reporte.</figcaption></figure>
