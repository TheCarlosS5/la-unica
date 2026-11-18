# La Única

Aplicación PHP local para administrar clientes, productos, ventas e inventario. Esta documentación se revisó el **11 de noviembre de 2026** contra la operación de `main`. El nombre de una carpeta o una versión documental no identifica por sí solo una versión de software.

## Funciones

- Clientes: documento único, nombre y fecha automática; búsqueda y edición del nombre. Eliminación solo sin facturas.
- Listados: clientes, productos y facturas en páginas de 25 filas; filtros conservados y total de registros.
- Productos: nombre, unidad, stock entero y precio. Editar el catálogo no altera los nombres, unidades ni precios guardados en ventas anteriores.
- Facturas: ventas **Pagado**, descuento transaccional de stock y comprobante imprimible. Cancelación únicamente el mismo día en America/Bogota, con motivo obligatorio y reposición una sola vez. Las facturas no se editan ni se eliminan.
- Cinco reportes: **Ventas**, **Unidades vendidas por producto**, **Existencias**, **Clientes y cantidad de facturas** y **Detalle de facturas**; resumen diario e impresión/PDF.
- Historial de acciones en SQL y errores técnicos en archivo. La wiki y el historial de errores funcionan sin SQL.
- Acceso: cuentas locales con roles Administrador, Operador y Consulta; sesión con 30 minutos de inactividad máxima, cierre por POST y CSRF. El aviso inicial conserva su aceptación de 30 días y exige CSRF.
- Sobre nosotros: SRS completo, manual del usuario, manual técnico, PAC, video y wiki ilustrada.

No implementa crédito, pagos pendientes, impuestos ni factura electrónica. El uso previsto es un equipo local controlado; publicar el código en GitHub no despliega la aplicación ni añade autorización de acceso.

## Requisitos

PHP **8.2 o posterior** con `mysqli`, `mysqlnd`, `mbstring` y sesiones operativas; Apache con `AllowOverride` para `.htaccess`; MySQL **8.0.16 o posterior** o MariaDB compatible con las restricciones del esquema. Laragon es el entorno de referencia. La revisión local usa PHP 8.3.33 y MariaDB 13.0.2; no certifica todas las versiones de ambos motores. Composer no es necesario para abrir el aplicativo.

## Instalación nueva e independiente

1. Clona o copia el repositorio en una **carpeta nueva** de Laragon. Si ya tienes otro proyecto, conserva su carpeta y su base. Los ejemplos `la-unica` y `la-unica-2` son rutas de instalación, no nombres de bases obligatorios.
2. Inicia Apache y SQL y comprueba el puerto real. `3306` es el predeterminado; la demostración de este equipo usa `3307`.
3. Importa `referencias/db.sql` solo en una instalación nueva. Crea `la_unica`; para otra copia cambia `CREATE DATABASE` y `USE` en una copia del script por un nombre exclusivo. No reimportes el esquema sobre datos de trabajo.
4. Opcionalmente carga `referencias/clientes_prueba.sql` y `referencias/productos_prueba.sql`, ajustando sus `USE`: diez clientes y treinta productos ficticios. El conjunto alternativo de `wiki/herramientas/datos_ejemplo.sql` es para las capturas; no se acumula sobre una base ya poblada.
5. Copia `php/config/conexion.local.example.php` a `conexion.local.php` y configura host, puerto, base, usuario y contraseña del equipo. Git ignora este archivo. `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` tienen prioridad; no se carga `.env` automáticamente. Usa una cuenta limitada a esa base, con SELECT, INSERT, UPDATE, DELETE y EXECUTE.
6. Abre la URL de esa carpeta, por ejemplo `http://localhost/la-unica-2/`. Revisa el portal con Siguiente (o Saltar al final) y pulsa Aceptar y entrar; Rechazar vuelve al aviso. Inicia sesión con la cuenta creada. Comprueba una venta y sus reportes en la instalación ficticia antes de usar datos reales.

No se incluye migración automática desde el modelo anterior con Pendiente. Un traslado requiere respaldo, revisión del esquema y ensayo en otra base. No borres una base para resolver un error de conexión o importación.

## Documentación vigente

| Documento | Versión | Archivos |
| --- | --- | --- |
| SRS completo | 1.6 | [Word](referencias/SRS.docx) · [PDF](referencias/SRS.pdf) |
| Manual del usuario | 2.1 | [Word](referencias/manual.docx) · [PDF](referencias/manual.pdf) |
| Manual técnico | 1.4 | [Word](referencias/manual_tecnico.docx) · [PDF](referencias/manual_tecnico.pdf) |
| Plan de Acción de Mejora Continua | 1.4 | [Word](referencias/PAC.docx) · [PDF](referencias/PAC.pdf) |

La [wiki](wiki/) conserva la estructura, pasos y capturas numeradas elegidos para la entrega y describe las reglas actuales. Sus páginas PHP se consultan a través del servidor. Sobre nosotros integra los cuatro PDF. Los visores de esa sección cargan SQL; la wiki y el historial de errores lo omiten. Los PDF públicos también pueden abrirse directamente si el servidor web funciona.

La [revisión de coherencia](referencias/COHERENCIA_DOCUMENTAL.md) registra las correcciones y comprobaciones del 11/11/2026. QA conserva el [plan vigente](referencias/QA/plan-de-pruebas.md), [casos manuales](referencias/QA/casos-de-prueba.md), [checklist](referencias/QA/checklist-seguridad.md) y evidencias históricas identificadas por fecha. Se añade la prueba ejecutable `php tests/seguridad.php`; las verificaciones HTTP y de navegador del 18/11 están en `referencias/QA/revision_2026-11-18/`. No hay Composer ni workflows activos.

