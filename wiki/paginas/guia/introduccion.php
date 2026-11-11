<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>La Única administra clientes, productos, ventas pagadas e inventario en un equipo local con PHP y MySQL o MariaDB. Esta wiki describe la copia integrada desde la-unica-2 y las reglas de la versión actual de main.</p>
<h2 id="alcance">Qué puedes hacer</h2>
<p>Registra clientes y productos, crea una factura con una o varias líneas, consulta el comprobante y revisa los cinco reportes. Cada venta descuenta existencias dentro de una transacción. Una cancelación del mismo día devuelve las unidades una sola vez.</p>
<h2 id="ayuda">Dónde consultar la documentación</h2>
<p><a href="../php/sobre_nosotros/index.php">Sobre nosotros</a> reúne el SRS 1.5 completo, manual de usuario 2.0, manual técnico 1.3, PAC 1.3 y video. El historial Git conserva la documentación anterior; esta wiki presenta solo las instrucciones vigentes.</p>
<p>La aplicación no tiene autenticación ni roles. Su uso previsto es local; no se debe exponer a Internet. No implementa ventas a crédito, pagos pendientes ni facturación electrónica.</p>
