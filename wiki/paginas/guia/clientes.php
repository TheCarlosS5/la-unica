<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p>Un <strong>cliente</strong> es quien compra. Cada factura queda asociada a uno. Los datos son mínimos a propósito:</p>
<div class="tabla-wiki"><table>
<thead>
<tr>
<th>Campo</th>
<th>Tipo</th>
<th>Reglas</th>
</tr>
</thead>
<tbody>
<tr>
<td>ID del cliente</td>
<td>número entero</td>
<td><strong>Obligatorio y único.</strong> Lo escribes tú (cédula, NIT o un consecutivo propio). Entre 1 y 2.147.483.647.</td>
</tr>
<tr>
<td>Nombre completo</td>
<td>texto</td>
<td>Obligatorio, máximo 100 caracteres. <strong>Se permiten nombres repetidos.</strong></td>
</tr>
<tr>
<td>Fecha de registro</td>
<td>fecha</td>
<td>Opcional. Si la dejas vacía se usa la fecha de hoy. Puede ser anterior, pero no posterior a hoy.</td>
</tr>
</tbody>
</table></div>
<h2 id="abrir-el-módulo">Abrir el módulo</h2>
<p>En el menú lateral, grupo <em>Catálogo</em>:</p>
<figure class="captura"><img src="img/clientes-menu.webp" alt="Clientes en el menú" loading="lazy"><figcaption>Clientes en el menú</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Clientes</strong> abre el listado.</li>
<li><span class="marca-num">2</span> <strong>Nuevo cliente</strong> va directo al formulario de creación.</li>
</ul>
<h2 id="leer-el-listado">Leer el listado</h2>
<figure class="captura"><img src="img/clientes-lista-columnas.webp" alt="Columnas importantes del listado" loading="lazy"><figcaption>Columnas importantes del listado</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Facturas</strong>: cuántas facturas tiene el cliente (de cualquier estado).</li>
<li><span class="marca-num">2</span> <strong>Eliminar</strong>: solo aparece si el cliente tiene <strong>0 facturas</strong>.</li>
<li><span class="marca-num">3</span> <strong>Tiene facturas</strong>: candado. El cliente tiene historial y no se puede eliminar.</li>
</ul>
<h2 id="buscar-un-cliente">Buscar un cliente</h2>
<div class="pasos">
<ol>
<li><span class="marca-num">1</span> Escribe en el buscador un <strong>ID</strong> o <strong>parte del nombre</strong> (no importan mayúsculas).</li>
<li>
<p><span class="marca-num">2</span> Pulsa <strong>Buscar</strong>.</p>
<figure class="captura"><img src="img/clientes-buscar.webp" alt="Buscador de clientes" loading="lazy"><figcaption>Buscador de clientes</figcaption></figure>
</li>
<li>
<p>La tabla muestra solo las coincidencias. Para ver todos otra vez, borra el texto y busca de nuevo.</p>
<figure class="captura"><img src="img/clientes-buscar-resultado.webp" alt="Resultado de buscar «tienda»" loading="lazy"><figcaption>Resultado de buscar «tienda»</figcaption></figure>
</li>
</ol>
</div>
<p>Un número busca a la vez ese <strong>ID exacto</strong> y los nombres que contengan ese número.</p>
<h2 id="crear-un-cliente">Crear un cliente</h2>
<div class="pasos">
<ol>
<li>Menú → <em>Clientes → <strong>Nuevo cliente</strong></em>.</li>
<li>
<p>Llena el formulario:</p>
<figure class="captura"><img src="img/clientes-crear-formulario.webp" alt="Formulario de nuevo cliente" loading="lazy"><figcaption>Formulario de nuevo cliente</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>ID del cliente</strong>: el número que identifica al cliente en tu negocio. No se puede cambiar después.</li>
<li><span class="marca-num">2</span> <strong>Nombre completo</strong>: hasta 100 caracteres.</li>
<li><span class="marca-num">3</span> <strong>Fecha de registro</strong>: déjala vacía para usar la fecha de hoy.</li>
<li><span class="marca-num">4</span> Pulsa <strong>Crear cliente</strong>.</li>
</ul>
</li>
<li>
<p>Verás el aviso verde y el formulario vacío, listo para registrar otro:</p>
<figure class="captura"><img src="img/clientes-crear-exito.webp" alt="Cliente creado correctamente" loading="lazy"><figcaption>Cliente creado correctamente</figcaption></figure>
</li>
</ol>
</div>
<h3 id="si-algo-sale-mal">Si algo sale mal</h3>
<p>El formulario <strong>conserva lo que escribiste</strong> y te explica el problema. Por ejemplo, con un ID que ya existe:</p>
<figure class="captura"><img src="img/clientes-crear-error.webp" alt="Error: ya existe un cliente con ese ID" loading="lazy"><figcaption>Error: ya existe un cliente con ese ID</figcaption></figure>
<p><span class="marca-num">1</span> El mensaje dice qué pasó; <span class="marca-num">2</span> corrige el campo y vuelve a pulsar <strong>Crear cliente</strong>. Todos los mensajes posibles están en el <a href="index.php?pagina=guia/mensajes#clientes">Catálogo de mensajes</a>.</p>
<h2 id="editar-un-cliente">Editar un cliente</h2>
<div class="pasos">
<ol>
<li>
<p>En el listado, pulsa <span class="marca-num">1</span> <strong>Editar</strong> en la fila del cliente:</p>
<figure class="captura"><img src="img/clientes-editar-boton.webp" alt="Botón Editar" loading="lazy"><figcaption>Botón Editar</figcaption></figure>
</li>
<li>
<p>En el formulario de edición:</p>
<figure class="captura"><img src="img/clientes-editar-formulario.webp" alt="Formulario de edición" loading="lazy"><figcaption>Formulario de edición</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> <strong>Identificador fijo</strong>: el ID no se puede cambiar (está ligado a sus facturas).</li>
<li><span class="marca-num">2</span> <strong>Nombre</strong>: es lo único que puedes modificar.</li>
<li><span class="marca-num">3</span> <strong>Fecha de registro</strong>: solo de consulta.</li>
<li><span class="marca-num">4</span> Pulsa <strong>Guardar cambios</strong>.</li>
</ul>
</li>
</ol>
</div>
<p>Las facturas guardan el <strong>ID</strong> y no una copia del nombre: al cambiar el nombre, todas sus facturas anteriores muestran el nombre nuevo.</p>
<h2 id="eliminar-un-cliente">Eliminar un cliente</h2>
<div class="pasos">
<ol>
<li>
<p>Pulsa <span class="marca-num">1</span> <strong>Eliminar</strong> en la fila. Solo aparece en clientes <strong>sin facturas</strong>.</p>
<figure class="captura"><img src="img/clientes-eliminar-boton.webp" alt="Botón Eliminar" loading="lazy"><figcaption>Botón Eliminar</figcaption></figure>
</li>
<li>
<p>El navegador pregunta <em>«¿Eliminar este cliente?»</em>. Acepta.</p>
</li>
<li>
<p>Se abre la página de confirmación:</p>
<figure class="captura"><img src="img/clientes-eliminar-confirmar.webp" alt="Confirmación de eliminación" loading="lazy"><figcaption>Confirmación de eliminación</figcaption></figure>
<ul>
<li><span class="marca-num">1</span> Revisa que el ID y el nombre sean los correctos.</li>
<li><span class="marca-num">2</span> <strong>Cancelar</strong> vuelve al listado sin borrar nada.</li>
<li><span class="marca-num">3</span> <strong>Confirmar eliminación</strong> borra el cliente definitivamente.</li>
</ul>
</li>
</ol>
</div>
<div class="aviso aviso-caution">
<p class="aviso-titulo">La eliminación es permanente</p>
<p>No hay papelera. Aunque alguien intente forzar la eliminación de un cliente con facturas, el servidor lo vuelve a comprobar y la base de datos lo impide: verás <em>«No se puede eliminar: el cliente tiene facturas asociadas.»</em></p>
</div>
<h2 id="preguntas-rápidas">Preguntas rápidas</h2>
<p><strong>¿Puedo cambiar el ID de un cliente?</strong> No. Si te equivocaste y aún no tiene facturas, elimínalo y créalo de nuevo.</p>
<p><strong>¿Por qué no hay teléfono, correo o dirección?</strong> El SRS (desde la versión 1.2) se alineó con <code>db.sql</code>, que solo define ID, nombre y fecha. Ver <a href="index.php?pagina=tecnico/requisitos">Requisitos</a>.</p>
