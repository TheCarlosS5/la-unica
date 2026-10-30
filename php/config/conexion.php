<?php
/**
 * Archivo: conexion.php
 * Módulo: Configuración y utilidades compartidas.
 * Propósito: abrir la conexión MySQL y proporcionar funciones comunes a los módulos.
 * Funcionamiento: carga entorno.php (URL base, cabeceras, h()), habilita errores MySQLi como
 * excepciones, establece UTF-8 y la zona horaria, y define validaciones y utilidades.
 * Puntos clave:
 * - Valores por defecto: 127.0.0.1:3306, base la_unica, usuario root sin contraseña (Laragon). Se
 *   sobrescriben con variables DB_* o con conexion.local.php (ver conexion.local.example.php).
 * - h() codifica salida HTML; las consultas de los módulos usan sentencias preparadas.
 * - Las funciones de sesión y token CSRF protegen los formularios que escriben datos.
 * - No implementa autenticación ni roles de usuario.
 */

require_once __DIR__ . '/entorno.php';
require_once __DIR__ . '/consultas.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


try {
    $configLocalPath = __DIR__ . '/conexion.local.php';
    $configLocal = [];

    if (is_file($configLocalPath)) {
        $configLocal = require $configLocalPath;

        if (!is_array($configLocal)) {
            throw new RuntimeException(
                'La configuración local de MySQL no es válida.'
            );
        }
    }

    $obtenerConfig = static function (
        string $claveLocal,
        string $variableEntorno,
        $valorPredeterminado
    ) use ($configLocal) {
        $valorEntorno = getenv($variableEntorno);

        if ($valorEntorno !== false) {
            return $valorEntorno;
        }

        if (array_key_exists($claveLocal, $configLocal)) {
            return $configLocal[$claveLocal];
        }

        return $valorPredeterminado;
    };

    $host = (string) $obtenerConfig(
        'host', 'DB_HOST', '127.0.0.1'
    );
    $usuario = (string) $obtenerConfig(
        'usuario', 'DB_USERNAME', 'root'
    );
    $contrasena = (string) $obtenerConfig(
        'contrasena', 'DB_PASSWORD', ''
    );
    $baseDatos = (string) $obtenerConfig(
        'base_datos', 'DB_DATABASE', 'la_unica'
    );
    $puerto = filter_var(
        $obtenerConfig('puerto', 'DB_PORT', 3306),
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1, 'max_range' => 65535]]
    );

    if ($puerto === false) {
        throw new RuntimeException('El puerto MySQL no es válido.');
    }

    $conexion = new mysqli(
        $host,
        $usuario,
        $contrasena,
        $baseDatos,
        $puerto
    );

    $conexion->set_charset('utf8mb4');
    $conexion->query("SET time_zone = '-05:00'");
} catch (Throwable $e) {
    http_response_code(500);
    exit('Error de conexión a la base de datos.');
}


if (!function_exists('registrarErrorServidor')) {
    /**
     * Escribe el contexto y el detalle técnico en el log del servidor, sin mostrarlos al operador.
     * @param mixed $contexto Nombre de la operación que falló.
     * @param Throwable $error Excepción capturada.
     * @return void
     */
    function registrarErrorServidor($contexto, Throwable $error) {
        $detalle = preg_replace('/[\r\n]+/', ' ', $error->getMessage());
        $registro = '[' . (string)$contexto . '] ' . get_class($error) . ' (' . (string)$error->getCode() . '): ' . $detalle;
        if ($error instanceof mysqli_sql_exception) {
            $registro .= ' SQLSTATE=' . $error->getSqlState();
        }
        error_log($registro);
    }
}

if (!function_exists('sesionDisponible')) {
    /**
     * Comprueba y, cuando es posible, inicia la sesión PHP de forma segura.
     * @return bool Indica si la sesión quedó activa.
     */
    function sesionDisponible() {
        if (session_status() === PHP_SESSION_DISABLED) {
            return false;
        }

        if (session_status() === PHP_SESSION_NONE) {
            if (headers_sent()) {
                return false;
            }
            try {
                $opcionesSesion = [
                    'cookie_httponly' => true,
                    'cookie_samesite' => 'Strict',
                    'use_strict_mode' => true,
                ];
                if (!session_start($opcionesSesion)) {
                    return false;
                }
            } catch (Throwable $e) {
                registrarErrorServidor('sesion_csrf', $e);
                return false;
            }
        }

        return session_status() === PHP_SESSION_ACTIVE;
    }
}

