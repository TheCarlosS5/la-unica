# Revisión técnica de la-unica-2

Fecha: 10 de noviembre de 2026.

## Alcance y referencia

Se revisaron PAC y manual técnico frente al SRS 1.5 y al código local. No se modifica el SRS ni el código funcional. Las correcciones documentales no certifican las funciones pendientes.

## Identificación de la referencia

- SRS.docx: SHA-256 `acefe2122b17cbc2a5b7194d5704b89d58c1e95cf1f2c450fbb483a693dfcbe6`.
- SRS.pdf: SHA-256 `27ceb77dd9d798bbbdd2ebd2f8ae0d7e23c1a14220b8c929d6aa3dd591a291ae`.
- db.sql: SHA-256 `dc57f3c9d0b06b6a1fcd239b905c7180e31b2e499cb4c40d9a9861f76ac7cbef`.

## Correcciones

- Sustituida la afirmación de cumplimiento total por resultados y límites verificables.
- Separados valores de instalación y configuración observada: PHP 8.3.33, MariaDB 13.0.2, puerto 3307.
- Aclarada la base exclusiva y el hecho de que cambiar la carpeta no separa los datos.
- Documentadas cinco tablas, cuatro procedimientos, estados Pagado/Cancelado y cancelación solo el mismo día.
- Registrada la discrepancia entre diagnóstico de conexión y RNF-06; no se ocultó ni se relajó el SRS.
- Reemplazada la limpieza destructiva de la entrega por separación de demostración y operación.
- Incorporados respaldo con rutinas, restauración en otra base, permisos, DEFINER y comprobación de resultados.
- PAC con nueve acciones, criterios de cierre y estados sustentados. Ninguna acción completa se da por cerrada.

## Pruebas ejecutadas

