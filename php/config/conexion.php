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

// $baseUrl es la carpeta del proyecto vista desde el navegador
// (por ejemplo "/la-unica"). Sirve para armar enlaces a CSS e imágenes.
$carpetaActual = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$baseUrl = preg_replace('#/php/(clientes|productos)$#', '', $carpetaActual);
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

// Valores permitidos por el SRS (3.5.3).
const TIPOS_DOCUMENTO = ['CC', 'CE', 'NIT', 'PAS'];
const UNIDADES = ['UND', 'KG', 'G', 'LB', 'L', 'ML'];

// Revisa los datos de un cliente que llegan del formulario ($_POST).
// Devuelve [datos_limpios, error]. Si todo está bien, error es ''.
// Los campos opcionales vacíos quedan en null (NULL en la base de datos).
function validar_cliente($entrada) {
    $d = [
        'tipo_documento'   => trim($entrada['tipo_documento'] ?? ''),
        'numero_documento' => mb_strtoupper(trim($entrada['numero_documento'] ?? ''), 'UTF-8'),
        'nombre'           => trim($entrada['nombre'] ?? ''),
        'telefono'         => trim($entrada['telefono'] ?? ''),
        'correo'           => trim($entrada['correo'] ?? ''),
        'direccion'        => trim($entrada['direccion'] ?? ''),
    ];

    $error = '';
    if (!in_array($d['tipo_documento'], TIPOS_DOCUMENTO, true)) {
        $error = 'Elige un tipo de documento válido.';
    } elseif (!preg_match('/^[A-Z0-9]{5,20}$/', $d['numero_documento'])) {
        $error = 'El número de documento debe tener de 5 a 20 letras o dígitos, sin espacios.';
    } elseif (texto_largo($d['nombre']) < 2 || texto_largo($d['nombre']) > 120) {
        $error = 'El nombre es obligatorio y debe tener de 2 a 120 caracteres.';
    } elseif ($d['telefono'] !== '' && !preg_match('/^\d{7,15}$/', $d['telefono'])) {
        $error = 'El teléfono debe tener de 7 a 15 dígitos, sin espacios ni guiones.';
    } elseif ($d['correo'] !== '' && (texto_largo($d['correo']) > 120 || !filter_var($d['correo'], FILTER_VALIDATE_EMAIL))) {
        $error = 'El correo no tiene un formato válido (máximo 120 caracteres).';
    } elseif (texto_largo($d['direccion']) > 150) {
        $error = 'La dirección admite hasta 150 caracteres.';
    }

    foreach (['telefono', 'correo', 'direccion'] as $campo) {
        if ($d[$campo] === '') {
            $d[$campo] = null;
        }
    }
    return [$d, $error];
}

// Igual que validar_cliente, pero para un producto.
function validar_producto($entrada) {
    $d = [
        'codigo'        => mb_strtoupper(trim($entrada['codigo'] ?? ''), 'UTF-8'),
        'nombre'        => trim($entrada['nombre'] ?? ''),
        'descripcion'   => trim($entrada['descripcion'] ?? ''),
        'unidad_medida' => trim($entrada['unidad_medida'] ?? ''),
        'precio'        => trim($entrada['precio'] ?? ''),
        'stock'         => trim($entrada['stock'] ?? ''),
    ];

    // Si la unidad es UND el stock es entero; en las demás admite hasta 3 decimales.
    $patronStock = $d['unidad_medida'] === 'UND' ? '/^\d{1,9}$/' : '/^\d{1,9}(\.\d{1,3})?$/';

    $error = '';
    if (!preg_match('/^[A-Z0-9_-]{3,30}$/', $d['codigo'])) {
        $error = 'El código debe tener de 3 a 30 letras, dígitos, guion o guion bajo.';
    } elseif (texto_largo($d['nombre']) < 2 || texto_largo($d['nombre']) > 120) {
        $error = 'El nombre es obligatorio y debe tener de 2 a 120 caracteres.';
    } elseif (texto_largo($d['descripcion']) > 500) {
        $error = 'La descripción admite hasta 500 caracteres.';
    } elseif (!in_array($d['unidad_medida'], UNIDADES, true)) {
        $error = 'Elige una unidad de medida válida.';
    } elseif (!preg_match('/^\d{1,10}(\.\d{1,2})?$/', $d['precio'])) {
        $error = 'El precio debe ser positivo y tener máximo dos decimales.';
    } elseif (!preg_match($patronStock, $d['stock'])) {
        $error = $d['unidad_medida'] === 'UND'
            ? 'Con unidad UND el stock debe ser un número entero.'
            : 'El stock debe ser positivo y tener máximo tres decimales.';
    }

    if ($d['descripcion'] === '') {
        $d['descripcion'] = null;
    }
    return [$d, $error];
}
