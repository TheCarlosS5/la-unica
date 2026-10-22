# La Única

Aplicación web local para administrar clientes y productos de una tienda. Está desarrollada en PHP y se conecta a MySQL mediante MySQLi. Laragon es el entorno documentado para ejecutarla.

## Funciones disponibles

- **Clientes:** crear, consultar, editar y eliminar. El identificador se ingresa manualmente; la fecha de registro es opcional y, si se deja vacía, la asigna la base de datos.
- **Productos:** crear, consultar, editar y eliminar. El identificador se genera automáticamente. Se administran nombre, unidad de medida, stock entero y precio unitario.
- Las operaciones validan los datos y las eliminaciones requieren confirmación. No se permite eliminar clientes con facturas ni productos que aparezcan en detalles de factura.

El esquema incluye las tablas `facturas` y `detalle_facturas`, pero la aplicación actual no ofrece un módulo para crear, consultar o cancelar facturas. La especificación de requisitos está en [referencias/SRS.md](referencias/SRS.md); describe alcance previsto que puede exceder lo implementado actualmente.

## Requisitos

- Apache.
- PHP con la extensión `mysqli`.
- MySQL.
- Navegador web.

## Base de datos

El script [referencias/db.sql](referencias/db.sql) crea la base `la_unica`, cuatro tablas (`clientes`, `facturas`, `productos` y `detalle_facturas`), tres vistas y cinco procedimientos almacenados. **No inserta datos de ejemplo.**

Importa el script una sola vez en una instancia donde todavía no exista la base `la_unica`, ya que contiene `CREATE DATABASE la_unica`. No es un script de actualización ni de reinicio para una base existente.

## Ejecución local con Laragon

1. Inicia Apache y MySQL desde Laragon.
2. Importa `referencias/db.sql` en tu instancia de MySQL. La configuración inicial de la aplicación espera MySQL en `127.0.0.1:8000`.
3. Si tu MySQL usa otro puerto o credenciales, ajusta la conexión en [php/config/conexion.php](php/config/conexion.php). La configuración inicial usa el usuario `root` y contraseña vacía.
4. Configura Laragon para servir la carpeta del proyecto. Si está disponible en `http://localhost/la-unica/`, abre esa dirección en el navegador.

Debido a que la aplicación no implementa autenticación ni roles y su configuración inicial usa credenciales locales de desarrollo, está destinada a uso local. No la expongas directamente a Internet.

## Estructura del proyecto

```text
index.php                 Punto de entrada que carga main.php
main.php                  Carga la página de inicio
assets/
  css/estilos.css         Estilos de la aplicación
  fonts/                  Fuentes locales y archivo de licencias
php/
  config/conexion.php     Conexión MySQLi y funciones compartidas
  includes/               Menú y pie de página
  inicio/                 Página de inicio
  clientes/               Operaciones de clientes
  productos/              Operaciones de productos
referencias/
  db.sql                  Esquema inicial de la base de datos
  SRS.md                  Especificación de requisitos
RELEASE.md                Guía para preparar y publicar un release
```

## Publicar una versión

Consulta [RELEASE.md](RELEASE.md) para preparar la primera versión, etiquetarla y publicarla en GitHub.
