<?php
/**
 * Archivo: conexion.php
 * Módulo: Configuración y utilidades compartidas.
 * Propósito: abrir la conexión MySQL, calcular la URL base y definir las funciones comunes.
 * Puntos clave:
 * - Los valores se leen de variables de entorno o de conexion.local.php, ignorado por Git.
 * - Si la conexión falla, se muestra el error exacto que devuelve MySQL y una pista para corregirlo.
 * - Log de errores: registrarErrorServidor() escribe en logs/errores.log; un manejador global
 *   registra también los errores no capturados y muestra una página amable.
 * - Log general: registrarAccion() guarda en la tabla log_acciones lo que hace el operador.
 * - No implementa autenticación ni roles de usuario.
 */

// Configuración portable: el archivo local y las variables de entorno no se publican.
// Una variable de entorno definida tiene prioridad sobre conexion.local.php.
$configLocal = is_file(__DIR__ . '/conexion.local.php')
    ? require __DIR__ . '/conexion.local.php' : [];
if (!is_array($configLocal)) {
    throw new RuntimeException('La configuración local debe devolver un arreglo.');
}
$dbHost = getenv('DB_HOST') !== false ? getenv('DB_HOST') : ($configLocal['host'] ?? '127.0.0.1');
$dbPuerto = (int)(getenv('DB_PORT') !== false ? getenv('DB_PORT') : ($configLocal['puerto'] ?? 3306));
$dbNombre = getenv('DB_DATABASE') !== false ? getenv('DB_DATABASE') : ($configLocal['base_datos'] ?? 'la_unica');
$dbUsuario = getenv('DB_USERNAME') !== false ? getenv('DB_USERNAME') : ($configLocal['usuario'] ?? 'root');
$dbClave = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ($configLocal['contrasena'] ?? '');

// Un producto con esta cantidad o menos se marca como «Bajo» en los reportes de existencias.
const STOCK_BAJO = 5;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
date_default_timezone_set('America/Bogota');

// Archivo donde se guardan los errores técnicos (uno por línea, en formato JSON).
const RUTA_LOG_ERRORES = __DIR__ . '/../../logs/errores.log';

/**
 * Codifica un valor como texto HTML seguro en UTF-8.
 * @param mixed $valor Valor que se va a imprimir.
 * @return string Valor escapado.
 */
function h($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

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
 * @param string $detalleHtml HTML adicional ya escapado (opcional).
 * @return void
 */
function mostrarPaginaError($titulo, $mensaje, $detalleHtml = '') {
    $caja = '<div class="caja-error"><h1>' . h($titulo) . '</h1><p>' . h($mensaje) . '</p>' . $detalleHtml . '</div>';
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

// Tratamiento global de errores: todo lo que no se capture con try/catch se registra
// en logs/errores.log y el operador ve una página amable en lugar del error técnico.
set_exception_handler(function (Throwable $error) {
    registrarErrorServidor('error_no_controlado', $error);
    mostrarPaginaError(
        'Ocurrió un error inesperado',
        'La operación no se pudo completar. El detalle quedó registrado en Historial › Errores.'
    );
});

set_error_handler(function ($nivel, $mensaje, $archivo, $linea) {
    if (!(error_reporting() & $nivel)) {
        return false; // Errores silenciados con @ o excluidos de error_reporting.
    }
    registrarErrorServidor('aviso_php', new ErrorException($mensaje, 0, $nivel, $archivo, $linea));
    return true; // El aviso queda registrado y no se muestra al operador.
});

register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        registrarErrorServidor('error_fatal', new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
        mostrarPaginaError(
            'Ocurrió un error inesperado',
            'La operación no se pudo completar. El detalle quedó registrado en Historial › Errores.'
        );
    }
});

// Una página puede definir SIN_BASE_DE_DATOS antes de incluir este archivo para no conectarse
// (lo usa Historial › Errores, que debe poder consultarse aunque MySQL esté caído).
$conexion = null;
if (!defined('SIN_BASE_DE_DATOS')) {
    try {
        $conexion = new mysqli($dbHost, $dbUsuario, $dbClave, $dbNombre, $dbPuerto);
        $conexion->set_charset('utf8mb4');
        $conexion->query("SET time_zone = '-05:00'");
    } catch (mysqli_sql_exception $e) {
        registrarErrorServidor('conexion_mysql', $e);
        $codigo = (int)$e->getCode();
        $pistas = [
            2002 => "No hay un servidor MySQL escuchando en {$dbHost}:{$dbPuerto}. Verifica que MySQL esté iniciado en Laragon y que el puerto sea el correcto.",
            2005 => "No se encontró el servidor «{$dbHost}». Revisa el valor de \$dbHost.",
            2006 => 'El servidor MySQL cerró la conexión. Reinicia MySQL e inténtalo de nuevo.',
            1045 => "El usuario «{$dbUsuario}» o su contraseña no son correctos. Revisa \$dbUsuario y \$dbClave.",
            1049 => "La base de datos «{$dbNombre}» no existe. Importa referencias/db.sql antes de usar la aplicación.",
        ];
        $pista = $pistas[$codigo] ?? 'Revisa los datos de conexión en php/config/conexion.php.';

        mostrarPaginaError(
            'No se pudo conectar a la base de datos',
            'Qué revisar: ' . $pista,
            '<p><strong>Error de MySQL (' . $codigo . '):</strong> <code>' . h($e->getMessage()) . '</code></p>'
            . '<dl><dt>Servidor</dt><dd>' . h($dbHost) . '</dd><dt>Puerto</dt><dd>' . h($dbPuerto) . '</dd>'
            . '<dt>Base de datos</dt><dd>' . h($dbNombre) . '</dd><dt>Usuario</dt><dd>' . h($dbUsuario) . '</dd></dl>'
            . '<p>Los datos de conexión se configuran en <code>php/config/conexion.php</code>.</p>'
        );
    }
}