El [modelo de entidades y relaciones](referencias/mer.png) representa las cinco tablas del esquema actual.

## Imprimir y guardar como PDF

Aplica los filtros y pulsa **Imprimir reporte** o **Guardar como PDF**. Ambos abren el diálogo del navegador; para exportar selecciona el destino PDF y dónde guardarlo. Usa A4, escala 100 % y desactiva encabezados y pies del navegador: el documento incorpora su propio título, período, fecha y numeración. Detalle de facturas utiliza horizontal; los demás, vertical.

La revisión visual de reportes del 10/11/2026 se realizó en Brave, incluidos ejemplos multipágina. El resultado puede variar con navegador, papel o escala. El comprobante también se imprime desde su vista.

## Protección y límites

`.htaccess` bloquea configuración, herramientas, pruebas, Git, logs, SQL y Markdown internos; los PDF y Word de ayuda siguen públicos. Usa Apache con AllowOverride; PHP `-S` no aplica esas reglas. La autenticación se verifica antes de abrir SQL o ejecutar cambios, también al acceder por URL directa. El historial técnico y la cancelación requieren Administrador. Operador consulta el catálogo, crea/edita clientes y registra facturas; Consulta solo lee. Las cuentas se administran localmente con `tools/usuario.php`; repetir el comando cambia contraseña/rol y revoca las sesiones previas, y `php tools/usuario.php nombre eliminar` retira una cuenta.

La sesión usa nombre propio por instalación, HttpOnly, SameSite=Lax y modo estricto; Secure se activa bajo HTTPS. Las cookies de aceptación se limitan a la ruta de instalación. Hay un máximo de cinco intentos fallidos por usuario e IP en cinco minutos. Se envían CSP (con inline permitido por compatibilidad), X-Frame-Options, X-Content-Type-Options y Referrer-Policy; se elimina X-Powered-By. Mantén la instalación local: estos cambios no certifican un despliegue público. Respalda el archivo privado de usuarios con permisos restringidos, además de SQL.

Las evidencias de concurrencia y rendimiento corresponden a los escenarios medidos y a su fecha. El PAC conserva tareas de respaldo, mantenimiento y aceptación humana; una documentación coherente no equivale a aceptación formal de todos los requisitos.

## Estructura del proyecto

```text
index.php / main.php                 Entrada e inicio con comprobación de aceptación
terminos.php                         Aviso operativo en cinco pasos
procesar_terminos.php                Aceptación/rechazo en sesión y cookie de 30 días
README.md                            Instalación y referencia vigente
.htaccess                            Protección de archivos internos en Apache
assets/
  css/estilos.css                     Estilos y diseño adaptable
  fonts/ y LICENCIAS.txt              Fuentes locales y licencias
  img/logo.png                       Marca del aplicativo
  js/facturas.js                     Líneas y total estimado de la venta
  video/1.mp4                        Video explicativo
php/
  config/conexion.php                Arranque y conexión SQL
  config/configuracion.php           Valores privados y variables DB_*
  config/entorno.php                 Hora, constantes y URL base
  config/utilidades.php              Validación, escape y formato
  config/seguridad.php               Sesión y tokens CSRF
  config/consultas.php               Historial y procedimientos
  config/errores.php                 Diagnóstico y respuestas generales
  config/conexion.local.example.php  Plantilla privada; conexion.local.php se ignora
  includes/                         Menú y pie compartidos
  inicio/ clientes/ productos/       Catálogos y portada
  facturas/                         Venta, comprobante y cancelación del día
  reportes/                         Cinco consultas, resumen e impresión
  historial/                        Acciones SQL y diagnóstico en archivo
  sobre_nosotros/                    SRS, manuales, PAC, video y acceso a wiki
wiki/
  index.php / includes/              Enrutador por catálogo, búsqueda y plantillas
  paginas/                          Guías, configuración y notas técnicas
  img/                              Capturas numeradas WebP
  herramientas/                     Datos ficticios y generación aislada de capturas
  css/ y js/                        Diseño, menú y visor de imágenes
referencias/
  db.sql                            Cinco tablas, cuatro procedimientos, sin vistas
  mer.png                           Modelo de entidades y relaciones
  clientes_prueba.sql                Diez clientes ficticios
  productos_prueba.sql               Treinta productos ficticios
  SRS.docx / SRS.pdf                SRS 1.6
  manual.docx / manual.pdf          Manual del usuario 2.1
  manual_tecnico.docx / .pdf        Manual técnico 1.4
  PAC.docx / PAC.pdf                PAC 1.4
  QA/                               Plan, casos, hallazgos y evidencia fechada
  COHERENCIA_DOCUMENTAL.md           Revisión de documentos y referencias
logs/                               Diagnóstico privado; no se publican archivos .log
```

## Equipo

Proyecto de la ficha **3407799**, programa **Análisis y Desarrollo de Software**, SENA Regional Huila, Centro de la Industria, la Empresa y los Servicios. Instructor: **José de Jesús Motta Vargas**.

| Integrante | Responsabilidad principal documentada en el SRS |
| --- | --- |
| Carlos Stiven Gutiérrez Ramírez | Interfaces y navegación |
| Camilo Andrés Tamayo Duran | Lógica del aplicativo |
| Kevin Fernando Martínez Pérez | Requisitos y documentación |
| Samuel Santiago Ramírez Fierro | Base de datos |
| Santiago Ortiz Claros | Calidad, pruebas y seguridad |

El historial conserva los aportes de cada integrante.