| N.º | Comprobación | Resultado | Referencia |
| --- | --- | --- | --- |
| 1 | HTTP inicio | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 2 | HTTP php/clientes/ver_clientes.php | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 3 | HTTP php/productos/ver_productos.php | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 4 | HTTP php/facturas/ver_facturas.php | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 5 | HTTP php/facturas/crear_factura.php | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 6 | HTTP php/reportes/ver_reportes.php | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 7 | HTTP php/reportes/ventas.php | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 8 | HTTP php/reportes/unidades_vendidas_por_producto.php | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 9 | HTTP php/reportes/existencias_productos.php | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 10 | HTTP php/historial/acciones.php | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 11 | HTTP php/historial/errores.php | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 12 | HTTP php/sobre_nosotros/index.php | APROBADA | RF-01 a RF-16 (disponibilidad) |
| 13 | Rechazo sin CSRF | APROBADA | RNF-03 |
| 14 | Alta de cliente y fecha automática | APROBADA | RF-01 |
| 15 | Documento duplicado rechazado | APROBADA | RF-01, RF-11 |
| 16 | Nombre vacío rechazado | APROBADA | RF-11 |
| 17 | Edición y búsqueda de cliente | APROBADA | RF-02 |
| 18 | Alta de producto con ID automático | APROBADA | RF-03 |
| 19 | Stock negativo rechazado | APROBADA | RF-11, RNF-04 |
| 20 | Venta y descuento de stock | APROBADA | RF-05, RF-06, RF-08 |
| 21 | Líneas repetidas agrupadas y total exacto | APROBADA | RF-05, RF-07 |
| 22 | Factura conserva nombres y precio histórico | APROBADA | RF-06, RF-07, RF-09 |
| 23 | Edición y búsqueda de productos | APROBADA | RF-04 |
| 24 | Venta sin stock revierte todos los cambios | APROBADA | RF-06, RF-08, RNF-04 |
| 25 | Bloqueo de borrado con facturas | APROBADA | RF-12 |
| 26 | Confirmación previa sin cancelar | APROBADA | RF-10 |
| 27 | Cancelación repone stock | APROBADA | RF-10 |
| 28 | Cancelación repetida no duplica inventario | APROBADA | RF-10, RNF-04 |
| 29 | Factura anterior no se puede cancelar | APROBADA | RF-10 |
| 30 | Reporte Ventas por factura | APROBADA | RF-13 |
| 31 | Reporte Ventas por dia | APROBADA | RF-13 |
| 32 | Reporte Ventas por semana | APROBADA | RF-13 |
| 33 | Reporte Ventas por mes | APROBADA | RF-13 |
| 34 | Reporte Ventas por anio | APROBADA | RF-13 |
| 35 | Reporte diario excluye canceladas y coincide con SQL | APROBADA | RF-13 |
| 36 | Unidades excluyen canceladas | APROBADA | RF-13 |
| 37 | Eliminación de cliente y producto sin ventas | APROBADA | RF-12 |
| 38 | Historial registra crear, editar, eliminar y cancelar | APROBADA | RF-14 |
| 39 | Historial se consulta con filtro | APROBADA | RF-14 |
| 40 | Fallo de conexión produce diagnóstico controlado | APROBADA | RF-15 |
| 41 | Historial de errores disponible sin base | APROBADA | RF-15 |
| 42 | Sintaxis de 27 archivos PHP | APROBADA | RNF-08 |
| 43 | Respaldo restaurado en otra base con cinco tablas iguales | APROBADA | PAC AM-01 |
| 44 | Restauración conserva cuatro procedimientos | APROBADA | PAC AM-01 |
| 45 | Datos intactos en la_unica | APROBADA | Aislamiento |
| 46 | Datos intactos en la_unica_2_demo_20261110 | APROBADA | Aislamiento |
| 47 | Acceso HTTP logs/errores.log | APROBADA | RF-16, RNF-03 |
| 48 | Acceso HTTP referencias/manual.pdf | APROBADA | RF-16, RNF-03 |
| 49 | Acceso HTTP referencias/manual_tecnico.pdf | APROBADA | RF-16, RNF-03 |
| 50 | Acceso HTTP referencias/PAC.pdf | APROBADA | RF-16, RNF-03 |
| 51 | Acceso HTTP assets/video/1.mp4 | APROBADA | RF-16, RNF-03 |
| 52 | Proyecto original sin cambios de archivos | APROBADA | Aislamiento |

## Límites

- No se probó la interacción visual de Brave ni JavaScript, impresión, audio o pantalla completa.
- No se midió RNF-02 con 1.000 registros ni ventas simultáneas de RNF-04.
- La restricción al equipo local no está certificada.
- La prueba de respaldo fue puntual y sobre una base de pruebas; no acredita una programación semanal.
- HTTP de recursos se verificó antes de sustituir documentos; la revisión final comprobará los archivos actualizados.

## Integración Git

La carpeta del aplicativo no contiene Git. Se preparó una copia Git independiente para revisar solo la documentación. No se publica hasta que el usuario confirme la organización MAIN, DEV y QA y la rama de destino. No se incluyen credenciales, volcados SQL, logs ni el video.

## Revisión final de documentos

- Manual técnico: versión 1.1, 8 páginas; PAC: versión 1.1, 7 páginas.
- Word actualizado y exportado con Microsoft Word 16.0; revisión visual de las 15 páginas mediante el rasterizador de la habilidad. LibreOffice no está disponible en el runtime de Windows.
- Sin páginas vacías, tablas partidas ni texto cortado en la revisión final.
- Sustitución limitada a los cuatro documentos solicitados, al informe de revisión y a la referencia de versiones en el README local.

- manual_tecnico.docx: copia local y HTTP coinciden con el archivo validado
- manual_tecnico.pdf: copia local y HTTP coinciden con el archivo validado
- PAC.docx: copia local y HTTP coinciden con el archivo validado
- PAC.pdf: copia local y HTTP coinciden con el archivo validado
- SRS Word y PDF originales sin cambios
