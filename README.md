# La Única

Aplicación web local para administrar clientes, productos, ventas mediante facturas y reportes. Está desarrollada en PHP y se conecta a MySQL mediante MySQLi. Laragon es el entorno documentado para ejecutarla.

La especificación de requisitos vigente es [referencias/SRS.docx](referencias/SRS.docx), versión 1.5. Para consulta están disponibles [referencias/SRS.pdf](referencias/SRS.pdf) y [referencias/SRS.md](referencias/SRS.md), también en versión 1.5. Los tres documentos describen el alcance de clientes, productos, facturas, cobros, reportes y la sección Sobre nosotros, además de la arquitectura del aplicativo.

El [manual de usuario en Word](referencias/manual.docx) y su [versión PDF](referencias/manual.pdf) incluyen instrucciones y capturas de los módulos principales. La sección «Sobre nosotros», añadida después, permite consultar el PDF dentro del aplicativo y acceder a la página del video explicativo; esa navegación está en el SRS 1.5 (RF-15), pero todavía no en el manual.

## Funciones implementadas

- **Clientes:** crear, consultar, editar y eliminar. El identificador se ingresa manualmente; la fecha de registro es opcional y, si se deja vacía, la asigna la base de datos.
- **Productos:** crear, consultar, editar y eliminar. El identificador se genera automáticamente. Se administran nombre, unidad de medida de texto, existencias enteras y precio unitario.
- **Ventas:** crear una factura de contado (*Pagado*) o por cobrar (*Pendiente*) para un cliente con uno o varios productos. La aplicación comprueba y descuenta existencias en una transacción y **guarda el precio de cada línea**: editar después el catálogo no altera las facturas ya emitidas.
- **Facturas:** buscar y consultar facturas, ver sus detalles, imprimir un comprobante interno, registrar el pago de una factura *Pendiente* y cancelar facturas *Pagado* o *Pendiente* reponiendo el inventario una sola vez.
- **Reportes:** un resumen general y siete reportes individuales: clientes y cantidad de facturas, total por factura, unidades vendidas por producto, detalle de facturas, existencias de productos, facturas por estado y ventas por cobrar (pendientes). Los totales y las unidades vendidas excluyen las facturas canceladas.
- **Sobre nosotros:** acceso al manual PDF integrado, a la sección del video explicativo y a la wiki. Para reproducir el video, coloca el archivo en `assets/video/1.mp4`.
- **Wiki:** documentación completa en `wiki/` (abre `…/wiki/` o *Menú → Información → Wiki*): guías paso a paso con capturas señaladas, configuración, solución de problemas y notas técnicas. Está hecha en PHP plano y no necesita la base de datos.
- Las operaciones validan los datos; se protegen las solicitudes de modificación con tokens CSRF y las eliminaciones requieren confirmación. No se permite eliminar clientes con facturas ni productos que aparezcan en detalles de factura.

> **Pendiente de documentar en el manual de usuario:** la venta por cobrar, el botón *Registrar pago* y el precio guardado en cada factura ya están en el SRS 1.5, pero todavía no en `manual.docx`/`manual.pdf`.

## Funciones fuera del alcance actual

La autenticación, los roles y las demás funciones excluidas del SRS siguen fuera de esta versión.

## Requisitos

- Apache.
- PHP con las extensiones requeridas por la aplicación, incluida `mysqli` y `mysqlnd`.
- MySQL 8.0.16 o posterior.
- Navegador web moderno.
- Laragon como entorno documentado para ejecución local.

La interfaz usa CSS propio, fuentes locales y SVG integrados; no requiere Bootstrap ni una carpeta de paquetes `vendor`.

## Base de datos

El script [referencias/db.sql](referencias/db.sql) crea la base `la_unica`, cuatro tablas (`clientes`, `facturas`, `productos` y `detalle_facturas`), tres vistas y cinco procedimientos almacenados. **No inserta datos de ejemplo.**

Importa el script una sola vez en una instancia donde todavía no exista la base `la_unica`, ya que contiene `CREATE DATABASE la_unica`. No es un script de actualización ni de reinicio para una base existente.

**Si tu base se creó con una versión anterior**, haz una copia de seguridad y ejecuta una sola vez [referencias/migraciones/001_precio_historico.sql](referencias/migraciones/001_precio_historico.sql). Agrega `precio_unitario` a `detalle_facturas` y actualiza las vistas. Las líneas antiguas no tenían su precio guardado, así que se rellenan con el precio que tenga el catálogo en ese momento.

## Ejecución local con Laragon

