# Pruebas de integración — entorno local

## Requisitos

- PHP 8.2 o superior con la extensión `mysqli`.
- Composer y las dependencias instaladas (`composer install`).
- MySQL/MariaDB con una base de datos **exclusiva para pruebas**.

## Seguridad: base de datos aislada

La suite solo permite nombres que comiencen por `la_unica_test`. Por defecto utiliza
`la_unica_test_camilo`. No cambies esta protección para apuntar a `la_unica` ni a otra
base de datos de trabajo o producción.

El archivo `php/config/conexion.local.php` puede aportar host, usuario, contraseña y puerto
cuando no se hayan definido las variables `DB_TEST_*`; el nombre de la base de datos de
pruebas no se toma de ese archivo.

## Crear una base de pruebas nueva

1. En phpMyAdmin, crea una base llamada `la_unica_test_camilo`.
2. Selecciona esa base e importa el esquema de `referencias/db.sql`. Como ese archivo crea
   y selecciona `la_unica`, no lo importes sin adaptar esas dos instrucciones: en una copia
   del archivo cambia `CREATE DATABASE la_unica;` por `CREATE DATABASE la_unica_test_camilo;`
   y `USE la_unica;` por `USE la_unica_test_camilo;`.
3. Comprueba que la tabla `detalle_facturas` contiene `precio_unitario`.
4. Ejecuta `vendor/bin/phpunit` (en PowerShell: `vendor\\bin\\phpunit`).

## Actualizar una base de pruebas antigua

Si la base ya existe y procede del esquema anterior:

1. Haz primero una exportación/copia de seguridad desde phpMyAdmin.
2. Confirma que el nombre seleccionado es exactamente `la_unica_test_camilo` (o el nombre
   de pruebas que hayas configurado).
3. La migración `referencias/migraciones/001_precio_historico.sql` contiene
   `USE la_unica;`. **No la ejecutes tal como está** en un entorno local: prepara una copia
   y sustituye únicamente esa instrucción por `USE la_unica_test_camilo;` (o por el nombre
   exacto de tu base de pruebas).
4. Revisa la copia adaptada antes de importarla y verifica en phpMyAdmin que la base activa
   sigue siendo la de pruebas.
5. La migración rellena los precios históricos que faltan usando el precio actual del catálogo.
   Las líneas antiguas no conservaban el precio original, por lo que ese valor no se puede
   reconstruir desde los datos existentes.
6. Importa la copia adaptada y verifica `SHOW COLUMNS FROM detalle_facturas;`.

No ejecutes esta migración sobre `la_unica` ni sobre una base que contenga datos reales.

## Diagnóstico previo automático

`tests/bootstrap.php` comprueba que existan las tablas y las columnas de precio requeridas
antes de cargar los tests. Si detecta un esquema antiguo, detiene la suite con un mensaje
que identifica las columnas ausentes y recuerda que la actualización debe hacerse solo en
la base de pruebas. Esta comprobación evita decenas de errores repetidos durante los INSERT.

## Ejecutar la suite

```powershell
composer install
vendor\\bin\\phpunit --list-tests
vendor\\bin\\phpunit
```

La suite de integración inserta datos temporales. Las pruebas del servicio de facturas eliminan
sus fixtures al terminar; las pruebas de reportes utilizan una transacción y hacen rollback.