if (!function_exists('obtenerTokenCsrf')) {
    /**
     * Recupera o crea un token aleatorio de sesión para formularios con cambios.
     * @return string|null Token hexadecimal o null cuando la sesión no está disponible.
     */
    function obtenerTokenCsrf() {
        if (!sesionDisponible()) {
            return null;
        }

        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Throwable $e) {
                registrarErrorServidor('token_csrf', $e);
                return null;
            }
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validarTokenCsrf')) {
    /**
     * Compara el token recibido con el de sesión usando una comparación constante.
     * @param mixed $tokenRecibido Token enviado por el formulario.
     * @return bool Resultado de la validación.
     */
    function validarTokenCsrf($tokenRecibido) {
        if (!is_string($tokenRecibido)) {
            return false;
        }

        $tokenEsperado = obtenerTokenCsrf();
        return $tokenEsperado !== null && hash_equals($tokenEsperado, $tokenRecibido);
    }
}

if (!function_exists('validarId')) {
    /**
     * Convierte un ID a entero y limita su valor al rango positivo de INT de la base.
     * @param mixed $valor Entrada que se validará.
     * @return int|null ID válido o null si no cumple el formato/rango.
     */
    function validarId($valor) {
        if ((!is_string($valor) && !is_int($valor)) || $valor === '') {
            return null;
        }
        $id = filter_var($valor, FILTER_VALIDATE_INT);
        return ($id !== false && $id > 0 && $id <= 2147483647) ? $id : null;
    }
}

if (!function_exists('validarFecha')) {
    /**
     * Acepta fechas exactas con formato ISO YYYY-MM-DD.
     * @param mixed $valor Fecha recibida.
     * @return bool Indica si la fecha existe y conserva el formato esperado.
     */
    function validarFecha($valor) {
        if ($valor === null || $valor === '') {
            return false;
        }
        $fecha = DateTime::createFromFormat('!Y-m-d', (string)$valor);
        return $fecha !== false && $fecha->format('Y-m-d') === (string)$valor;
    }
}

if (!function_exists('longitudTexto')) {
    /**
     * Cuenta caracteres UTF-8 usando mbstring o un respaldo compatible con Unicode.
     * @param mixed $valor Texto que se va a medir.
     * @return int Cantidad de caracteres.
     */
    function longitudTexto($valor) {
        if ($valor === null) {
            return 0;
        }

        $texto = (string)$valor;
        if (function_exists('mb_strlen')) {
            return mb_strlen($texto, 'UTF-8');
        }

        $coincidencias = [];
        $longitudUtf8 = preg_match_all('/./us', $texto, $coincidencias);
        return ($longitudUtf8 !== false) ? $longitudUtf8 : strlen($texto);
    }
}

if (!function_exists('formatearMonedaCop')) {
    /**
     * Presenta importes no negativos con separadores locales y sufijo COP.
     * @param mixed $valor Importe decimal con hasta dos posiciones.
     * @return string Importe formateado o $0,00 COP si no cumple el patrón.
     */
    function formatearMonedaCop($valor) {
        $texto = (string)$valor;
        if (!preg_match('/^([0-9]+)(?:\.([0-9]{1,2}))?$/D', $texto, $partes)) {
            return '$0,00 COP';
        }

        $entero = ltrim($partes[1], '0');
        $entero = $entero === '' ? '0' : $entero;
        $decimales = str_pad($partes[2] ?? '', 2, '0');
        $enteroAgrupado = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $entero);
        return '$' . $enteroAgrupado . ',' . $decimales . ' COP';
    }
}

if (!function_exists('guardarMensajeFlash')) {
    /**
     * Guarda un mensaje para mostrarlo una sola vez en la siguiente página (patrón POST-redirección-GET).
     * @param string $clave Identificador del mensaje.
     * @param string $texto Texto que verá el operador.
     * @return void
     */
    function guardarMensajeFlash(string $clave, string $texto): void
    {
        if (sesionDisponible()) {
            $_SESSION['flash'][$clave] = $texto;
        }
    }
}

if (!function_exists('tomarMensajeFlash')) {
    /**
     * Devuelve y borra un mensaje guardado con guardarMensajeFlash().
     * @param string $clave Identificador del mensaje.
     * @return string Texto del mensaje o cadena vacía si no hay.
     */
    function tomarMensajeFlash(string $clave): string
    {
        if (!sesionDisponible() || !isset($_SESSION['flash'][$clave]) || !is_string($_SESSION['flash'][$clave])) {
            return '';
        }
        $texto = $_SESSION['flash'][$clave];
        unset($_SESSION['flash'][$clave]);
        return $texto;
    }
}
