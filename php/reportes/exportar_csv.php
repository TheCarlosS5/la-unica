<?php
/** Descarga las mismas filas y filtros mostrados por el reporte. */
function exportarReporteCsv(string $nombre, array $columnas, array $filas, bool $disponible): void
{
    if (($_GET['formato'] ?? null) !== 'csv') {
        return;
    }
    if (!$disponible) {
        http_response_code(422);
        exit('El reporte no está disponible con estos filtros.');
    }

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $nombre . '.csv"');
    $salida = fopen('php://output', 'wb');
    fwrite($salida, "\xEF\xBB\xBF");
    fputcsv($salida, array_column($columnas, 'titulo'), ';', '"', '', "\r\n");
    foreach ($filas as $fila) {
        $valores = [];
        foreach ($columnas as $columna) {
            $valor = (string)($fila[$columna['campo']] ?? '');
            if (($columna['tipo'] ?? '') === 'moneda') {
                $valor = str_replace('.', ',', $valor);
            } elseif (preg_match('/^[\p{Z}\s]*[=+\-@\t\r\n]/u', $valor)) {
                // Las hojas de cálculo deben abrir los nombres como texto, incluso si empiezan con "=".
                $valor = "'" . $valor;
            }
            $valores[] = $valor;
        }
        fputcsv($salida, $valores, ';', '"', '', "\r\n");
    }
    fclose($salida);
    exit;
}
