# Plan de Pruebas — La Única

## 1. Información del documento
- **Proyecto:** La Única
- **Versión del documento:** 1.0
- **Autor:** Santiago — Responsable de Calidad, Pruebas y Seguridad
- **Estado:** Borrador inicial
- **Referencia:** SRS v1.4 del proyecto

## 2. Objetivo
Definir la estrategia, el alcance y los recursos para verificar que la aplicación **La Única** cumple con los requisitos especificados en el SRS v1.4 y no presenta defectos críticos en sus módulos de clientes, productos, ventas, facturas y reportes, incluyendo una revisión de seguridad básica del código PHP/MySQL.

## 3. Alcance

### 3.1 Dentro del alcance
- Módulo de **Clientes** (CRUD).
- Módulo de **Productos** (CRUD).
- Módulo de **Ventas** (creación de facturas y descuento de stock).
- Módulo de **Facturas** (consulta, impresión y cancelación con reposición de inventario).
- Módulo de **Reportes**.
- **Revisión de seguridad** del código: inyección SQL, XSS, validación de entradas y manejo de errores.

### 3.2 Fuera del alcance
- Autenticación y roles de usuario (no implementados en esta versión).
- Despliegue en producción.
- Pruebas de rendimiento y carga.
- Pruebas en dispositivos móviles.

## 4. Estrategia de pruebas

| Tipo de prueba | Descripción | Responsable |
|---|---|---|
| Funcionales | Verificar cada funcionalidad contra el SRS. | QA |
| Regresión | Re-ejecutar casos tras cada cambio de código. | QA |
| Integridad de datos | Validar transacciones y consistencia del stock. | QA |
| Seguridad | Revisar validación, inyección SQL, XSS y manejo de errores. | QA |
| Humo | Verificación rápida antes de cada Pull Request. | QA |

## 5. Entorno de pruebas
- **SO:** Windows
- **Servidor local:** Laragon (Apache + MySQL + PHP)
- **Navegador:** por definir
- **Base de datos:** script `referencias/db.sql`
- **Datos de prueba:** se requiere crear un set mínimo, ya que `db.sql` no incluye datos de ejemplo.

## 6. Criterios de aceptación
- 100% de los casos de prueba críticos ejecutados.
- 0 defectos críticos o bloqueantes abiertos.
- 0 hallazgos de seguridad de severidad alta sin mitigar o documentar.

## 7. Criterios de salida
- Informe de resultados entregado.
- Matriz de trazabilidad SRS ↔ casos de prueba completa.

## 8. Riesgos identificados
- **Falta de datos de ejemplo en `db.sql`** → crear set mínimo de datos de prueba.
- **Cambios concurrentes entre ramas** → ejecutar pruebas de regresión antes de cada merge.
- **Ausencia de autenticación** → revisar que no existan endpoints o vistas expuestas innecesariamente.

## 9. Entregables
- Plan de pruebas (este documento).
- Casos de prueba por módulo.
- Informes de revisión de código.
- Checklist de seguridad.
- Matriz de trazabilidad.
- Reporte de bugs.