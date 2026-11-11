<?php
/** Inicializa las utilidades y, salvo SIN_BASE_DE_DATOS, la conexión de la página. */
require_once __DIR__ . '/entorno.php';
require_once __DIR__ . '/utilidades.php';
require_once __DIR__ . '/errores.php';
require_once __DIR__ . '/seguridad.php';
require_once __DIR__ . '/consultas.php';
require_once __DIR__ . '/configuracion.php';

$conexion = null;
if (!defined('SIN_BASE_DE_DATOS')) {
    try {
        $configBD = cargarConfiguracionBD();
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conexion = new mysqli($configBD['host'], $configBD['usuario'], $configBD['contrasena'], $configBD['base_datos'], $configBD['puerto']);
        $conexion->set_charset('utf8mb4');
        $conexion->query("SET time_zone = '-05:00'");
        unset($configBD);
    } catch (Throwable $error) {
        registrarErrorServidor('conexion_mysql', $error);
        mostrarPaginaError(
            'No se pudo conectar a la base de datos',
            'Comprueba que el servicio de base de datos de Laragon esté iniciado. Si el problema continúa, consulta al responsable del proyecto para revisar la configuración local.'
        );
    }
}
