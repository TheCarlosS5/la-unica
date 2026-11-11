# Evidencia de coherencia documental del 11 de noviembre de 2026

Resultados de la revisión descrita en [COHERENCIA_DOCUMENTAL.md](../../COHERENCIA_DOCUMENTAL.md). Código funcional de referencia: `200ce3472ff9c299458bc0c331ebd9e9eedbfec5`; se conserva el aporte remoto `1b8de60`, que añade el MER sin alterar la lógica.

| Archivo | Contenido y alcance |
| --- | --- |
| integridad-resultados.json | Versiones, páginas, figuras y SHA-256 de los cuatro pares Word/PDF; texto contrastado, sintaxis PHP y conservación de archivos. |
| http-resultados.json | Entorno y 40 comprobaciones HTTP aprobadas en una base nueva. La disponibilidad de una ruta no certifica todos sus requisitos. |
| enlaces-resultados.json | Destinos y anclas de wiki/ayuda y enlaces relativos Markdown; se cuentan referencias, no direcciones únicas. |
| navegador-resultados.json | Recorrido de los 29 artículos en Brave, tres vistas móviles, carga de imágenes y errores JavaScript. El visor de imágenes tiene un elemento vacío hasta abrirse, que no es un recurso roto. |
| herramientas-resultados.json | 58 capturas regeneradas, sintaxis del generador y rechazo de base no autorizada/navegador ausente. |

Los scripts externos y los renders se conservaron en el entorno de trabajo; no se reintroducen `tests/`, Composer ni workflows. La revisión visual cubrió las 77 páginas finales y vistas representativas de wiki. Las comprobaciones automáticas no certifican ausencia absoluta de defectos ni la aceptación completa del SRS.

Las evidencias de concurrencia, rendimiento y restauración de `../revision_mejoras/` son históricas del 10/11/2026. Sus huellas de documentos no sustituyen las huellas vigentes de esta carpeta.
