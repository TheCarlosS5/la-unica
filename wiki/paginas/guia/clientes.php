<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="registrar">Registrar y buscar</h2>
<p>El documento identifica al cliente: debe ser un entero positivo, único y no mayor que 2147483647. El nombre admite hasta 100 caracteres. La fecha de registro es automática. Busca por documento o por parte del nombre.</p>
<h2 id="editar">Editar y eliminar</h2>
<p>La edición permite cambiar el nombre. Las facturas conservan el nombre registrado al vender. Solo se puede eliminar un cliente sin facturas asociadas; el sistema pide confirmación y valida la solicitud.</p>
<p>El conteo de facturas incluye Pagado y Cancelado. Cancelar una factura no elimina su relación con el cliente.</p>
