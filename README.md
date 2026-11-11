# La Única

Versión integrada desde la copia `la-unica-2`, preparada el 10 de noviembre de 2026. Aplicación PHP local para administrar clientes, productos, ventas e inventario. El repositorio conserva el historial de la versión anterior; esta publicación usa cinco tablas, cuatro procedimientos y los estados **Pagado** y **Cancelado**.

## Funciones

- Clientes: documento único, nombre y fecha automática; consulta, búsqueda y edición del nombre. Eliminación solo sin facturas.
- Productos: nombre, unidad, stock entero y precio; búsqueda y edición. Las ventas conservan sus nombres y precios históricos.
- Ventas: factura Pagado, descuento transaccional del inventario y comprobante imprimible. Cancelación únicamente el mismo día, con reposición de stock una sola vez.
- Reportes: Ventas por factura o período, Unidades vendidas por fechas, Existencias, Clientes y cantidad de facturas y Detalle de facturas. Resumen diario y salida de impresión/PDF.
- Historial de acciones y errores. El historial de errores y la wiki funcionan sin conexión SQL.
- Sobre nosotros: SRS completo, manual de usuario, manual técnico, PAC, video y wiki.

No implementa ventas a crédito, registro de pagos pendientes, autenticación, roles, impuestos ni factura electrónica. Debe utilizarse solo en el equipo local; subir el código a GitHub no significa desplegar públicamente la aplicación.

## Requisitos

PHP 8.2 o posterior con `mysqli`, `mysqlnd` y `mbstring`; Apache con AllowOverride para aplicar `.htaccess`; MySQL 8.0.16 o posterior o un MariaDB compatible con las restricciones del esquema. La validación local se realizó con PHP 8.3.33 y MariaDB 13.0.2. Laragon es el entorno de referencia. Composer se necesita para las pruebas PHPUnit, no para abrir el aplicativo.

## Instalación nueva

1. Copiar o clonar la carpeta y servirla desde Laragon, por ejemplo en `C:\laragon\www\la-unica-2`.
2. Iniciar Apache y la base de datos. Comprobar el puerto real; el ejemplo usa 3306 y la demostración local utiliza 3307.
3. Importar `referencias/db.sql` **solo donde no exista la base la_unica**. Para mantener otra copia aislada, cambiar CREATE DATABASE y USE en una copia del script por un nombre exclusivo. No reutilizar la base de otro proyecto.
4. Opcionalmente importar `clientes_prueba.sql` y `productos_prueba.sql`, ajustando antes sus USE al mismo nombre. Son datos ficticios, no datos operativos.
5. Copiar `php/config/conexion.local.example.php` como `php/config/conexion.local.php` y ajustar host, puerto, base, usuario y contraseña. Este último archivo está ignorado por Git. Las variables `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` tienen prioridad si se definen. No publicar credenciales.
6. Abrir la URL de la carpeta, por ejemplo `http://localhost/la-unica-2/`. Verificar una venta de demostración y sus reportes antes de usar datos reales.

### Si ya usabas la versión anterior

No reimportar el esquema sobre una base existente. Esta publicación cambia el modelo y las reglas respecto de la versión con Pendiente. Hacer un respaldo y una revisión del esquema antes de planificar cualquier traslado de datos. Esta publicación **no ejecuta ni incluye una migración automática** de bases previas; la instalación documentada es sobre una base nueva. El historial Git conserva el código y las migraciones anteriores.

## Documentación

- [SRS 1.5 en Word](referencias/SRS.docx) y [PDF completo](referencias/SRS.pdf).
- [Manual de usuario 2.0 en Word](referencias/manual.docx) y [PDF](referencias/manual.pdf).
- [Manual técnico 1.2 en Word](referencias/manual_tecnico.docx) y [PDF](referencias/manual_tecnico.pdf).
- [PAC 1.2 en Word](referencias/PAC.docx) y [PDF](referencias/PAC.pdf).
- [Wiki](wiki/): instrucciones vigentes y material histórico identificado en desplegables. Las capturas heredadas pueden mostrar otra versión.

Los PDF de SRS, manuales y PAC se muestran dentro de Sobre nosotros; SRS también ofrece descarga Word. Los documentos 1.2 incorporan la configuración portable y el estado de publicación. El SRS y el manual de usuario mantienen sus versiones originales; no se declara aceptación total de requisitos por haber publicado el código.

## Imprimir y guardar reportes

Configurar y aplicar los filtros y pulsar **Imprimir reporte** o **Guardar como PDF**. Ambos abren el diálogo del navegador. Para PDF, elegir ese destino y dónde guardar el archivo. Usar A4, escala 100 % y desactivar encabezados/pies del navegador: el documento ya incluye su propia fecha, período y numeración. Orientación vertical salvo Detalle de facturas, horizontal.

Se verificaron PDF con el motor de Brave, incluidos inventarios de varias páginas. El resultado puede variar con el navegador, el papel o la escala seleccionados.

## Pruebas

`composer install` y `composer test:database` ejecutan PHPUnit con una base cuyo nombre debe comenzar por `la_unica_test`. Ver [tests/README.md](tests/README.md). `python tests/functional.py` inicia un servidor PHP aislado y verifica el flujo real por HTTP contra esa base de pruebas. Requiere cargar previamente los datos de ejemplo y definir las variables DB_TEST indicadas en ese documento. Usar una base nueva en cada ejecución.

GitHub Actions comprueba sintaxis PHP/JavaScript, esquema, restricciones, reportes y flujos HTTP sobre MariaDB. Se retiró la prueba de migración del esquema anterior porque esta versión no incluye esa migración. Los tests anteriores permanecen en el historial Git.

## Protección y límites

`.htaccess` impide servir configuración, Git, pruebas, logs y scripts SQL; permite los PDF y Word públicos del proyecto. Estas reglas requieren Apache/AllowOverride. La aplicación no tiene control de acceso: mantener Apache y SQL restringidos al equipo local. El PAC registra como pendiente la exposición de detalles técnicos en los errores de conexión, y las verificaciones exhaustivas de carga y concurrencia.

## Organización

`php/` contiene los módulos y plantillas; `assets/` los estilos, fuentes, JavaScript y video; `referencias/` el esquema, datos de ejemplo y documentos; `wiki/` la documentación navegable; `logs/` el diagnóstico local, ignorado por Git. Los comentarios de módulos explican responsabilidades y bloques principales sin cambiar su lógica.

Integrantes y aportes: [AUTORES.md](AUTORES.md). La publicación actual se realiza directamente en main por autorización expresa del responsable de esta tarea; no cambia dev ni QA.
