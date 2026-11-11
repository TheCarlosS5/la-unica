<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="conexion">No se pudo conectar</h2>
<p>Comprueba que el servicio SQL esté iniciado y que host, puerto, base y usuario correspondan a esta copia. Revisa Historial de errores para el diagnóstico técnico local. La pantalla pública no muestra esos datos. La wiki y el historial de errores siguen disponibles sin SQL.</p>
<h2 id="formulario">Formulario o venta rechazados</h2>
<p>Si la sesión o el token caducaron, recarga el formulario antes de repetirlo. Si cambiaron las existencias, revisa las cantidades: una venta rechazada no debe dejar factura parcial. No repitas una venta sin comprobar primero la lista de facturas cuando haya una interrupción de red.</p>
<h2 id="documento">PDF o impresión</h2>
<p>Abre el PDF en otra pestaña si el visor del navegador no carga. Para reportes, aplica filtros y comprueba la vista previa A4, escala y orientación. Si cambiaste la carpeta, revisa que el servidor esté sirviendo las rutas y recursos correctos.</p>

<h2 id="guia-visual">Referencia visual de la versión actual</h2>
<p>Capturas reales con datos ficticios. Haz clic o pulsa Enter sobre una imagen para ampliarla.</p>
<figure class="captura"><img src="img/actuales/clientes-duplicado.jpg" alt="Ejemplo de validación — Documento ya registrado." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Ejemplo de validación — Documento ya registrado.</figcaption></figure>
<figure class="captura"><img src="img/actuales/productos-validacion.jpg" alt="Ejemplo de validación — Stock negativo rechazado." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Ejemplo de validación — Stock negativo rechazado.</figcaption></figure>
