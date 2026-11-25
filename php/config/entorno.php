<?php
/** Valores de la aplicación y URL base, independientes de la base de datos. */
date_default_timezone_set('America/Bogota');
const STOCK_BAJO = 5;
const ENTERO_MAXIMO = 2147483647;
const RUTA_LOG_ERRORES = __DIR__ . '/../../logs/errores.log';
$scriptDir = str_replace('\\', '/', dirname((string)($_SERVER['SCRIPT_NAME'] ?? '/')));
$baseUrl = rtrim(preg_replace('#/php/[^/]+$#', '', $scriptDir), '/.');
