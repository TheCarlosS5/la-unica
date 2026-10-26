<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = new mysqli('127.0.0.1', 'root', '', 'la_unica', 3306);
    $conexion->set_charset('utf8mb4');
} catch (Throwable $e) {
    http_response_code(500);
    exit('Error de conexión a la base de datos.');
}

$scriptDir = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/')));
$baseUrl = preg_replace('#/php/(clientes|productos|inicio)$#', '', $scriptDir);
if ($baseUrl === '/' || $baseUrl === '.') {
    $baseUrl = '';
}
$baseUrl = rtrim($baseUrl, '/');

if (!function_exists('escaparHtml')) {
    function escaparHtml($valor) {
        return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('h')) {
    function h($valor) {
        return escaparHtml($valor);
    }
}

if (!function_exists('registrarErrorServidor')) {
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
    function sesionDisponible() {
        if (!function_exists('session_status') || !function_exists('session_start') || session_status() === PHP_SESSION_DISABLED) {
            return false;
        }

        if (session_status() === PHP_SESSION_NONE) {
            if (headers_sent()) {
                return false;
            }
            try {
                if (!session_start()) {
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
    function obtenerTokenCsrf() {
        if (!sesionDisponible()) {
            return null;
        }

        if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Throwable $e) {
                registrarErrorServidor('token_csrf', $e);
                throw $e;
            }
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validarTokenCsrf')) {
    function validarTokenCsrf($tokenRecibido) {
        if (!is_string($tokenRecibido)) {
            return false;
        }

        $tokenEsperado = obtenerTokenCsrf();
        return $tokenEsperado !== null && hash_equals($tokenEsperado, $tokenRecibido);
    }
}

if (!function_exists('validarId')) {
    function validarId($valor) {
        if ($valor === null || $valor === '') {
            return null;
        }
        $id = filter_var($valor, FILTER_VALIDATE_INT);
        return ($id !== false && $id > 0 && $id <= 2147483647) ? $id : null;
    }
}

if (!function_exists('id_valido')) {
    function id_valido($valor) {
        return validarId($valor);
    }
}

if (!function_exists('validarFecha')) {
    function validarFecha($valor) {
        if ($valor === null || $valor === '') {
            return false;
        }
        $fecha = DateTime::createFromFormat('!Y-m-d', (string)$valor);
        return $fecha !== false && $fecha->format('Y-m-d') === (string)$valor;
    }
}

if (!function_exists('fecha_valida')) {
    function fecha_valida($valor) {
        return validarFecha($valor);
    }
}

if (!function_exists('longitudTexto')) {
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

if (!function_exists('texto_largo')) {
    function texto_largo($valor) {
        return longitudTexto($valor);
    }
}
