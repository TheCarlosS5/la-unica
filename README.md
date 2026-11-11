# La Única

Versión integrada desde la copia `la-unica`, preparada el 10 de noviembre de 2026. Aplicación PHP local para administrar clientes, productos, ventas e inventario. El repositorio conserva el historial de la versión anterior; esta publicación usa cinco tablas, cuatro procedimientos y los estados **Pagado** y **Cancelado**.

## Funciones

- Clientes: documento único, nombre y fecha automática; consulta, búsqueda y edición del nombre. Eliminación solo sin facturas.
- Productos: nombre, unidad, stock entero y precio; búsqueda y edición. Las ventas conservan sus nombres y precios históricos.
- Ventas: factura Pagado, descuento transaccional del inventario y comprobante imprimible. Cancelación únicamente el mismo día, con reposición de stock una sola vez.
- Reportes: Ventas por factura o período, Unidades vendidas por fechas, Existencias, Clientes y cantidad de facturas y Detalle de facturas. Resumen diario y salida de impresión/PDF.
- Historial de acciones y errores. El historial de errores y la wiki funcionan sin conexión SQL.
- Sobre nosotros: SRS completo, manual de usuario, manual técnico, PAC, video y wiki.

No implementa ventas a crédito, registro de pagos pendientes, autenticación, roles, impuestos ni factura electrónica. Debe utilizarse solo en el equipo local; subir el código a GitHub no significa desplegar públicamente la aplicación.

## Requisitos

PHP 8.2 o posterior con `mysqli`, `mysqlnd` y `mbstring`; Apache con AllowOverride para aplicar `.htaccess`; MySQL 8.0.16 o posterior o un MariaDB compatible con las restricciones del esquema. Laragon es el entorno de referencia.

## Instalación nueva

1. Copiar o clonar la carpeta y servirla desde Laragon, por ejemplo en `C:\laragon\www\la-unica-2`.
2. Iniciar Apache y la base de datos. Comprobar el puerto real; el ejemplo usa 3306 y la demostración local utiliza 3307.
3. Importar `referencias/db.sql` **solo donde no exista la base la_unica**. Para mantener otra copia aislada, cambiar CREATE DATABASE y USE en una copia del script por un nombre exclusivo. No reutilizar la base de otro proyecto.
4. Copiar `php/config/conexion.local.example.php` como `php/config/conexion.local.php` y ajustar host, puerto, base, usuario y contraseña. Este último archivo está ignorado por Git. Las variables `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` tienen prioridad si se definen. No publicar credenciales.
5. Abrir la URL de la carpeta, por ejemplo `http://localhost/la-unica-2/`. Verificar una venta de demostración y sus reportes en una instalación aislada.

### Si ya usabas la versión anterior

No reimportar el esquema sobre una base existente. Esta publicación cambia el modelo y las reglas respecto de la versión con Pendiente. Hacer un respaldo y una revisión del esquema antes de planificar cualquier traslado de datos. Esta publicación **no ejecuta ni incluye una migración automática** de bases previas; la instalación documentada es sobre una base nueva. El historial Git conserva el código y las migraciones anteriores.

## Documentación

- [SRS 1.5 en Word](referencias/SRS.docx) y [PDF completo](referencias/SRS.pdf).
- [Manual de usuario 2.0 en Word](referencias/manual.docx) y [PDF](referencias/manual.pdf).
- [Manual técnico 1.3 en Word](referencias/manual_tecnico.docx) y [PDF](referencias/manual_tecnico.pdf).
- [PAC 1.3 en Word](referencias/PAC.docx) y [PDF](referencias/PAC.pdf).
- [Wiki](wiki/): instrucciones vigentes y material histórico identificado en desplegables. Las capturas heredadas pueden mostrar otra versión.

Los PDF de SRS, manuales y PAC se muestran dentro de Sobre nosotros; SRS también ofrece descarga Word. El manual técnico y el PAC 1.3 documentan la configuración modular, el diagnóstico seguro y las pruebas de concurrencia y rendimiento. La wiki contiene 29 artículos actuales. El SRS y el manual de usuario mantienen sus versiones originales; no se declara aceptación total de requisitos por haber publicado el código.

