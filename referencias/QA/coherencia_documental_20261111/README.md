# Evidencia de coherencia documental del 11 de noviembre de 2026

Resultados de la revisión descrita en [COHERENCIA_DOCUMENTAL.md](../../COHERENCIA_DOCUMENTAL.md). Código funcional final de referencia: `a87bb480a72c0b2190b4237d94cf8e6dbc86ad43`, que incorpora el portal de aceptación. También se conserva el MER de `1b8de60`. El procesamiento del portal y los módulos comerciales se mantienen.

| Archivo | Contenido y alcance |
| --- | --- |
| integridad-resultados.json | Versiones, páginas, figuras y SHA-256 de los cuatro pares Word/PDF; texto contrastado, sintaxis PHP y conservación de archivos. |
| http-resultados.json | Entorno y 40 comprobaciones HTTP aprobadas en una base nueva. La disponibilidad de una ruta no certifica todos sus requisitos. |
| enlaces-resultados.json | Destinos y anclas de wiki/ayuda y enlaces relativos Markdown; se cuentan referencias, no direcciones únicas. |
| navegador-resultados.json | Recorrido de los 29 artículos en Brave, tres vistas móviles, carga de imágenes y errores JavaScript. El visor de imágenes tiene un elemento vacío hasta abrirse, que no es un recurso roto. |
| portal-resultados.json | 15 casos del portal: navegación, aceptación/rechazo, revocación, cookie y límites observados. |
| herramientas-resultados.json | 59 capturas regeneradas, sintaxis del generador y rechazo de base no autorizada/navegador ausente. |

Los scripts externos y los renders se conservaron en el entorno de trabajo; no se reintroducen `tests/`, Composer ni workflows. La revisión visual cubrió las 81 páginas finales y vistas representativas de wiki. Las comprobaciones automáticas no certifican ausencia absoluta de defectos ni la aceptación completa del SRS.

Las evidencias de concurrencia, rendimiento y restauración de `../revision_mejoras/` son históricas del 10/11/2026. Sus huellas de documentos no sustituyen las huellas vigentes de esta carpeta.
