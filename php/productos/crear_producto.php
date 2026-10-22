<?php
require __DIR__ . '/../config/conexion.php';
$titulo = 'Crear producto';
$seccionActiva = 'productos';
$mensaje = '';
$error = '';
$nombre = '';
$unidad = '';
$stock = '';
$precio = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim((string)($_POST['nombre'] ?? ''));
    $unidad = trim((string)($_POST['unidad_medida'] ?? ''));
    $stockTexto = trim((string)($_POST['stock'] ?? ''));
    $precio = trim((string)($_POST['precio_unitario'] ?? ''));
    $stock = filter_var($stockTexto, FILTER_VALIDATE_INT);

    if ($nombre === '' || texto_largo($nombre) > 100) {
        $error = 'El nombre es obligatorio y admite hasta 100 caracteres.';
    } elseif ($unidad === '' || texto_largo($unidad) > 20) {
        $error = 'La unidad es obligatoria y admite hasta 20 caracteres.';
    } elseif ($stock === false || $stock < 0 || $stock > 2147483647) {
        $error = 'El stock debe ser un entero no negativo.';
    } elseif (!preg_match('/^\d{1,8}(?:\.\d{1,2})?$/', $precio)) {
        $error = 'El precio debe ser no negativo y tener máximo dos decimales.';
    } else {
        $stmt = $conexion->prepare('INSERT INTO productos(nombre,unidad_medida,stock,precio_unitario) VALUES(?,?,?,?)');
        $stmt->bind_param('ssis', $nombre, $unidad, $stock, $precio);
        if ($stmt->execute()) {
            $mensaje = 'Producto creado correctamente.';
            $nombre = '';
            $unidad = '';
            $stock = '';
            $precio = '';
        } else {
            $error = 'No se pudo crear el producto.';
        }
    }
}
require __DIR__ . '/../includes/menu.php';
?>
<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <a href="ver_productos.php">Productos</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span>Nuevo producto</span>
        </nav>
        <h1 class="encabezado-titulo">Crear producto</h1>
        <p class="encabezado-descripcion">Agrega un nuevo artículo al catálogo con sus existencias iniciales y precio unitario de comercialización.</p>
    </div>
</div>

<div class="panel-formulario-wrap">
    <div class="formulario tarjeta-formulario">
        <?php if ($mensaje !== ''): ?>
            <div class="mensaje alerta" role="status">
                <svg class="alerta-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div class="alerta-contenido"><?= h($mensaje) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="mensaje error alerta alerta-error" role="alert">
                <svg class="alerta-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div class="alerta-contenido"><?= h($error) ?></div>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="formulario-grid">
                <div class="campo col-completa">
                    <label for="nombre">
                        <span>Nombre del producto</span>
                        <span class="ayuda" style="margin:0;">Hasta 100 caracteres</span>
                    </label>
                    <input id="nombre" name="nombre" type="text" maxlength="100" value="<?= h($nombre) ?>" placeholder="Ej. Café molido 500g" required>
                </div>

                <div class="campo">
                    <label for="unidad_medida">
                        <span>Unidad de medida</span>
                        <span class="ayuda" style="margin:0;">Hasta 20 car.</span>
                    </label>
                    <input id="unidad_medida" name="unidad_medida" type="text" maxlength="20" value="<?= h($unidad) ?>" placeholder="Ej. Kilogramo, Litro, Paquete" required>
                    <span class="ayuda texto-ayuda">Define la unidad para cuantificar ventas y existencias.</span>
                </div>

                <div class="campo">
                    <label for="stock">
                        <span>Stock inicial</span>
                        <span class="ayuda" style="margin:0;">Entero ≥ 0</span>
                    </label>
                    <input id="stock" name="stock" type="number" min="0" max="2147483647" step="1" value="<?= h($stock) ?>" placeholder="0" required>
                    <span class="ayuda texto-ayuda">Unidades disponibles en almacén.</span>
                </div>

                <div class="campo col-completa">
                    <label for="precio_unitario">
                        <span>Precio unitario ($)</span>
                        <span class="ayuda" style="margin:0;">Hasta 2 decimales</span>
                    </label>
                    <input id="precio_unitario" name="precio_unitario" type="number" min="0" max="99999999.99" step="0.01" value="<?= h($precio) ?>" placeholder="0.00" required>
                    <span class="ayuda texto-ayuda">Tarifa monetaria de venta por cada unidad de medida.</span>
                </div>
            </div>

            <div class="fila-botones">
                <button type="submit">
                    <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    <span>Crear producto</span>
                </button>
                <a class="boton secundario" href="ver_productos.php">
                    <svg class="boton-icono" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <line x1="19" y1="12" x2="5" y2="12"/>
                        <polyline points="12 19 5 12 12 5"/>
                    </svg>
                    <span>Ver productos</span>
                </a>
            </div>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../includes/pie.php'; ?>
