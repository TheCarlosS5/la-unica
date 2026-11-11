# Matriz de Casos de Prueba Básicos — La Única

Ejecuta los casos desde el navegador en una instalación aislada con datos ficticios. Los estados corresponden al registro histórico de QA. La wiki incluye guías ilustradas de [clientes](https://unica.commerces.today/wiki/?pagina=guia/clientes), [productos](https://unica.commerces.today/wiki/?pagina=guia/productos) y [facturas](https://unica.commerces.today/wiki/?pagina=guia/facturas).

| ID | Módulo | Acción a probar | Resultado Esperado | Estado |
| :--- | :--- | :--- | :--- | :--- |
| CP-01 | Clientes | Abre **Nuevo cliente**, ingresa un documento único y un nombre, pulsa **Crear cliente** y busca el documento en **Clientes**. | El cliente aparece en el listado con la fecha de registro asignada automáticamente. | Aprobado |
| CP-02 | Clientes | Abre **Nuevo cliente**, deja vacío el nombre o el documento y pulsa **Crear cliente**. | Se indica el campo obligatorio y el cliente no se guarda. | Aprobado |
| CP-03 | Productos | En **Productos**, busca un producto, pulsa **Editar**, cambia el precio y pulsa **Guardar cambios**. | El listado muestra el nuevo precio. | Aprobado |
| CP-04 | Ventas | Abre **Nueva factura**, elige un cliente y un producto con stock, indica una cantidad disponible y pulsa **Guardar venta**. | La factura aparece como **Pagado** y el stock disminuye en la cantidad vendida. | Aprobado |
| CP-05 | Ventas | Abre **Nueva factura**, selecciona un cliente y un producto, ingresa una cantidad superior al stock e intenta guardar. | La venta se bloquea. El inventario y el listado de facturas quedan sin cambios. | Aprobado |
| CP-06 | Facturas | En **Facturas**, abre **Ver factura** para una venta pagada del día. Pulsa **Cancelar factura** y luego **Confirmar cancelación**. | La factura cambia a **Cancelado** y el stock se repone una sola vez. | Aprobado |
| CP-07 | Productos | Abre **Nuevo producto** e intenta guardar por separado con un campo obligatorio vacío, letras en un campo numérico o un precio o stock negativo. | El formulario señala el dato inválido y el producto no se guarda. | Aprobado |
