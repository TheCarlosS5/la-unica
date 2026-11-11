# Capturas actuales de la wiki

`generar_capturas.cjs` toma 41 capturas reales de la interfaz en Brave y las guarda en `wiki/img/actuales`, junto con `capturas.json`. Las guías usan esas imágenes; las capturas anteriores se conservan en `wiki/img` como material histórico.

## Preparación segura

1. Prepara una base **nueva y exclusiva**, llamada `la_unica_test` o con prefijo `la_unica_test_`. Importa una copia de `referencias/db.sql` con CREATE DATABASE y USE ajustados al mismo nombre.
2. Importa los dos archivos de datos ficticios, cambiando sus USE al nombre exclusivo. Deben existir los 30 productos iniciales y no debe haber facturas.
3. Define `DB_TEST_HOST`, `DB_TEST_PORT`, `DB_TEST_USERNAME`, `DB_TEST_PASSWORD` y `DB_TEST_DATABASE`. La cuenta debe poder operar esa base. No escribas credenciales en archivos del repositorio.
4. Se requieren Node.js 20 o posterior, Playwright disponible para Node, Brave, PHP con las extensiones del proyecto y el cliente mysql. Puedes definir `PLAYWRIGHT_MODULE` con la ruta al paquete Playwright, `PHP_BINARY`, `MYSQL_BINARY` y `BRAVE_BINARY` con las rutas de sus ejecutables.
5. Ejecuta `node wiki/herramientas/generar_capturas.cjs` desde la raíz del proyecto.

El script inicia su propio servidor PHP en un puerto libre de 127.0.0.1, transmite las variables de la base de pruebas y lo cierra al finalizar. Nunca modifica conexion.local.php. Rechaza nombres de bases de trabajo y bases que ya contienen las ventas o el cliente de esta ejecución. No reutilices una base de una ejecución anterior.

## Escenarios capturados

Clientes: registro, confirmación, documento duplicado, listado, búsqueda, edición y confirmación de baja. Productos: alta, confirmación, catálogo, stock bajo, edición, validación y confirmación de baja. Facturas: selección, líneas, total, registro, listado, comprobante, impresión, confirmación y resultado de cancelación. También captura cinco reportes, resumen, impresión, historial, documentos, video y pantallas estrechas.

Las confirmaciones de eliminación no se completan. Se crea una venta y se cancela verificando descuento y reposición de stock; se registra otra venta vigente para ilustrar los reportes. Todo ocurre sobre datos ficticios de la base exclusiva. Las imágenes de impresión muestran los estilos de la página, no el diálogo del navegador. Las imágenes móviles simulan el tamaño de pantalla en el equipo local.

## Verificar antes de publicar

Revisa cada captura y su pie de imagen, contenido legible, mensajes, versiones documentales y ausencia de datos privados. Cada archivo del manifiesto debe estar utilizado por una guía. Comprueba las imágenes en escritorio y móvil y el visor al hacer clic, usar Enter y cerrar con Escape. No sustituyas imágenes por pantallas antiguas que describen crédito, pagos pendientes o reportes retirados.
