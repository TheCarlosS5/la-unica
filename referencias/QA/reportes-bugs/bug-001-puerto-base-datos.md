# BUG-001 Configuración del puerto SQL

Estado vigente al **11/11/2026: corregido en el código**. El puerto predeterminado es 3306 y puede cambiarse con DB_PORT o conexion.local.php; la demostración local utiliza 3307. No existe un puerto 5000 fijo en la implementación actual.

El reporte original describía una versión anterior con puerto 5000 y se conserva en el historial Git. Ese fragmento de código no debe presentarse como una línea vigente de conexion.php. La configuración se lee en configuracion.php y la conexión se coordina en conexion.php.

Verificación de entrega: iniciar SQL en su puerto real, configurar una base propia, abrir Inicio y comprobar reportes. No cambiar puertos globales ni reutilizar la base de otro proyecto. La corrección no equivale a haber probado todas las instalaciones posibles.
