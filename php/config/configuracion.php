<?php
/** Lee configuración privada y entorno; no conecta ni imprime sus valores. */
function cargarConfiguracionBD(): array {
    $ruta = __DIR__ . '/conexion.local.php';
    $local = is_file($ruta) ? require $ruta : [];
    if (!is_array($local)) {
        throw new RuntimeException('La configuración local debe devolver un arreglo.');
    }
    $mapa = [
        'host' => ['DB_HOST', '127.0.0.1'],
        'puerto' => ['DB_PORT', 3306],
        'base_datos' => ['DB_DATABASE', 'la_unica'],
        'usuario' => ['DB_USERNAME', 'root'],
        'contrasena' => ['DB_PASSWORD', ''],
    ];
    $config = [];
    foreach ($mapa as $clave => [$variable, $predeterminado]) {
        $valor = getenv($variable);
        $config[$clave] = $valor !== false ? $valor : ($local[$clave] ?? $predeterminado);
    }
    $puerto = filter_var($config['puerto'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
    if ($puerto === false) { throw new RuntimeException('Puerto de base de datos no válido.'); }
    $config['puerto'] = $puerto;
    foreach (['host', 'base_datos', 'usuario', 'contrasena'] as $clave) {
        if (!is_string($config[$clave]) || ($clave !== 'contrasena' && trim($config[$clave]) === '')) {
            throw new RuntimeException('Valor de configuración no válido.');
        }
    }
    return $config;
}
