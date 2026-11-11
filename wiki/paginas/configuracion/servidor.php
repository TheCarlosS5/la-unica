<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="apache-o-el-servidor-embebido">Apache y servidor de desarrollo</h2>
<p>Las páginas son archivos PHP directos y no requieren reescritura de rutas ni Composer. La protección de archivos internos sí depende de Apache y .htaccess con AllowOverride. El servidor integrado de PHP no interpreta .htaccess: úsalo únicamente para pruebas aisladas en 127.0.0.1, nunca como sustituto de esa protección.</p>
<h2 id="url-base-automática">URL base automática</h2>
<p>entorno.php calcula la base a partir de SCRIPT_NAME y elimina el sufijo de módulo /php/&lt;módulo&gt;. Así la instalación funciona en la raíz o en una carpeta como /la-unica-2.</p>
<h2 id="extensiones-de-php">PHP y extensiones</h2>
<p>El mínimo documentado es PHP 8.2. Comprueba la versión con php -v y las extensiones con php -m.</p><div class="tabla-wiki"><table><thead><tr><th>Extensión</th><th>Uso</th><th>Requisito</th></tr></thead><tbody><tr><td>mysqli y mysqlnd</td><td>Conexión y get_result de consultas preparadas.</td><td>Obligatorios</td></tr><tr><td>mbstring</td><td>Longitud UTF-8 y buscador de la wiki.</td><td>Obligatoria; no existe respaldo automático</td></tr><tr><td>session</td><td>CSRF y mensajes de factura/cancelación.</td><td>Sesiones operativas</td></tr></tbody></table></div>

<h2 id="sesiones">Sesiones</h2>
<p>seguridad.php inicia sesiones PHP para CSRF y mensajes temporales; no autentica usuarios. Revisa session.save_path y las cookies si no puede validarse un formulario. Para la cookie de sesión PHP no se fijan expresamente HttpOnly, SameSite ni use_strict_mode: dependen de PHP. La cookie distinta la_unica_terminos sí usa HttpOnly y SameSite=Lax, dura 30 días y tiene ruta /; alcanza las carpetas del mismo host. No identifica una persona ni separa instalaciones.</p>
<h2 id="zona-horaria-y-moneda">Zona horaria y moneda</h2>
<p>PHP usa America/Bogota y cada conexión SQL UTC−05:00. Los importes son COP con dos decimales. El formulario usa centavos BigInt para estimar y SQL calcula el total persistido desde los detalles.</p>
<h2 id="registro-de-errores">Registro de errores</h2>
<p>El registro principal es logs/errores.log: una línea JSON por error con fecha, operación, tipo, código, mensaje, ubicación y página. Si no puede escribirse se utiliza error_log como respaldo. El historial permite consulta por fechas aunque SQL falle; no hay rotación automática ni límite de 200 filas.</p>
<h2 id="acceso-local">Acceso local</h2>
<p>La ausencia de autenticación exige restringir Apache, SQL y el equipo al uso local previsto. Una etiqueta visual no verifica el firewall ni la escucha de red. El responsable comprueba esta configuración antes de usar datos reales.</p>
