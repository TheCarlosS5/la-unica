<?php
// Campos reutilizables del formulario de producto.
// La página que lo incluya debe preparar $p y $textoBoton.
?>
<form method="post" class="formulario-grid">
    <div class="campo col-completa">
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" type="text" maxlength="100"
               value="<?= h($p['nombre']) ?>" placeholder="Ej. Arroz blanco 1 kg" required autofocus>
    </div>
    <div class="campo">
        <label for="unidad_medida">Unidad</label>
        <input id="unidad_medida" name="unidad_medida" type="text" maxlength="20"
               value="<?= h($p['unidad_medida']) ?>" placeholder="Ej. bolsa" required>
    </div>
    <div class="campo">
        <label for="stock">Stock</label>
        <input id="stock" name="stock" type="number" min="0" max="2147483647" step="1"
               value="<?= h($p['stock']) ?>" placeholder="0" required>
    </div>
    <div class="campo col-completa">
        <label for="precio_unitario">Precio unitario</label>
        <input id="precio_unitario" name="precio_unitario" type="number" min="0" max="99999999.99" step="0.01"
               value="<?= h($p['precio_unitario']) ?>" placeholder="Ej. 4500" required>
    </div>
    <div class="fila-botones col-completa">
        <a class="boton secundario" href="ver_productos.php">Cancelar</a>
        <button class="boton" type="submit"><?= h($textoBoton) ?></button>
    </div>
</form>
