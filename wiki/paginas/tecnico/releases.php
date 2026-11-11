<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<h2 id="preparar-la-entrega">Preparar la entrega</h2>
<ol><li>Identifica el commit, la copia y su base; respalda antes de cambiar datos o esquema.</li><li>Ejecuta el plan de QA en una instalación independiente y registra resultados reales.</li><li>Comprueba los cinco reportes, sus PDF, el comprobante, documentos y video.</li><li>Revisa la diferencia de Git y excluye configuración privada y datos reales.</li><li>Confirma que SRS 1.6, manual de usuario 2.1, manual técnico/PAC 1.4 y wiki describan la misma operación.</li></ol>
<h2 id="publicación">Publicación</h2>
<p>Integra sin sobrescribir trabajo ajeno ni usar force push. Comprueba el SHA remoto. main no tiene workflows activos en esta distribución; una publicación no permite afirmar que existan comprobaciones automáticas actuales.</p>
<h2 id="etiquetas-y-versiones">Etiquetas y versiones</h2>
<p>Actualizar main no crea una etiqueta ni un release. Cuando el equipo acuerde un número y autorice publicarlo, etiqueta el commit verificado y escribe notas de sus cambios reales. No existe RELEASE.md vigente; esta página y el README describen la preparación de entrega.</p>
