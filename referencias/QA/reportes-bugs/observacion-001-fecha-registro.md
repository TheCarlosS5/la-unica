# Observación de Mejora: Validación de Fecha de Registro

**ID de la Observación:** OBS-001
**Módulo:** Clientes
**Descripción:** El formulario de creación de clientes permite ingresar manualmente una "Fecha de registro". Esto permite que el usuario ingrese fechas pasadas (por ejemplo, año 1944), ignorando el valor automático (`curdate()`) que tiene configurado la base de datos.

**Discusión con el equipo:**
Se revisó el caso con los compañeros desarrolladores. Ellos indicaron que la intención es que el campo se deje vacío para que la base de datos ponga la fecha actual automáticamente.

**Decisión del equipo:**
El equipo de desarrollo determina que, por ahora, el comportamiento se mantiene así. No se considera un bug bloqueante, sino una mejora a futuro (como ocultar el campo en el formulario o hacerlo de solo lectura).

**Estado:** Documentado como observación. Aprobado por el equipo.
**Severidad:** Baja (No afecta la operación principal del sistema).
