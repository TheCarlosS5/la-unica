<?php
/**
 * Archivo: cancelar_factura.php
 * Módulo: Facturas — cancelación.
 * Propósito: cancelar una factura Pagado o Pendiente y reponer su inventario una sola vez.
 * Funcionamiento: exige POST y token CSRF, delega en cancelarFactura() (servicio_facturas.php) y
 * devuelve el resultado al comprobante mediante sesión flash y redirección 303.
 */

require __DIR__ . '/../config/conexion.php';
require __DIR__ . '/servicio_facturas.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

$idFactura = validarId($_POST['id_factura'] ?? null);
if ($idFactura === null) {
    http_response_code(400);
    exit('El identificador de factura no es válido.');
}

if (!validarTokenCsrf($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    exit('No se pudo validar la solicitud. Actualiza la página e inténtalo de nuevo.');
}

$cancelacionExitosa = false;
try {
    cancelarFactura($conexion, $idFactura);
    $cancelacionExitosa = true;
    $mensajeResultado = 'Factura cancelada. Se repuso el inventario.';
} catch (DomainException $e) {
    $mensajeResultado = $e->getMessage();
} catch (Throwable $e) {
    registrarErrorServidor('cancelar_factura', $e);
    $mensajeResultado = 'No se pudo cancelar la factura. No se aplicaron cambios; inténtalo de nuevo.';
}

if (sesionDisponible()) {
    $_SESSION['resultado_accion_factura'] = [
        'id' => $idFactura,
        'exito' => $cancelacionExitosa,
        'mensaje' => $mensajeResultado,
    ];
}

header('Location: imprimir_factura.php?id=' . $idFactura, true, 303);
exit;
