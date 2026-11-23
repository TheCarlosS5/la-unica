<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2>Acceso local</h2>
<p>Apache limita el acceso al mismo equipo con <code>Require local</code>. El aplicativo está destinado a un solo operador. Los módulos verifican la aceptación de Términos y Condiciones al inicio del procesamiento. La aceptación se guarda en la sesión y en una cookie de 30 días de la instalación.</p>
<h2 id="portal">Portal y CSRF</h2>
<p>Aceptar términos no autentica. Las operaciones y el portal de aceptación/rechazo requieren POST y token CSRF. Los módulos operativos verifican la aceptación antes de consultar o modificar datos. Las cookies se emiten con la ruta base de la instalación. En la raíz se usa /, que también abarca las rutas inferiores del mismo host.</p>
<h2 id="sesion">Sesión y cabeceras</h2>
<p>La sesión usa nombre propio por instalación, modo estricto, HttpOnly, SameSite=Lax y Secure bajo HTTPS. Se envían CSP (inline permitido para la interfaz existente), X-Frame-Options, X-Content-Type-Options y Referrer-Policy; se elimina X-Powered-By.</p>
<h2 id="datos">Datos y archivos privados</h2>
<p>Las consultas preparadas, el escape HTML y las validaciones del servidor permanecen. Las ventas y cancelaciones conservan sus transacciones; cancelar requiere un motivo que queda guardado en el historial. Apache bloquea configuración, tools, tests, Git, SQL, Markdown y logs. Los PDF, documentos Word y la wiki se consultan desde el mismo equipo. PHP -S no aplica .htaccess. Los errores detallados se consultan en Historial de errores aun si SQL falla.</p>
<h2 id="limites">Límites</h2><p>El uso previsto sigue siendo local. Evita exponer el aplicativo por túneles o proxies. El operador realiza copias de seguridad y conserva los registros de errores de acuerdo con el PAC.</p>
