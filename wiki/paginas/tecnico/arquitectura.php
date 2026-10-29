<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="visión-general">Visión general</h2>
<p>La Única es <strong>PHP plano</strong> (sin framework, sin Composer) sobre <strong>MySQL</strong>, con <strong>HTML renderizado en el servidor</strong>, una sola hoja de estilos y un único archivo JavaScript para el formulario de facturas.</p>
<pre class="highlight"><code class="language-text">┌──────────── Navegador ────────────┐
│  HTML + estilos.css + facturas.js │
└───────────────┬───────────────────┘
                │ GET (consultas) / POST (cambios + token CSRF)
┌───────────────▼───────────────────┐
│ Apache + PHP                      │
│  php/&lt;módulo&gt;/&lt;acción&gt;.php        │
│   ├─ require config/conexion.php  │  ← conexión + utilidades
│   ├─ valida y procesa             │
│   ├─ include includes/menu.php    │  ← cabecera + menú
│   ├─ HTML de la vista             │
│   └─ include includes/pie.php     │  ← cierre + JS del menú
└───────────────┬───────────────────┘
                │ MySQLi, sentencias preparadas, transacciones
┌───────────────▼───────────────────┐
│ MySQL — base la_unica             │
│  4 tablas · 3 vistas · 5 procs    │
└───────────────────────────────────┘</code></pre>
<p><strong>Un archivo = una pantalla o una acción.</strong> No hay enrutador: la URL es la ruta del archivo (<code>/php/clientes/editar_cliente.php?id=1001</code>).</p>
<h2 id="ciclo-de-vida-de-una-página">Ciclo de vida de una página</h2>
<p>Todas las vistas siguen el mismo patrón, pensado para que alguien que empieza en PHP pueda leerlas de arriba abajo:</p>
<p class="codigo-titulo">Esqueleto típico (simplificado)</p>

<pre class="highlight"><code class="language-php">&lt;?php
require_once __DIR__ . '/../config/conexion.php';   // 1. Conexión y utilidades

$tokenCsrf = obtenerTokenCsrf();                     // 2. Token para el formulario
$mensajeError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {        // 3. Procesar el envío
    if (!validarTokenCsrf($_POST['csrf_token'] ?? null)) {
        $mensajeError = 'No se pudo validar la solicitud. ...';
    } elseif (/* validaciones de campos */) {
        $mensajeError = '...';
    } else {
        try {
            $sentencia = $conexion-&gt;prepare('INSERT ... VALUES (?, ?)');
            $sentencia-&gt;bind_param('is', $id, $nombre);
            $sentencia-&gt;execute();
            header('Location: crear_cliente.php?creado=1', true, 303);  // 4. PRG
            exit;
        } catch (Throwable $e) {
            registrarErrorServidor('crear_cliente', $e);  // detalle al log
            $mensajeError = 'No se pudo crear el cliente ...';  // mensaje amable
        }
    }
}

$titulo = 'Crear cliente';                           // 5. Datos para la plantilla
$seccionActiva = 'clientes';
require __DIR__ . '/../includes/menu.php';           // 6. Cabecera y menú
?&gt;
&lt;!-- 7. HTML de la vista, todo valor dinámico pasa por h() --&gt;
&lt;?php require __DIR__ . '/../includes/pie.php'; ?&gt;   &lt;!-- 8. Cierre --&gt;</code></pre>
<h3 id="postredirectget">Post/Redirect/Get</h3>
<p>Toda escritura exitosa termina con <strong><code>303 See Other</code></strong> hacia una página de consulta. Así, recargar no reenvía el formulario (no se duplican ventas). Los mensajes de éxito viajan:</p>
<ul>
<li>por la URL (<code>crear_cliente.php?creado=1</code>, <code>crear_producto.php?creado=1</code>), o</li>
<li>por la sesión (<code>$_SESSION['factura_creada']</code>) al crear facturas, para mostrar número y total; la cancelación redirige al comprobante (<code>imprimir_factura.php?id=N</code>).</li>
</ul>
<p>La edición y la eliminación muestran el resultado en la misma página.</p>
<h3 id="errores-en-dos-niveles">Errores en dos niveles</h3>
<ol>
<li><strong>Al usuario</strong>: frase en español, sin detalles técnicos, junto al formulario (que conserva lo escrito).</li>
<li><strong>Al log</strong>: <code>registrarErrorServidor($contexto, $e)</code> escribe clase, código, mensaje y <code>SQLSTATE</code>.</li>
</ol>
<h2 id="módulos">Módulos</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Módulo</th>
<th>Carpeta</th>
<th>Archivos</th>
<th>Escribe en BD</th>
</tr>
</thead>
<tbody>
<tr>
<td>Inicio</td>
<td><code>php/inicio/</code></td>
<td><code>main.php</code></td>
<td>No</td>
</tr>
<tr>
<td>Clientes</td>
<td><code>php/clientes/</code></td>
<td>ver, crear, editar, eliminar</td>
<td>Sí</td>
</tr>
<tr>
<td>Productos</td>
<td><code>php/productos/</code></td>
<td>ver, crear, editar, eliminar</td>
<td>Sí</td>
</tr>
<tr>
<td>Facturas</td>
<td><code>php/facturas/</code></td>
<td>ver, crear, imprimir, cancelar</td>
<td>Sí (transacciones)</td>
</tr>
<tr>
<td>Reportes</td>
<td><code>php/reportes/</code></td>
<td>resumen, plantilla, catálogo + 7 rutas</td>
<td>No</td>
</tr>
<tr>
<td>Sobre nosotros</td>
<td><code>php/sobre_nosotros/</code></td>
<td>índice, manual, video, inicializar</td>
<td>No (ni se conecta)</td>
</tr>
<tr>
<td>Compartido</td>
<td><code>php/config/</code>, <code>php/includes/</code></td>
<td>conexión, menú, pie</td>
<td>—</td>
</tr>
</tbody>
</table></div>
<h2 id="decisiones-de-diseño">Decisiones de diseño</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Decisión</th>
<th>Motivo</th>
</tr>
</thead>
<tbody>
<tr>
<td>PHP plano y snake_case/camelCase explícito, muy comentado</td>
<td>El equipo revirtió una versión «compacta»: el código debe ser legible para aprendices (cada archivo abre con un bloque <em>Archivo / Módulo / Propósito / Funcionamiento / Puntos clave</em>).</td>
</tr>
<tr>
<td>Sin dependencias externas (ni Bootstrap ni CDN)</td>
<td>Funciona sin Internet; nada que instalar con Composer/npm.</td>
</tr>
<tr>
<td>Totales calculados, no guardados</td>
<td><code>db.sql</code> no tiene columnas de subtotal/total ni precio histórico (SRS RF-07).</td>
</tr>
<tr>
<td>Reglas duplicadas en PHP <strong>y</strong> en la BD</td>
<td>PHP da mensajes claros; las FK y <code>CHECK</code> de MySQL son la última barrera.</td>
</tr>
<tr>
<td>Una plantilla para 7 reportes</td>
<td><code>reporte_individual.php</code> + <code>catalogoReportes()</code> evitan duplicar HTML.</td>
</tr>
</tbody>
</table></div>
<div class="aviso aviso-note">
<p class="aviso-titulo">Nota</p>
<p>La rama <code>main</code> del equipo y la rama <code>revision-samuel</code> comparten esta arquitectura. Esta wiki documenta <code>revision-samuel</code> (commit <code>2eec07a</code>).</p>
</div>
