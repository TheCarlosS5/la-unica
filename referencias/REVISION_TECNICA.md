# Revisión técnica de las mejoras

Fecha: 10 de noviembre de 2026. Copia: la-unica-2. Base exclusiva de pruebas; no se modifican los datos de las copias de trabajo.

## Cambios comprobados

- Configuración separada en entorno, valores privados, utilidades, sesión, consultas y diagnóstico. Los módulos conservan su punto de entrada conexion.php.
- Fallos de conexión y configuración con HTTP 500 y mensaje general; detalle en log local. Wiki e historial funcionan sin SQL.
- Wiki de 29 artículos actuales. Retiradas las instrucciones antiguas de crédito, pagos, tablas y ramas que no corresponden a esta versión. El historial Git conserva ese material.
- Comentarios depurados: se mantienen encabezados de propósito y explicaciones de reglas/transacciones, eliminando banners genéricos duplicados.
- Manual técnico y PAC 1.3 sincronizados en Word/PDF. AM 09 cerrada con evidencia; las tareas parciales permanecen identificadas. SRS 1.5 y manual de usuario 2.0 conservados.

## Validación local

PHP 8.3.33 y MariaDB 13.0.2, Windows/Laragon. Ocho pruebas PHPUnit con 16 aserciones y 40 comprobaciones HTTP aprobadas. Sintaxis de 84 archivos PHP válida.

Diez carreras de venta por la última unidad y diez de cancelación simultánea aprobadas. Dos servidores y dos sesiones independientes compiten por una fila retenida por otra conexión. Cada caso produjo una sola venta y una sola reposición, sin stock negativo ni doble acción de cancelación.

Se cargaron 1000 clientes, productos, facturas y detalles ficticios. Las búsquedas HTTP verifican contenido y tiempo. Todas las 60 consultas estuvieron por debajo de 2 s. p95 es el percentil por rango más cercano sobre veinte mediciones por módulo.

| Módulo | Consultas | p95 ms | Máximo ms |
| --- | ---: | ---: | ---: |
| clientes | 20 | 31.06 | 36.94 |
| productos | 20 | 31.2 | 32.04 |
| facturas | 20 | 31.32 | 31.51 |

También aprobaron disponibilidad y enlaces de los 29 artículos, rechazo de rutas ajenas, escape del buscador, fallo SQL seguro, configuración inválida e historial/wiki sin SQL. La evidencia ampliada está en [advanced-results.json](QA/revision_mejoras/advanced-results.json). La revisión anterior se conserva como [informe histórico](QA/revision_mejoras/revision_inicial.md), no como instrucciones vigentes.

## Reproducción y límites

Seguir [tests/README.md](../tests/README.md) sobre una base nueva exclusiva. GitHub Actions repite la secuencia en MariaDB 11.4 y conserva el resultado ampliado como artefacto.

La concurrencia probada corresponde a dos solicitudes por carrera y el rendimiento a 1000 registros por entidad, con búsquedas secuenciales en este entorno. No se certifican otras cargas ni todas las versiones de SQL. La aceptación completa requiere la revisión del equipo de interfaz, multimedia y acceso local, además del seguimiento de respaldos y mantenimiento del PAC. La aplicación sigue sin autenticación ni roles y debe utilizarse localmente.

Revisión visual adicional en Brave: wiki de escritorio y móvil, apertura/cierre de menú, selección de producto, cálculo y agregado de filas sin persistir ventas, disponibilidad de los dos PDF y bloqueo Apache de configuración, Git, logs y pruebas. No se detectaron errores JavaScript. Manual técnico de ocho páginas y PAC de siete páginas revisados visualmente.
