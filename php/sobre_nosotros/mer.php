<?php
/** Muestra el modelo entidad-relación dentro de la interfaz del aplicativo. */
define('SIN_BASE_DE_DATOS', true);
require __DIR__ . '/../config/conexion.php';

$titulo = 'Modelo entidad-relación (MER)';
$seccionActiva = 'sobre_nosotros';
$merUrl = $baseUrl . '/assets/img/mer.jpg';
require __DIR__ . '/../includes/menu.php';
?>

<div class="encabezado-vista">
    <div class="encabezado-info">
        <nav class="miga-pan" aria-label="Ruta de navegación">
            <a href="<?= h($baseUrl) ?>/main.php">Inicio</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <a href="<?= h($baseUrl) ?>/php/sobre_nosotros/index.php">Sobre nosotros</a>
            <span class="miga-separador" aria-hidden="true">/</span>
            <span aria-current="page">MER</span>
        </nav>
        <h1 class="encabezado-titulo">Modelo entidad-relación (MER)</h1>
        <p class="encabezado-descripcion">Consulta la estructura de la base de datos de La Única. Abre la imagen a tamaño completo para ampliar sus detalles.</p>
    </div>
    <div class="encabezado-acciones">
        <a class="boton secundario" href="<?= h($baseUrl) ?>/php/sobre_nosotros/index.php">Volver a Sobre nosotros</a>
        <a class="boton" href="<?= h($merUrl) ?>">Abrir a tamaño completo</a>
    </div>
</div>

<section class="panel-reporte manual-panel" aria-label="Diagrama del modelo entidad-relación">
    <figure class="mer-figura">
        <img class="mer-imagen" src="<?= h($merUrl) ?>" width="1112" height="752" alt="Modelo entidad-relación de La Única: clientes se relaciona con facturas de uno a muchos; facturas y productos se relacionan con detalle_facturas de uno a muchos. Incluye la tabla independiente log_acciones." aria-describedby="mer-descripcion">
        <figcaption id="mer-descripcion" class="manual-panel-descripcion">Modelo entidad-relación de La Única: clientes, facturas, detalle_facturas, productos y log_acciones.</figcaption>
    </figure>
</section>

<?php require __DIR__ . '/../includes/pie.php'; ?>
