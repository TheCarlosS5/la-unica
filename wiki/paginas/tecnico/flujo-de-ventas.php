<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="venta">Venta atómica</h2>
<p>crear_factura.php valida CSRF, cliente y líneas. Agrupa productos repetidos, abre una transacción y bloquea el cliente y los productos con SELECT FOR UPDATE. Los productos se bloquean por ID ordenado. Relee precios y stock, inserta cabecera y detalles históricos, descuenta stock y registra la acción antes de confirmar. Un fallo revierte el conjunto.</p>
<h2 id="cancelacion">Cancelación atómica</h2>
<p>cancelar_factura.php exige confirmación, bloquea la factura, valida Pagado y fecha del día, bloquea los productos y devuelve las cantidades. Cambia el estado y registra la acción dentro de la transacción. El bloqueo y la comprobación del estado impiden una segunda reposición.</p>
<h2 id="prueba">Prueba de contención</h2>
<p>tests/advanced.py utiliza dos procesos HTTP y dos sesiones independientes. Una tercera conexión retiene la fila para forzar la espera de ambas solicitudes. Se repiten diez carreras por la última unidad y diez carreras por la cancelación: solo debe persistir una venta y una reposición por caso.</p>
