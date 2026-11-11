<?php
/**
 * Archivo: existencias_productos.php
 * Módulo: Reportes — existencias de productos.
 * Propósito: mostrar qué productos están agotados o con pocas unidades para saber qué pedir.
 * Funcionamiento: llama a reporte_existencias() (orden por stock ascendente) y marca cada fila como
 * Agotado (0), Bajo (hasta STOCK_BAJO unidades) o Disponible. También calcula el valor del
 * inventario: suma de existencias × precio actual de cada producto.
 */

require __DIR__ . '/../config/conexion.php';

$titulo = 'Existencias de productos';
$seccionActiva = 'reportes';
$errorLectura = false;
$productos = [];

try {
    $productos = llamarProcedimiento($conexion, 'CALL reporte_existencias()');
} catch (Throwable $e) {
    registrarErrorServidor('reporte_existencias', $e);
    $errorLectura = true;
}

$agotados = 0;
$bajos = 0;
$valorInventario = 0.0;
foreach ($productos as $producto) {
    $valorInventario += (int)$producto['stock'] * (float)$producto['precio_unitario'];
    if ((int)$producto['stock'] === 0) {
        $agotados++;
    } elseif ((int)$producto['stock'] <= STOCK_BAJO) {
        $bajos++;
    }
}

require __DIR__ . '/../includes/menu.php';
require __DIR__ . '/acciones_impresion.php';
?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <a href="ver_reportes.php">Reportes</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Existencias</span>
        </nav>
        <h1 class="encabezado-titulo">Existencias de productos</h1>
        <p class="encabezado-descripcion">Productos con menos unidades primero. «Bajo» significa <?= STOCK_BAJO ?> unidades o menos.</p>
    </div>
</div>

<?php if ($errorLectura): ?>
    <div class="mensaje error alerta alerta-error" role="alert">No se pudo generar el reporte. Revisa que el procedimiento reporte_existencias exista en la base de datos.</div>
<?php elseif ($productos === []): ?>
    <div class="sin-datos estado-vacio" role="status">
        <h2 class="estado-vacio-titulo">Todavía no hay productos registrados</h2>
        <a class="boton" href="<?= h($baseUrl) ?>/php/productos/crear_producto.php">Crear producto</a>
    </div>
<?php else: ?>
    <section class="tarjetas reportes-resumen-grid" aria-label="Resumen de existencias">
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Agotados</h2>
            <p class="reportes-resumen-valor"><?= $agotados ?></p>
            <p class="reportes-resumen-detalle">Sin unidades; no se pueden vender.</p>
        </article>
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Bajos</h2>
            <p class="reportes-resumen-valor"><?= $bajos ?></p>
            <p class="reportes-resumen-detalle">Entre 1 y <?= STOCK_BAJO ?> unidades; conviene reponer.</p>
        </article>
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Productos</h2>
            <p class="reportes-resumen-valor"><?= count($productos) ?></p>
            <p class="reportes-resumen-detalle">Total de productos registrados.</p>
        </article>
        <article class="reportes-resumen-card">
            <h2 class="modulo-titulo">Valor del inventario</h2>
            <p class="reportes-resumen-valor"><?= h(formatearMonedaCop($valorInventario)) ?></p>
            <p class="reportes-resumen-detalle">Existencias × precio actual de cada producto.</p>
        </article>
    </section>

    <div class="tabla-wrap tabla-contenedor">
        <table class="tabla">
            <thead>
                <tr>
                    <th scope="col">Producto</th>
                    <th scope="col">Unidad</th>
                    <th scope="col" class="texto-derecha">Existencias</th>
                    <th scope="col">Estado</th>
                    <th scope="col" class="texto-derecha">Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <?php $stock = (int)$producto['stock']; ?>
                    <tr>
                        <td><?= h($producto['nombre']) ?></td>
                        <td><span class="insignia-unidad"><?= h($producto['unidad_medida']) ?></span></td>
                        <td class="texto-derecha"><?= number_format($stock, 0, ',', '.') ?></td>
                        <td>
                            <?php if ($stock === 0): ?>
                                <span class="insignia-stock agotado">Agotado</span>
                            <?php elseif ($stock <= STOCK_BAJO): ?>
                                <span class="insignia-stock bajo">Bajo</span>
                            <?php else: ?>
                                <span class="insignia-stock disponible">Disponible</span>
                            <?php endif; ?>
                        </td>
                        <td class="texto-derecha"><?= h(formatearMonedaCop($producto['precio_unitario'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>
