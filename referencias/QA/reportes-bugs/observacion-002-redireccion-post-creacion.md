# Observación de Mejora: Falta de redirección después de crear un registro

**ID de la Observación:** OBS-002
**Módulos afectados:** Clientes, Productos y Facturas.
**Descripción:** Al guardar un nuevo registro (cliente, producto o factura), el sistema mantiene al usuario en la misma pantalla del formulario de creación con los datos aún cargados, en lugar de redirigirlo a la lista general o a la vista del registro creado.

**Impacto (UX):**
Esto puede generar confusión en el usuario, quien podría pensar que el guardado falló y hacer clic de nuevo, creando registros duplicados en la base de datos.

**Recomendación:**
Implementar una redirección automática (usando `header("Location: ...")` en PHP) hacia la lista del módulo correspondiente después de un guardado exitoso, o en su defecto, limpiar los campos del formulario y mostrar un mensaje claro de éxito.

**Severidad:** Media/Baja (No rompe la base de datos, pero afecta la usabilidad).
**Estado:** Pendiente de revisión por el equipo de desarrollo.
