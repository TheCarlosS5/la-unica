<?php
/*
 * NUEVA VENTA (caja)  ->  SOLO FRONT por ahora.
 *
 * Qué hace esta página hoy:
 *   - Lee productos y clientes con dos SELECT simples (igual que ver_productos.php).
 *   - El ticket (agregar, quitar, total, cambio) lo maneja assets/js/caja.js en el navegador.
 *
 * Qué falta (lo conecta el equipo de backend):
 *   1. Crear php/facturas/guardar_factura.php que reciba este formulario por POST:
 *        id_cliente        -> cliente elegido (tabla facturas)
 *        cantidades[ID]    -> cantidad por producto, ej. cantidades[3]=2 (tabla detalle_facturas)
 *   2. Dentro de una transacción: INSERT en facturas, INSERT en detalle_facturas y
 *      restar el stock de cada producto (revisar que alcance).
 *   3. Cambiar data-conectado="no" por data-conectado="si" en el <form> de abajo
 *      y listo: el JS deja de bloquear el envío.
 */
require __DIR__ . '/../config/conexion.php';
$titulo = 'Nueva venta';
$seccionActiva = 'facturas';
$scriptExtra = 'caja.js';

$productos = $conexion->query('SELECT id_producto,nombre,unidad_medida,stock,precio_unitario FROM productos ORDER BY nombre');
$clientes = $conexion->query('SELECT id_cliente,nombre FROM clientes ORDER BY nombre');
require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Nueva venta</span>
        </nav>
        <h1 class="encabezado-titulo">Nueva venta</h1>
    </div>
</div>

<div class="caja">
    <!-- Columna izquierda: productos -->
    <section class="caja-productos" aria-labelledby="titulo-productos">
        <h2 id="titulo-productos" class="solo-lectores">Productos disponibles</h2>

        <div class="caja-buscador">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <label class="solo-lectores" for="buscar-producto">Buscar producto</label>
            <input id="buscar-producto" type="search" placeholder="Buscar producto (Enter lo agrega)…" autocomplete="off" spellcheck="false">
        </div>

        <?php if (!$productos || $productos->num_rows === 0): ?>
            <div class="sin-datos estado-vacio">
                <h2 class="estado-vacio-titulo">Todavía no hay productos</h2>
                <p class="estado-vacio-texto">Crea tu primer producto para poder venderlo.</p>
                <a class="boton" href="<?= h($baseUrl) ?>/php/productos/crear_producto.php">Crear producto</a>
            </div>
        <?php else: ?>
            <div id="productos-grid" class="productos-grid">
                <?php while ($p = $productos->fetch_assoc()): ?>
                    <?php $stock = (int)$p['stock']; ?>
                    <button type="button" class="producto-boton"
                        data-id="<?= h($p['id_producto']) ?>"
                        data-nombre="<?= h($p['nombre']) ?>"
                        data-unidad="<?= h($p['unidad_medida']) ?>"
                        data-precio="<?= h($p['precio_unitario']) ?>"
                        data-stock="<?= h($stock) ?>"
                        <?= $stock === 0 ? 'disabled' : '' ?>>
                        <span class="producto-cantidad" aria-hidden="true"></span>
                        <span class="producto-nombre"><?= h($p['nombre']) ?></span>
                        <span class="producto-unidad"><?= h($p['unidad_medida']) ?></span>
                        <span class="producto-precio">$ <?= number_format((float)$p['precio_unitario'], 2, ',', '.') ?></span>
                        <?php if ($stock === 0): ?>
                            <span class="producto-stock agotado">Agotado</span>
                        <?php else: ?>
                            <span class="producto-stock<?= $stock <= 10 ? ' poco' : '' ?>">Quedan <?= h($stock) ?></span>
                        <?php endif; ?>
                    </button>
                <?php endwhile; ?>
                <p id="sin-resultados" class="productos-sin-resultados" hidden>No encontramos ese producto.</p>
            </div>
        <?php endif; ?>
    </section>

    <!-- Columna derecha: ticket -->
    <form id="form-venta" class="ticket" method="post" action="guardar_factura.php" data-conectado="no" novalidate aria-labelledby="titulo-ticket">
        <h2 id="titulo-ticket" class="ticket-titulo">
            <span>Ticket</span>
            <button type="button" id="vaciar-ticket" class="ticket-vaciar" disabled>Vaciar</button>
        </h2>

        <div class="campo">
            <label for="id_cliente">
                <span>Cliente</span>
            </label>
            <select id="id_cliente" name="id_cliente" required>
                <option value="">Selecciona un cliente…</option>
                <?php if ($clientes): while ($c = $clientes->fetch_assoc()): ?>
                    <option value="<?= h($c['id_cliente']) ?>"><?= h($c['nombre']) ?></option>
                <?php endwhile; endif; ?>
            </select>
        </div>

        <ul id="ticket-lineas" class="ticket-lineas" aria-live="polite">
            <li class="ticket-vacio" id="ticket-vacio">Aún no hay productos. Toca uno de la izquierda.</li>
        </ul>

        <!-- Aquí el JS crea un <input type="hidden" name="cantidades[ID]"> por cada producto del ticket -->
        <div id="campos-ocultos"></div>

        <dl class="ticket-total">
            <dt>Total</dt>
            <dd id="ticket-total" aria-live="polite">$ 0,00</dd>
        </dl>

        <div class="ticket-pago">
            <div class="campo">
                <label for="efectivo"><span>Efectivo recibido</span></label>
                <input id="efectivo" type="number" inputmode="decimal" min="0" step="0.01" placeholder="0,00" autocomplete="off">
            </div>
            <div class="ticket-cambio">
                <span class="etiqueta" id="etiqueta-cambio">Cambio</span>
                <output id="ticket-cambio" for="efectivo" aria-labelledby="etiqueta-cambio">$ 0,00</output>
            </div>
        </div>

        <div id="aviso-caja" class="mensaje aviso alerta alerta-aviso aviso-flotante" role="status" hidden>
            <svg class="alerta-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div class="alerta-contenido" id="aviso-caja-texto"></div>
        </div>

        <button type="submit" id="cobrar" class="ticket-cobrar" disabled>Cobrar</button>
    </form>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
