<?php
/**
 * Archivo: registrar_pago_factura.php
 * Módulo: Facturas — cobro de ventas por cobrar.
 * Propósito: marcar como Pagado una factura Pendiente cuando el cliente paga.
 * Funcionamiento: exige POST y token CSRF, delega en registrarPagoFactura() (servicio_facturas.php) y
 * devuelve el resultado al comprobante mediante sesión flash y redirección 303.
 * Punto clave: no modifica el inventario; las existencias se descontaron al registrar la venta.
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

$pagoExitoso = false;
try {
    registrarPagoFactura($conexion, $idFactura);
    $pagoExitoso = true;
    $mensajeResultado = 'Pago registrado. La factura quedó como Pagado.';
} catch (DomainException $e) {
    $mensajeResultado = $e->getMessage();
} catch (Throwable $e) {
    registrarErrorServidor('registrar_pago_factura', $e);
    $mensajeResultado = 'No se pudo registrar el pago. No se aplicaron cambios; inténtalo de nuevo.';
}

if (sesionDisponible()) {
    $_SESSION['resultado_accion_factura'] = [
        'id' => $idFactura,
        'exito' => $pagoExitoso,
        'mensaje' => $mensajeResultado,
    ];
}

header('Location: imprimir_factura.php?id=' . $idFactura, true, 303);
exit;
