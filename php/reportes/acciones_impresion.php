<?php
/*
 * ============================================================================
 * MODULO  REPORTES E IMPRESION
 * ============================================================================
 * Transforma los datos de ventas, clientes e inventario en consultas para revisar la
 * tienda. Las consultas conservan las reglas de cada reporte; la presentación ofrece
 * impresión y guardado PDF mediante el navegador.
 *
 * RESPONSABILIDAD DE ESTE ARCHIVO
 * Añade los controles de impresión y las reglas de página: encabezado, período,
 * tablas, numeración y orientación del papel. El PDF se guarda desde el diálogo
 * nativo.
 * ============================================================================
 */

/** Controles de impresión y PDF nativo para la consulta actualmente visible. */
if (!isset($seccionActiva) || $seccionActiva !== 'reportes') {
    http_response_code(404);
    exit;
}
$impresionDisponible = empty($errorFiltro) && empty($errorLectura);
$papelHorizontal = basename($_SERVER['SCRIPT_NAME'] ?? '') === 'detalle_facturas.php';
?>
<div class="acciones-impresion-reporte solo-pantalla" aria-label="Imprimir o guardar el reporte">
    <button class="boton" type="button" data-imprimir-reporte <?= $impresionDisponible ? '' : 'disabled' ?>>Imprimir reporte</button>
    <button class="boton secundario" type="button" data-imprimir-reporte <?= $impresionDisponible ? '' : 'disabled' ?>>Guardar como PDF</button>
    <p>Para obtener el PDF, pulsa «Guardar como PDF» y elige ese destino en el cuadro de impresión del navegador. Se incluyen los datos de la consulta actual. Usa papel A4, escala 100 % y desactiva los encabezados y pies del navegador para evitar que aparezcan la URL y la fecha duplicadas.</p>
</div>
<header class="datos-impresion-reporte">
    <p class="marca-documento">LA ÚNICA <span>Reporte de consulta</span></p>
    <h1><?= h($titulo) ?></h1>
    <p class="metadatos-documento">Generado el <?= h(date('d/m/Y')) ?> a las <?= h(date('H:i')) ?> · Hora de Colombia</p>
    <?php if (isset($desde, $hasta) && empty($errorFiltro)): ?>
        <p><strong>Período:</strong> <?= h(date('d/m/Y', strtotime($desde))) ?> al <?= h(date('d/m/Y', strtotime($hasta))) ?>, ambos incluidos.
        <?php if (isset($ver, $vistas[$ver])): ?> <strong>Agrupación:</strong> <?= h($vistas[$ver]) ?>.<?php endif; ?></p>
    <?php elseif (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'ver_reportes.php'): ?>
        <p><strong>Resumen del día:</strong> <?= h(date('d/m/Y')) ?>.</p>
    <?php elseif (basename($_SERVER['SCRIPT_NAME'] ?? '') === 'existencias_productos.php'): ?>
        <p>Inventario y precios vigentes al generar este documento.</p>
    <?php else: ?>
        <p>Historial completo disponible al generar este documento.</p>
    <?php endif; ?>
