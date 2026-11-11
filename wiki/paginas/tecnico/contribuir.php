<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="ramas">Ramas del equipo</h2>
<p>El repositorio conserva main, dev y QA. Antes de integrar, identifica la copia, la rama y su base de datos. El flujo habitual debe acordarse con el equipo. La actualización directa de esta copia a main fue autorizada expresamente para la entrega; no implica autorización permanente para borrar o sobrescribir otras ramas.</p>
<h2 id="verificacion">Antes de integrar</h2>
<ol><li>Revisa git status y la diferencia completa; no incluyas configuración privada, logs ni datos reales.</li><li>Ejecuta la sintaxis y los tests de tests/README.md sobre una base nueva y exclusiva.</li><li>Comprueba la interfaz y los PDF afectados. Mantén sincronizados Word, PDF y etiquetas de versión.</li><li>Integra sin force push y espera las comprobaciones de GitHub Actions del commit publicado.</li></ol>
<p>Conserva comentarios de propósito y decisiones de negocio; evita repetir banners genéricos en cada bloque.</p>
