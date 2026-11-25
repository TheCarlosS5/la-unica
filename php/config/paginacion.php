<?php
/** Paginación en servidor; los filtros siempre se conservan en los enlaces. */
function consultarPagina(mysqli $conexion, string $sql, string $tipos = '', array $parametros = []): array {
    $conteo = $conexion->prepare('SELECT COUNT(*) AS total FROM (' . $sql . ') AS listado');
    if ($tipos !== '') { $conteo->bind_param($tipos, ...$parametros); }
    $conteo->execute();
    $total = (int)$conteo->get_result()->fetch_assoc()['total'];
    $conteo->close();
    $paginas = max(1, (int)ceil($total / 25));
    $solicitada = validarEntero(leerTexto($_GET, 'pagina'), 1) ?? 1;
    $pagina = min($solicitada, $paginas);
    $consulta = $conexion->prepare($sql . ' LIMIT 25 OFFSET ' . (($pagina - 1) * 25));
    if ($tipos !== '') { $consulta->bind_param($tipos, ...$parametros); }
    $consulta->execute();
    $filas = $consulta->get_result()->fetch_all(MYSQLI_ASSOC);
    $consulta->close();
    return ['filas' => $filas, 'total' => $total, 'pagina' => $pagina, 'paginas' => $paginas];
}

function mostrarPaginacion(?array $datos): void {
    if (!$datos) { return; }
    $filtros = [];
    foreach (['buscar', 'fecha'] as $campo) {
        if (leerTexto($_GET, $campo) !== '') { $filtros[$campo] = leerTexto($_GET, $campo); }
    }
    echo '<nav class="paginacion" aria-label="Páginas del listado"><span>' . (int)$datos['total']
        . ' registros · Página ' . (int)$datos['pagina'] . ' de ' . (int)$datos['paginas'] . '</span>';
    foreach ([-1 => 'Anterior', 1 => 'Siguiente'] as $salto => $etiqueta) {
        $destino = $datos['pagina'] + $salto;
        if ($destino >= 1 && $destino <= $datos['paginas']) {
            echo '<a class="boton secundario" href="?' . h(http_build_query($filtros + ['pagina' => $destino])) . '">' . $etiqueta . '</a>';
        }
    }
    echo '</nav>';
}
