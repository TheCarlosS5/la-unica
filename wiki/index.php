<?php
/**
 * Archivo: index.php
 * Módulo: Wiki — enrutador.
 * Propósito: mostrar cualquier página de la wiki o los resultados de una búsqueda.
 * Funcionamiento: lee ?pagina=seccion/pagina o ?buscar=texto, valida la página contra el catálogo,
 * carga su HTML desde wiki/paginas y lo envuelve con la cabecera y el pie de la wiki.
 * Puntos clave:
 * - No abre conexión a MySQL: la wiki funciona aunque la base de datos esté apagada.
 * - Una página que no está en el catálogo responde 404; así la URL no puede pedir otros archivos.
 * - Sin parámetros se muestra la página de bienvenida (guia/introduccion).
 */

define('LA_UNICA_WIKI', true);
require __DIR__ . '/includes/funciones.php';
require __DIR__ . '/includes/paginas.php';

$catalogo = catalogoWiki();
$paginaInicial = 'guia/introduccion';

$terminoRecibido = $_GET['buscar'] ?? '';
$terminoBusqueda = is_string($terminoRecibido) ? trim($terminoRecibido) : '';
if (mb_strlen($terminoBusqueda, 'UTF-8') > 100) {
    $terminoBusqueda = mb_substr($terminoBusqueda, 0, 100, 'UTF-8');
}

$claveRecibida = $_GET['pagina'] ?? $paginaInicial;
$clavePagina = is_string($claveRecibida) ? $claveRecibida : '';

$resultadosBusqueda = [];
$pagina = null;
$contenido = '';
$indice = [];
$vecinas = ['anterior' => null, 'siguiente' => null];

if ($terminoBusqueda !== '') {
    // Modo búsqueda: la vista lista las páginas que contienen el término.
    $resultadosBusqueda = buscarEnWiki($catalogo, $terminoBusqueda);
    $tituloPagina = 'Buscar: ' . $terminoBusqueda;
} else {
    $pagina = buscarPaginaWiki($catalogo, $clavePagina);
    if ($pagina === null) {
        http_response_code(404);
        $tituloPagina = 'Página no encontrada';
    } else {
        $contenido = cargarPaginaWiki($pagina['clave']);
        $indice = indicePaginaWiki($contenido);
        $vecinas = paginasVecinasWiki($catalogo, $pagina['clave']);
        $tituloPagina = $pagina['titulo'];
    }
}

require __DIR__ . '/includes/cabecera.php';
?>
<article class="wiki-articulo">
<?php if ($terminoBusqueda !== ''): ?>
    <p class="wiki-migas">Búsqueda</p>
    <h1 class="wiki-titulo">Resultados para «<?= h($terminoBusqueda) ?>»</h1>
    <?php if ($resultadosBusqueda === []): ?>
        <p class="wiki-descripcion">No se encontró ninguna página con ese texto. Prueba con otra palabra, por ejemplo «factura», «stock» o «puerto».</p>
    <?php else: ?>
        <p class="wiki-descripcion"><?= count($resultadosBusqueda) ?> página(s) encontradas.</p>
        <ol class="wiki-resultados">
            <?php foreach ($resultadosBusqueda as $resultado): ?>
                <li>
                    <a href="index.php?pagina=<?= h($resultado['clave']) ?>"><?= h($resultado['titulo']) ?></a>
                    <span class="wiki-resultado-seccion"><?= h($resultado['seccion']) ?></span>
                    <p><?= resaltarTerminoWiki($resultado['fragmento'], $terminoBusqueda) ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php endif; ?>
<?php elseif ($pagina === null): ?>
    <p class="wiki-migas">Error 404</p>
    <h1 class="wiki-titulo">Página no encontrada</h1>
    <p class="wiki-descripcion">La página que buscas no existe en esta wiki. Usa el menú de la izquierda o vuelve a la <a href="index.php">bienvenida</a>.</p>
<?php else: ?>
    <p class="wiki-migas"><?= h($pagina['seccion']) ?></p>
    <h1 class="wiki-titulo"><?= h($pagina['titulo']) ?></h1>
    <p class="wiki-descripcion"><?= h($pagina['descripcion']) ?></p>
    <div class="wiki-contenido">
        <?= $contenido ?>
    </div>
    <nav class="wiki-paginacion" aria-label="Páginas vecinas">
        <?php if ($vecinas['anterior'] !== null): ?>
            <a class="wiki-vecina anterior" href="index.php?pagina=<?= h($vecinas['anterior']['clave']) ?>">
                <span>Anterior</span><?= h($vecinas['anterior']['menu'] ?? $vecinas['anterior']['titulo']) ?>
            </a>
        <?php endif; ?>
        <?php if ($vecinas['siguiente'] !== null): ?>
            <a class="wiki-vecina siguiente" href="index.php?pagina=<?= h($vecinas['siguiente']['clave']) ?>">
                <span>Siguiente</span><?= h($vecinas['siguiente']['menu'] ?? $vecinas['siguiente']['titulo']) ?>
            </a>
        <?php endif; ?>
    </nav>
<?php endif; ?>
</article>
<?php require __DIR__ . '/includes/pie.php'; ?>
