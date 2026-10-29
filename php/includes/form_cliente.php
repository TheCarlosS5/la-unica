<?php
// Campos reutilizables del formulario de cliente.
// La página que lo incluya debe preparar $c, $esEdicion y $textoBoton.
?>
<form method="post" class="formulario-grid">
    <div class="campo">
        <label for="id_cliente">ID del cliente</label>
        <input id="id_cliente" name="id_cliente" type="number" min="1" max="2147483647" step="1"
               value="<?= h($c['id_cliente']) ?>" placeholder="Ej. 1023456789"
               <?= $esEdicion ? 'readonly' : 'required autofocus' ?>>
        <?php if ($esEdicion): ?>
            <span class="ayuda texto-ayuda">El identificador no se puede cambiar.</span>
        <?php endif; ?>
    </div>
    <div class="campo">
        <label for="fecha_registro">Fecha de registro <span class="ayuda">(opcional)</span></label>
        <input id="fecha_registro" name="fecha_registro" type="date"
               value="<?= h($c['fecha_registro']) ?>" <?= $esEdicion ? 'readonly' : '' ?>>
    </div>
    <div class="campo col-completa">
        <label for="nombre">Nombre completo</label>
        <input id="nombre" name="nombre" type="text" maxlength="100"
               value="<?= h($c['nombre']) ?>" placeholder="Ej. Ana María Ruiz" required <?= $esEdicion ? 'autofocus' : '' ?>>
    </div>
    <div class="fila-botones col-completa">
        <a class="boton secundario" href="ver_clientes.php">Cancelar</a>
        <button class="boton" type="submit"><?= h($textoBoton) ?></button>
    </div>
</form>
