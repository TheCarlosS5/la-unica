# Revisión de equipos y actualización operativa de La Única

> **Antecedente del 18/11.** Este anexo y su PDF describen el corte anterior con cuentas y roles. Para la instalación actual consulta el [README](../README.md) y el [PAC 2.0](PAC.pdf).

18 de noviembre de 2026 · Línea base de código: f5243afc9964b2302a7eb678ce760588390dcd0a

Se revisaron los cinco archivos disponibles en la carpeta LA_UNICA y se contrastaron sus observaciones con el código de main. Se corrigieron acceso, seguridad del portal, listados, validación, interacción y peso de recursos. Este anexo actualiza los puntos afectados del SRS 1.6, Manual del usuario 2.1, Manual técnico 1.4 y PAC 1.4, cuya entrega del 11/11/2026 se conserva como línea base. No se atribuyen versiones nuevas ni aprobación humana a esos documentos.

## Fuentes revisadas

- [evaluacion_equipo5_la_unica.docx](https://drive.google.com/file/d/1wqIFOOPz8J5bFGVOB3SZC1Qzy7OSzVC5/view), evaluación del 15/11: identifica ausencia de login y roles, falta de paginación, motivo de cancelación e iconos de estado. También solicita formalizar QA. Se atienden esas acciones con cuentas, páginas, historial con motivo y este PDF; las evidencias estructuradas permanecen en JSON.
- [evaluacion_integral_la_unica.md](https://drive.google.com/file/d/1_A0JKyohK6Ix44UuahTkgHXePNJqicKO/view), Equipo 7, 14/11: recomienda revisar móvil menor de 480 px, hover secundario y retroalimentación. La revisión nueva mide 320/375 px y amplía los estados de envío; el hover queda más visible. La nota positiva sobre RBAC no era evidencia de autorización real.
- [evaluacion_la_unica_resumen.docx](https://drive.google.com/file/d/1QyB4FkfgdWj0e6vuohBkO0njIegClrCN/view), Santiago Castro Ceron, 11/11, revisión estática: señala controles pequeños, límites de campos con poco contraste, tipografía en tablas, tamaño del video, MER sin lazy y verbos mezclados. Se corrigen en CSS, recursos y microcopia; su informe no equivale a prueba real en dispositivos.
- [Evaluacion_LaUnica_Completa.pdf](https://drive.google.com/file/d/1PThAWVKDulFu5H-nmWkEu5mONmZzl2sp/view), 17/11: documenta ausencia de RBAC, CSRF del portal y cabeceras, errores generales, listados completos y peso multimedia. Se atienden con controles de servidor y pruebas nuevas. El anexo de evidencias mencionado dentro de ese PDF no estaba entre los cinco archivos de la carpeta; no se ha verificado como si estuviera disponible.
- [la unica.md](https://drive.google.com/file/d/11A3Um-aXDvNfr_oJRDfubDgaP1g_JcQY/view), Jefferson Andres Valderrama: pide POST/CSRF en bajas, accesibilidad y coherencia documental. Las bajas ya usaban POST/CSRF en la línea base. IVA, teléfono y color de ejemplo figuran como filas de plantilla; no se implementan campos ni impuestos ajenos al alcance.

El instrumento integral enumera 24 criterios (3+3+2+3+4+4+5). Algunos consolidados de los evaluadores usan 22 como denominador. Se conservan las calificaciones originales sin corregir su autoría ni utilizarlas para certificar la versión nueva.

<!-- page -->

## Acceso y actualización de una instalación

La actualización no cambia las cinco tablas ni reimporta db.sql. Respalda previamente archivos y SQL. Cada instalación necesita crear sus propias cuentas antes de operar; las credenciales de prueba no se distribuyen.

1. Desde la carpeta del proyecto ejecuta: php tools/usuario.php nombre administrador.
2. Introduce una contraseña de 12 a 72 bytes por entrada estándar. La entrada de consola es visible; no se pasa como argumento ni queda en el historial del comando. El archivo privado guarda un hash, nunca la contraseña en texto plano.
3. Crea las cuentas necesarias con operador o consulta. Repetir el comando modifica el rol o contraseña e invalida sesiones anteriores. Para retirar una cuenta usa: php tools/usuario.php nombre eliminar.
4. Abre la aplicación, acepta el aviso e inicia sesión. Cierra desde el botón Cerrar sesión. Tras 30 minutos sin actividad se exige iniciar sesión otra vez.

El archivo php/config/usuarios.local.php está ignorado por Git. Inclúyelo en las copias de seguridad privadas. Solo el responsable con acceso a la consola administra cuentas; no hay registro público ni contraseña por defecto.

## Permisos

- Administrador: consulta y modifica clientes y productos, registra y cancela facturas, consulta reportes e historiales técnicos y de acciones.
- Operador: consulta catálogo, facturas y reportes; crea o edita clientes y registra facturas. No cambia productos, elimina registros, cancela facturas ni accede a historiales.
- Consulta: consulta clientes, productos, facturas, comprobantes y reportes. No ejecuta cambios ni accede a historiales.

La autorización se verifica antes de conectar SQL y antes de ejecutar operaciones; también se comprueba la aceptación del aviso. Los botones se ocultan cuando la cuenta carece de permisos. La documentación pública en wiki, PDF y Word sigue disponible; no contiene cuentas ni secretos.

## Cambios en la operación

Clientes, productos y facturas muestran 25 registros por página y mantienen los filtros al navegar con Anterior y Siguiente. Se muestra el total filtrado. Una página fuera de rango se ajusta al límite; los parámetros manipulados se normalizan.

Cancelar requiere Administrador y un motivo de 1 a 160 caracteres. El motivo y la cuenta responsable quedan en log_acciones dentro de la transacción. Se conservan la cancelación del mismo día en America/Bogota, el estado definitivo y la reposición única de inventario. El resto de acciones también registra la cuenta.

Los errores de clientes/productos aparecen junto al campo con aria-invalid y aria-describedby. La factura asocia errores al cliente o al grupo de líneas. Los formularios muestran el envío y evitan un segundo submit mientras se procesa.

<!-- page -->

## Correcciones técnicas y trazabilidad

- Acceso: php/config/autenticacion.php, login.php, logout.php y tools/usuario.php. Hash de contraseña, renovación de ID de sesión, revocación al modificar cuentas y límite compartido de cinco fallos por cuenta/IP en cinco minutos.
- Sesiones y portal: seguridad.php, index.php, main.php, terminos.php y procesar_terminos.php. Cookies limitadas a la instalación, HttpOnly, SameSite=Lax, Secure cuando existe HTTPS y modo estricto. CSRF en aceptar/rechazar y método GET rechazado con 405.
- Cabeceras: CSP con recursos propios, framing al mismo origen, nosniff y Referrer-Policy. Se elimina X-Powered-By y se impide caché de respuestas operativas. CSP conserva inline por compatibilidad con los scripts y estilos existentes; no se presenta como una política estricta sin excepciones.
- Listados: paginacion.php y ver_clientes/ver_productos/ver_facturas. Consultas paginadas de servidor con parámetros y conteo total. Los reportes conservan su alcance completo, no se limitan a la página de consulta.
- Validación y auditoría: formularios de clientes, productos y facturas; cancelar_factura.php y consultas.php. Motivo, cuenta y validaciones de servidor. Los cambios no agregan IVA, crédito, pagos pendientes ni factura electrónica.
- Accesibilidad: controles de navegación/tabla de al menos 44 px, foco visible, bordes más oscuros, texto e iconos de estado, tipografía de sistema en tablas e impresión y jerarquía de términos corregida.
- Recursos: logo WebP de 10.096 bytes frente a 884.547 del PNG original, con dimensiones declaradas. Video MP4 reducido de 75.051.972 a 8.147.545 bytes; versión WebM, poster y preload=none. MER con carga diferida. El PNG original permanece para referencias históricas.
- Documentación: README y wiki de instalación/seguridad actualizados, anexo integrado en Sobre nosotros y aviso de actualización junto a los cuatro documentos de la entrega anterior. Se usa Crear para altas, Producto para catálogo y Existencias para cantidades visibles; los identificadores SQL stock permanecen por compatibilidad.

## Pendientes y límites de aceptación

Se requiere revisión humana de subtítulos/transcripción del video; no se inventó una transcripción. Las capturas históricas de los manuales aún muestran parte de la interfaz anterior: se conservan identificadas por su entrega y se acompaña su consulta con este anexo. La actualización completa de sus capturas y una nueva aceptación formal del SRS/PAC quedan pendientes.

La instalación sigue destinada a un equipo local controlado. No se certifica exposición pública, cumplimiento WCAG completo, recuperación de contraseñas por correo, MFA, gestión web de cuentas ni rotación automática del log. Apache con AllowOverride es necesario; PHP -S no aplica las reglas de bloqueo. La cuenta administrativa se crea localmente antes del primer uso.

<!-- page -->

## Evidencia de verificación del 18/11/2026

Entorno nuevo y exclusivo: Apache 2.4.68, PHP 8.3.33 y MySQL 8.4.3. Base ficticia con 1.001 clientes, 1.001 productos y más de 1.000 facturas; puerto SQL independiente. No se usaron datos comerciales para probar las correcciones.

- 78 verificaciones ejecutables de permisos, sesión ausente, roles desconocidos, CSRF, parámetros anidados, límites de enteros y fecha inválida: correctas. Comando desde el proyecto: php tests/seguridad.php. No requiere SQL ni cuentas y no modifica datos.
- Pruebas HTTP de login, acceso directo, autorización por rol, bloqueo de archivos internos, paginación y filtros, errores inline, venta, descuento de existencias, motivo obligatorio, cancelación única, logout, cabeceras, historial sin SQL, revocación de sesión y límite de intentos: correctas. Resultados saneados en referencias/QA/revision_2026-11-18/http.json.
- 32 combinaciones visuales: ocho rutas a 320, 375, 768 y 1280 px. Todas respondieron 200; cero desbordamientos del documento y cero controles bajo 44 px en el conjunto medido. Cero errores de JavaScript; el drawer abre y cierra con Escape. Resultados en navegador.json, en la misma carpeta.
- Se comprobaron los flujos con Brave mediante Playwright. Las mediciones no prueban todos los navegadores, lectores de pantalla ni dispositivos físicos. No se repiten como resultados propios las pruebas de concurrencia/rollback descritas por los evaluadores; se conserva su carácter histórico.

## Comprobación manual de la instalación destino

1. Crea cuentas locales de los tres roles y confirma sus permisos por URL directa, además de los botones visibles.
2. Comprueba un cliente/producto inválido, un cliente duplicado, una venta, el comprobante y una cancelación con motivo usando datos ficticios.
3. Comprueba filtros y navegación de páginas. Los reportes deben mantener el alcance filtrado completo.
4. Verifica que config, logs, tools, tests y .git no se descargan por HTTP. Comprueba acceso al historial técnico solo como Administrador cuando SQL está detenido.
5. Conserva respaldos privados de SQL y usuarios.local.php; no publiques ese archivo ni la configuración de conexión.

Las comprobaciones respaldan los cambios descritos y no sustituyen la aceptación institucional ni una auditoría de seguridad externa.
