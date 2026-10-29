# La Única

Aplicación web local para administrar clientes, productos, ventas mediante facturas y reportes. Está desarrollada en PHP y se conecta a MySQL mediante MySQLi. Laragon es el entorno documentado para ejecutarla.

La especificación de requisitos vigente es [referencias/SRS.docx](referencias/SRS.docx), versión 1.4. Para consulta están disponibles [referencias/SRS.pdf](referencias/SRS.pdf) y [referencias/SRS.md](referencias/SRS.md), también en versión 1.4. Los tres documentos describen el alcance central de clientes, productos, facturas y reportes.

El [manual de usuario en Word](referencias/manual.docx) y su [versión PDF](referencias/manual.pdf) incluyen instrucciones y capturas de los módulos principales. La sección «Sobre nosotros», añadida después, permite consultar el PDF dentro del aplicativo y acceder a la página del video explicativo; esa navegación nueva todavía no está detallada en el manual ni en el SRS 1.4.

## Funciones implementadas

- **Clientes:** crear, consultar, editar y eliminar. El identificador se ingresa manualmente; la fecha de registro es opcional y, si se deja vacía, la asigna la base de datos.
- **Productos:** crear, consultar, editar y eliminar. El identificador se genera automáticamente. Se administran nombre, unidad de medida de texto, existencias enteras y precio unitario.
- **Ventas:** crear una factura pagada para un cliente con uno o varios productos. La aplicación comprueba y descuenta existencias en una transacción; el total usa los precios vigentes del catálogo.
- **Facturas:** buscar y consultar facturas, ver sus detalles, imprimir un comprobante interno y cancelar facturas pagadas reponiendo el inventario.
- **Reportes:** un resumen general y siete reportes individuales: clientes y cantidad de facturas, total vigente por factura, unidades facturadas por producto, detalle de facturas, existencias de productos, facturas por estado y facturas pendientes. Los importes reflejan los precios vigentes del catálogo.
- **Sobre nosotros:** acceso al manual PDF integrado y a la sección del video explicativo. Para reproducirlo, coloca el archivo en `assets/video/1.mp4`.
- Las operaciones validan los datos; se protegen las solicitudes de modificación con tokens CSRF y las eliminaciones requieren confirmación. No se permite eliminar clientes con facturas ni productos que aparezcan en detalles de factura.

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

Importa el script una sola vez en una instancia donde todavía no exista la base `la_unica`, ya que contiene `CREATE DATABASE la_unica`. No es un script de actualización ni de reinicio para una base existente. Las facturas no guardan un precio histórico: los importes de los reportes se calculan con el precio vigente del producto.

## Ejecución local con Laragon

1. Inicia Apache y MySQL desde Laragon.
2. Importa `referencias/db.sql` en tu instancia local de MySQL.
3. La conexión inicial de [php/config/conexion.php](php/config/conexion.php) usa `127.0.0.1`, el puerto MySQL `5000`, la base `la_unica`, el usuario `root` y contraseña vacía. Ajusta estos valores a la configuración de tu instancia; el puerto depende del entorno local.
4. Configura Laragon para servir la carpeta del proyecto y abre la URL local que exponga Apache. `http://localhost/la-unica/` es un ejemplo; la dirección y el puerto web también pueden variar según la configuración.

PHP utiliza la zona horaria `America/Bogota` y cada conexión MySQL fija su sesión a UTC-05:00 para registrar y consultar las fechas locales.

El SRS limita la aplicación y MySQL al equipo local y no contempla autenticación ni roles. La configuración inicial usa credenciales locales de desarrollo; no expongas el servicio directamente a Internet.

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
  config/conexion.php     Conexión MySQLi y funciones compartidas
  includes/               Menú, pie de página y utilidades de vista
  inicio/                 Dashboard
  clientes/               Operaciones de clientes
  productos/              Operaciones de productos
  facturas/               Ventas, consultas, cancelaciones e impresión
  reportes/               Resumen y reportes individuales
  sobre_nosotros/         Manual integrado y página del video
referencias/
  db.sql                  Esquema inicial de la base de datos
  manual.docx             Manual editable
  manual.pdf              Manual para consulta y visor integrado
  SRS.docx                Especificación de requisitos, versión 1.4
  SRS.pdf                 Copia de consulta del SRS, versión 1.4
  SRS.md                  Versión Markdown del SRS, versión 1.4
```
