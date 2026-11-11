<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p><code>conexion.php</code> coordina el arranque y <code>configuracion.php</code> lee los valores privados. No edites estos archivos para configurar un equipo: copia <code>conexion.local.example.php</code> a <code>conexion.local.php</code>, que Git ignora.</p><h2 id="prioridad">Prioridad de configuración</h2>
<p>Una variable DB_* definida tiene prioridad; después se usa el valor del archivo local y, al final, el predeterminado. No se carga un archivo .env automáticamente.</p><div class="tabla-wiki"><table><thead><tr><th>Clave privada</th><th>Variable</th><th>Predeterminado</th></tr></thead><tbody><tr><td>host</td><td>DB_HOST</td><td>127.0.0.1</td></tr><tr><td>puerto</td><td>DB_PORT</td><td>3306</td></tr><tr><td>base_datos</td><td>DB_DATABASE</td><td>la_unica</td></tr><tr><td>usuario</td><td>DB_USERNAME</td><td>root</td></tr><tr><td>contrasena</td><td>DB_PASSWORD</td><td>Cadena vacía</td></tr></tbody></table></div>

<h2 id="cambiar-el-puerto">Cambiar el puerto y la base</h2>
<p>Comprueba el puerto real del servicio SQL. El 3306 es un valor predeterminado, no una obligación: la demostración de este equipo usa 3307. Dos carpetas que apuntan a la misma base comparten datos; para otra copia crea una base y configuración independientes.</p><pre class="highlight"><code class="language-php">&lt;?php
return [
    &#x27;host&#x27; =&gt; &#x27;127.0.0.1&#x27;,
    &#x27;puerto&#x27; =&gt; 3307,
    &#x27;base_datos&#x27; =&gt; &#x27;NOMBRE_BASE_EXCLUSIVA&#x27;,
    &#x27;usuario&#x27; =&gt; &#x27;USUARIO_DE_ESTA_BASE&#x27;,
    &#x27;contrasena&#x27; =&gt; &#x27;CLAVE_PRIVADA_DEL_EQUIPO&#x27;,
];</code></pre>
<p>El puerto debe ser un entero entre 1 y 65535. Sustituye los marcadores; no publiques la contraseña.</p>
<h2 id="usar-un-usuario-con-contraseña">Usuario limitado a una base</h2>
<p>La cuenta del aplicativo necesita SELECT, INSERT, UPDATE, DELETE y EXECUTE sobre su base. Usa una cuenta administrativa distinta para instalar o respaldar. Esta entrega no incluye usuario_app.sql: el administrador crea la cuenta local y configura sus credenciales fuera de Git.</p>
<h2 id="juego-de-caracteres-y-zona-horaria">Caracteres y zona horaria</h2>
<p>La conexión utiliza utf8mb4 y UTC−05:00; PHP usa America/Bogota en entorno.php. Esto mantiene coherentes la fecha automática y la cancelación del mismo día.</p>
<h2 id="qué-pasa-si-falla">Qué pasa si falla</h2>
<p>Los fallos de SQL o de configuración responden HTTP 500 con «No se pudo conectar a la base de datos» y orientación general. El detalle queda en logs/errores.log, visible en Historial de errores dentro del entorno local. La wiki y ese historial omiten la conexión; Sobre nosotros no la omite.</p>
