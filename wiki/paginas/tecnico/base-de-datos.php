<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="modelo">Cinco tablas</h2>
<p>clientes y productos forman los catálogos. facturas relaciona al cliente y conserva su nombre, fecha y estado. detalle_facturas relaciona la factura y el producto, con cantidad y copia de nombre, unidad y precio. log_acciones registra fecha, módulo, acción y descripción. El total se calcula sumando cantidad por precio de los detalles.</p>
<h2 id="reglas">Integridad y reportes</h2>
<p>Las claves foráneas conservan las relaciones; las restricciones CHECK impiden cantidades no positivas, stock negativo y precios negativos. El estado admite Pagado y Cancelado. referencias/db.sql define cuatro procedimientos: reporte_ventas_por_fecha, reporte_ventas_por_periodo, reporte_unidades_vendidas y reporte_existencias. Este esquema no crea vistas.</p>
<p>El script sirve para una instalación nueva. No existe un traslado automático de datos desde el modelo anterior.</p>
