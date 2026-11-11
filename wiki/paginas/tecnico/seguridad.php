<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="modelo-local">Modelo local</h2>
<p>No existen autenticación ni roles. El SRS exige el uso en un equipo controlado; el historial técnico no tiene una autorización separada de la del resto del aplicativo. Publicar el código en GitHub no equivale a desplegar la aplicación de forma segura en una red.</p>
<h2 id="inyección-sql--sentencias-preparadas">Consultas preparadas</h2>
<p>Los campos del operador se envían con parámetros mysqli. Las búsquedas forman un patrón LIKE como dato; no existe patronLike() ni se promete tratar % y _ como literales. Las opciones de agrupación se validan contra una lista permitida.</p>
<h2 id="xss--escape-de-salida">Escape de salida</h2>
<p>h(), en utilidades.php, aplica htmlspecialchars con ENT_QUOTES, ENT_SUBSTITUTE y UTF-8 al generar HTML. No se modifica el nombre almacenado para escaparlo; se codifica al presentarlo.</p>
<h2 id="csrf--token-por-sesión">Tokens CSRF y métodos</h2>
<p>seguridad.php genera 32 bytes aleatorios y compara el token con hash_equals. Los cambios usan POST; cancelar_factura.php devuelve 405 y Allow: POST ante otro método. No existe registrar_pago_factura.php.</p>
<h2 id="validación-de-entrada">Validación de entrada</h2>
<p>Se validan campos obligatorios, longitudes UTF-8, ID y stock en rango INT, fechas reales y precios de hasta ocho cifras enteras y dos decimales. CHECK y claves foráneas agregan controles de integridad, sin reemplazar la validación del servidor. No hay un CHECK de todas las reglas de texto o sesión.</p>
<h2 id="confirmaciones">Confirmaciones</h2>
<p>Eliminar clientes/productos puede abrir primero una confirmación del navegador y después una página del servidor. Cancelar muestra una página de confirmación y solo modifica datos con confirmar=si. Las validaciones se repiten al ejecutar, no dependen del botón visible.</p>
<h2 id="información-de-errores">Información de errores</h2>
<p>Los errores de conexión y no controlados devuelven orientación general. El detalle queda en líneas JSON de logs/errores.log. JSON codifica los saltos de línea dentro de los campos; el historial muestra el detalle técnico para revisión local. La consulta del log no implementa roles ni rotación automática.</p>
<h2 id="sesión-y-cabeceras">Sesiones y protección de archivos</h2>
<p>La aplicación inicia sesiones, pero no fija explícitamente HttpOnly, SameSite=Strict ni session.use_strict_mode. No se atribuyen esas garantías al código. Tampoco se envían desde entorno.php las cabeceras X-Frame-Options, X-Content-Type-Options o Referrer-Policy descritas en una versión anterior.</p><p>.htaccess bloquea Git, configuración, herramientas, SQL, Markdown, logs y rutas internas. Permite los PDF y Word públicos para sus visores/descargas. Requiere Apache con AllowOverride; PHP -S no aplica esas reglas.</p>
<h2 id="exposición-de-red-rnf-03">Acceso local y límites</h2>
<p>Comprueba la escucha de Apache, el enlace SQL y el firewall del equipo. La configuración de una instalación puede diferir de la de otra. Antes de ampliar el acceso se requieren controles de autenticación y autorización fuera del alcance actual. Las pruebas registradas no constituyen una auditoría completa de seguridad.</p>
