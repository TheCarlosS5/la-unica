<?php
// ===================================================================
// conexion.php
// Todas las páginas incluyen este archivo primero. Hace dos cosas:
//   1. Abre la conexión a MySQL y la guarda en $conexion.
//   2. Define funciones pequeñas que se usan en varias páginas.
// ===================================================================

// --- 1. Conexión a MySQL -------------------------------------------
// Datos: servidor, usuario, contraseña, base de datos y puerto.
// Si tu MySQL usa otro puerto o contraseña, cámbialo aquí.
mysqli_report(MYSQLI_REPORT_OFF); // los errores los revisamos nosotros
$conexion = new mysqli('127.0.0.1', 'root', '', 'la_unica', 8000);

if ($conexion->connect_errno) {
    http_response_code(500);
    exit('Error de conexión a MySQL. Revisa que esté iniciado y que use el puerto 8000.');
}
$conexion->set_charset('utf8mb4'); // para que las tildes y la ñ se guarden bien

// Las fechas se manejan en hora de Bogotá (el SRS lo pide), tanto en PHP como en MySQL.
date_default_timezone_set('America/Bogota');
$conexion->query("SET time_zone = '-05:00'");

// $baseUrl es la carpeta del proyecto vista desde el navegador
// (por ejemplo "/la-unica"). Sirve para armar enlaces a CSS e imágenes.
$carpetaActual = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$baseUrl = preg_replace('#/php/(clientes|productos|facturas|reportes)$#', '', $carpetaActual);
$baseUrl = rtrim($baseUrl, '/.');

// --- 2. Funciones de ayuda -----------------------------------------

// Escapa un texto antes de mostrarlo en HTML (evita ataques XSS).
function h($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Devuelve el número si es un ID válido (entero positivo), o null si no lo es.
function id_valido($valor) {
    $id = filter_var($valor, FILTER_VALIDATE_INT);
    if ($id === false || $id <= 0 || $id > 2147483647) {
        return null;
    }
    return $id;
}

// Cuenta letras (no bytes): "Peña" tiene 4 letras aunque la ñ ocupe 2 bytes.
function texto_largo($valor) {
    return mb_strlen($valor, 'UTF-8');
}

// true si el texto es una fecha real con formato AAAA-MM-DD.
function fecha_valida($valor) {
    $fecha = DateTime::createFromFormat('!Y-m-d', (string)$valor);
    return $fecha !== false && $fecha->format('Y-m-d') === $valor;
}

// Revisa los datos de un cliente que llegan del formulario ($_POST).
// Devuelve [datos_limpios, error]. Si todo está bien, error es ''.
// La fecha de registro es opcional: si va vacía la pone la base de datos.
function validar_cliente($entrada) {
    $d = [
        'id_cliente'     => trim($entrada['id_cliente'] ?? ''),
        'nombre'         => trim($entrada['nombre'] ?? ''),
        'fecha_registro' => trim($entrada['fecha_registro'] ?? ''),
    ];

    $error = '';
    if (id_valido($d['id_cliente']) === null) {
        $error = 'El id del cliente debe ser un número entre 1 y 2.147.483.647.';
    } elseif ($d['nombre'] === '' || texto_largo($d['nombre']) > 100) {
        $error = 'El nombre es obligatorio y admite hasta 100 caracteres.';
    } elseif ($d['fecha_registro'] !== '' && !fecha_valida($d['fecha_registro'])) {
        $error = 'La fecha de registro no es válida.';
    }
    return [$d, $error];
}

// Igual que validar_cliente, pero para un producto.
function validar_producto($entrada) {
    $d = [
        'nombre'          => trim($entrada['nombre'] ?? ''),
        'unidad_medida'   => trim($entrada['unidad_medida'] ?? ''),
        'stock'           => trim($entrada['stock'] ?? ''),
        'precio_unitario' => trim($entrada['precio_unitario'] ?? ''),
    ];
    $stock = filter_var($d['stock'], FILTER_VALIDATE_INT);

    $error = '';
    if ($d['nombre'] === '' || texto_largo($d['nombre']) > 100) {
        $error = 'El nombre es obligatorio y admite hasta 100 caracteres.';
    } elseif ($d['unidad_medida'] === '' || texto_largo($d['unidad_medida']) > 20) {
        $error = 'La unidad es obligatoria y admite hasta 20 caracteres.';
    } elseif ($stock === false || $stock < 0 || $stock > 2147483647) {
        $error = 'El stock debe ser un número entero mayor o igual a 0.';
    } elseif (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $d['precio_unitario'])) {
        // Acepta por ejemplo: 2500  o  2500.50  (máximo 2 decimales)
        $error = 'El precio debe ser positivo y tener máximo dos decimales.';
    }
    return [$d, $error];
}
