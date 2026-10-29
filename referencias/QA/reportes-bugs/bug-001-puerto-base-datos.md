# Reporte de Bug: Puerto de conexión a base de datos

**ID del Bug:** BUG-001
**Módulo:** Configuración / Base de datos (`php/config/conexion.php`)
**Descripción:** La conexión a la base de datos tiene "quemado" (hardcoded) el puerto 5000. Esto impide que el sistema funcione por defecto en entornos locales estándar (como Laragon o XAMPP), los cuales utilizan el puerto 3306 por defecto.

**Pasos para reproducir el error:**
1. Instalar el proyecto en un entorno local con el puerto por defecto de MySQL (3306).
2. Importar la base de datos `db.sql` correctamente.
3. Abrir el proyecto en el navegador (`http://localhost/la-unica`).
4. Ocurre el error en pantalla: "Error de conexión a la base de datos".

**Resultado Esperado:** El sistema debe conectar y cargar la página de inicio sin problemas, usando el puerto estándar 3306 o leyendo el puerto desde un archivo de configuración externo.
**Resultado Actual:** La página lanza un error de conexión fatal porque intenta buscar MySQL en el puerto 5000.
**Severidad:** Alta (Bloquea la ejecución del proyecto en otros equipos).
**Evidencia:** Línea 19 de `php/config/conexion.php`:
`$conexion = new mysqli('127.0.0.1', 'root', '', 'la_unica', 5000);`

**Nota del QA:** Se deja documentado para revisión con el equipo. No se modifica el código en esta rama para mantener la separación entre QA y Desarrollo.