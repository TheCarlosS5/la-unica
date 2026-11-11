<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="validacion">Mensajes de validación</h2>
<ul><li>Solicitud no validada: recarga la página y revisa que la sesión funcione.</li><li>Documento o ID no válido: usa un entero positivo dentro del límite admitido.</li><li>Documento duplicado: busca el cliente existente antes de crear otro.</li><li>Existencias insuficientes o modificadas: actualiza la selección y reduce las cantidades.</li><li>Factura ya cancelada o de otro día: consulta el comprobante; no corresponde repetir la cancelación.</li></ul>
<h2 id="tecnicos">Mensajes técnicos</h2>
<p>Los errores inesperados y de conexión muestran una explicación general. Consulta el historial local para identificar la operación y el diagnóstico. No compartas el log completo si contiene datos privados. Este catálogo resume los casos principales; el texto exacto puede variar según el formulario.</p>
