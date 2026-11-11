# Casos de prueba de La Única

Revisión **11/11/2026**, SRS **1.6**. Ejecutar únicamente con datos ficticios en una instalación independiente. La columna de resultados no se prellena como aprobada sin una ejecución identificada.

Los siete casos del registro anterior y sus estados se conservan en el historial. La revisión HTTP del 11/11/2026 aporta evidencia funcional parcial en [coherencia documental](../COHERENCIA_DOCUMENTAL.md); no sustituye aceptación humana de todos los casos de esta matriz. Las guías ilustradas están en [Clientes](../../wiki/paginas/guia/clientes.php), [Productos](../../wiki/paginas/guia/productos.php) y [Facturas](../../wiki/paginas/guia/facturas.php) del repositorio; para verlas renderizadas usa el servidor PHP y el menú de la wiki.

| ID | Referencia | Escenario | Pasos | Resultado esperado |
| --- | --- | --- | --- | --- |
| CP-01 | RF-01, RF-11 | Cliente: registrar documento único y nombre | Crear cliente y buscar el documento. | Se guarda con fecha automática del día. |
| CP-02 | RF-01, RF-11 | Cliente: duplicado y nombre inválido | Repetir documento y probar nombre vacío o largo. | Rechazo sin duplicado ni registro parcial. |
| CP-03 | RF-02 | Cliente: buscar y editar nombre | Buscar por documento/nombre, abrir Editar y guardar. | El nombre cambia; documento y fecha no se editan. |
| CP-04 | RF-03, RF-04 | Producto: alta, búsqueda y edición | Registrar nombre, unidad, stock y precio; buscar y editar. | ID automático y datos válidos persistidos. |
| CP-05 | RF-11 | Producto: valores inválidos | Probar stock negativo, precio inválido y campos vacíos. | El servidor rechaza cada caso y conserva datos para corregir. |
| CP-06 | RF-05 a RF-08 | Venta con varias líneas | Seleccionar cliente y productos con stock; guardar. | Pagado, total correcto y stock descontado en conjunto. |
| CP-07 | RF-08, RNF-04 | Venta sin stock | Solicitar una cantidad superior a la disponible. | No se guarda factura ni se modifica stock o historial de venta. |
| CP-08 | RF-06, RF-07 | Copia histórica | Vender y luego editar nombre de cliente y nombre, unidad y precio del producto. | El comprobante conserva los datos de la venta. |
| CP-09 | RF-09 | Consulta e impresión | Buscar por número, cliente y fecha; abrir Ver / imprimir. | Comprobante con líneas, total y diálogo de impresión. |
| CP-10 | RF-10 | Cancelar el mismo día | Abrir comprobante Pagado de hoy, solicitar y confirmar. | Primero confirma; después Cancelado y stock repuesto una vez. |
| CP-11 | RF-10, RNF-04 | Repetir cancelación | Reenviar confirmación de una factura ya cancelada. | No se duplica la reposición. |
| CP-12 | RF-10 | Factura anterior | Intentar cancelar una factura de otro día. | Botón restringido y rechazo del servidor sin cambios. |
| CP-13 | RF-12 | Eliminar sin referencias | Eliminar cliente y producto sin ventas, confirmando. | Baja permitida; abrir confirmación por sí sola no borra. |
| CP-14 | RF-12 | Eliminar con referencias | Intentar baja de registros relacionados, incluso con factura cancelada. | El sistema conserva referencias y rechaza la baja. |
| CP-15 | RF-13 | Ventas y agrupaciones | Aplicar fechas y cada opción Factura/Día/Semana/Mes/Año. | Total vendido coincide con Pagado; canceladas se distinguen. |
| CP-16 | RF-13 | Unidades y existencias | Comparar cantidades vigentes y stock/precio actuales con SQL. | Unidades excluye Cancelado y Existencias describe el catálogo actual. |
| CP-17 | RF-13 | Clientes y detalle | Consultar los dos reportes adicionales. | Clientes incluye cero facturas; Detalle conserva precio histórico y estado. |
| CP-18 | RF-13, RNF-05 | PDF y multipágina | Aplicar filtros, abrir impresión y guardar PDF A4. | Título/período correctos; tablas legibles, orientación y saltos coherentes. |
| CP-19 | RF-14 | Historial de acciones | Crear, editar, eliminar, vender y cancelar en la base ficticia. | Se registran fecha, módulo, acción y descripción; no se borra desde la interfaz. |
| CP-20 | RF-15, RNF-06 | Fallo SQL y diagnóstico | Provocar fallo solo en servidor/base de pruebas independiente. | HTTP 500 general; detalle en archivo, historial/wiki disponibles sin SQL. |
| CP-21 | RF-16 | Ayuda integrada | Abrir SRS, dos manuales, PAC, wiki y video. | Versiones vigentes, archivos disponibles, controles y navegación funcionales. |
| CP-22 | RNF-03 | CSRF y archivos internos | Enviar cambio sin token y pedir configuración/log/Git por HTTP Apache. | Cambio rechazado; archivos internos no disponibles directamente. |
| CP-23 | RNF-02 | Rendimiento | Cargar 1000 registros por entidad y medir búsquedas por ID. | ≥95 % en ≤2 s; registrar tamaño de muestra, percentil y entorno. |
| CP-24 | RNF-04 | Contención real | Dos solicitudes independientes compiten por la última unidad y por cancelar el mismo ID. | Una venta y una reposición; sin stock negativo ni factura parcial. |
| CP-25 | RNF-01, RNF-05 | Pantalla estrecha y visor wiki | Abrir menú, navegar y ampliar/cerrar imágenes con ratón/teclado. | Contenido accesible e imágenes ajustadas al ancho; visor y cierre funcionales. |

## Registrar una ejecución

Guardar ID, fecha, commit, equipo/navegador, base ficticia, datos utilizados, esperado/obtenido, Aprobado o Fallido, evidencia y responsable real. Los casos aún no ejecutados en el equipo de entrega quedan Por verificar. Nunca se asigna al instructor ni a otro integrante una aprobación que no haya emitido.

## Portal incorporado desde main

Revisión del aporte a87bb48: comprobar entrada nueva, cinco pasos, aceptación, rechazo, revocación y cookie persistente. La aceptación no es autenticación: las rutas directas siguen disponibles. Verificar la cookie HttpOnly/SameSite=Lax, su duración de 30 días y su ruta / compartida por el mismo host. El POST del portal no contiene CSRF; registrar esa diferencia al evaluar seguridad y aislamiento. No confundir aceptar el aviso con aprobar el PAC o el SRS.