1. Inicia Apache y MySQL desde Laragon.
2. Importa `referencias/db.sql` en tu instancia local de MySQL.
3. Por defecto la aplicación se conecta a `127.0.0.1:3306`, base `la_unica`, usuario `root` sin contraseña (los valores de Laragon). Si los tuyos son distintos, **no edites `conexion.php`**: copia [php/config/conexion.local.example.php](php/config/conexion.local.example.php) como `php/config/conexion.local.php` (Git lo ignora) y ajústalo, o define las variables `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.
4. Recomendado: crea un usuario MySQL exclusivo con [referencias/usuario_app.sql](referencias/usuario_app.sql) (cambia antes la contraseña) y úsalo en `conexion.local.php` en lugar de `root`.
5. Configura Laragon para servir la carpeta del proyecto y abre la URL local que exponga Apache. `http://localhost/la-unica/` es un ejemplo; la dirección y el puerto web también pueden variar según la configuración.

PHP utiliza la zona horaria `America/Bogota` y cada conexión MySQL fija su sesión a UTC-05:00 para registrar y consultar las fechas locales.

### Seguridad del equipo local

El SRS limita la aplicación y MySQL al equipo local y no contempla autenticación ni roles. Eso solo es seguro si de verdad nadie más puede llegar a la aplicación:

- **Apache:** Laragon escucha por defecto en todas las interfaces (`Listen 80`), así que cualquiera en la misma red WiFi podría abrir la app. En *Menú → Apache → httpd.conf* cambia la línea por `Listen 127.0.0.1:80` y reinicia Apache.
- **MySQL:** deja `bind-address = 127.0.0.1` en `my.ini` y usa el usuario `la_unica_app` en lugar de `root`.
- **Archivos internos:** el [.htaccess](.htaccess) de la raíz responde 404 a `.git`, `vendor`, `tests`, `php/config`, los scripts SQL y los documentos de `referencias`, y `wiki/herramientas`. Requiere `AllowOverride` activo (Laragon lo trae).
- La cookie de sesión usa `HttpOnly` y `SameSite=Strict`, y las páginas envían `X-Frame-Options`, `X-Content-Type-Options` y `Referrer-Policy`.

## Pruebas automatizadas

Las pruebas de integración usan PHPUnit y una base MySQL/MariaDB **aparte**: el nombre debe empezar por `la_unica_test`, y [tests/bootstrap.php](tests/bootstrap.php) rechaza cualquier otro.

```bash
composer install
# Crear la base de pruebas a partir del esquema (una vez):
sed -e 's/^CREATE DATABASE la_unica;$/CREATE DATABASE la_unica_test;/' \
    -e 's/^USE la_unica;$/USE la_unica_test;/' referencias/db.sql | mysql -u root -p
DB_TEST_HOST=127.0.0.1 DB_TEST_PORT=3306 DB_TEST_USERNAME=root DB_TEST_PASSWORD=... \
DB_TEST_DATABASE=la_unica_test composer test
```

Cubren las vistas y los procedimientos, las restricciones de la base, y el flujo de ventas (`registrarVenta`, `registrarPagoFactura`, `cancelarFactura` en [php/facturas/servicio_facturas.php](php/facturas/servicio_facturas.php)): precio histórico, reversión cuando falta stock, cancelación única y exclusión de canceladas en los reportes. GitHub Actions las ejecuta en cada push y en cada pull request a `main`, junto con la validación de sintaxis PHP/JS y una prueba de la migración desde el esquema anterior.

## Estructura del proyecto

```text
index.php                 Punto de entrada que redirige a main.php
main.php                  Carga la página de inicio
assets/
  css/estilos.css         Hoja de estilos propia
  fonts/                  Fuentes locales y archivo de licencias
  img/                    Logotipo del aplicativo
  js/facturas.js          Interacciones del formulario de facturas
  video/                  Ubicación esperada para 1.mp4
php/
  config/                 Conexión MySQLi, entorno (URL base, cabeceras, h()) y ayudantes de consulta
  includes/               Menú y pie de página
  inicio/                 Dashboard
  clientes/               Operaciones de clientes
  productos/              Operaciones de productos
  facturas/               Ventas, pagos, cancelaciones e impresión; servicio_facturas.php tiene las reglas
  reportes/               Resumen y reportes individuales
  sobre_nosotros/         Manual integrado y página del video
wiki/
  index.php               Wiki del proyecto (guías, configuración y notas técnicas)
  paginas/                Contenido de cada página
  img/                    Capturas señaladas paso a paso
  herramientas/           Script que regenera las capturas
tests/                    Pruebas de integración (PHPUnit)
referencias/
  db.sql                  Esquema inicial de la base de datos
  migraciones/            Scripts para actualizar bases existentes
  usuario_app.sql         Usuario MySQL con permisos mínimos
  manual.docx             Manual editable
  manual.pdf              Manual para consulta y visor integrado
  SRS.docx                Especificación de requisitos, versión 1.5
  SRS.pdf                 Copia de consulta del SRS, versión 1.5
  SRS.md                  Versión Markdown del SRS, versión 1.5
```
