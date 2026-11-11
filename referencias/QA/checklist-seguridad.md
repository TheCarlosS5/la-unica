# Checklist de seguridad de La Única

Revisión documental y de código: **11/11/2026**, SRS 1.6. Los controles observados no constituyen una certificación completa ni un permiso para publicar la aplicación en Internet.

## Observado en el código actual

- [x] Entradas del operador en consultas preparadas mysqli; los filtros y agrupaciones se validan. No se presenta mysqli_real_escape_string como sustituto de parametrizar.
- [x] h() codifica la salida HTML con ENT_QUOTES/ENT_SUBSTITUTE y UTF-8; no elimina caracteres del dato almacenado.
- [x] Validación de campos, longitudes, enteros, fechas y precios en el servidor. Cantidad de venta positiva; stock y precio admiten cero.
- [x] Token CSRF de sesión para cambios de clientes, productos y facturas y comprobación con hash_equals.
- [x] Venta/cancelación con transacciones, bloqueos y registro conjunto; restricciones CHECK y claves foráneas.
- [x] Mensajes generales de conexión/configuración sin trazas ni credenciales; detalle en logs/errores.log y consulta técnica local.
- [x] Configuración privada ignorada por Git; variables DB_* y archivo local documentados.
- [x] .htaccess define bloqueo directo de Git, configuración, herramientas, logs, SQL y Markdown internos, permitiendo Word/PDF públicos. Su efectividad depende de Apache/AllowOverride.

## Comprobar en el equipo de entrega

- [ ] Escucha de Apache/SQL y firewall limitados al acceso local previsto.
- [ ] Permisos del sistema operativo y cuenta SQL limitada a la base propia.
- [ ] Bloqueo HTTP de recursos internos con la configuración real de Apache.
- [ ] Copias de respaldo y restauración comprobadas fuera de la base de trabajo.
- [ ] Configuración PHP de cookies/sesiones y política de conservación de logs.

No hay autenticación ni roles. La cookie de sesión PHP no tiene opciones fijadas expresamente por el código; la cookie de aceptación distinta sí usa HttpOnly y SameSite=Lax durante 30 días, con ruta / compartida por el mismo host. No hay CSRF en el POST de aceptación/rechazo y el portal no protege las rutas directas de módulos. No se afirma aislamiento entre carpetas ni autenticación. Para la sesión PHP no se fijan HttpOnly, SameSite=Strict ni session.use_strict_mode, y entorno.php no envía las cabeceras de seguridad que describía una wiki anterior. No atribuir esas garantías al aplicativo sin verificarlas o implementarlas mediante un cambio aparte. El Historial de errores muestra diagnóstico técnico y no tiene un permiso exclusivo de técnico dentro de la aplicación.
