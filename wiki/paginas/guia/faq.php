<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="necesito-internet">¿Necesito Internet?</h2>
<p>No. Todo (PHP, MySQL, fuentes, íconos, manual) es local. Solo necesitas Internet para descargar el proyecto la primera vez.</p>
<h2 id="tiene-usuarios-y-contraseñas">¿Tiene usuarios y contraseñas?</h2>
<p>No. El SRS deja la autenticación y los roles <strong>fuera del alcance</strong>. El control de acceso es el del propio equipo (la sesión de Windows). Por eso la app no debe exponerse a la red.</p>
<h2 id="por-qué-cambió-el-total-de-una-factura-vieja">¿Cambia el total de una factura vieja si edito un precio?</h2>
<p>No. Cada línea de <code>detalle_facturas</code> guarda su <code>precio_unitario</code> al momento de la venta, y los totales se calculan con ese precio. Hasta la versión anterior se usaba el precio actual del catálogo y las facturas cambiaban solas; si tu base viene de esa versión, aplica <code>referencias/migraciones/001_precio_historico.sql</code> (las ventas antiguas quedan con el precio que tenía el catálogo al migrar).</p>
<h2 id="puedo-vender-con-decimales-15-kg">¿Puedo vender con decimales (1,5 kg)?</h2>
<p>No. <code>stock</code> y <code>cantidad</code> son enteros en la base. Si vendes a granel, define la unidad en una medida más pequeña (por ejemplo <code>gramos</code> o <code>media libra</code>).</p>
<h2 id="puedo-cambiar-la-unidad-de-un-producto">¿Puedo cambiar la unidad de un producto?</h2>
<p>Solo mientras <strong>no</strong> aparezca en facturas. Después queda fija para que las facturas antiguas sigan teniendo sentido.</p>
<h2 id="puedo-editar-o-borrar-una-factura">¿Puedo editar o borrar una factura?</h2>
<p>No. Si te equivocaste, <strong>cancela</strong> la factura (repone el stock) y registra una nueva. Las facturas y sus líneas nunca se eliminan.</p>
<h2 id="puedo-cancelar-una-factura-dos-veces">¿Puedo cancelar una factura dos veces?</h2>
<p>No. La segunda vez el servidor responde <em>«Esta factura ya está cancelada; no se repuso el inventario otra vez.»</em> La reposición de stock ocurre una sola vez.</p>
<h2 id="para-qué-sirve-el-estado-pendiente-si-la-app-no-lo-usa">¿Para qué sirve el estado «Pendiente»?</h2>
<p>Para las ventas <strong>por cobrar</strong> (fiado). Al registrar la venta eliges <em>Por cobrar</em>; el inventario se descuenta igual y la factura aparece en el reporte <em>Facturas pendientes</em> hasta que pulses <strong>Registrar pago</strong> en su comprobante, o la canceles.</p>
<h2 id="puedo-tener-dos-clientes-con-el-mismo-nombre">¿Puedo tener dos clientes con el mismo nombre?</h2>
<p>Sí. Solo el <strong>ID</strong> es único. Usa la búsqueda por ID para distinguirlos.</p>
<h2 id="por-qué-el-id-del-cliente-lo-escribo-yo-y-el-del-producto-no">¿Por qué el ID del cliente lo escribo yo y el del producto no?</h2>
<p>Así lo define <code>db.sql</code>: <code>clientes.id_cliente</code> es <code>INT PRIMARY KEY</code> sin autoincremento (la tienda usa su propio identificador, como la cédula o el NIT), mientras que <code>productos.id_producto</code> y <code>facturas.id_factura</code> son <code>AUTO_INCREMENT</code>.</p>
<h2 id="puedo-usar-la-cédula-como-id">¿Puedo usar la cédula como ID?</h2>
<p>Sí, si cabe en un <code>INT</code>: hasta <strong>2.147.483.647</strong>. Cédulas o NIT más largos no caben; en ese caso usa un consecutivo interno.</p>
<h2 id="el-comprobante-sirve-como-factura-legal">¿El comprobante sirve como factura legal?</h2>
<p>No. Es un <strong>comprobante interno</strong>; no es factura electrónica ni documento fiscal.</p>
<h2 id="qué-pasa-si-dos-personas-venden-el-mismo-producto-a-la-vez">¿Qué pasa si dos personas venden el mismo producto a la vez?</h2>
<p>La venta corre en una transacción que <strong>bloquea</strong> las filas de los productos (<code>SELECT … FOR UPDATE</code>) en orden de ID. La segunda espera a la primera y, si ya no hay stock suficiente, se rechaza sin cambios. Ver <a href="index.php?pagina=tecnico/flujo-de-ventas">Flujo de ventas</a>.</p>
<h2 id="funciona-con-xampp-o-wamp">¿Funciona con XAMPP o WAMP?</h2>
<p>Debería, siempre que tengan <strong>MySQL 8.0.16+</strong> (XAMPP trae MariaDB; las versiones recientes soportan <code>CHECK</code> y <code>DEFAULT (expresión)</code>, pero el entorno documentado es Laragon) y PHP con <code>mysqli</code>. Ajusta el puerto en <code>conexion.php</code>.</p>
<h2 id="hay-modo-oscuro">¿Hay modo oscuro?</h2>
<p>No. El aplicativo y esta wiki usan solo el tema claro de «papel y marcador». Los colores se pueden cambiar en <code>assets/css/estilos.css</code> (ver <a href="index.php?pagina=configuracion/personalizar#colores">Personalizar</a>).</p>
<h2 id="hay-pruebas-automatizadas">¿Hay pruebas automatizadas?</h2>
<p>No. El repositorio no define una suite de pruebas ni proceso de compilación; la verificación es manual siguiendo la lista de <a href="index.php?pagina=tecnico/releases">Publicar un release</a>.</p>