</header>
<style>
.acciones-impresion-reporte { margin-bottom: 1.25rem; }
.acciones-impresion-reporte p { margin-top: .5rem; }
.datos-impresion-reporte { display: none; }
@media print {
    @page {
        size: A4 <?= $papelHorizontal ? 'landscape' : 'portrait' ?>;
        margin: 15mm 13mm 18mm;
        @bottom-left { content: "La Única · Reporte de consulta"; font: 8pt Arial, sans-serif; color: #555; }
        @bottom-right { content: "Página " counter(page) " de " counter(pages); font: 8pt Arial, sans-serif; color: #555; }
    }
    html, body { background: white !important; color: #111 !important; font: 10pt/1.35 Arial, sans-serif !important; }
    body *, body *::before, body *::after { box-sizing: border-box; text-shadow: none !important; font-family: Arial, sans-serif !important; }
    .shell-layout, .layout-app, .area-principal { display: block !important; position: static !important; margin: 0 !important; padding: 0 !important; width: auto !important; min-height: 0 !important; background: white !important; }
    .barra-lateral, .topbar-principal, .pie-app, .drawer-backdrop, .salto-contenido,
    .acciones-impresion-reporte, .encabezado-vista, .miga-pan, .filtro-acciones, .filtros-facturas,
    .no-imprimir-reporte, .modulo-acciones, button, .boton, .solo-pantalla { display: none !important; }
    #contenido-principal { display: block !important; width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
    .datos-impresion-reporte { display: block; margin: 0 0 6mm; padding-bottom: 4mm; border-bottom: 1.5pt solid #222; break-inside: avoid; }
    .datos-impresion-reporte p { margin: 1.5mm 0; font: 9pt/1.35 Arial, sans-serif; }
    .datos-impresion-reporte .marca-documento { font-weight: bold; font-size: 13pt; letter-spacing: .8pt; }
    .marca-documento span { font-size: 9pt; font-weight: normal; letter-spacing: 0; margin-left: 4mm; }
    .datos-impresion-reporte h1 { margin: 3mm 0; font: bold 19pt/1.15 Arial, sans-serif; color: #111; }
    .metadatos-documento { color: #555; }
    .tabla-wrap, .tabla-contenedor { overflow: visible !important; max-height: none !important; width: 100% !important; margin: 4mm 0 !important; border: 0 !important; border-radius: 0 !important; }
    table.tabla { width: 100% !important; min-width: 0 !important; border-collapse: collapse; table-layout: auto; font: 9pt/1.3 Arial, sans-serif !important; }
    .tabla th, .tabla td { white-space: normal !important; overflow-wrap: anywhere; padding: 2.2mm 2mm !important; color: #111 !important; border: .5pt solid #bbb !important; height: auto !important; vertical-align: top; font-size: 9pt !important; }
    .tabla th { background: #e9e9e9 !important; font-weight: bold; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    .tabla tbody tr:nth-child(even) { background: #f5f5f5 !important; print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    .tabla td *, .tabla th * { font: inherit !important; background: transparent !important; padding: 0 !important; border: 0 !important; color: #111 !important; }
    .texto-derecha { text-align: right !important; font-variant-numeric: tabular-nums; }
    thead { display: table-header-group; }
    tr { break-inside: avoid; page-break-inside: avoid; }
    h2, h3 { break-after: avoid; }
    .panel-reporte { border: 0 !important; border-radius: 0 !important; padding: 0 !important; box-shadow: none !important; background: white !important; }
    .panel-reporte-cabecera { display: block !important; }
    .reporte-fuente, .panel-reporte-cabecera .reporte-titulo { display: none !important; }
    .reporte-titulo { font: bold 12pt Arial, sans-serif !important; }
    .nota-reporte, .reporte-descripcion, .facturas-conteo { font: 9pt/1.35 Arial, sans-serif !important; margin: 2mm 0; }
    .reportes-resumen-grid { display: flex !important; flex-wrap: wrap; gap: 3mm; margin: 0 0 5mm !important; break-inside: avoid; }
    .reportes-resumen-card { flex: 0 0 calc(50% - 1.5mm); min-width: 0 !important; padding: 3mm !important; margin: 0 !important; border: .5pt solid #bbb !important; border-radius: 0 !important; box-shadow: none !important; background: white !important; break-inside: avoid; }
    .reportes-resumen-card .modulo-titulo { font: bold 10pt Arial, sans-serif !important; margin: 0 0 2mm !important; }
    .reportes-resumen-valor { font: bold 16pt/1.15 Arial, sans-serif !important; color: #111 !important; margin: 0 0 1mm !important; }
    .reportes-resumen-detalle { font: 9pt/1.3 Arial, sans-serif !important; margin: 0 !important; }
    .sin-datos { padding: 5mm !important; border: .5pt solid #bbb; text-align: left; }
    a { color: #111 !important; text-decoration: none; }
}
</style>
<script>
document.querySelectorAll('[data-imprimir-reporte]').forEach(function(boton) {
    boton.addEventListener('click', function() { window.print(); });
});
</script>
