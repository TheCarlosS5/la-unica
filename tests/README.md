# Pruebas de la versión publicada desde la-unica-2

Se requiere una base NUEVA con prefijo la_unica_test y el esquema de referencias/db.sql, más los dos scripts de datos de ejemplo. No ejecutar sobre una base de trabajo. No hay migración automática desde la versión anterior.

Definir DB_TEST_HOST, DB_TEST_PORT, DB_TEST_USERNAME, DB_TEST_PASSWORD y DB_TEST_DATABASE.

- `composer test:database`: ocho pruebas de esquema, restricciones y consultas con transacciones y rollback.
- `python tests/functional.py`: escenarios HTTP de clientes, productos, ventas, precio histórico, stock, cancelación, reportes e historial. Inicia su propio servidor PHP local y lo cierra al terminar. Modifica SOLO la base de pruebas; usar una base recién creada para cada ejecución. Requiere PHP y mysql en PATH o PHP_BINARY y MYSQL_BINARY.

Las pruebas de servicios y migración de la versión anterior se conservan en el historial Git. Esta versión guarda únicamente Pagado y Cancelado y usa cinco tablas y cuatro procedimientos.

Las conexiones de las pruebas SQL fijan su zona horaria en UTC-05:00, igual que la aplicación, para que las comprobaciones de fecha automática y cancelación no dependan del huso horario del runner.
