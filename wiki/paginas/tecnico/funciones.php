<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="utilidades">Validación y formato</h2>
<p>utilidades.php ofrece h para escape HTML, leerTexto para entradas escalares, validarEntero y validarId para rangos numéricos, validarFecha, longitudTexto, formatearMonedaCop y esFacturaDeHoy. entorno.php define STOCK_BAJO y ENTERO_MAXIMO.</p>
<h2 id="sesion">Sesión y consultas</h2>
<p>seguridad.php contiene sesionDisponible, obtenerTokenCsrf y validarTokenCsrf. consultas.php ofrece registrarAccion y llamarProcedimiento; limpia los resultados pendientes de procedimientos para poder reutilizar la conexión.</p>
<h2 id="errores">Configuración y errores</h2>
<p>cargarConfiguracionBD, en configuracion.php, devuelve un arreglo validado sin imprimir valores. registrarErrorServidor y mostrarPaginaError, en errores.php, separan diagnóstico y respuesta. Los módulos siguen cargando conexion.php como punto de entrada; SIN_BASE_DE_DATOS omite la carga de valores SQL y la conexión.</p>
