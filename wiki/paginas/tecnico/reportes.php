<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="fuentes">Fuentes de datos</h2>
<p>Ventas y Unidades consultan procedimientos por fechas. Existencias usa el procedimiento de stock. Clientes y cantidad de facturas y Detalle usan consultas sobre las tablas actuales. datos_reportes.php contiene las definiciones auxiliares y reporte_individual.php presenta las consultas reutilizables. Algunas rutas antiguas redirigen a reportes vigentes por compatibilidad y no se muestran como opciones adicionales.</p>
<h2 id="calculos">Cálculos y presentación</h2>
<p>Los importes de ventas usan el precio histórico del detalle. Las canceladas se conservan para consulta, pero se excluyen de unidades vendidas y del total vendido. acciones_impresion.php prepara filtros, título y estilos para imprimir sin los controles de navegación.</p>
<p>La salida a PDF depende del diálogo del navegador. Los documentos técnicos registran la validación visual realizada y sus límites. La orientación horizontal del detalle evita comprimir sus ocho columnas.</p>
