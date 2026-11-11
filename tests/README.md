# Pruebas de La Única

Todas las pruebas escriben en una base **nueva y exclusiva**, llamada la_unica_test o con prefijo la_unica_test_. Nunca uses una base de trabajo. No hay migración automática desde el esquema anterior.

1. Importa una copia de referencias/db.sql, ajustando CREATE DATABASE y USE al nombre exclusivo. No reemplaces una base existente.
2. Define DB_TEST_HOST, DB_TEST_PORT, DB_TEST_USERNAME, DB_TEST_PASSWORD y DB_TEST_DATABASE. Conserva la contraseña fuera del repositorio. La cuenta debe poder operar el esquema, sus procedimientos y transacciones.
3. Ejecuta `composer install` y `composer test:database`: ocho pruebas PHPUnit y 16 aserciones; los datos se revierten con rollback.
4. Importa clientes_prueba.sql y productos_prueba.sql, cambiando sus USE a la base de pruebas: diez clientes y treinta productos ficticios.
5. Ejecuta `python tests/functional.py`: 40 comprobaciones HTTP de clientes, productos, ventas, precios históricos, inventario, cancelaciones, reportes e historial.
6. Ejecuta `python tests/advanced.py`: contención, rendimiento, diagnóstico y wiki. No reutilices una base de una ejecución anterior.

PHP y mysql deben estar en PATH; alternativamente define PHP_BINARY y MYSQL_BINARY con sus rutas completas. Los scripts Python usan la biblioteca estándar. Sus servidores escuchan en puertos libres de 127.0.0.1 y se cierran al terminar. QA_REPORT_PATH permite elegir dónde guardar el JSON ampliado; por defecto se imprime su ubicación temporal.

`python tests/wiki_visual.py` no necesita base de datos. Comprueba que las guías esenciales conserven sus capturas actuales, con archivos existentes, dimensiones, texto alternativo y pies de imagen, y que coincidan con el manifiesto. GitHub Actions lo ejecuta para prevenir que una limpieza documental retire otra vez esa ayuda visual.

## Batería ampliada

- Diez carreras por la última unidad: dos procesos HTTP y dos sesiones distintas deben producir una venta y un rechazo por stock, sin factura parcial ni stock negativo.
- Diez carreras de cancelación: dos peticiones deben reponer una sola vez y registrar una sola cancelación.
- Una tercera conexión bloquea la fila antes de disparar cada carrera; ambas peticiones deben esperar. lock_fixture.php exige una base exclusiva y tablas permitidas.
- Carga 1000 clientes, productos, facturas y detalles y mide veinte búsquedas por módulo. Cada respuesta debe contener el registro solicitado. Se exige que al menos el 95 % tarde como máximo 2 s; registra p95 y máximo. Es una medición con búsquedas secuenciales y dos solicitudes por carrera, no una prueba de estrés ilimitada.
- Abre los 29 artículos de wiki, verifica recursos locales, rechazo de rutas ajenas y escape HTML del buscador.
- Provoca fallos SQL y de configuración: HTTP 500 sin diagnóstico técnico público; wiki e historial disponibles sin SQL. Los errores intencionales quedan identificados en el log local, ignorado por Git.

Todas las conexiones SQL de pruebas usan UTC−05:00. GitHub Actions repite la secuencia en MariaDB 11.4 y conserva el JSON como artefacto validation-results. Las evidencias locales están en referencias/QA/revision_mejoras.

La aceptación completa del SRS también requiere revisión de interfaz, multimedia y configuración de acceso local. Una batería aprobada no equivale al 100 % de aceptación de requisitos.
