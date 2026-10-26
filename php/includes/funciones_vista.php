<?php
// ===================================================================
// funciones_vista.php
// Funciones que solo sirven para MOSTRAR información en pantalla.
// No consultan ni modifican la base de datos.
// ===================================================================

// Muestra un ícono de pixeles del pack Pixelarticons (assets/vendor/pixelarticons).
// Ejemplo: icono('trash') -> <i class="pxi pxi-trash"></i>
// Si necesitas otro ícono, agrégalo en pixelarticons.css (lista: pixelarticons.com)
function icono($nombre) {
    return '<i class="pxi pxi-' . h($nombre) . '" aria-hidden="true"></i>';
}

// Elige una foto según palabras del nombre del producto.
// Ejemplo: "Leche entera 1L" contiene "leche" -> assets/img/productos/leche.jpg
// Si ninguna palabra coincide se usa la foto "generico".
function foto_producto($nombreProducto) {
    global $baseUrl;
    $fotos = [
        'arroz'    => ['arroz'],
        'leche'    => ['leche', 'yogur', 'queso', 'kumis', 'mantequilla'],
        'huevos'   => ['huevo'],
        'aceite'   => ['aceite'],
        'gaseosa'  => ['gaseosa', 'refresco', 'jugo', 'cola', 'bebida'],
        'pan'      => ['pan', 'galleta', 'tostada'],
        'frutas'   => ['banano', 'manzana', 'naranja', 'fruta', 'mango', 'pera', 'uva'],
        'verduras' => ['tomate', 'cebolla', 'papa', 'zanahoria', 'verdura', 'lechuga'],
        'aseo'     => ['jabon', 'jabón', 'detergente', 'papel', 'shampoo', 'cloro', 'aseo'],
        'granos'   => ['frijol', 'fríjol', 'lenteja', 'garbanzo', 'arveja', 'grano'],
        'cafe'     => ['cafe', 'café', 'chocolate', 'aromática'],
    ];

    $nombre = mb_strtolower($nombreProducto, 'UTF-8');
    $elegida = 'generico';
    foreach ($fotos as $foto => $palabras) {
        foreach ($palabras as $palabra) {
            if (strpos($nombre, $palabra) !== false) {
                $elegida = $foto;
                break 2; // salir de los dos foreach
            }
        }
    }
    return $baseUrl . '/assets/img/productos/' . $elegida . '.jpg';
}

// Iniciales para el avatar del cliente. "Ana María Ruiz" -> "AM"
function iniciales($nombre) {
    $partes = preg_split('/\s+/', trim($nombre));
    $letras = '';
    foreach (array_slice($partes, 0, 2) as $parte) {
        $letras .= mb_strtoupper(mb_substr($parte, 0, 1, 'UTF-8'), 'UTF-8');
    }
    return $letras;
}

// Caja de mensaje. $tipo puede ser 'exito' (verde) o 'error' (rojo).
// Si el texto está vacío no muestra nada.
function alerta($texto, $tipo = 'exito') {
    if ($texto === '') {
        return '';
    }
    $clase = $tipo === 'error' ? 'alert-danger' : 'alert-success';
    $rol = $tipo === 'error' ? 'alert' : 'status';
    return '<div class="alert ' . $clase . ' aparecer" role="' . $rol . '">' . h($texto) . '</div>';
}

// Formato de dinero colombiano: 2500 -> "$ 2.500"
function pesos($valor) {
    $decimales = floor($valor) == $valor ? 0 : 2;
    return '$ ' . number_format((float)$valor, $decimales, ',', '.');
}

// Cantidad sin ceros sobrantes: "12.500" -> "12,5"  y  "3.000" -> "3"
function cantidad($valor) {
    $texto = number_format((float)$valor, 3, ',', '.');
    return rtrim(rtrim($texto, '0'), ',');
}
