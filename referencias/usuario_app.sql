-- Archivo: referencias/usuario_app.sql
-- Propósito: crear un usuario MySQL exclusivo de la aplicación, con permisos solo sobre la_unica.
-- Uso: ejecútalo como root después de importar db.sql, cambiando antes la contraseña. Luego copia
-- php/config/conexion.local.example.php como conexion.local.php con el mismo usuario y contraseña.
-- El usuario solo puede conectarse desde el propio equipo (localhost) y no puede crear ni borrar tablas.

CREATE USER 'la_unica_app'@'localhost' IDENTIFIED BY 'cambia-esta-contrasena';
CREATE USER 'la_unica_app'@'127.0.0.1' IDENTIFIED BY 'cambia-esta-contrasena';

GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON la_unica.* TO 'la_unica_app'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON la_unica.* TO 'la_unica_app'@'127.0.0.1';

FLUSH PRIVILEGES;
