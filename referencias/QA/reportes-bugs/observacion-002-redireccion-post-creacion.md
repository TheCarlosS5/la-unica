# OBS-002 Redirección después de crear

Estado vigente al **11/11/2026: no se reproduce el defecto en los flujos revisados**. Crear cliente, producto o factura correctamente responde con redirección HTTP 303 al formulario de creación vacío y muestra confirmación. Permanecer en el mismo tipo de pantalla no significa que se reenvíe el POST.

Los campos se conservan cuando hay error de validación para permitir corregirlos. Clientes y productos usan creado=1 en la URL para su mensaje; la factura guarda número y total en sesión. No se documentan funciones flash que no existen.

El reporte y la respuesta de desarrollo de octubre son antecedentes históricos. Si se observa otro comportamiento, registrar pasos, navegador, commit, resultado y evidencia usando la plantilla. No atribuir a QA un cierre formal que no haya emitido.
