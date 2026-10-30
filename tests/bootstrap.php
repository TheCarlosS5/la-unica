<?php
declare(strict_types=1);

/*
 * La suite solo admite bases de pruebas con prefijo la_unica_test.
 * Nunca toma el nombre de la base de producción desde conexion.local.php.
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$database = getenv('DB_TEST_DATABASE') ?: 'la_unica_test_camilo';

if (!preg_match('/^la_unica_test(?:_[a-z0-9_]+)?$/i', $database)) {
    throw new RuntimeException(
        'Base de datos no autorizada para pruebas. Usa la_unica_test o la_unica_test_camilo.'
    );
}

$host = getenv('DB_TEST_HOST') ?: null;
$username = getenv('DB_TEST_USERNAME') ?: null;
$password = getenv('DB_TEST_PASSWORD');
$port = getenv('DB_TEST_PORT');

if ($host === null || $username === null || $password === false || $port === false) {
    $localConfigPath = dirname(__DIR__) . '/php/config/conexion.local.php';

    if (!is_file($localConfigPath)) {
        throw new RuntimeException(
            'Configura DB_TEST_HOST, DB_TEST_USERNAME, DB_TEST_PASSWORD y DB_TEST_PORT, '
            . 'o crea php/config/conexion.local.php para el entorno local.'
        );
    }

    $localConfig = require $localConfigPath;

    if (!is_array($localConfig)) {
        throw new RuntimeException('La configuración local no es válida.');
    }

    $host = $host ?? (string)($localConfig['host'] ?? '127.0.0.1');
    $username = $username ?? (string)($localConfig['usuario'] ?? '');
    $password = $password === false
        ? (string)($localConfig['contrasena'] ?? '')
        : $password;
    $port = $port === false
        ? (string)($localConfig['puerto'] ?? '3307')
        : $port;
}

$portNumber = filter_var(
    $port,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1, 'max_range' => 65535]]
);

if ($portNumber === false) {
    throw new RuntimeException('El puerto de la base de pruebas no es válido.');
}

$GLOBALS['dbTestConnection'] = new mysqli(
    $host,
    $username,
    $password,
    $database,
    $portNumber
);

$GLOBALS['dbTestConnection']->set_charset('utf8mb4');

$requiredTables = ['clientes', 'facturas', 'productos', 'detalle_facturas'];
$result = $GLOBALS['dbTestConnection']->query('SHOW FULL TABLES');
$foundTables = [];

while ($row = $result->fetch_row()) {
    if ($row[1] === 'BASE TABLE') {
        $foundTables[] = $row[0];
    }
}

$missingTables = array_diff($requiredTables, $foundTables);

if ($missingTables !== []) {
    throw new RuntimeException(
        'Faltan tablas en la base de pruebas: ' . implode(', ', $missingTables)
    );
}

require_once dirname(__DIR__) . '/php/config/consultas.php';
require_once dirname(__DIR__) . '/php/reportes/datos_reportes.php';
require_once dirname(__DIR__) . '/php/facturas/servicio_facturas.php';
