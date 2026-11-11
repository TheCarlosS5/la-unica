<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="consultas">Los cinco reportes</h2>
<ul><li>Ventas: facturas por fechas o agrupación por día, semana, mes o año.</li><li>Unidades vendidas por producto: cantidades e importes de ventas vigentes en el rango.</li><li>Existencias: stock y precio actuales.</li><li>Clientes y cantidad de facturas: incluye clientes sin facturas y cuenta ambos estados.</li><li>Detalle de facturas: líneas con precio histórico y estado de cada factura.</li></ul>
<p>Ventas y unidades excluyen canceladas del total vendido. Detalle conserva ambos estados. La vista de ventas por factura permite revisar el estado; las agrupaciones distinguen los importes vendidos y cancelados. El resumen diario facilita la revisión del día.</p>
<h2 id="pdf">Imprimir y guardar como PDF</h2>
<p>Aplica los filtros antes de pulsar Imprimir reporte o Guardar como PDF. Ambos abren el diálogo del navegador; elige el destino PDF para exportar. Usa A4 y escala 100 %, sin encabezados ni pies añadidos por el navegador. Detalle de facturas usa orientación horizontal; los demás, vertical. La plantilla incorpora título, período, fecha y numeración.</p>
