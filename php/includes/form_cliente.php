<?php
// ===================================================================
// form_cliente.php
// Campos del formulario de cliente. Lo usan crear_cliente.php y
// editar_cliente.php, así el formulario se escribe una sola vez.
// Antes de incluirlo, la página define:
//   $c           -> arreglo con los valores actuales de los campos
//   $esEdicion   -> true al editar (el id y la fecha no se pueden cambiar)
//   $textoBoton  -> texto del botón de guardar
// ===================================================================
?>
<form method="post" class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="id_cliente">Id del cliente</label>
        <input class="form-control" id="id_cliente" name="id_cliente" type="number"
               min="1" max="2147483647" step="1" value="<?= h($c['id_cliente']) ?>" placeholder="Ej. 1023456789"
               <?= $esEdicion ? 'readonly' : 'required autofocus' ?>>
        <?php if ($esEdicion): ?>
            <div class="form-text">El id no se puede cambiar.</div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="fecha_registro">
            Fecha de registro <?= $esEdicion ? '' : '<span class="text-secondary">(opcional)</span>' ?>
        </label>
        <input class="form-control" id="fecha_registro" name="fecha_registro" type="date"
               value="<?= h($c['fecha_registro']) ?>" <?= $esEdicion ? 'readonly' : '' ?>>
    </div>
    <div class="col-12">
        <label class="form-label" for="nombre">Nombre completo</label>
        <input class="form-control" id="nombre" name="nombre" type="text" maxlength="100"
               value="<?= h($c['nombre']) ?>" placeholder="Ej. Ana María Ruiz" required <?= $esEdicion ? 'autofocus' : '' ?>>
    </div>
    <div class="col-12 d-flex gap-2 justify-content-end">
        <a class="btn btn-light" href="ver_clientes.php">Cancelar</a>
        <button class="btn btn-primary" type="submit"><?= h($textoBoton) ?></button>
    </div>
</form>
