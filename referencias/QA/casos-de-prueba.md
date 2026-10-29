# Matriz de Casos de Prueba Básicos — La Única

Este documento detalla los escenarios principales que se probarán para asegurar el correcto funcionamiento de los módulos del sistema.

| ID | Módulo | Acción a probar | Resultado Esperado | Estado |
| :--- | :--- | :--- | :--- | :--- |
| CP-01 | Clientes | Crear un cliente con todos los datos válidos. | El cliente se guarda y aparece en la lista. | Pendiente |
| CP-02 | Clientes | Intentar crear un cliente sin nombre. | El sistema muestra error y no guarda. | Pendiente |
| CP-03 | Productos | Editar el precio de un producto existente. | El precio se actualiza correctamente en el sistema. | Pendiente |
| CP-04 | Ventas | Crear una venta con un producto que tiene stock disponible. | Factura generada exitosamente y stock descontado. | Pendiente |
| CP-05 | Ventas | Intentar vender una cantidad mayor al stock disponible. | El sistema bloquea la venta por falta de stock. | Pendiente |
| CP-06 | Facturas | Cancelar una factura generada previamente. | Factura anulada y el stock del producto es devuelto. | Pendiente |