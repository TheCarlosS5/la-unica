# La Única: estructura PHP

index.php dirige a main.php; el panel carga desde php/inicio/main.php.

## Organización

- php/config/conexion.php: conexión MySQLi (127.0.0.1, puerto 8000, base la_unica).
- php/includes/: menú y pie compartidos.
- php/inicio/: panel principal.
- php/clientes/: crear_cliente.php, ver_clientes.php, editar_cliente.php y eliminar_cliente.php.
- php/productos/: crear_producto.php, ver_productos.php, editar_producto.php y eliminar_producto.php.
- assets/css/estilos.css: estilos compartidos.
- referencias/: archivos de referencia y db.sql.

Importa referencias/db (1).sql antes de usar. La estructura respeta sus columnas; clientes.id_cliente se ingresa manualmente y productos.id_producto se autogenera. Los borrados comprueban las relaciones con facturas y detalle_facturas.

Para Laragon, inicia Apache y MySQL y abre http://localhost/la_unica/. Ajusta usuario y contraseña en php/config/conexion.php si tu configuración es distinta. No se ejecutó la aplicación durante este cambio.

La carpeta php/_anterior/app conserva la versión previa como respaldo, no participa en las rutas actuales.

## Front

Diseño de papel cálido con letra manuscrita para el CRUD de clientes y productos. Los PHP conservan su lógica; solo cambió cómo se ven.

- assets/css/estilos.css: todo el estilo. Colores, bordes y sombras están en las variables de :root (arriba del archivo).
- assets/fonts/: Architects Daughter (títulos) y Patrick Hand (texto), alojadas en el proyecto para que funcionen sin internet (licencias en LICENCIAS.txt).
- php/includes/menu.php y pie.php: menú lateral y pie compartidos, con el nuevo diseño.
- php/inicio/main.php: portada con acceso a Clientes y Productos.
