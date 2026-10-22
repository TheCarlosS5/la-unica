# Publicar un release

Guía para preparar y publicar la primera versión de **La Única** en GitHub. Está escrita a partir de la implementación del repositorio: una aplicación web en PHP con MySQLi para administrar clientes y productos, ejecutada localmente con Apache y MySQL.

## Versión inicial propuesta

- **Etiqueta:** `v1.0.0`
- **Título del release:** `La Única v1.0.0`
- **Tipo:** primera versión estable
- **Alcance publicado:** página de inicio y operaciones CRUD de clientes y productos, con validaciones y protección de registros relacionados.

La especificación SRS también plantea facturación, pero ese módulo no está implementado en la aplicación actual. No se debe anunciar como una función de esta versión.

## Requisitos y límites de esta versión

- Servidor web Apache y PHP con la extensión `mysqli`.
- MySQL y un navegador web.
- Laragon es el entorno documentado para ejecutarla localmente.
- La configuración actual de [conexion.php](php/config/conexion.php) usa `127.0.0.1`, el puerto `8000`, la base `la_unica`, el usuario `root` y contraseña vacía. Ajusta esos valores al entorno de instalación antes de ejecutar la aplicación.
- El aplicativo no implementa inicio de sesión ni roles. Esta versión está pensada para uso local; no se debe exponer directamente a Internet.
- [db.sql](referencias/db.sql) crea el esquema, pero no inserta datos de ejemplo. Importarlo requiere una base aún no creada, porque el script ejecuta `CREATE DATABASE la_unica`.

## Antes de publicar

Completa esta lista desde la rama que se publicará:

- [ ] Confirma que todos los cambios de `main` están revisados, guardados y subidos a GitHub.
- [ ] Verifica que la rama de publicación no contiene archivos de configuración personales, contraseñas reales ni datos de clientes.
- [ ] Revisa la documentación de instalación: el README actual menciona `referencias/db (1).sql` y datos de ejemplo, pero el archivo presente es `referencias/db.sql` y no contiene inserciones de datos. Corrige el README antes de anunciar el release para que los pasos de instalación coincidan con el repositorio.
- [ ] Prueba la instalación desde cero en una base vacía: iniciar Apache y MySQL, importar `referencias/db.sql`, ajustar `php/config/conexion.php` y abrir `http://localhost/la-unica/` (o la ruta configurada en Laragon).
- [ ] Recorre manualmente la creación, consulta, edición y eliminación de un cliente y de un producto. Comprueba también que una baja con registros relacionados se bloquea y solicita confirmación cuando corresponde.
- [ ] Confirma que la aplicación sigue mostrando errores de conexión o validación de forma comprensible y que los estilos y las fuentes locales cargan.
- [ ] Confirma que la nota de versión describe únicamente funciones presentes en el código.

El repositorio no define una suite de pruebas automatizadas ni un proceso de compilación. Registra como verificación únicamente las pruebas manuales que efectivamente hayas completado.

## Crear y subir la etiqueta

Desde PowerShell, en la carpeta del repositorio:

```powershell
git switch main
git pull --ff-only origin main
git status --short
```

Si hay cambios pendientes, guárdalos y súbelos antes de continuar. Cuando `main` esté listo y limpio, crea la etiqueta anotada y súbela:

```powershell
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

Si `v1.0.0` ya existe, no la muevas ni la reutilices: corrige el problema en una nueva versión, por ejemplo `v1.0.1`.

## Publicar el release en GitHub

1. Abre el repositorio `TheCarlosS5/la-unica` en GitHub y entra a **Releases** → **Draft a new release**.
2. Selecciona la etiqueta existente `v1.0.0` y como destino la rama `main`.
3. Usa el título `La Única v1.0.0`.
4. Copia y ajusta las notas propuestas abajo. Añade la fecha de publicación y las verificaciones que realmente realizaste.
5. Deja desmarcada la opción de *pre-release* y publica el release cuando la revisión final esté completa.
6. GitHub generará automáticamente los archivos fuente `.zip` y `.tar.gz` asociados a la etiqueta. No adjuntes volcados de bases con datos reales ni archivos de configuración con credenciales.

## Notas propuestas para `v1.0.0`

```markdown
# La Única v1.0.0

Primera versión del aplicativo web La Única para administrar clientes y productos de una tienda en un entorno local.

## Incluye

- Crear, consultar, editar y eliminar clientes.
- Crear, consultar, editar y eliminar productos.
- Validación de datos y confirmación para las bajas.
- Protección contra la eliminación de registros relacionados con facturas.
- Interfaz web con estilos y fuentes locales.

## Requisitos

- Apache y PHP con la extensión mysqli.
- MySQL.
- Laragon es el entorno documentado para la ejecución local.

## Instalación

1. Configura Apache y MySQL.
2. Importa `referencias/db.sql` en una instancia donde todavía no exista la base `la_unica`.
3. Ajusta la conexión en `php/config/conexion.php`.
4. Sirve la carpeta del proyecto y abre la URL local correspondiente, por ejemplo `http://localhost/la-unica/`.

## Alcance

Esta versión incluye gestión de clientes y productos. No incluye una interfaz para crear, consultar o cancelar facturas, autenticación de usuarios ni facturación electrónica. Está destinada a uso local y no debe exponerse directamente a Internet.
```

## Después de publicar

- Comprueba que la página del release muestra la etiqueta y las notas correctas.
- Descarga el `.zip` generado y confirma que contiene el código y `referencias/db.sql`.
- Comparte el enlace del release con las instrucciones de instalación, no una copia de una base de datos con información real.
- Para una corrección posterior, publica una nueva etiqueta (por ejemplo `v1.0.1`) y conserva `v1.0.0` como el registro inmutable de la primera publicación.
