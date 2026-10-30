<?php
/**
 * Archivo: consultas.php
 * Módulo: Configuración — ayudantes de acceso a datos.
 * Propósito: ejecutar consultas preparadas cerrando siempre la sentencia, incluso cuando fallan.
 * Punto clave: no abre conexiones ni envía cabeceras, así que las pruebas pueden cargarlo directamente.
 */

if (!function_exists('consultaPreparada')) {
    /**
     * Ejecuta una consulta preparada y devuelve todas sus filas; cierra la sentencia aunque falle.
     * @param mysqli $conexion Conexión compartida.
     * @param string $sql Consulta con marcadores ?.
     * @param string $tipos Tipos de bind_param ('' si no hay parámetros).
     * @param mixed ...$parametros Valores para los marcadores.
     * @return array Filas asociativas.
     */
    function consultaPreparada(mysqli $conexion, string $sql, string $tipos = '', ...$parametros): array
    {
        $sentencia = $conexion->prepare($sql);
        try {
            if ($tipos !== '') {
                $sentencia->bind_param($tipos, ...$parametros);
            }
            $sentencia->execute();
            return $sentencia->get_result()->fetch_all(MYSQLI_ASSOC);
        } finally {
            $sentencia->close();
        }
    }
}

if (!function_exists('patronLike')) {
    /**
     * Construye un patrón "contiene" para LIKE escapando los comodines % y _ del texto buscado.
     * @param string $texto Texto ingresado por el usuario.
     * @return string Patrón listo para pasar como parámetro de LIKE.
     */
    function patronLike(string $texto): string
    {
        return '%' . addcslashes($texto, '\\%_') . '%';
    }
}
