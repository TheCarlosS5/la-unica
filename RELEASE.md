# Publicar La Única

La versión actual procede de la-unica-2: cinco tablas, cuatro procedimientos, ventas Pagado y Cancelado, cancelación del mismo día y cinco reportes con impresión/PDF. No hay crédito, cobros pendientes ni migración automática desde el esquema anterior.

## Preparar la entrega

1. Identifica carpeta, rama y base; conserva un respaldo y mantén independientes las otras copias.
2. Revisa git status y la diferencia completa. Excluye conexion.local.php, credenciales, logs y datos reales.
3. Comprueba la sintaxis de los archivos PHP y JavaScript modificados. Verifica los flujos afectados con los [casos de prueba manuales](referencias/QA/casos-de-prueba.md) en una instalación aislada con una base nueva.
4. Revisa los cinco reportes y sus PDF, comprobante, confirmaciones y formulario dinámico. Comprueba el video, audio y controles en el equipo de presentación.
5. Mantén sincronizados manual técnico y PAC 1.3 en Word y PDF. Conserva SRS 1.5 y manual de usuario 2.0 salvo revisión explícita.
6. Comprueba .htaccess y el acceso exclusivamente local previsto para Apache y SQL.

## Integrar y verificar

El equipo conserva main, dev y QA. Acuerda el flujo antes de modificar ramas compartidas. Para esta entrega se autorizó publicar la-unica-2 directamente en main; esto no autoriza borrar o sobrescribir otras ramas.

Integra sin force push. Si main remoto avanzó, revisa sus cambios antes de incorporar los propios. Después del push confirma que el SHA remoto coincide con el commit verificado.

## Etiquetas y releases

Actualizar main no crea una etiqueta ni un release. Cuando el equipo defina una versión y autorice publicarla, etiqueta el commit comprobado y describe sus cambios reales. Una corrección compatible aumenta el parche; una función compatible, la versión menor; una incompatibilidad, la mayor.

Comprueba que el paquete incluya código, esquema y documentos vigentes y excluya configuración privada y datos operativos. Publicar código no equivale a desplegar la aplicación en Internet ni a aceptar todos los requisitos del SRS.
