<?php
// ===================================================================
// form_producto.php
// Campos del formulario de producto. Lo usan crear_producto.php y
// editar_producto.php, así el formulario se escribe una sola vez.
// Antes de incluirlo, la página define:
//   $p              -> arreglo con los valores actuales de los campos
//   $bloquearClave  -> true si el código y la unidad no se pueden cambiar
//   $textoBoton     -> texto del botón de guardar
// ===================================================================
?>
<form method="post" class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="codigo">Código</label>
        <input class="form-control" id="codigo" name="codigo" type="text" maxlength="30"
               value="<?= h($p['codigo']) ?>" placeholder="Ej. ARR-001"
               <?= $bloquearClave ? 'readonly' : 'required autofocus' ?>>
    </div>
    <div class="col-md-8">
        <label class="form-label" for="nombre">Nombre</label>
        <input class="form-control" id="nombre" name="nombre" type="text" maxlength="120"
               value="<?= h($p['nombre']) ?>" placeholder="Ej. Arroz blanco 1 kg" required>
    </div>
    <div class="col-12">
        <label class="form-label" for="descripcion">Descripción <span class="text-secondary">(opcional)</span></label>
        <input class="form-control" id="descripcion" name="descripcion" type="text" maxlength="500"
               value="<?= h($p['descripcion']) ?>" placeholder="Ej. Bolsa de arroz de grano largo">
    </div>
    <div class="col-md-4">
        <label class="form-label" for="unidad_medida">Unidad de medida</label>
        <select class="form-select" id="unidad_medida" name="unidad_medida" <?= $bloquearClave ? 'disabled' : 'required' ?>>
            <?php foreach (UNIDADES as $unidad): ?>
                <option value="<?= h($unidad) ?>" <?= $p['unidad_medida'] === $unidad ? 'selected' : '' ?>><?= h($unidad) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="stock">Stock</label>
        <input class="form-control" id="stock" name="stock" type="number" min="0" step="any"
               value="<?= h($p['stock']) ?>" placeholder="0" required>
        <div class="form-text">UND: entero. Otras unidades: hasta 3 decimales.</div>
    </div>
    <div class="col-md-4">
        <label class="form-label" for="precio">Precio por unidad</label>
        <input class="form-control" id="precio" name="precio" type="number" min="0" step="0.01"
               value="<?= h($p['precio']) ?>" placeholder="Ej. 4500" required>
    </div>
    <?php if ($bloquearClave): ?>
        <div class="col-12 form-text mt-0">El código y la unidad no cambian porque el producto ya está en facturas.</div>
    <?php endif; ?>
    <div class="col-12 d-flex gap-2 justify-content-end">
        <a class="btn btn-light" href="ver_productos.php">Cancelar</a>
        <button class="btn btn-primary" type="submit"><?= h($textoBoton) ?></button>
    </div>
</form>
