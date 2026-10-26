<?php
// ===================================================================
// form_producto.php
// Campos del formulario de producto. Lo usan crear_producto.php y
// editar_producto.php, así el formulario se escribe una sola vez.
// Antes de incluirlo, la página define:
//   $p           -> arreglo con los valores actuales de los campos
//   $textoBoton  -> texto del botón de guardar
// ===================================================================
?>
<form method="post" class="row g-3">
    <div class="col-12">
        <label class="form-label" for="nombre">Nombre</label>
        <input class="form-control" id="nombre" name="nombre" type="text" maxlength="100"
               value="<?= h($p['nombre']) ?>" placeholder="Ej. Arroz blanco 1 kg" required autofocus>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="unidad_medida">Unidad</label>
        <input class="form-control" id="unidad_medida" name="unidad_medida" type="text" maxlength="20"
               value="<?= h($p['unidad_medida']) ?>" placeholder="Ej. bolsa" required>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="stock">Stock</label>
        <input class="form-control" id="stock" name="stock" type="number" min="0" max="2147483647" step="1"
               value="<?= h($p['stock']) ?>" placeholder="0" required>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="precio_unitario">Precio unitario</label>
        <input class="form-control" id="precio_unitario" name="precio_unitario" type="number" min="0" max="99999999.99" step="0.01"
               value="<?= h($p['precio_unitario']) ?>" placeholder="Ej. 4500" required>
    </div>
    <div class="col-12 d-flex gap-2 justify-content-end">
        <a class="btn btn-light" href="ver_productos.php">Cancelar</a>
        <button class="btn btn-primary" type="submit"><?= h($textoBoton) ?></button>
    </div>
</form>
