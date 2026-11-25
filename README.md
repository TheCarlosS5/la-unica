# La Única

Aplicación PHP local para administrar clientes, productos, ventas e inventario. Documentación alineada el **23 de noviembre de 2026** con el commit [`e527b75`](https://github.com/TheCarlosS5/la-unica/commit/e527b752204e0faf23fb4bb05876ccc4096082ad), publicado el 22 de noviembre en hora de Colombia. El nombre de una carpeta o una versión documental no identifica por sí solo una versión de software.

## Funciones

- Clientes: documento único, nombre y fecha automática; búsqueda y edición del nombre. Eliminación solo sin facturas.
- Listados: clientes, productos y facturas en páginas de 25 filas; filtros conservados y total de registros.
- Productos: nombre, unidad, stock entero y precio. Editar el catálogo no altera los nombres, unidades ni precios guardados en ventas anteriores.
- Facturas: ventas **Pagado**, descuento transaccional de stock y comprobante imprimible. Cancelación únicamente el mismo día en America/Bogota, con motivo obligatorio y reposición una sola vez. Las facturas no se editan ni se eliminan.
- Cinco reportes: **Ventas**, **Unidades vendidas por producto**, **Existencias**, **Clientes y cantidad de facturas** y **Detalle de facturas**; resumen diario, impresión/PDF y descarga CSV de cada consulta.
- Historial de acciones en SQL y errores técnicos en archivo. La wiki y el historial de errores funcionan sin SQL.
- Acceso: un operador en el mismo equipo. Términos y Condiciones obligatorios con aceptación recordada durante 30 días. Los módulos operativos exigen la aceptación antes de abrir SQL. El operador entra sin cuenta y el portal usa un token CSRF.
- Sobre nosotros: SRS completo, manual del usuario, manual técnico, PAC, video y wiki ilustrada.

No implementa crédito, pagos pendientes, impuestos ni factura electrónica. El sistema está diseñado para un único operador local.

## Requisitos

PHP **8.2 o posterior** con `mysqli`, `mysqlnd`, `mbstring` y sesiones operativas; Apache con `AllowOverride` para `.htaccess`; MySQL **8.0.16 o posterior** o MariaDB compatible con las restricciones del esquema. Laragon es el entorno de referencia. La revisión local usa PHP 8.3.33 y MariaDB 13.0.2; no certifica todas las versiones de ambos motores. Composer no es necesario para abrir el aplicativo.

## Instalación nueva e independiente

1. Clona o copia el repositorio en una **carpeta nueva** de Laragon. Si ya tienes otro proyecto, conserva su carpeta y su base. Los ejemplos `la-unica` y `la-unica-2` son rutas de instalación, no nombres de bases obligatorios.
2. Inicia Apache y SQL y comprueba el puerto real. `3306` es el valor predeterminado. Utiliza el puerto configurado en tu instalación.
3. Importa `referencias/db.sql` solo en una instalación nueva. Crea `la_unica`; para otra copia cambia `CREATE DATABASE` y `USE` en una copia del script por un nombre exclusivo. No reimportes el esquema sobre datos de trabajo.
4. Opcionalmente carga `referencias/clientes_prueba.sql` y `referencias/productos_prueba.sql`, ajustando sus `USE`: diez clientes y treinta productos ficticios. El conjunto alternativo de `wiki/herramientas/datos_ejemplo.sql` es para las capturas; no se acumula sobre una base ya poblada.
5. Copia `php/config/conexion.local.example.php` a `conexion.local.php` y configura host, puerto, base, usuario y contraseña del equipo. Git ignora este archivo. `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` tienen prioridad; no se carga `.env` automáticamente. Usa una cuenta limitada a esa base, con SELECT, INSERT, UPDATE, DELETE y EXECUTE.
6. Abre la URL de esa carpeta, por ejemplo `http://localhost/la-unica-2/`. Revisa el portal con Siguiente (o Saltar al final) y pulsa Aceptar y entrar; Rechazar vuelve al aviso. Comprueba una venta y sus reportes en la instalación ficticia antes de usar datos reales.

No se incluye migración automática desde el modelo anterior con Pendiente. Un traslado requiere respaldo, revisión del esquema y ensayo en otra base. No borres una base para resolver un error de conexión o importación.

## Documentación vigente

| Documento | Versión | Archivos |
| --- | --- | --- |
| SRS completo | 1.6 | [Word](referencias/SRS.docx) · [PDF](referencias/SRS.pdf) |
| Manual del usuario | 2.1 | [Word](referencias/manual.docx) · [PDF](referencias/manual.pdf) |
| Manual técnico | 1.4 | [Word](referencias/manual_tecnico.docx) · [PDF](referencias/manual_tecnico.pdf) |
| Plan de Aseguramiento de la Calidad | 2.0 | [Word](referencias/PAC.docx) · [PDF](referencias/PAC.pdf) |

La [wiki](wiki/) conserva la estructura, pasos y capturas numeradas elegidos para la entrega y describe las reglas actuales. Sus páginas PHP se consultan a través del servidor. Sobre nosotros integra los cuatro PDF. Los visores de esa sección cargan SQL; la wiki y el historial de errores lo omiten. Los PDF pueden abrirse directamente desde el mismo equipo.

El [anexo del 18/11](referencias/REVISION_EQUIPOS_2026-11-18.md) y sus evidencias corresponden a un corte anterior con cuentas y roles. Las regresiones de acceso local y CSV del 22/11 están en [resultados del grupo 5](referencias/QA/resultados_grupo_5.json). La [revisión de coherencia](referencias/COHERENCIA_DOCUMENTAL.md) registra las correcciones y comprobaciones del 11/11/2026. QA conserva el [plan vigente](referencias/QA/plan-de-pruebas.md), [casos manuales](referencias/QA/casos-de-prueba.md), [checklist](referencias/QA/checklist-seguridad.md) y evidencias históricas identificadas por fecha. Se añade la prueba ejecutable `php tests/seguridad.php`; las verificaciones HTTP y de navegador del 18/11 están en `referencias/QA/revision_2026-11-18/`. No hay Composer ni workflows activos.

El [modelo de entidades y relaciones](referencias/mer.png) representa las cinco tablas del esquema actual.

## Exportar, imprimir y guardar como PDF

Usa **Descargar CSV** para trabajar en Excel o LibreOffice con las filas de la consulta y sus filtros. El separador es punto y coma y el archivo usa UTF-8. Para imprimir, aplica los filtros y pulsa **Imprimir reporte** o **Guardar como PDF**. Ambos abren el diálogo del navegador; para exportar selecciona el destino PDF y dónde guardarlo. Usa A4, escala 100 % y desactiva encabezados y pies del navegador: el documento incorpora su propio título, período, fecha y numeración. Detalle de facturas utiliza horizontal; los demás, vertical.

La revisión visual de reportes del 10/11/2026 se realizó en Brave, incluidos ejemplos multipágina. El resultado puede variar con navegador, papel o escala. El comprobante también se imprime desde su vista.

## Protección y límites

Apache restringe el acceso al mismo equipo con `Require local` en `.htaccess`. Usa Apache con `AllowOverride All` y no publiques el aplicativo por Internet ni lo reenvíes mediante un túnel o proxy. PHP `-S` no aplica esas restricciones.

Los módulos verifican Términos y Condiciones antes de abrir SQL. El portal y los cambios de datos usan POST con tokens comprobados mediante `validarTokenCsrf()` en `php/config/seguridad.php`. En `php/config/consultas.php` se reutilizan las llamadas preparadas a procedimientos y el registro de auditoría. Cada módulo prepara sus consultas específicas mediante `mysqli::prepare()`. El historial guarda los motivos de cancelación. El MER y el esquema conservan cinco tablas. El documento del cliente sigue siendo un entero positivo de hasta 2.147.483.647.

Las evidencias de concurrencia y rendimiento corresponden a los escenarios medidos y a su fecha. El PAC registra el resultado, la evidencia y el estado por verificar de cada caso. Los registros anteriores siguen identificados por fecha.

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
  js/formularios.js                  Bloqueo temporal de envíos repetidos
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
  reportes/                         Cinco consultas, resumen, impresión y CSV
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
  PAC.docx / PAC.pdf                PAC 2.0
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
