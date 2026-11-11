<?php
/** Formato, escape HTML y validación de entradas sin abrir conexiones. */
/**
 * Codifica un valor como texto HTML seguro en UTF-8.
 * @param mixed $valor Valor que se va a imprimir.
 * @return string Valor escapado.
 */
function h($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
/**
 * Lee un campo de texto de $_POST o $_GET ya recortado.
 * Si el campo no existe o no es texto (por ejemplo, una petición manipulada que envía un arreglo),
 * devuelve '' y la validación normal lo trata como un campo vacío.
 * @param array $origen $_POST o $_GET.
 * @param string $campo Nombre del campo.
 * @return string Texto sin espacios al inicio ni al final.
 */
function leerTexto(array $origen, $campo) {
    return isset($origen[$campo]) && is_string($origen[$campo]) ? trim($origen[$campo]) : '';
}
/**
 * Valida un número entero entre $minimo y el máximo de INT de MySQL.
 * @param mixed $valor Entrada que se validará (por ejemplo, el texto de un formulario).
 * @param int $minimo Valor mínimo permitido (0 para stock, 1 para IDs y cantidades).
 * @return int|null Entero válido o null.
 */
function validarEntero($valor, $minimo = 0) {
    $numero = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => $minimo, 'max_range' => ENTERO_MAXIMO]]);
    return $numero === false ? null : $numero;
}
/**
 * Valida un ID o documento: entero positivo dentro del rango INT de MySQL.
 * @param mixed $valor Entrada que se validará.
 * @return int|null ID válido o null.
 */
function validarId($valor) {
    return validarEntero($valor, 1);
}
/**
 * Acepta fechas reales con formato YYYY-MM-DD.
 * @param mixed $valor Fecha recibida.
 * @return bool Indica si la fecha es válida.
 */
function validarFecha($valor) {
    $fecha = DateTime::createFromFormat('!Y-m-d', (string)$valor);
    return $fecha !== false && $fecha->format('Y-m-d') === (string)$valor;
}
/**
 * Cuenta los caracteres de un texto UTF-8.
 * @param mixed $valor Texto que se va a medir.
 * @return int Cantidad de caracteres.
 */
function longitudTexto($valor) {
    return mb_strlen((string)$valor, 'UTF-8');
}
/**
 * Presenta un importe con separadores colombianos y sufijo COP. Ej.: 1234.5 → $1.234,50 COP
 * @param mixed $valor Importe decimal.
 * @return string Importe formateado.
 */
function formatearMonedaCop($valor) {
    return '$' . number_format((float)$valor, 2, ',', '.') . ' COP';
}
/**
 * Regla de negocio: una factura solo se puede cancelar el mismo día en que se vendió.
 * Así ningún día ya cerrado cambia después por una cancelación.
 * @param string $fechaFactura Fecha y hora de la venta (YYYY-MM-DD HH:MM:SS).
 * @return bool true si la venta es de hoy.
 */
function esFacturaDeHoy($fechaFactura) {
    return substr((string)$fechaFactura, 0, 10) === date('Y-m-d');
}
