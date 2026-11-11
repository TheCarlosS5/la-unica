# Revisión de coherencia documental de La Única

Revisión del **11/11/2026** sobre la operación de `main`, tomando como base el commit `200ce3472ff9c299458bc0c331ebd9e9eedbfec5`. Se contrastaron README, las 29 páginas de la wiki elegida, SRS, los dos manuales, PAC, documentación de QA, esquema SQL y páginas de Sobre nosotros. Los registros de revisiones anteriores conservan su fecha y alcance.

Esta revisión corrige documentación y herramientas de capturas. No cambia la lógica de clientes, productos, ventas, cancelaciones, reportes ni configuración del aplicativo. La carpeta original `la-unica` y su rama independiente se conservan.

Durante la revisión se incorporó el aporte remoto `1b8de60` de Samuel, que añade [mer.png](mer.png). Sus cinco tablas, campos y relaciones coinciden con `db.sql`; se conserva la imagen recibida sin editarla.

## Documentos coordinados

| Documento | Versión vigente | Páginas PDF | Archivos |
| --- | --- | --- | --- |
| SRS | 1.6 | 21 | [Word](SRS.docx) · [PDF](SRS.pdf) |
| Manual del usuario | 2.1 | 41 | [Word](manual.docx) · [PDF](manual.pdf) |
| Manual técnico | 1.4 | 8 | [Word](manual_tecnico.docx) · [PDF](manual_tecnico.pdf) |
| PAC | 1.4 | 7 | [Word](PAC.docx) · [PDF](PAC.pdf) |

Cada PDF se exportó desde el Word de su misma versión. SRS y manual del usuario tienen índices actualizables de Word, con páginas recalculadas. Se revisaron visualmente las 77 páginas renderizadas. El manual contiene 26 figuras de la interfaz actual, incluidos los dos reportes que faltaban y una vista de impresión.

Las versiones son documentales: no se crea una etiqueta ni una versión funcional de software. Se conservan los 16 requisitos funcionales, los ocho no funcionales y las metas de rendimiento e integridad del SRS; no se reducen para presentar una aprobación completa.

## Diferencias corregidas

| Tema | Diferencia encontrada | Referencia vigente |
| --- | --- | --- |
| Reportes | La wiki describía siete opciones y consultas heredadas. El manual omitía dos reportes. | Cinco: Ventas, Unidades vendidas, Existencias, Clientes y cantidad de facturas y Detalle; más resumen diario. Las rutas antiguas son compatibilidad, no funciones adicionales. |
| Estados y cancelación | Había indicaciones de Pendiente, cobro posterior o cancelación sin confirmación. | Pagado y Cancelado. Se confirma la cancelación y solo se permite el mismo día en America/Bogota; repone stock una vez. |
| Clientes | Algunas instrucciones pedían introducir la fecha de registro. | Fecha automática; documento inmutable y edición del nombre. |
| Productos y ventas anteriores | La guía mostraba la unidad bloqueada después de vender. | La unidad puede editarse. El comprobante conserva los nombres, unidad y precio guardados al vender. |
| Datos | Había referencias a vistas, scripts y funciones de otra estructura. | Cinco tablas y cuatro procedimientos; funciones y archivos verificados en el código actual. |
| Instalación | Puerto, cuenta, extensiones o base se presentaban como universales. | PHP 8.2 y extensiones necesarias; puerto real, configuración privada y base exclusiva. `DB_*` tiene prioridad; no se carga `.env` automáticamente. |
| Seguridad y errores | La wiki atribuía al código opciones de cookies y cabeceras que no configura; confundía el mensaje general con el historial técnico. | Controles observados separados de configuración operativa pendiente. Mensajes generales sin detalles internos; diagnóstico en historial local, sin roles. |
| Ayuda integrada | Versiones y recursos de Sobre nosotros no coincidían en todas las guías. | SRS, manuales, PAC, wiki y video. La wiki y el historial de errores omiten SQL; los visores de Sobre nosotros sí cargan la conexión. |
| Pruebas y archivos retirados | Se anunciaban suite, Composer, workflows y enlaces a archivos que ya no se distribuyen. | Plan y casos actuales; resultados previos identificados como históricos. No se restauran los archivos retirados del repositorio. |
| Impresión | Las guías no explicaban el destino PDF ni la orientación. | Diálogo del navegador, A4, escala 100 %, sin encabezados/pies externos; Detalle horizontal, demás reportes verticales. |

## Wiki e imágenes

Se conserva la wiki ilustrada elegida y sus pasos con recuadros naranjas numerados. Permanecen las 51 imágenes originales de la carpeta; las capturas utilizadas se regeneraron contra la interfaz actual y se añadieron nueve recursos, para un total de 60 WebP. Las imágenes heredadas sin referencias en las guías activas se conservan y no representan funciones adicionales.

El generador usa Playwright y Sharp y abre un PHP independiente en 127.0.0.1. Exige una base de pruebas con prefijo `la_unica_test_`, cargada con el esquema actual y ocho facturas ficticias. El recorrido registra un cliente, guarda una venta y la cancela: se usa una base nueva en cada ejecución. El conjunto de la wiki es alternativo a los diez clientes y treinta productos de `referencias/`; no se acumulan sobre datos de trabajo.

## Comprobaciones del 11 de noviembre

Los resultados detallados se conservan en [QA de esta revisión](QA/coherencia_documental_20261111/). Los scripts de verificación permanecen fuera de la distribución de producción.

- **40 comprobaciones HTTP aprobadas**, en una base nueva e independiente: disponibilidad, formularios, CSRF, validación, venta, copia histórica, falta de stock, cancelación única/día anterior, eliminación protegida, reportes e historial. Incluye comprobación de sintaxis de los 81 PHP.
- **81 archivos PHP válidos** en la comprobación final; sintaxis del generador de capturas válida.
- **29 artículos de wiki y seis páginas de ayuda** comprobados por HTTP: 1.653 referencias locales y 36 enlaces Markdown sin destinos ni anclas inexistentes. Brave recorrió los 29 artículos y tres vistas móviles sin errores JavaScript, imágenes rotas ni desbordamiento de la página. Las tablas anchas conservan su desplazamiento horizontal. Los parámetros `#view=FitH` pertenecen al visor PDF y no son anclas HTML.
- Word/PDF contrastados por texto, versiones y huellas SHA-256; todas las páginas renderizadas revisadas visualmente. El visor de usuario muestra el PDF vigente de 41 páginas.
- **49 archivos funcionales y recursos base sin cambios**, comparados por SHA-256 con el inicio de esta revisión. No cambian el esquema, CSS, JavaScript ni la lógica de los módulos.
- **1.941 archivos de la copia original conservados**, comparados con su respaldo; no se cambia su rama ni su base de datos.

Las pruebas de concurrencia, rendimiento con 1.000 registros y restauración conservadas en `QA/revision_mejoras/` corresponden al **10/11/2026**. No se presentan como repetidas hoy ni como una suite o CI activa en main. Sus hashes de documentos identifican aquel corte; las huellas vigentes están en la carpeta de evidencia de esta revisión.

## Seguimiento

La aceptación formal del equipo y del instructor sigue pendiente. El PAC mantiene el seguimiento de respaldos, acceso exclusivamente local, conservación de logs y aceptación visual/multimedia completa. No se marca todo el SRS como aprobado a partir de estas comprobaciones parciales. Una publicación correcta en GitHub tampoco constituye un despliegue público del aplicativo.
