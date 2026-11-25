# QA de La Única

Documentación de calidad alineada al **SRS 1.6** y a la operación de main. Alineación: **23/11/2026**, sobre el corte `e527b75` del 22/11. Responsabilidad de QA documentada en el proyecto: **Santiago Ortiz Claros**; esta revisión no inventa una aceptación individual del responsable.

- [PAC 2.0](../PAC.pdf) y [regresiones del grupo 5](resultados_grupo_5.json): matriz de aceptación, resultados de acceso y CSV. Los casos CSV de este registro se ejecutaron con datos simulados.
- [Plan de pruebas](plan-de-pruebas.md): alcance y criterios vigentes.
- [Casos de prueba](casos-de-prueba.md): pasos reproducibles, requisitos y estados identificados.
- [Checklist de seguridad](checklist-seguridad.md): controles observados y comprobaciones operativas pendientes.
- [Reportes de hallazgos](reportes-bugs/): estado actual separado del antecedente histórico.
- [Coherencia documental](../COHERENCIA_DOCUMENTAL.md): versión, cambios y verificaciones del 11/11/2026.
- [Revisión técnica anterior](../REVISION_TECNICA.md): registro histórico del 10/11/2026.
- [revision_mejoras](revision_mejoras/): resultados e imágenes de verificaciones anteriores, conservados como evidencia histórica. Sus hashes identifican los documentos de aquel corte, no los documentos vigentes.

Desde el proyecto se puede ejecutar `php tests/seguridad.php` sin SQL. Las pruebas HTTP y de navegador se ejecutan por separado. Main no tiene workflows activos. Un resultado histórico aprobado no sustituye ejecutar los casos en el equipo de entrega.