// URL base del proyecto: elimina /php/<módulo> de la ruta del script actual.
$scriptDir = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/')));
$baseUrl = rtrim(preg_replace('#/php/[^/]+$#', '', $scriptDir), '/.');

/**
 * Guarda una acción del operador en la tabla log_acciones (log general).
 * - Fuera de una transacción: si no se puede guardar, el fallo va al log de errores y la operación
 *   del usuario (que ya se completó) no se interrumpe.
 * - Dentro de una transacción ($enTransaccion = true): el error se propaga para que la operación
 *   haga rollback; así la venta o cancelación y su registro se guardan juntos o no se guarda nada.
 * @param mysqli $conexion Conexión abierta.
 * @param string $modulo Clientes, Productos o Facturas.
 * @param string $accion Crear, Editar, Eliminar o Cancelar.
 * @param string $descripcion Detalle legible de lo que se hizo.
 * @param bool $enTransaccion Indica si se llama antes del commit de una transacción.
 * @return void
 */
function registrarAccion(mysqli $conexion, $modulo, $accion, $descripcion, $enTransaccion = false) {
    try {
        $descripcion = mb_substr((string)$descripcion, 0, 255, 'UTF-8');
        $sentencia = $conexion->prepare('INSERT INTO log_acciones (modulo, accion, descripcion) VALUES (?, ?, ?)');
        $sentencia->bind_param('sss', $modulo, $accion, $descripcion);
        $sentencia->execute();
        $sentencia->close();
    } catch (Throwable $e) {
        if ($enTransaccion) {
            throw $e;
        }
        registrarErrorServidor('registrar_accion', $e);
    }
}

/**
 * Inicia la sesión PHP si todavía no está activa.
 * @return bool Indica si la sesión quedó activa.
 */
function sesionDisponible() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
    return session_status() === PHP_SESSION_ACTIVE;
}

/**
 * Recupera o crea el token CSRF de la sesión para los formularios con cambios.
 * @return string|null Token hexadecimal o null si la sesión no está disponible.
 */
function obtenerTokenCsrf() {
    if (!sesionDisponible()) {
        return null;
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Compara el token recibido con el de la sesión.
 * @param mixed $tokenRecibido Token enviado por el formulario.
 * @return bool Resultado de la validación.
 */
function validarTokenCsrf($tokenRecibido) {
    $tokenEsperado = obtenerTokenCsrf();
    return is_string($tokenRecibido) && $tokenEsperado !== null && hash_equals($tokenEsperado, $tokenRecibido);
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

// Máximo valor de una columna INT de MySQL (documentos, IDs, stock y cantidades).
const ENTERO_MAXIMO = 2147483647;

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

/**
 * Ejecuta un procedimiento almacenado con parámetros y devuelve sus filas.
 * Consume los resultados adicionales que MySQL devuelve tras un CALL para dejar libre la conexión.
 * @param mysqli $conexion Conexión abierta.
 * @param string $llamada Sentencia CALL con marcadores ?, por ejemplo 'CALL reporte(?, ?)'.
 * @param string $tipos Tipos de bind_param ('ss', 'i'...); vacío si no hay parámetros.
 * @param array $parametros Valores de los marcadores, en orden.
 * @return array Filas asociativas.
 */
function llamarProcedimiento(mysqli $conexion, $llamada, $tipos = '', array $parametros = []) {
    $sentencia = $conexion->prepare($llamada);
    if ($tipos !== '') {
        $sentencia->bind_param($tipos, ...$parametros);
    }
    $sentencia->execute();
    $resultado = $sentencia->get_result();
    $filas = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    while ($sentencia->more_results() && $sentencia->next_result()) {
        // Descarta el estado final que MySQL envía después del CALL.
    }
    $sentencia->close();
    return $filas;
}
