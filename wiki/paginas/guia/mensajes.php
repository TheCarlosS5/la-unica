<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Lista de los mensajes que puede mostrar la aplicación, extraídos directamente del código PHP. Usa <kbd>Ctrl</kbd>+<kbd>F</kbd> para encontrar el tuyo.</p>
<h2 id="generales">Generales</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Mensaje</th>
<th>Significado / qué hacer</th>
</tr>
</thead>
<tbody>
<tr>
<td>Error de conexión a la base de datos.</td>
<td>MySQL apagado, puerto o credenciales incorrectos, o falta la base. Ver <a href="index.php?pagina=guia/problemas#error-de-conexión-a-la-base-de-datos">Solución de problemas</a>.</td>
</tr>
<tr>
<td>No se pudo validar la solicitud. Actualiza la página e inténtalo de nuevo.</td>
<td>Token CSRF vencido o ausente. Recarga (F5).</td>
</tr>
<tr>
<td>No fue posible iniciar la sesión de seguridad. Recarga la página e inténtalo de nuevo.</td>
<td>PHP no puede crear la sesión. Revisa <code>session.save_path</code> y cookies.</td>
</tr>
<tr>
<td>Los datos enviados no tienen el formato esperado.</td>
<td>La petición llegó manipulada (por ejemplo, un campo enviado como lista). Recarga y usa el formulario normal.</td>
</tr>
<tr>
<td>Solicitud no válida. / Método no permitido.</td>
<td>Se abrió por URL una acción que solo acepta formularios (POST). Usa los botones de la interfaz.</td>
</tr>
</tbody>
</table></div>
<h2 id="clientes">Clientes</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Mensaje</th>
<th>Tipo</th>
<th>Causa</th>
</tr>
</thead>
<tbody>
<tr>
<td>Cliente creado / actualizado / eliminado correctamente.</td>
<td>✅</td>
<td>Operación exitosa.</td>
</tr>
<tr>
<td>Ya existe un cliente con ese ID.</td>
<td>❌</td>
<td>El ID está en uso; elige otro.</td>
</tr>
<tr>
<td>El ID debe ser un entero entre 1 y 2.147.483.647.</td>
<td>❌</td>
<td>ID vacío, negativo, decimal o demasiado grande.</td>
</tr>
<tr>
<td>El nombre es obligatorio y admite hasta 100 caracteres.</td>
<td>❌</td>
<td>Nombre vacío o muy largo.</td>
</tr>
<tr>
<td>El nombre es obligatorio y no puede contener solo espacios.</td>
<td>❌</td>
<td>Solo se escribieron espacios.</td>
</tr>
<tr>
<td>La fecha no es válida.</td>
<td>❌</td>
<td>Fecha inexistente (p. ej. 31 de febrero).</td>
</tr>
<tr>
<td>No se puede eliminar: el cliente tiene facturas asociadas.</td>
<td>🔒</td>
<td>Tiene historial; no se puede borrar.</td>
</tr>
<tr>
<td>No se encontró el cliente. / ID de cliente no válido.</td>
<td>❌</td>
<td>El ID de la URL no existe o es inválido.</td>
</tr>
<tr>
<td>No se pudo crear / actualizar el cliente…</td>
<td>⚠️</td>
<td>Falla de base de datos; revisa el log.</td>
</tr>
</tbody>
</table></div>
<h2 id="productos">Productos</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Mensaje</th>
<th>Tipo</th>
<th>Causa</th>
</tr>
</thead>
<tbody>
<tr>
<td>Producto creado / actualizado / eliminado correctamente.</td>
<td>✅</td>
<td>Operación exitosa.</td>
</tr>
<tr>
<td>La unidad es obligatoria y admite hasta 20 caracteres.</td>
<td>❌</td>
<td>Unidad vacía o muy larga.</td>
</tr>
<tr>
<td>El stock debe ser un entero no negativo.</td>
<td>❌</td>
<td>Stock con decimales, negativo o vacío.</td>
</tr>
<tr>
<td>El precio debe ser no negativo y tener máximo dos decimales.</td>
<td>❌</td>
<td>Precio negativo, con 3+ decimales o con coma (usa punto: <code>4800.50</code>).</td>
</tr>
<tr>
<td>No se puede cambiar la unidad: el producto ya aparece en facturas.</td>
<td>🔒</td>
<td>La unidad queda fija tras la primera venta.</td>
</tr>
<tr>
<td>No se puede eliminar: el producto aparece en detalles de factura.</td>
<td>🔒</td>
<td>Tiene historial. Deja su stock en 0.</td>
</tr>
</tbody>
</table></div>
<h2 id="facturas--registrar-venta">Facturas — registrar venta</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Mensaje</th>
<th>Tipo</th>
<th>Causa</th>
</tr>
</thead>
<tbody>
<tr>
<td>Factura #N registrada como Pagado / Pendiente. Total: …</td>
<td>✅</td>
<td>Venta guardada con el precio de cada producto; stock descontado.</td>
</tr>
<tr>
<td>Pago registrado. La factura quedó como Pagado.</td>
<td>✅</td>
<td>Se cobró una venta por cobrar; el inventario no cambia.</td>
</tr>
<tr>
<td>Solo se puede registrar el pago de una factura Pendiente.</td>
<td>🔒</td>
<td>La factura ya estaba pagada o cancelada.</td>
</tr>
<tr>
<td>Selecciona un cliente válido.</td>
<td>❌</td>
<td>No elegiste cliente o el ID no es válido.</td>
</tr>
<tr>
<td>Agrega entre 1 y 100 productos con sus cantidades.</td>
<td>❌</td>
<td>Sin líneas o más de 100.</td>
</tr>
<tr>
<td>Cada línea debe tener un producto válido y una cantidad entera mayor que cero.</td>
<td>❌</td>
<td>Línea incompleta o cantidad 0/negativa/decimal.</td>
</tr>
<tr>
<td>La cantidad debe ser un entero entre 1 y 2.147.483.647.</td>
<td>❌</td>
<td>Cantidad fuera de rango.</td>
</tr>
<tr>
<td>La cantidad acumulada de un producto supera el límite permitido.</td>
<td>❌</td>
<td>Suma de cantidades del mismo producto demasiado grande.</td>
</tr>
<tr>
<td>El cliente seleccionado ya no existe. Actualiza la página y vuelve a seleccionarlo.</td>
<td>⚠️</td>
<td>Se eliminó mientras llenabas el formulario.</td>
</tr>
<tr>
<td>Uno de los productos seleccionados ya no existe. Actualiza la página.</td>
<td>⚠️</td>
<td>Ídem, con un producto.</td>
</tr>
<tr>
<td>Las existencias cambiaron y no alcanzan para completar la venta…</td>
<td>⚠️</td>
<td>Stock insuficiente al momento de guardar. <strong>No se guardó nada.</strong></td>
</tr>
<tr>
<td>Las existencias cambiaron durante la venta. No se guardó la factura…</td>
<td>⚠️</td>
<td>Otra operación modificó el stock en el último instante.</td>
</tr>
<tr>
<td>No se pudo registrar la factura. No se aplicaron cambios…</td>
<td>⚠️</td>
<td>Error de base de datos; todo se revirtió.</td>
</tr>
<tr>
<td>No se pudieron cargar los clientes y productos…</td>
<td>⚠️</td>
<td>Fallo al leer las listas del formulario.</td>
</tr>
</tbody>
</table></div>
<h2 id="facturas--consultar-y-cancelar">Facturas — consultar y cancelar</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Mensaje</th>
<th>Tipo</th>
<th>Causa</th>
</tr>
</thead>
<tbody>
<tr>
<td>Factura cancelada. Se repuso el inventario.</td>
<td>✅</td>
<td>Cancelación exitosa.</td>
</tr>
<tr>
<td>Esta factura ya está cancelada; no se repuso el inventario otra vez.</td>
<td>🔒</td>
<td>Segundo intento de cancelación.</td>
</tr>
<tr>
<td>La factura no tiene detalles; no se puede reponer el inventario.</td>
<td>❌</td>
<td>Factura sin líneas (solo posible si se creó por SQL).</td>
</tr>
<tr>
<td>No se encontró un producto de la factura; no se modificó el inventario.</td>
<td>❌</td>
<td>Una línea apunta a un producto que ya no existe (solo posible por SQL).</td>
</tr>
<tr>
<td>No existe una factura con ese identificador. / El identificador de factura no es válido.</td>
<td>❌</td>
<td>ID de la URL inválido o inexistente.</td>
</tr>
<tr>
<td>Ingresa una fecha válida en formato año-mes-día.</td>
<td>❌</td>
<td>Filtro de fecha mal formado.</td>
</tr>
<tr>
<td>La búsqueda admite hasta 100 caracteres.</td>
<td>❌</td>
<td>Texto de búsqueda muy largo.</td>
</tr>
<tr>
<td>No hay resultados con esos criterios. Prueba con otro ID, nombre o fecha.</td>
<td>ℹ️</td>
<td>La búsqueda no encontró facturas.</td>
</tr>
</tbody>
</table></div>
<h2 id="reportes">Reportes</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Mensaje</th>
<th>Causa</th>
</tr>
</thead>
<tbody>
<tr>
<td>Todavía no hay clientes / productos / facturas / detalles registrados.</td>
<td>La tabla consultada está vacía.</td>
</tr>
<tr>
<td>No hay ventas por cobrar.</td>
<td>Ninguna factura en estado <em>Pendiente</em>.</td>
</tr>
<tr>
<td>El reporte solicitado no está disponible.</td>
<td>URL de reporte desconocida.</td>
</tr>
</tbody>
</table></div>
<p><strong>Leyenda:</strong> ✅ éxito · ❌ dato inválido · 🔒 bloqueado por una regla de negocio · ⚠️ problema temporal o de servidor · ℹ️ informativo.</p>
