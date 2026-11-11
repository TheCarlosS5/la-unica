<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="ramas">Ramas y copia de trabajo</h2>
<p>El repositorio utiliza main, dev y QA. Identifica la rama y la base de datos antes de modificar algo. La carpeta la-unica original y esta copia de revisión la-unica-2 se conservan independientes.</p>
<h2 id="antes-de-integrar">Antes de integrar</h2>
<ol><li>Revisa git status y la diferencia completa; conserva el trabajo pendiente de otros.</li><li>Prueba en una base nueva con datos ficticios. No publiques credenciales, logs, dependencias ni respaldos operativos.</li><li>Revisa reglas, enlaces, capturas numeradas y versiones Word/PDF.</li><li>Integra según el acuerdo del equipo y sin force push. Comprueba el commit remoto.</li></ol>
<h2 id="pruebas">Pruebas y documentación</h2>
<p>Los pasos actuales están en referencias/QA/plan-de-pruebas.md y casos-de-prueba.md. Esta distribución no incluye una suite automatizada ni workflows; no anuncies composer test o GitHub Actions vigentes. El historial conserva los scripts y resultados anteriores.</p>
<h2 id="estilo">Estilo</h2>
<p>Conserva la estructura de módulos y comentarios de propósito o reglas. No conviertas ejemplos antiguos de crédito, pagos o migraciones en instrucciones de esta versión. Cada captura debe corresponder al texto que la acompaña.</p>
