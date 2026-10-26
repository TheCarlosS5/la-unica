# La Única

Aplicación web local para administrar clientes y productos de una tienda. Está desarrollada en PHP y se conecta a MySQL mediante MySQLi. Laragon es el entorno documentado para ejecutarla.

## Funciones disponibles

- **Clientes:** crear, consultar, editar y eliminar. Se guardan tipo y número de documento (únicos en conjunto), nombre y, opcionalmente, teléfono, correo y dirección. Si el cliente tiene facturas, su documento no se puede cambiar.
- **Productos:** crear, consultar, editar y eliminar. Se guardan código único, nombre, descripción opcional, unidad de medida (UND, KG, G, LB, L o ML), precio por unidad y stock (entero para UND; hasta 3 decimales en las demás). Si el producto está en facturas, su código y unidad no se pueden cambiar. Cada producto muestra una foto elegida según palabras de su nombre; si no coincide ninguna, se usa una genérica.
- **Búsqueda:** clientes por nombre o documento; productos por código o nombre.
- **Interfaz:** modo claro y oscuro (botón arriba a la derecha, se recuerda en el navegador). Todo funciona sin internet.
- Las operaciones validan los datos y las eliminaciones requieren confirmación. No se permite eliminar clientes con facturas ni productos que aparezcan en detalles de factura.

El esquema incluye las tablas `factura` y `detalle_factura`, pero la aplicación actual no ofrece un módulo para crear, consultar o cancelar facturas. La especificación de requisitos está en [referencias/SRS.md](referencias/SRS.md); describe alcance previsto que puede exceder lo implementado actualmente.

## Requisitos

- Apache.
- PHP con la extensión `mysqli`.
- MySQL.
- Navegador web.

## Base de datos

El script [referencias/db.sql](referencias/db.sql) crea la base `la_unica`, cuatro tablas (`clientes`, `productos`, `factura` y `detalle_factura`, según el SRS v1.1), tres vistas y cuatro procedimientos almacenados. **No inserta datos de ejemplo.**

Importa el script una sola vez en una instancia donde todavía no exista la base `la_unica`, ya que contiene `CREATE DATABASE la_unica`. No es un script de actualización ni de reinicio para una base existente.

## Ejecución local con Laragon

1. Inicia Apache y MySQL desde Laragon.
2. Importa `referencias/db.sql` en tu instancia de MySQL. La configuración inicial de la aplicación espera MySQL en `127.0.0.1:8000`.
3. Si tu MySQL usa otro puerto o credenciales, ajusta la conexión en [php/config/conexion.php](php/config/conexion.php). La configuración inicial usa el usuario `root` y contraseña vacía.
4. Configura Laragon para servir la carpeta del proyecto. Si está disponible en `http://localhost/la-unica/`, abre esa dirección en el navegador.

Si ya tenías una base `la_unica` con el esquema anterior, elimínala antes de importar (`DROP DATABASE la_unica;`): el esquema cambió.

Debido a que la aplicación no implementa autenticación ni roles y su configuración inicial usa credenciales locales de desarrollo, está destinada a uso local. No la expongas directamente a Internet.

## Estructura del proyecto

```text
index.php                   Abre el listado de clientes
assets/
  css/estilos.css           Estilos propios (colores, modo claro/oscuro, animaciones)
  vendor/                   Bootstrap 5.3 e íconos de pixeles Pixelarticons (copias locales)
  fonts/                    Fuentes Inter e Inter Tight
  img/productos/            Fotos de productos
php/
  config/conexion.php       Conexión MySQLi y validación de clientes y productos
  includes/menu.php         Parte de arriba de cada página (menú)
  includes/pie.php          Parte de abajo de cada página
  includes/funciones_vista.php  Funciones para mostrar (fotos, íconos, alertas)
  includes/form_cliente.php     Campos del formulario de cliente (crear y editar)
  includes/form_producto.php    Campos del formulario de producto (crear y editar)

  clientes/                 ver, crear, editar y eliminar clientes
  productos/                ver, crear, editar y eliminar productos
referencias/
  db.sql                    Esquema inicial de la base de datos
  SRS.md                    Especificación de requisitos
```

## Cómo leer el código

Cada página de `php/clientes` y `php/productos` sigue el mismo orden:

1. `require conexion.php` para tener `$conexion` y las funciones de ayuda.
2. Si llegó un formulario (`POST`), validar los datos y guardar con una consulta preparada (`prepare` + `bind_param`).
3. `require menu.php`, luego el HTML de la página y al final `require pie.php`.

Las páginas de eliminar trabajan en dos pasos: primero muestran "¿Seguro?" y, al confirmar, eliminan y regresan al listado con un mensaje.
