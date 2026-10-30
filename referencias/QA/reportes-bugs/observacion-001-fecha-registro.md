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

**Seguimiento (30/10/2026):** Las fechas pasadas se mantienen permitidas (sirven para registrar clientes antiguos, y el SRS lo admite en RF-01). La revisión encontró un caso que la observación no cubría: se aceptaban fechas **futuras** (por ejemplo, 2099). Ahora `crear_cliente.php` las rechaza en el servidor y el campo tiene `max` con la fecha de hoy. La regla quedó en el SRS 1.5 (RF-01, RF-11 y 3.5.4).
