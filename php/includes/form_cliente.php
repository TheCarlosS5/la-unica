<?php
// ===================================================================
// form_cliente.php
// Campos del formulario de cliente. Lo usan crear_cliente.php y
// editar_cliente.php, así el formulario se escribe una sola vez.
// Antes de incluirlo, la página define:
//   $c               -> arreglo con los valores actuales de los campos
//   $bloquearDoc     -> true si el documento no se puede cambiar
//   $textoBoton      -> texto del botón de guardar
// ===================================================================
?>
<form method="post" class="row g-3">
    <div class="col-md-4">
        <label class="form-label" for="tipo_documento">Tipo de documento</label>
        <select class="form-select" id="tipo_documento" name="tipo_documento" <?= $bloquearDoc ? 'disabled' : 'required' ?>>
            <?php foreach (TIPOS_DOCUMENTO as $tipo): ?>
                <option value="<?= h($tipo) ?>" <?= $c['tipo_documento'] === $tipo ? 'selected' : '' ?>><?= h($tipo) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-8">
        <label class="form-label" for="numero_documento">Número de documento</label>
        <input class="form-control" id="numero_documento" name="numero_documento" type="text" maxlength="20"
               value="<?= h($c['numero_documento']) ?>" placeholder="Ej. 1023456789"
               <?= $bloquearDoc ? 'readonly' : 'required autofocus' ?>>
        <?php if ($bloquearDoc): ?>
            <div class="form-text">El documento no cambia porque el cliente ya tiene facturas.</div>
        <?php endif; ?>
    </div>
    <div class="col-12">
        <label class="form-label" for="nombre">Nombre completo</label>
        <input class="form-control" id="nombre" name="nombre" type="text" maxlength="120"
               value="<?= h($c['nombre']) ?>" placeholder="Ej. Ana María Ruiz" required>
    </div>
    <div class="col-md-6">
        <label class="form-label" for="telefono">Teléfono <span class="text-secondary">(opcional)</span></label>
        <input class="form-control" id="telefono" name="telefono" type="tel" maxlength="15"
               value="<?= h($c['telefono']) ?>" placeholder="Ej. 3101234567">
    </div>
    <div class="col-md-6">
        <label class="form-label" for="correo">Correo <span class="text-secondary">(opcional)</span></label>
        <input class="form-control" id="correo" name="correo" type="email" maxlength="120"
               value="<?= h($c['correo']) ?>" placeholder="Ej. ana@correo.com">
    </div>
    <div class="col-12">
        <label class="form-label" for="direccion">Dirección <span class="text-secondary">(opcional)</span></label>
        <input class="form-control" id="direccion" name="direccion" type="text" maxlength="150"
               value="<?= h($c['direccion']) ?>" placeholder="Ej. Calle 5 # 10-20, Neiva">
    </div>
    <div class="col-12 d-flex gap-2 justify-content-end">
        <a class="btn btn-light" href="ver_clientes.php">Cancelar</a>
        <button class="btn btn-primary" type="submit"><?= h($textoBoton) ?></button>
    </div>
</form>
