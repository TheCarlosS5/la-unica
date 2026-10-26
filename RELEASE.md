# Publicar un release

Esta guía describe el proceso para publicar versiones de **La Única** en GitHub. No es el historial de cambios: las notas de cada versión se escriben en el release correspondiente de GitHub, que conserva las versiones publicadas y sus etiquetas. No es necesario duplicar ese historial en este archivo.

## Alcance actual de la aplicación

La aplicación está desarrollada en PHP con MySQLi y permite administrar clientes y productos desde un entorno local con Apache y MySQL. El SRS también plantea facturación, pero la aplicación actual no cuenta con una interfaz para crear, consultar o cancelar facturas; no anuncies esas funciones como disponibles.

La aplicación tampoco implementa autenticación ni roles. Está pensada para uso local y no debe exponerse directamente a Internet.

## Convención de versiones

Usa etiquetas con el formato `vMAYOR.MENOR.PARCHE`:

- Primera versión estable: `v1.0.0`.
- Corrección compatible: aumenta el parche, por ejemplo `v1.0.1`.
- Función nueva compatible: aumenta la versión menor, por ejemplo `v1.1.0`.
- Cambio que rompe compatibilidad: aumenta la versión mayor, por ejemplo `v2.0.0`.

Una versión publicada conserva su etiqueta. Si encuentras un problema después de publicar, prepara una nueva versión en lugar de mover o reutilizar la etiqueta anterior.

## Requisitos de ejecución

- Apache.
- PHP con la extensión `mysqli`.
- MySQL y un navegador web.
- Laragon es el entorno documentado para la ejecución local.
- La configuración inicial de [conexion.php](php/config/conexion.php) usa `127.0.0.1`, puerto `8000`, base `la_unica`, usuario `root` y contraseña vacía. Ajusta esos valores al entorno de instalación.
- [db.sql](referencias/db.sql) crea el esquema, pero no inserta datos de ejemplo. Importa el script solo en una instancia donde todavía no exista la base `la_unica`, porque contiene `CREATE DATABASE la_unica`.

## Antes de publicar

Completa esta lista para la versión que vas a etiquetar:

- [ ] Confirma que los cambios están revisados, guardados y subidos a `main` en GitHub.
- [ ] Verifica que el repositorio no contiene archivos de configuración personales, contraseñas reales ni datos de clientes.
- [ ] Revisa que el README y estas instrucciones coincidan con los archivos y funciones presentes.
- [ ] Prueba la instalación desde cero en una base vacía: inicia Apache y MySQL, importa `referencias/db.sql`, ajusta `php/config/conexion.php` y abre la ruta local configurada, por ejemplo `http://localhost/la-unica/`.
- [ ] Recorre manualmente la creación, consulta, edición y eliminación de un cliente y de un producto. Comprueba que las bajas con registros relacionados se bloquean y que las eliminaciones solicitan confirmación.
- [ ] Comprueba que los estilos y las fuentes locales cargan y que los errores de conexión o validación se muestran de forma comprensible.
- [ ] Prepara notas de versión que describan solo los cambios incluidos en esta etiqueta. Esas notas se guardarán en GitHub al publicar el release.

El repositorio no define una suite de pruebas automatizadas ni un proceso de compilación. Registra como verificación únicamente las pruebas manuales que efectivamente hayas completado.

## Crear y subir la etiqueta

Antes de etiquetar, confirma que no tienes cambios locales pendientes. Desde PowerShell, en la carpeta del repositorio:

```powershell
git switch main
git pull --ff-only origin main
git status --short
```

Si `git status --short` muestra cambios pendientes, guárdalos y súbelos antes de continuar. Cuando `main` esté listo, crea una etiqueta anotada. Para la primera versión:

```powershell
git tag -a v1.0.0 -m "Release v1.0.0"
git push origin v1.0.0
```

En las siguientes publicaciones, reemplaza `v1.0.0` por la nueva versión elegida.

## Publicar en GitHub

1. Abre `TheCarlosS5/la-unica` en GitHub y entra a **Releases** → **Draft a new release**.
2. Selecciona la etiqueta que acabas de subir. Si vas a crearla desde GitHub, elige `main` como destino.
3. Usa un título como `La Única v1.0.0`.
4. Escribe las notas de los cambios de esa versión usando la plantilla de abajo. Incluye fecha o verificaciones solo si aportan información y efectivamente se realizaron.
5. Para una versión estable, deja desmarcada la opción de *pre-release* y conserva marcada la opción de último release.
6. Publica el release. GitHub conservará las notas junto con la etiqueta y generará los archivos fuente `.zip` y `.tar.gz`.

No adjuntes volcados de bases con datos reales ni archivos de configuración con credenciales.

### Plantilla para las notas de GitHub

Copia esta plantilla en el formulario de GitHub y reemplaza los marcadores por los cambios reales. Omite las secciones vacías.

```markdown
# La Única vX.Y.Z

Resumen breve de esta versión.

## Novedades

- Describe las funciones nuevas incluidas.

## Correcciones

- Describe los errores corregidos.

## Instalación o actualización

- Incluye pasos especiales si esta versión los requiere.
```

GitHub será el registro de cambios por versión. Para una versión futura, crea un nuevo release con sus propias notas; no reemplaces las notas de versiones anteriores ni mantengas una copia de ellas en `RELEASE.md`.

## Después de publicar

- Comprueba que el release muestra la etiqueta, el título y las notas correctas.
- Descarga el `.zip` generado y confirma que contiene el código y `referencias/db.sql`.
- Comparte el enlace de ese release y sus instrucciones; no compartas una base con información real.
