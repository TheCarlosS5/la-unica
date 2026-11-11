<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="visión-general">Visión general</h2>
<p>La Única usa PHP por módulos, mysqli y HTML generado en el servidor, sin framework ni dependencia de Composer. La wiki tiene un enrutador propio por catálogo y no abre SQL.</p><pre class="highlight"><code class="language-text">Navegador → Apache/PHP → MySQL o MariaDB compatible
HTML/CSS/JS ← plantillas y módulos ← cinco tablas y cuatro procedimientos</code></pre>

<h2 id="ciclo-de-vida-de-una-página">Ciclo de una página</h2>
<ol><li>Carga conexion.php y las utilidades compartidas.</li><li>Lee y valida los parámetros; si modifica datos comprueba POST y CSRF.</li><li>Ejecuta consultas preparadas o una transacción de negocio.</li><li>Genera HTML con menú y pie; escapa los valores con h().</li></ol>
<h2 id="postredirectget">Post Redirect Get</h2>
<p>Las creaciones correctas redirigen con HTTP 303 al formulario vacío. Clientes y productos usan la señal creado=1 en la URL; la factura utiliza la sesión para número y total. No existen guardarMensajeFlash ni tomarMensajeFlash. La cancelación redirige al comprobante y las ediciones muestran su resultado según el módulo.</p>
<h2 id="módulos-compartidos">Módulos compartidos</h2>
<div class="tabla-wiki"><table><thead><tr><th>Archivo en php/config</th><th>Responsabilidad</th></tr></thead><tbody><tr><td>conexion.php</td><td>Coordina los módulos y abre mysqli salvo SIN_BASE_DE_DATOS.</td></tr><tr><td>entorno.php</td><td>Hora, constantes STOCK_BAJO/ENTERO_MAXIMO y URL base.</td></tr><tr><td>configuracion.php</td><td>Archivo privado y variables DB_*.</td></tr><tr><td>utilidades.php</td><td>Escape HTML, validación y formato COP.</td></tr><tr><td>seguridad.php</td><td>Sesiones y tokens CSRF.</td></tr><tr><td>consultas.php</td><td>Acciones y ejecución de procedimientos.</td></tr><tr><td>errores.php</td><td>Registro técnico y respuestas generales de error.</td></tr></tbody></table></div>

<h2 id="errores-en-dos-niveles">Errores y diagnóstico</h2>
<p>El operador recibe un mensaje general. El detalle se registra en archivo y puede consultarse en Historial de errores. El historial es técnico y no está separado por roles: el control de acceso es el del equipo local.</p>
<h2 id="módulos">Módulos funcionales</h2>
<div class="tabla-wiki"><table><thead><tr><th>Módulo</th><th>Entradas principales</th></tr></thead><tbody><tr><td>Inicio</td><td>main.php → php/inicio/main.php</td></tr><tr><td>Clientes y productos</td><td>Crear, listar/buscar, editar y eliminar sin referencias.</td></tr><tr><td>Facturas</td><td>Crear, consultar, imprimir y cancelar durante el mismo día.</td></tr><tr><td>Reportes</td><td>Cinco consultas y resumen diario.</td></tr><tr><td>Historial</td><td>Acciones en SQL y errores en archivo.</td></tr><tr><td>Sobre nosotros</td><td>SRS, manuales, PAC, video y enlace a wiki.</td></tr></tbody></table></div>
