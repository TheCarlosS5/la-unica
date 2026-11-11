# Plan de pruebas de La Única

Versión documental **1.1**, revisión **11/11/2026**. Referencia: **SRS 1.6**. Este plan actualiza el borrador 1.0 asociado al SRS 1.4, conservado en el historial Git. Responsable de QA según el SRS: Santiago Ortiz Claros.

## Objetivo y alcance

Verificar las reglas de clientes, productos, ventas, comprobantes, cancelaciones, cinco reportes, historial y ayuda. Incluye integridad, entradas, CSRF, salida HTML, diagnóstico, rendimiento RNF-02, concurrencia RNF-04, impresión/PDF y revisión de pantallas estrechas. El estado Pendiente, registro de pagos, autenticación, roles y factura electrónica están fuera de alcance.

Las pruebas no constituyen una auditoría de penetración ni autorización para desplegar el sistema públicamente. La configuración de acceso local y las restauraciones deben comprobarse en el entorno correspondiente.

## Entorno y datos

Usar una carpeta y base nuevas, separadas de datos operativos, con esquema de referencias/db.sql. Para verificaciones automatizadas externas se utiliza un nombre con prefijo la_unica_test_. Importar uno de los conjuntos ficticios ajustando su USE; no acumular conjuntos ni reimportar sobre una base existente.

Registrar PHP, motor SQL, puerto real, navegador, fecha y commit. La revisión de referencia utiliza Windows/Laragon, PHP 8.3.33, MariaDB 13.0.2, SQL en 3307 y Brave. El mínimo distribuido sigue siendo PHP 8.2 y MySQL 8.0.16 o MariaDB compatible; estos valores no obligan a otra instalación a usar el mismo puerto.

## Estrategia

| Área | Comprobación |
| --- | --- |
| Funcional | Ejecutar los pasos de casos-de-prueba.md y comparar resultados con el SRS. |
| Integridad | Comprobar stock, precios históricos, rollback y reposición única, también con solicitudes simultáneas. |
| Seguridad | CSRF, validación en servidor, escape HTML, mensajes generales y bloqueo de archivos internos. |
| Documentación | Rutas reales, versiones iguales en Word/PDF/visores, imágenes y numeración de las guías. |
| Rendimiento | Con 1000 clientes, productos y facturas, al menos el 95 % de las búsquedas por ID en 2 s o menos. Registrar muestra y entorno. |
| Navegador | Formulario dinámico, confirmaciones, documentos integrados, impresión/PDF, video y pantallas estrechas. |

## Registro y criterios de salida

Cada resultado incluye ID de caso, requisito, fecha, commit, entorno, datos, esperado, obtenido, estado y evidencia. Los estados Aprobado/Fallido se asignan solo a ejecuciones registradas; Por verificar no significa defecto ni aceptación.

Para aceptación completa: ejecutar el 100 % de los casos críticos planificados, resolver o justificar los bloqueantes, conservar evidencia y completar la matriz de criterios. La revisión del equipo/instructor debe quedar registrada. No deducir el 100 % del SRS a partir de una batería parcial ni de una publicación de Git.

## Evidencia y regresión

Los resultados del 10/11/2026 se conservan como históricos. La revisión del 11/11/2026 repite los flujos HTTP en una base nueva y verifica documentación e imágenes. main no contiene tests, Composer ni workflows activos; no se exige ejecutar comandos que no se distribuyen. Repetir los criterios afectados al cambiar consultas, transacciones, entorno o documentos.
