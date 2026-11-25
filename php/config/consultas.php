<?php
/** Operaciones SQL compartidas: historial y ejecución de procedimientos. */
/**
 * Guarda una acción del operador en la tabla log_acciones (log general).
 * - Fuera de una transacción: si no se puede guardar, el fallo va al log de errores y la operación
 *   del usuario (que ya se completó) no se interrumpe.
 * - Dentro de una transacción ($enTransaccion = true): el error se propaga para que la operación
 *   haga rollback; así la venta o cancelación y su registro se guardan juntos o no se guarda nada.
 * @param mysqli $conexion Conexión abierta.
 * @param string $modulo Clientes, Productos o Facturas.
 * @param string $accion Crear, Editar, Eliminar o Cancelar.
 * @param string $descripcion Detalle legible de lo que se hizo.
 * @param bool $enTransaccion Indica si se llama antes del commit de una transacción.
 * @return void
 */
function registrarAccion(mysqli $conexion, $modulo, $accion, $descripcion, $enTransaccion = false) {
    try {
        $descripcion = mb_substr((string)$descripcion, 0, 255, 'UTF-8');
        $sentencia = $conexion->prepare('INSERT INTO log_acciones (modulo, accion, descripcion) VALUES (?, ?, ?)');
        $sentencia->bind_param('sss', $modulo, $accion, $descripcion);
        $sentencia->execute();
        $sentencia->close();
    } catch (Throwable $e) {
        if ($enTransaccion) {
            throw $e;
        }
        registrarErrorServidor('registrar_accion', $e);
    }
}
/**
 * Ejecuta un procedimiento almacenado con parámetros y devuelve sus filas.
 * Consume los resultados adicionales que MySQL devuelve tras un CALL para dejar libre la conexión.
 * @param mysqli $conexion Conexión abierta.
 * @param string $llamada Sentencia CALL con marcadores ?, por ejemplo 'CALL reporte(?, ?)'.
 * @param string $tipos Tipos de bind_param ('ss', 'i'...); vacío si no hay parámetros.
 * @param array $parametros Valores de los marcadores, en orden.
 * @return array Filas asociativas.
 */
function llamarProcedimiento(mysqli $conexion, $llamada, $tipos = '', array $parametros = []) {
    $sentencia = $conexion->prepare($llamada);
    if ($tipos !== '') {
        $sentencia->bind_param($tipos, ...$parametros);
    }
    $sentencia->execute();
    $resultado = $sentencia->get_result();
    $filas = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    while ($sentencia->more_results() && $sentencia->next_result()) {
        // Descarta el estado final que MySQL envía después del CALL.
    }
    $sentencia->close();
    return $filas;
}
