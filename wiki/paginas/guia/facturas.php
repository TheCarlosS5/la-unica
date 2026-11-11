<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="vender">Crear una factura</h2>
<p>Selecciona un cliente existente y una o varias líneas de productos con cantidades enteras positivas. Si repites un producto, el servidor suma sus cantidades. El precio final se consulta en SQL; el total del navegador es orientativo. Solo se guarda la venta si hay stock suficiente para todas las líneas.</p>
<h2 id="consultar">Consultar e imprimir</h2>
<p>Busca por número de factura, documento, nombre del cliente o fecha. Abre el comprobante para ver productos, cantidades, precios históricos y total. Usa el diálogo de impresión del navegador para imprimir o guardar como PDF.</p>
<h2 id="cancelar">Cancelar el mismo día</h2>
<p>Desde el comprobante de una factura Pagado del día actual solicita la cancelación y confirma. El estado cambia a Cancelado y el inventario se repone una vez. Una factura de otro día o ya cancelada no puede cancelarse de nuevo. El sistema utiliza la zona horaria America/Bogota.</p>
