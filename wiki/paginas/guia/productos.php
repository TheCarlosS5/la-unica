<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="catalogo">Registrar el catálogo</h2>
<p>Cada producto tiene ID automático, nombre, unidad de medida, stock entero no negativo y precio no negativo con dos decimales. La unidad es una descripción, por ejemplo unidad o caja; las cantidades de venta son enteras.</p>
<h2 id="existencias">Editar y controlar existencias</h2>
<p>Busca por ID o nombre. Una edición de nombre, unidad o precio no altera los detalles históricos de las facturas. El stock actual determina si se puede completar una venta; la marca de stock bajo usa el umbral 5.</p>
<p>Solo se elimina un producto sin detalles de factura asociados. Para un producto ya vendido, conserva su registro para mantener la trazabilidad.</p>
