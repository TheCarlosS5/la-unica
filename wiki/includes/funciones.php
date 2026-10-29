<?php
/**
 * Archivo: funciones.php
 * Módulo: Wiki — utilidades compartidas.
 * Propósito: reunir las funciones que usa la wiki para cargar páginas, armar el índice y buscar.
 * Funcionamiento: no abre conexión a MySQL; trabaja solo con los archivos de wiki/paginas y con el
 * catálogo de includes/paginas.php.
 * Puntos clave:
 * - Las páginas se cargan solo si su clave existe en el catálogo (lista blanca).
 * - El índice «En esta página» se arma leyendo los <h2> y <h3> que tienen id.
 * - La búsqueda compara sin distinguir mayúsculas ni tildes.
 */

if (!function_exists('h')) {
    /**
     * Codifica un valor como texto HTML seguro en UTF-8.
     * @param mixed $valor Valor que se va a imprimir.
     * @return string Texto escapado.
     */
    function h($valor)
    {
        return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

/**
 * Busca una página en el catálogo.
 * @param array $catalogo Resultado de catalogoWiki().
 * @param string $clave Clave de la página, por ejemplo «guia/clientes».
 * @return array|null Datos de la página con su sección, o null si no existe.
 */
function buscarPaginaWiki(array $catalogo, $clave)
{
    foreach ($catalogo as $seccion => $paginas) {
        if (isset($paginas[$clave])) {
            return $paginas[$clave] + ['clave' => $clave, 'seccion' => $seccion];
        }
    }
    return null;
}

/**
 * Convierte el catálogo en una lista ordenada de claves, para los botones Anterior / Siguiente.
 * @param array $catalogo Resultado de catalogoWiki().
 * @return array Lista de páginas con clave, título y sección.
 */
function listaPaginasWiki(array $catalogo)
{
    $lista = [];
    foreach ($catalogo as $seccion => $paginas) {
        foreach ($paginas as $clave => $pagina) {
            $lista[] = $pagina + ['clave' => $clave, 'seccion' => $seccion];
        }
    }
    return $lista;
}

/**
 * Devuelve la página anterior y la siguiente a la indicada.
 * @param array $catalogo Resultado de catalogoWiki().
 * @param string $clave Clave de la página actual.
 * @return array Arreglo con las claves 'anterior' y 'siguiente' (cada una puede ser null).
 */
function paginasVecinasWiki(array $catalogo, $clave)
{
    $lista = listaPaginasWiki($catalogo);
    $posicion = array_search($clave, array_column($lista, 'clave'), true);
    return [
        'anterior' => ($posicion !== false && $posicion > 0) ? $lista[$posicion - 1] : null,
        'siguiente' => ($posicion !== false && $posicion < count($lista) - 1) ? $lista[$posicion + 1] : null,
    ];
}

/**
 * Lee el HTML de una página. Solo se llama con claves que ya se validaron contra el catálogo.
 * @param string $clave Clave de la página.
 * @return string HTML de la página o cadena vacía si el archivo no existe.
 */
function cargarPaginaWiki($clave)
{
    $ruta = __DIR__ . '/../paginas/' . $clave . '.php';
    if (!is_file($ruta)) {
        return '';
    }
    ob_start();
    include $ruta;
    return (string)ob_get_clean();
}

/**
 * Arma el índice «En esta página» con los títulos de nivel 2 y 3.
 * @param string $html Contenido de la página.
 * @return array Lista de ['nivel' => 2|3, 'id' => ..., 'texto' => ...].
 */
function indicePaginaWiki($html)
{
    $indice = [];
    if (preg_match_all('#<h([23]) id="([^"]+)">(.*?)</h\1>#s', $html, $coincidencias, PREG_SET_ORDER)) {
        foreach ($coincidencias as $titulo) {
            $indice[] = [
                'nivel' => (int)$titulo[1],
                'id' => html_entity_decode($titulo[2], ENT_QUOTES, 'UTF-8'),
                'texto' => trim(html_entity_decode(strip_tags($titulo[3]), ENT_QUOTES, 'UTF-8')),
            ];
        }
    }
    return $indice;
}

/**
 * Quita tildes y pasa a minúsculas para comparar textos en la búsqueda.
 * @param string $texto Texto original.
 * @return string Texto normalizado.
 */
function normalizarTextoWiki($texto)
{
    $texto = mb_strtolower((string)$texto, 'UTF-8');
    return strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);
}

/**
 * Busca un término en el título, la descripción y el texto de todas las páginas.
 * @param array $catalogo Resultado de catalogoWiki().
 * @param string $termino Texto buscado.
 * @return array Resultados ordenados por relevancia, cada uno con un fragmento del texto.
 */
function buscarEnWiki(array $catalogo, $termino)
{
    $buscado = normalizarTextoWiki(trim($termino));
    if ($buscado === '') {
        return [];
    }

    $resultados = [];
    foreach (listaPaginasWiki($catalogo) as $pagina) {
        $texto = html_entity_decode(strip_tags(cargarPaginaWiki($pagina['clave'])), ENT_QUOTES, 'UTF-8');
        $texto = trim(preg_replace('/\s+/u', ' ', $texto));
        $normalizado = normalizarTextoWiki($texto);

        $apariciones = substr_count($normalizado, $buscado);
        $enTitulo = strpos(normalizarTextoWiki($pagina['titulo'] . ' ' . $pagina['descripcion']), $buscado) !== false;
        if ($apariciones === 0 && !$enTitulo) {
            continue;
        }

        // Fragmento de unos 160 caracteres alrededor de la primera aparición.
        $fragmento = $pagina['descripcion'];
        $posicion = mb_strpos($normalizado, $buscado, 0, 'UTF-8');
        if ($posicion !== false) {
            $inicio = max(0, $posicion - 70);
            $fragmento = ($inicio > 0 ? '…' : '') . mb_substr($texto, $inicio, 160, 'UTF-8') . '…';
        }

        $resultados[] = $pagina + [
            'fragmento' => $fragmento,
            'puntaje' => $apariciones + ($enTitulo ? 10 : 0),
        ];
    }

    usort($resultados, static fn(array $a, array $b): int => $b['puntaje'] <=> $a['puntaje']);
    return $resultados;
}

/**
 * Escapa un fragmento y resalta el término buscado con <mark>.
 * @param string $fragmento Texto plano.
 * @param string $termino Texto buscado.
 * @return string HTML seguro con el término resaltado.
 */
function resaltarTerminoWiki($fragmento, $termino)
{
    $buscado = normalizarTextoWiki(trim($termino));
    $normalizado = normalizarTextoWiki($fragmento);
    $largo = mb_strlen($buscado, 'UTF-8');
    if ($largo === 0) {
        return h($fragmento);
    }

    $salida = '';
    $desde = 0;
    while (($posicion = mb_strpos($normalizado, $buscado, $desde, 'UTF-8')) !== false) {
        $salida .= h(mb_substr($fragmento, $desde, $posicion - $desde, 'UTF-8'));
        $salida .= '<mark>' . h(mb_substr($fragmento, $posicion, $largo, 'UTF-8')) . '</mark>';
        $desde = $posicion + $largo;
    }
    return $salida . h(mb_substr($fragmento, $desde, null, 'UTF-8'));
}
