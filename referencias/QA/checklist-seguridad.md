# Checklist de seguridad de La Única

Revisión documental y de código: **23/11/2026** (código del corte `e527b75`), SRS 1.6. Los controles observados no constituyen una certificación completa ni un permiso para publicar la aplicación en Internet.

## Observado en el código actual

- [x] Entradas del operador en consultas preparadas mysqli; los filtros y agrupaciones se validan. No se presenta mysqli_real_escape_string como sustituto de parametrizar.
- [x] h() codifica la salida HTML con ENT_QUOTES/ENT_SUBSTITUTE y UTF-8; no elimina caracteres del dato almacenado.
- [x] Validación de campos, longitudes, enteros, fechas y precios en el servidor. Cantidad de venta positiva; stock y precio admiten cero.
- [x] Token CSRF de sesión para cambios de clientes, productos, facturas y portal de términos. Comprobación con hash_equals.
- [x] Venta/cancelación con transacciones, bloqueos y registro conjunto; restricciones CHECK y claves foráneas.
- [x] Mensajes generales de conexión/configuración sin trazas ni credenciales; detalle en logs/errores.log y consulta técnica local.
- [x] Configuración privada ignorada por Git; variables DB_* y archivo local documentados.
- [x] .htaccess define bloqueo directo de Git, configuración, herramientas, logs, SQL y Markdown internos, permitiendo Word/PDF públicos. Su efectividad depende de Apache/AllowOverride.

- [x] Los módulos operativos comprueban la aceptación antes de abrir SQL. Apache define `Require local`.
- [x] Las cookies usan la ruta base, HttpOnly, SameSite=Lax y Secure con HTTPS. La sesión usa modo estricto.
- [x] Las páginas que cargan `seguridad.php` envían CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy y Cache-Control.

## Comprobar en el equipo de entrega

- [ ] Escucha de Apache/SQL y firewall limitados al acceso local previsto.
- [ ] Permisos del sistema operativo y cuenta SQL limitada a la base propia.
- [ ] Bloqueo HTTP de recursos internos con la configuración real de Apache.
- [ ] Copias de respaldo y restauración comprobadas fuera de la base de trabajo.
- [ ] Configuración PHP de cookies/sesiones y política de conservación de logs.

El control de acceso depende del equipo local y de Apache. Aceptar el aviso habilita el acceso a los módulos, incluido el historial técnico, sin identificar al operador. La cookie de aceptación dura 30 días.
