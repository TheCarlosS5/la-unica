<?php
/** Diagnóstico en archivo y respuestas seguras, disponibles aunque SQL falle. */
/**
 * Guarda el detalle técnico de un error en logs/errores.log, sin mostrarlo al operador.
 * Se usa un archivo (y no la base de datos) para poder registrar también los fallos de MySQL.
 * @param string $operacion Nombre de la operación que falló, por ejemplo 'crear_factura'.
 * @param Throwable $error Excepción capturada.
 * @return void
 */
function registrarErrorServidor($operacion, Throwable $error) {
    $registro = [
        'fecha' => date('Y-m-d H:i:s'),
        'operacion' => (string)$operacion,
        'tipo' => get_class($error),
        'codigo' => $error->getCode(),
        'mensaje' => $error->getMessage(),
        'ubicacion' => basename($error->getFile()) . ':' . $error->getLine(),
        'pagina' => (string)($_SERVER['SCRIPT_NAME'] ?? ''),
    ];
    $linea = json_encode($registro, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) . PHP_EOL;

    $carpeta = dirname(RUTA_LOG_ERRORES);
    if (!is_dir($carpeta)) {
        @mkdir($carpeta, 0775, true);
    }
    if (@file_put_contents(RUTA_LOG_ERRORES, $linea, FILE_APPEND | LOCK_EX) === false) {
        // Si no se puede escribir el archivo, el error queda al menos en el log de PHP.
        error_log('[' . $operacion . '] ' . $error->getMessage());
    }
}
/**
 * Muestra una página de error amable y termina la ejecución.
 * @param string $titulo Encabezado del mensaje.
 * @param string $mensaje Explicación para el operador.

 * @return void
 */
function mostrarPaginaError($titulo, $mensaje) {
    $caja = '<div class="caja-error"><h1>' . h($titulo) . '</h1><p>' . h($mensaje) . '</p></div>';
    if (headers_sent()) {
        // La página ya empezó a dibujarse: solo se agrega el mensaje.
        echo $caja;
        exit;
    }
    http_response_code(500);
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>' . h($titulo) . ' · La Única</title>'
        . '<style>body{font-family:sans-serif;background:#fbf7ec;color:#222;padding:2rem}'
        . '.caja-error{max-width:640px;margin:auto;background:#fff;border:2px solid #c0392b;border-radius:8px;padding:1.5rem}'
        . '.caja-error h1{color:#c0392b;margin-top:0}code{background:#f3f3f3;padding:2px 6px;border-radius:4px}'
        . 'dl{display:grid;grid-template-columns:max-content 1fr;gap:.3rem 1rem}dt{font-weight:bold}</style></head><body>'
        . $caja . '</body></html>';
    exit;
}
// La página pública nunca debe mostrar excepciones, credenciales ni rutas internas.
ini_set('display_errors', '0');
ini_set('log_errors', '1');
set_exception_handler(function (Throwable $error) {
    registrarErrorServidor('error_no_controlado', $error);
    mostrarPaginaError('Ocurrió un error inesperado', 'La operación no se pudo completar. Consulta al responsable del proyecto.');
});
set_error_handler(function ($nivel, $mensaje, $archivo, $linea) {
    if (!(error_reporting() & $nivel)) { return false; }
    registrarErrorServidor('aviso_php', new ErrorException($mensaje, 0, $nivel, $archivo, $linea));
    return true;
});
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        registrarErrorServidor('error_fatal', new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        mostrarPaginaError('Ocurrió un error inesperado', 'La operación no se pudo completar. Consulta al responsable del proyecto.');
    }
});
