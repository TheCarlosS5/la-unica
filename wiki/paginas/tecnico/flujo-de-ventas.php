<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="venta">Venta atómica</h2>
<p>crear_factura.php valida CSRF, cliente y líneas. Agrupa productos repetidos, abre una transacción y bloquea el cliente y los productos con SELECT FOR UPDATE. Los productos se bloquean por ID ordenado. Relee precios y stock, inserta cabecera y detalles históricos, descuenta stock y registra la acción antes de confirmar. Un fallo revierte el conjunto.</p>
<h2 id="cancelacion">Cancelación atómica</h2>
<p>cancelar_factura.php exige confirmación, bloquea la factura, valida Pagado y fecha del día, bloquea los productos y devuelve las cantidades. Cambia el estado y registra la acción dentro de la transacción. El bloqueo y la comprobación del estado impiden una segunda reposición.</p>
<h2 id="prueba">Verificación de contención</h2>
<p>En la revisión técnica del 10 de noviembre de 2026, dos sesiones compitieron por la última unidad disponible y luego por cancelar una misma factura. Se repitió cada escenario diez veces. En cada caso persistió una sola venta o una sola reposición, según la operación.</p>
