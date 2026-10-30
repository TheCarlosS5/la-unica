<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Todas están en <code>php/config/conexion.php</code> salvo que se indique otra cosa. Se declaran dentro de <code>if (!function_exists(...))</code>.</p>
<h2 id="variables-globales">Variables globales</h2>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Variable</th>
<th>Tipo</th>
<th>Descripción</th>
</tr>
</thead>
<tbody>
<tr>
<td><code>$conexion</code></td>
<td><code>mysqli</code></td>
<td>Conexión abierta, <code>utf8mb4</code>, zona <code>-05:00</code>, errores como excepciones (<code>MYSQLI_REPORT_STRICT</code>).</td>
</tr>
<tr>
<td><code>$baseUrl</code></td>
<td><code>string</code></td>
<td>Prefijo de la instalación (<code>''</code> o <code>/la-unica</code>). Úsalo en todo <code>href</code>/<code>src</code>.</td>
</tr>
<tr>
<td><code>$titulo</code></td>
<td><code>string</code></td>
<td><em>(la define cada vista)</em> Título de la pestaña: <code>"{$titulo} · La Única - Administración"</code>.</td>
</tr>
<tr>
<td><code>$seccionActiva</code></td>
<td><code>string</code></td>
<td><em>(la define cada vista)</em> <code>inicio</code>, <code>clientes</code>, <code>productos</code>, <code>facturas</code>, <code>reportes</code> o <code>sobre_nosotros</code>. Si falta, <code>menu.php</code> la deduce de la ruta.</td>
</tr>
</tbody>
</table></div>
<h2 id="salida-html">Salida HTML</h2>
<h3 id="hmixed-valor-string"><code>h(mixed $valor): string</code></h3>
<p><code>htmlspecialchars</code> con <code>ENT_QUOTES | ENT_SUBSTITUTE</code> en UTF-8 (<code>php/config/entorno.php</code>). Uso: <code>&lt;?= h($cliente['nombre']) ?&gt;</code>.</p>
<h2 id="validación">Validación</h2>
<h3 id="validaridmixed-valor-int"><code>validarId(mixed $valor): ?int</code></h3>
<p>Devuelve el entero si está entre 1 y 2.147.483.647; si no, <code>null</code>. Acepta <code>string</code> o <code>int</code>.</p>
<pre class="highlight"><code class="language-php">validarId('1001');  // 1001
validarId('0');     // null
validarId('12.5');  // null
validarId(['1']);   // null</code></pre>
<h3 id="validarfechamixed-valor-bool"><code>validarFecha(mixed $valor): bool</code></h3>
<p><code>true</code> solo para fechas reales en formato <code>YYYY-MM-DD</code>.</p>
<h3 id="longitudtextomixed-valor-int"><code>longitudTexto(mixed $valor): int</code></h3>
<p>Número de caracteres UTF-8 (usa <code>mb_strlen</code>, o una expresión regular <code>/./us</code> si no hay <code>mbstring</code>).</p>
<h2 id="formato">Formato</h2>
<h3 id="formatearmonedacopmixed-valor-string"><code>formatearMonedaCop(mixed $valor): string</code></h3>
<p>Convierte un decimal de MySQL (<code>"105600.00"</code>) en <code>"$105.600,00 COP"</code>. Trabaja con texto (no <code>float</code>) para no perder centavos. Si la entrada no es un número no negativo con hasta 2 decimales, devuelve <code>"$0,00 COP"</code>.</p>
<h2 id="consultas">Consultas</h2>
<h3 id="consultapreparada"><code>consultaPreparada(mysqli $conexion, string $sql, string $tipos = '', mixed ...$parametros): array</code></h3>
<p>Prepara, ejecuta y devuelve todas las filas asociativas; cierra la sentencia en un <code>finally</code>, también cuando la consulta falla (<code>php/config/consultas.php</code>).</p>
<pre class="highlight"><code class="language-php">$factura = consultaPreparada($conexion, 'SELECT * FROM facturas WHERE id_factura = ?', 'i', $id)[0] ?? null;</code></pre>
<h3 id="patronlike"><code>patronLike(string $texto): string</code></h3>
<p>Devuelve <code>%texto%</code> escapando <code>%</code>, <code>_</code> y <code>\</code>, para que buscar «50%» encuentre ese texto y no «todo lo que empiece por 50» (<code>php/config/consultas.php</code>).</p>
<h2 id="sesión-y-csrf">Sesión y CSRF</h2>
<h3 id="sesiondisponible-bool"><code>sesionDisponible(): bool</code></h3>
<p>Inicia la sesión (cookie <code>HttpOnly</code>, <code>SameSite=Strict</code>, modo estricto) si aún no lo está y si las cabeceras no se han enviado. Devuelve si quedó activa.</p>
<h3 id="obtenertokencsrf-string"><code>obtenerTokenCsrf(): ?string</code></h3>
<p>Token hexadecimal de 64 caracteres guardado en <code>$_SESSION['csrf_token']</code>; <code>null</code> si no hay sesión.</p>
<h3 id="validartokencsrfmixed-tokenrecibido-bool"><code>validarTokenCsrf(mixed $tokenRecibido): bool</code></h3>
<p>Compara con <code>hash_equals()</code>.</p>
<pre class="highlight"><code class="language-php">&lt;input type="hidden" name="csrf_token" value="&lt;?= h($tokenCsrf) ?&gt;"&gt;
...
if (!validarTokenCsrf($_POST['csrf_token'] ?? null)) { /* rechazar */ }</code></pre>
<h3 id="mensajes-flash"><code>guardarMensajeFlash(string $clave, string $texto): void</code> / <code>tomarMensajeFlash(string $clave): string</code></h3>
<p>Guardan un mensaje en la sesión antes de redirigir y lo devuelven (y borran) en la página siguiente.</p>
<h2 id="errores">Errores</h2>
<h3 id="registrarerrorservidormixed-contexto-throwable-error-void"><code>registrarErrorServidor(mixed $contexto, Throwable $error): void</code></h3>
<p>Escribe en el log de PHP: <code>[contexto] Clase (código): mensaje SQLSTATE=xxxxx</code>. No muestra nada al usuario.</p>
<h2 id="reportes-phpreportesdatos_reportesphp">Reportes (<code>php/reportes/datos_reportes.php</code>)</h2>
<h3 id="catalogoreportes-array"><code>catalogoReportes(): array</code></h3>
<p>Mapa <code>clave =&gt; ['archivo', 'titulo', 'descripcion', 'fuente', 'nota', 'vacio', 'columnas', 'enlace_factura']</code> de los 7 reportes. <code>nota</code> es la aclaración bajo la tabla y <code>vacio</code> el texto cuando no hay filas.</p>
<h3 id="obtenerfilasreportemysqli-conexion-string-tiporeporte-array"><code>obtenerFilasReporte(mysqli $conexion, string $tipoReporte): array</code></h3>
<p>Ejecuta la fuente del reporte y devuelve filas asociativas. Lanza <code>InvalidArgumentException</code> si la clave no existe.</p>
<h3 id="consultarfilasreportemysqli-conexion-string-consulta-bool-esprocedimiento--false-array"><code>consultarFilasReporte(mysqli $conexion, string $consulta, bool $esProcedimiento = false): array</code></h3>
<p>Ejecuta, lee con <code>fetch_all(MYSQLI_ASSOC)</code>, libera y —si es <code>CALL</code>— consume los resultados adicionales.</p>
<h2 id="facturas-phpfacturasservicio_facturasphp">Facturas (<code>php/facturas/servicio_facturas.php</code>)</h2>
<p>Cada función corre en una transacción (<code>ejecutarEnTransaccion()</code>) y lanza <code>DomainException</code> con un mensaje para el operador cuando una regla no se cumple.</p>
<h3 id="registrarventa"><code>registrarVenta(mysqli $conexion, int $idCliente, array $cantidadesPorProducto, string $estado = 'Pagado'): array</code></h3>
<p>Bloquea cliente y productos (en orden de ID), valida stock, guarda la factura con el <strong>precio de cada producto</strong> y descuenta existencias. <code>$estado</code> es <code>Pagado</code> o <code>Pendiente</code>. Devuelve <code>['id' =&gt; int, 'total' =&gt; string]</code>.</p>
<h3 id="registrarpagofactura"><code>registrarPagoFactura(mysqli $conexion, int $idFactura): void</code></h3>
<p>Pendiente → Pagado. No mueve inventario.</p>
<h3 id="cancelarfactura"><code>cancelarFactura(mysqli $conexion, int $idFactura): void</code></h3>
<p>Pagado o Pendiente → Cancelado, reponiendo las cantidades una sola vez.</p>
<h3 id="totalfactura"><code>totalFactura(mysqli $conexion, int $idFactura): string</code></h3>
<p>Suma <code>cantidad × precio_unitario</code> de las líneas, en SQL para no perder centavos.</p>
<h2 id="sobre-nosotros-phpsobre_nosotrosinicializarphp">Sobre nosotros (<code>php/sobre_nosotros/inicializar.php</code>)</h2>
<p>Carga <code>entorno.php</code> (<code>$baseUrl</code>, cabeceras y <code>h()</code>) <strong>sin</strong> abrir conexión a MySQL, para que el manual y el video se vean aunque la base esté caída.</p>