Los archivos de validación citados en el manual técnico y el PAC se pueden consultar en el [historial de Git](https://github.com/TheCarlosS5/la-unica/tree/98d6261cbb40116cce128c2b6c06d1a0860800f6).

## Imprimir y guardar reportes

Configurar y aplicar los filtros y pulsar **Imprimir reporte** o **Guardar como PDF**. Ambos abren el diálogo del navegador. Para PDF, elegir ese destino y dónde guardar el archivo. Usar A4, escala 100 % y desactivar encabezados/pies del navegador: el documento ya incluye su propia fecha, período y numeración. Orientación vertical salvo Detalle de facturas, horizontal.

Se verificaron PDF con el motor de Brave, incluidos inventarios de varias páginas. El resultado puede variar con el navegador, el papel o la escala seleccionados.

## Protección y límites

`.htaccess` impide servir configuración, Git, pruebas, logs y scripts SQL; permite los PDF y Word públicos del proyecto. Estas reglas requieren Apache/AllowOverride. La aplicación no tiene control de acceso: mantener Apache y SQL restringidos al equipo local. Los fallos de conexión y configuración responden HTTP 500 con un mensaje general; el diagnóstico queda en el log local. Las pruebas cubren el volumen y los escenarios medidos; el PAC conserva las tareas de aceptación y mantenimiento que requieren seguimiento del equipo.

## Estructura del proyecto

El código está organizado por módulos. Esta estructura corresponde a la versión actual publicada en `main`:

```text
AUTORES.md                            Equipo, roles, aportes y asistencia de IA
index.php                             Entrada que redirige a main.php
main.php                              Carga el módulo de Inicio
.htaccess                             Protección de archivos internos en Apache
assets/                               Recursos visuales del aplicativo
  css/estilos.css                     Estilos de la interfaz y diseño adaptable
  fonts/                              Fuentes locales y sus licencias
  img/                                Logotipo del proyecto
  js/facturas.js                      Filas, cantidades y total estimado de la venta
  video/1.mp4                         Video explicativo integrado
php/                                  Módulos y plantillas PHP
  config/conexion.php                 Arranque y conexión SQL de cada página
  config/configuracion.php            Valores privados y variables DB_*
  config/entorno.php                  Zona horaria, constantes y URL base
  config/utilidades.php               Validación, escape HTML y formato
  config/seguridad.php                Sesión y tokens CSRF
  config/consultas.php                Historial y procedimientos almacenados
  config/errores.php                  Diagnóstico local y respuestas seguras
  config/conexion.local.example.php   Ejemplo de configuración privada del equipo
  includes/                           Menú, cabecera y pie compartidos
  inicio/                             Portada y accesos a los módulos
  clientes/                           Registro, búsqueda, edición y eliminación protegida
  productos/                          Catálogo, precios y existencias
  facturas/                           Ventas, comprobantes y cancelación del mismo día
  reportes/                           Cinco reportes, resumen diario e impresión/PDF
  historial/                          Consulta de acciones y errores técnicos
  sobre_nosotros/                     Visores de documentos y acceso a video y wiki
wiki/                                 Documentación navegable sin conexión SQL
  index.php                           Entrada, búsqueda y selección de artículos
  paginas/                            Guías, configuración y notas técnicas
  includes/                           Catálogo, buscador y plantillas de la wiki
  img/actuales/                       41 capturas reales actuales con datos ficticios
  css/ y js/                          Diseño e interacciones de la documentación
referencias/                          Esquema y documentos del proyecto
  db.sql                              Cinco tablas y cuatro procedimientos; instalación nueva
  SRS.docx / SRS.pdf                  Especificación de requisitos, versión 1.5
  manual.docx / manual.pdf            Manual del usuario, versión 2.0
  manual_tecnico.docx / .pdf          Manual técnico, versión 1.3
  PAC.docx / PAC.pdf                  Plan de Acción de Mejora Continua, versión 1.3
  QA/                                 Documentación histórica de calidad
logs/                                 Diagnóstico local; los archivos .log no se publican
RELEASE.md                            Notas de publicación y diferencias con versiones previas
```

La configuración personal se guarda en `php/config/conexion.local.php`, creado a partir del ejemplo. Git ignora ese archivo y los logs para conservar privados los datos del equipo.

## Autores

Proyecto de la ficha **3407799**, programa **Análisis y Desarrollo de Software (ADSO)** del SENA Regional Huila, Centro de la Industria, la Empresa y los Servicios.

Consulta los integrantes, sus roles y los aportes en [AUTORES.md](AUTORES.md).
