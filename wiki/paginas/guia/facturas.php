<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p class="aviso"><strong>Guía visual actualizada.</strong> Las capturas se tomaron de la interfaz actual con datos ficticios y una base independiente. Haz clic en una imagen o enfócala y pulsa Enter para ampliarla; Escape cierra el visor.</p>
<h2 id="cliente">1 Seleccionar el cliente</h2>
<p>Abre Nueva factura y elige un cliente existente. Si no está registrado, créalo antes de iniciar la venta.</p>
<figure class="captura"><img src="img/actuales/facturas-cliente.jpg" alt="Paso 1 — Cliente seleccionado en el formulario actual." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Paso 1 — Cliente seleccionado en el formulario actual.</figcaption></figure>
<h2 id="lineas">2 Agregar productos y cantidades</h2>
<p>Selecciona un producto e indica una cantidad entera positiva. Usa Agregar producto para otra línea y Quitar para retirarla. La interfaz evita repetir productos; el servidor agrupa las cantidades si recibe líneas repetidas.</p>
<figure class="captura"><img src="img/actuales/facturas-lineas.jpg" alt="Paso 2 — Dos productos, cantidades y disponibilidad de ejemplo." width="1280" height="1118" loading="lazy" decoding="async"><figcaption>Paso 2 — Dos productos, cantidades y disponibilidad de ejemplo.</figcaption></figure>
<h2 id="total">3 Revisar el total y guardar</h2>
<p>El total se actualiza mientras completas las líneas. Es una estimación: el servidor relee precios y existencias antes de guardar. Si falta stock para una línea, no se guarda una factura parcial. Pulsa el botón de registrar la venta cuando los datos estén correctos.</p>
<figure class="captura"><img src="img/actuales/facturas-total.jpg" alt="Total estimado — 2 paquetes de café y 3 de arroz suman $27.400,00 COP en este ejemplo." width="892" height="99" loading="lazy" decoding="async"><figcaption>Total estimado — 2 paquetes de café y 3 de arroz suman $27.400,00 COP en este ejemplo.</figcaption></figure>
<h2 id="guardada">4 Comprobar la confirmación</h2>
<p>La venta se registra como Pagado y descuenta el inventario dentro de una transacción. La aplicación registra el estado; no procesa un pago bancario.</p>
<figure class="captura"><img src="img/actuales/facturas-creada.jpg" alt="Paso 4 — Confirmación de la venta guardada y su total." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Paso 4 — Confirmación de la venta guardada y su total.</figcaption></figure>
<h2 id="buscar">5 Consultar las facturas</h2>
<p>En Facturas busca por número, documento o nombre del cliente; también puedes filtrar por fecha. Abre Ver factura para consultar el comprobante.</p>
<figure class="captura"><img src="img/actuales/facturas-lista.jpg" alt="Listado — Filtros y acceso al comprobante de una factura." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Listado — Filtros y acceso al comprobante de una factura.</figcaption></figure>
<h2 id="comprobante">6 Revisar el comprobante</h2>
<p>El comprobante conserva el nombre del cliente y el nombre, unidad y precio de cada producto en el momento de vender. Editar el catálogo después no altera estos datos.</p>
<figure class="captura"><img src="img/actuales/facturas-comprobante.jpg" alt="Comprobante — Detalles históricos de la venta del ejemplo." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Comprobante — Detalles históricos de la venta del ejemplo.</figcaption></figure>
<h2 id="imprimir">7 Imprimir o guardar el comprobante</h2>
<p>Usa la impresión del navegador y revisa la vista previa. Selecciona Guardar como PDF si necesitas un archivo. La siguiente imagen muestra los estilos de impresión de la página, no el diálogo del sistema operativo.</p>
<figure class="captura"><img src="img/actuales/facturas-impresion.jpg" alt="Impresión — Comprobante sin los controles de navegación." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Impresión — Comprobante sin los controles de navegación.</figcaption></figure>
<h2 id="confirmar-cancelacion">8 Solicitar y confirmar la cancelación</h2>
<p>Solo se permite cancelar una factura Pagado durante el mismo día de venta en America/Bogota. Desde el comprobante abre Cancelar y revisa esta confirmación antes de continuar.</p>
<figure class="captura"><img src="img/actuales/facturas-confirmar-cancelacion.jpg" alt="Cancelación — Confirmación antes de cambiar el estado y reponer stock." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Cancelación — Confirmación antes de cambiar el estado y reponer stock.</figcaption></figure>
<h2 id="cancelada">9 Comprobar la cancelación</h2>
<p>Después de confirmar, el estado es Cancelado y las cantidades regresan al inventario una sola vez. No se borra la factura. No puede cancelarse de nuevo ni cancelarse una factura de otro día.</p>
<figure class="captura"><img src="img/actuales/facturas-cancelada.jpg" alt="Resultado — Factura cancelada y reposición confirmada en la prueba." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Resultado — Factura cancelada y reposición confirmada en la prueba.</figcaption></figure>
