<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>referencias/db.sql crea una base nueva con cinco tablas y cuatro procedimientos. No contiene vistas ni una migración desde el modelo antiguo.</p>
<p>El <a href="../referencias/mer.png">modelo de entidades y relaciones</a> representa las cinco tablas, sus campos y relaciones. Se conserva el diagrama aportado por Samuel a main.</p>
<h2 id="tablas">Tablas y datos históricos</h2>
<div class="tabla-wiki"><table><thead><tr><th>Tabla</th><th>Contenido</th></tr></thead><tbody><tr><td>clientes</td><td>id_cliente manual, nombre y fecha_registro automática.</td></tr><tr><td>productos</td><td>id_producto automático, nombre, unidad_medida, stock y precio_unitario actual.</td></tr><tr><td>facturas</td><td>ID automático, id_cliente, nombre_cliente histórico, fecha y estado Pagado/Cancelado.</td></tr><tr><td>detalle_facturas</td><td>Factura y producto, nombre_producto, unidad_medida y precio históricos, cantidad positiva.</td></tr><tr><td>log_acciones</td><td>Fecha, módulo, acción y descripción; sin relación obligatoria con otras tablas.</td></tr></tbody></table></div>

<h2 id="relaciones">Relaciones e integridad</h2>
<p>clientes 1:N facturas; facturas 1:N detalle_facturas; productos 1:N detalle_facturas. Las claves foráneas conservan las referencias. CHECK impide stock y precios negativos y cantidades no positivas. El total no se almacena en facturas: es SUM(cantidad * precio_unitario) del detalle.</p>
<h2 id="procedimientos-almacenados">Procedimientos almacenados</h2>
<div class="tabla-wiki"><table><thead><tr><th>Procedimiento</th><th>Resultado</th></tr></thead><tbody><tr><td>reporte_ventas_por_fecha</td><td>Facturas de un rango, estado y total histórico.</td></tr><tr><td>reporte_ventas_por_periodo</td><td>Totales pagados y cancelados por día, semana, mes o año.</td></tr><tr><td>reporte_unidades_vendidas</td><td>Cantidades e importe por producto; solo Pagado.</td></tr><tr><td>reporte_existencias</td><td>Productos ordenados por stock, con precio vigente.</td></tr></tbody></table></div>

<h2 id="estados">Estados y reglas</h2>
<p>Toda venta nace Pagado. Solo puede pasar a Cancelado durante el mismo día, con reposición única de stock. No hay Pendiente ni registro de pago posterior. Los nombres, la unidad y el precio guardados en el detalle conservan su valor aunque se edite el catálogo.</p>
<h2 id="instalación-y-datos">Instalación y datos de ejemplo</h2>
<p>No ejecutes el esquema sobre una base operativa. Para otra copia cambia CREATE DATABASE y USE en una copia del script por un nombre exclusivo. Los scripts de clientes/productos de referencias no incluyen ventas. El conjunto de wiki/herramientas incluye datos ficticios con ambos estados y copias históricas; úsalo solo en una base nueva de demostración.</p>
<h2 id="actualizaciones">Actualizaciones</h2>
<p>No hay carpeta de migraciones vigente ni usuario_app.sql. Un traslado de una versión anterior requiere respaldo, revisión del esquema y ensayo en otra base. La instalación nueva no borra ni convierte datos existentes automáticamente.</p>
