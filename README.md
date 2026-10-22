# La Única

Aplicación web local para administrar clientes y productos. Está desarrollada en PHP con MySQLi y utiliza MySQL.

## Alcance implementado

- **Clientes:** crear, consultar, editar y eliminar. El `id_cliente` se ingresa manualmente; la fecha de registro puede quedar vacía para usar el valor predeterminado de la base de datos.
- **Productos:** crear, consultar, editar y eliminar. El `id_producto` se genera automáticamente. Se administran nombre, unidad de medida, stock entero y precio unitario.
- Las bajas solicitan confirmación y se bloquean cuando existen registros relacionados.

La interfaz implementa los CRUD de clientes y productos. Las tablas de facturas del esquema se consultan para mostrar relaciones y proteger las bajas.

## Estructura del proyecto

```text
index.php                 Redirección a main.php
main.php                  Carga la página de inicio
assets/
  css/estilos.css         Estilos de la aplicación
  fonts/                  Fuentes locales y sus licencias
php/
  config/conexion.php     Conexión MySQLi y funciones compartidas
  includes/               Menú y pie de página
  inicio/                 Página de inicio
  clientes/               CRUD de clientes
  productos/              CRUD de productos
referencias/              Documentación y archivos de referencia
db.sql                    Creación del esquema de base de datos
```

## Base de datos

El archivo `db.sql`, ubicado en la raíz, crea la base `la_unica`, sus cuatro tablas (`clientes`, `facturas`, `productos` y `detalle_facturas`), tres vistas y cinco procedimientos almacenados. No contiene instrucciones para insertar datos.

Importa `db.sql` una sola vez en una instancia donde todavía no exista la base `la_unica`. El archivo no elimina ni reinicia una base ya creada. En una instalación nueva, los listados de clientes y productos estarán vacíos hasta que se registren elementos desde la aplicación.

## Ejecución local con Laragon

1. Inicia Apache y MySQL en Laragon.
2. Importa `db.sql` en la instancia MySQL que escucha en el puerto `5000`.
3. Configura Laragon para servir esta carpeta. Si la carpeta se publica como `la-unica`, abre `http://localhost/la-unica/`.

La configuración en `php/config/conexion.php` usa `127.0.0.1`, puerto `5000`, base `la_unica`, usuario `root` y contraseña vacía. Si las credenciales de MySQL son distintas, actualiza ese archivo.
