<?php
// ===================================================================
// funciones_vista.php
// Funciones que solo sirven para MOSTRAR información en pantalla.
// No consultan ni modifican la base de datos.
// ===================================================================

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
    $clase = $tipo === 'error'
        ? 'mensaje error alerta alerta-error aparecer'
        : 'mensaje alerta aparecer';
    $rol = $tipo === 'error' ? 'alert' : 'status';
    return '<div class="' . $clase . '" role="' . $rol . '">' . h($texto) . '</div>';
}

// Formato de dinero colombiano: 2500 -> "$ 2.500"
function pesos($valor) {
    $decimales = floor($valor) == $valor ? 0 : 2;
    return '$ ' . number_format((float)$valor, $decimales, ',', '.');
}

// Fecha y hora como la pide el SRS: "2026-10-26 14:30:00" -> "26/10/2026 14:30:00"
function fecha_hora($valor) {
    $fecha = DateTime::createFromFormat('Y-m-d H:i:s', (string)$valor);
    return $fecha ? $fecha->format('d/m/Y H:i:s') : (string)$valor;
}

// Insignia de color según el estado de la factura (Pagado, Cancelado o Pendiente).
function insignia_estado($estado) {
    $clases = ['Pagado' => 'pagado', 'Cancelado' => 'cancelado', 'Pendiente' => 'pendiente'];
    return '<span class="estado-factura ' . ($clases[$estado] ?? '') . '">' . h($estado) . '</span>';
}

// Dinero con siempre dos decimales: 10500 -> "$ 10.500,00"
function pesos2($valor) {
    return '$ ' . number_format((float)$valor, 2, ',', '.');
}
