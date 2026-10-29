# Matriz de Casos de Prueba Básicos — La Única

Este documento detalla los escenarios principales que se probarán para asegurar el correcto funcionamiento de los módulos del sistema.

| ID | Módulo | Acción a probar | Resultado Esperado | Estado |
| :--- | :--- | :--- | :--- | :--- |
| CP-01 | Clientes | Crear un cliente con todos los datos válidos. | El cliente se guarda y aparece en la lista. | Aprobado |
| CP-02 | Clientes | Intentar crear un cliente sin nombre o ID. | El sistema muestra error y no guarda. | Aprobado |
| CP-03 | Productos | Editar el precio de un producto existente. | El precio se actualiza correctamente en el sistema. | Aprobado |
| CP-04 | Ventas | Crear una venta con un producto que tiene stock disponible. | Factura generada exitosamente y stock descontado. | Aprobado |
| CP-05 | Ventas | Intentar vender una cantidad mayor al stock disponible. | El sistema bloquea la venta por falta de stock. | Aprobado |
| CP-06 | Facturas | Cancelar una factura generada previamente. | Factura anulada y el stock del producto es devuelto. | Aprobado |
| CP-07 | Productos | Intentar crear un producto con campos vacíos, letras o números negativos. | El sistema bloquea el guardado y muestra mensajes de validación. | Aprobado |
