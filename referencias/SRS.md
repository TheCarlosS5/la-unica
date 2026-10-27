# Especificación de requisitos de software: La Única

**Versión vigente:** 1.3 — 26 de octubre de 2026  
**Fuente normativa:** [SRS.pdf](SRS.pdf). Este archivo Markdown está sincronizado con esa versión para facilitar la consulta del equipo.

## Control de versiones

| Versión | Fecha | Estado | Descripción |
| --- | --- | --- | --- |
| 1.0 | 05/10/2026 | Reemplazada | Creación del SRS y planteamiento inicial de requisitos funcionales y no funcionales. |
| 1.1 | 06/10/2026 | Reemplazada | Revisión del modelo de datos y de los requisitos, reglas de negocio y formatos. |
| 1.2 | 26/10/2026 | Reemplazada | Alineación de entidades, campos, estados, límites e informes con `db.sql`. |
| 1.3 | 26/10/2026 | Vigente | Actualización del stack tecnológico a PHP y MySQL. |

## Resumen ejecutivo

La Única será una aplicación web local para registrar clientes y productos, gestionar facturas y consultar reportes. La base `la_unica` se crea con cuatro tablas (`clientes`, `facturas`, `productos` y `detalle_facturas`), tres vistas y cinco procedimientos almacenados definidos en `db.sql`.

La aplicación gestionará clientes identificados por `id_cliente`, productos con unidad de medida de texto, existencias enteras y precios vigentes, y facturas con detalles de cantidades enteras. El esquema no guarda el precio histórico en cada detalle: los subtotales y totales se calculan con el precio actual del producto. La lógica de venta, actualización de existencias y cancelación corresponde a la aplicación PHP.

El estado predeterminado de una factura en el esquema es `Pendiente`. Al completar una venta, la aplicación guardará explícitamente `Pagado`; la cancelación permitirá pasar de `Pagado` a `Cancelado`. El procedimiento de pendientes consulta las facturas cuyo estado sea `Pendiente`.

## 1. Introducción

### 1.1 Planteamiento del problema

Los registros manuales o dispersos de clientes, productos y ventas dificultan localizar información y controlar existencias. La elaboración manual de facturas puede causar errores en cantidades, precios y cálculos. La Única centralizará esta información y relacionará cada factura con su cliente y sus productos mediante `facturas` y `detalle_facturas`. El detalle registra la cantidad, pero no conserva el precio histórico; las consultas obtienen `precio_unitario` vigente desde `productos`.

### 1.2 Propósito

Definir los requisitos de una aplicación web básica para registrar clientes y productos, generar facturas, consultar reportes y utilizar las cuatro tablas, tres vistas y cinco procedimientos almacenados creados por `db.sql`.

### 1.3 Justificación

El proyecto permite aplicar análisis de requisitos, diseño de bases de datos relacionales y desarrollo web mediante un proceso de venta de alcance controlado. El SRS refleja las columnas y restricciones de `db.sql` y distingue esas reglas de las validaciones y operaciones que implementará la aplicación PHP.

### 1.4 Objetivo general

Diseñar y desarrollar La Única como una aplicación web para gestionar clientes, productos y facturas, con `detalle_facturas` como relación entre las facturas y sus productos.

#### 1.4.1 Objetivos específicos

- Registrar, consultar y actualizar clientes usando `id_cliente`, nombre y fecha de registro.
- Registrar, consultar y actualizar productos con unidad de medida, precio vigente y existencias enteras.
- Generar facturas asociadas a un cliente y a uno o varios productos.
- Registrar cantidades enteras en `detalle_facturas` y calcular importes con el precio vigente de `productos`.
- Actualizar existencias al completar o cancelar una factura.
- Consultar e imprimir facturas y conservar el historial de ventas.
- Mantener la integridad de las relaciones y utilizar las vistas y procedimientos de reporte definidos en `db.sql`.

### 1.5 Alcance

El aplicativo comprenderá los módulos de clientes, productos, facturación y reportes. Un operador realizará las funciones desde el mismo aplicativo. Los clientes son compradores asociados a facturas, no cuentas de acceso.

Los clientes tendrán `id_cliente` y nombre; si no se proporciona `fecha_registro`, se asignará la fecha actual. Los productos tendrán nombre, `unidad_medida` como texto de hasta 20 caracteres, `precio_unitario` no negativo y `stock` entero no negativo. El esquema no define documentos, teléfonos, correos, direcciones, códigos ni descripciones.

Para generar una factura, el operador seleccionará un cliente existente, agregará productos e indicará cantidades enteras positivas. MySQL asignará `id_factura` y `fecha_factura`; cada detalle tendrá su propio `id_detalle_factura`. Al completar una venta, la aplicación PHP guardará explícitamente el estado `Pagado` y actualizará las existencias. Los importes se calcularán con los precios vigentes: no se guardará un número comercial `FAC-` ni un precio histórico en `detalle_facturas`.

La factura será un comprobante interno de venta calculado como cantidad por precio vigente. No incluirá IVA u otros impuestos ni se presentará como factura fiscal o electrónica. El alcance no incluye administración de usuarios o roles, autenticación por cuentas, recuperación de contraseñas, categorías, tabla de tienda, contabilidad avanzada, descuentos, pasarelas de pago, facturación electrónica ante servicios externos ni aplicaciones móviles nativas. Las operaciones persistentes utilizarán las cuatro tablas; los reportes podrán consultar las vistas y procedimientos incluidos en `db.sql`.

### 1.6 Personal involucrado

El desarrollo está a cargo de los cinco integrantes del grupo. Cada integrante tiene una responsabilidad principal y puede apoyar otras actividades.

| Integrante | Rol principal | Responsabilidades |
| --- | --- | --- |
| Carlos Stiven Gutiérrez Ramírez | Desarrollo frontend y UX/UI | Diseñar y desarrollar interfaces, navegación, componentes visuales y adaptación responsive. |
| Camilo Andrés Tamayo Duran | Desarrollo backend e integraciones | Desarrollar lógica del servidor, servicios y procesos de negocio. |
| Kevin Fernando Martínez Pérez | Análisis de requisitos y documentación | Mantener el SRS, organizar requisitos, trazabilidad y documentación técnica. |
| Samuel Santiago Ramírez Fierro | Base de datos | Diseñar y mantener estructura, relaciones, consultas e integridad de datos. |
| Santiago Ortiz Claros | Calidad, pruebas y seguridad | Diseñar y ejecutar pruebas, registrar errores, validar requisitos y apoyar revisiones de seguridad. |

### 1.7 Definiciones, acrónimos y abreviaturas

| Término | Definición |
| --- | --- |
| Cliente | Comprador registrado en `clientes`; puede no tener facturas o estar asociado a una o varias. |
| Operador | Persona de la tienda que registra clientes y productos y realiza operaciones de facturación. No es una tabla del modelo. |
| Producto | Artículo de `productos` con nombre, unidad de medida, precio unitario y stock. La unidad se almacena como texto, sin catálogo cerrado. |
| Factura | Registro de `facturas` asociado a un cliente, con identificador, fecha y estado. El total se calcula en los reportes con el precio vigente. |
| Detalle de factura | Fila de `detalle_facturas` que enlaza factura y producto mediante `id_detalle_factura`, `id_factura` e `id_producto`, y guarda una cantidad entera. No guarda precio unitario ni subtotal. |
| Stock | Existencia entera no negativa del producto, limitada por `INT` y por el `CHECK` de `db.sql`. |
| Unidad de medida | Texto almacenado en `unidad_medida` (`VARCHAR(20)`); el esquema no fija valores permitidos ni admite cantidades fraccionarias. |
| Tabla pivote | `detalle_facturas` relaciona facturas y productos. Cada fila tiene clave primaria propia y no se declara unicidad para el par factura-producto. |
| Vista | Consulta guardada creada por `db.sql` para resumir información de clientes, facturas o productos. |
| Procedimiento almacenado | Rutina de consulta creada por `db.sql` para generar reportes. |

**Acrónimos:** SRS: *Software Requirements Specification*; RF: requisito funcional; RNF: requisito no funcional; BD: base de datos.  
**Abreviaturas:** PK: clave primaria; FK: clave foránea; 1-N: uno a muchos; N-N: muchos a muchos.

### 1.8 Referencias

La organización documental y los datos del grupo e instructor proceden del documento base del proyecto, *Especificación de requisitos de software (SRS): La Única* (2026).

### 1.9 Resumen

La Única permitirá gestionar clientes y productos y registrar ventas mediante facturas y detalles. Este documento describe las cuatro tablas, las tres vistas y los cinco procedimientos almacenados del esquema, además de las reglas de aplicación necesarias para operar las ventas.

### 1.10 Fuentes primarias

Los requisitos se fundamentan en la definición del proyecto y en `db.sql`. El archivo crea la base `la_unica`, cuatro tablas con sus claves y restricciones, tres vistas y cinco procedimientos almacenados; no inserta datos iniciales.

## 2. Descripción general

### 2.1 Perspectiva del producto

La Única será una aplicación web local para una tienda y se ejecutará en el equipo del operador. PHP implementará la lógica y MySQL almacenará `la_unica`, sus tablas, vistas y procedimientos. El navegador accederá al servicio mediante la interfaz de bucle local del mismo equipo.

| Elemento | Tecnología |
| --- | --- |
| Lenguaje de programación | PHP |
| Motor de base de datos | MySQL |

### 2.2 Características de los usuarios

| Participante | Características | Funciones o relación con el aplicativo |
| --- | --- | --- |
| Operador de la tienda | Persona con conocimientos básicos de formularios y ventas. | Registrar, consultar y actualizar clientes y productos; generar, consultar, imprimir y cancelar facturas. |
| Cliente | Persona que realiza una compra y cuyos datos se registran en `clientes`. | Quedar asociado a sus facturas y recibir la factura impresa. No opera el aplicativo ni tiene cuenta. |

### 2.3 Restricciones

- La aplicación será web y utilizará PHP y MySQL.
- La persistencia utilizará únicamente las cuatro tablas `clientes`, `facturas`, `productos` y `detalle_facturas`, además de las vistas y procedimientos que crea `db.sql`.
- `detalle_facturas` resolverá la relación N-N mediante una clave primaria propia; no se declara unicidad para el par de claves foráneas.
- Cada factura se asociará a un cliente existente. La aplicación PHP debe exigir al menos un detalle, ya que `db.sql` no impone esa regla.
- No se implementarán módulos que requieran tablas adicionales.
- Reportes e impresiones calcularán importes con el precio unitario actual. Como el esquema no guarda el precio histórico, cambiar el precio puede alterar los importes mostrados para facturas anteriores.

### 2.4 Suposiciones y dependencias

#### 2.4.1 Suposiciones

- La aplicación y la base de datos se ejecutarán en el equipo del operador, sin acceso desde otros equipos ni publicación en Internet. El sistema operativo administra el acceso al equipo; el aplicativo no autentica al operador.
- El operador registrará datos válidos y mantendrá actualizados clientes, productos y existencias. La aplicación validará formatos y reglas que `db.sql` no impone.
- Los clientes se registrarán antes de completar una factura.
- Los equipos dispondrán de un navegador compatible; la impresión utilizará las opciones del navegador.
- El despliegue configurará juego de caracteres, intercalación, motor y zona horaria de MySQL. Se utilizará `utf8mb4`; PHP y la sesión de base de datos configurarán explícitamente la zona horaria para presentar fechas de Bogotá.

#### 2.4.2 Dependencias

- Servidor local con PHP habilitado.
- Servicio MySQL disponible.
- Entorno local de desarrollo con PHP y MySQL.
- Interfaz de bucle local para comunicar navegador, aplicación y base de datos en el mismo equipo.

## 3. Requisitos específicos

### 3.1 Requisitos comunes de las interfaces

#### 3.1.1 Interfaz de usuario

La interfaz ofrecerá acceso a Clientes, Productos, Facturas y Reportes. Los formularios de clientes solicitarán `id_cliente` y nombre; los de productos, nombre, `unidad_medida`, `precio_unitario` y stock. La preparación de factura mostrará cliente, productos, cantidades e importes basados en el precio vigente. Los productos con stock cero podrán mostrarse como agotados. La cancelación estará deshabilitada para facturas `Cancelado`; la eliminación estará deshabilitada cuando haya referencias asociadas.

#### 3.1.2 Interfaz de hardware

El sistema se utilizará desde un equipo con navegador web, sin hardware especializado. La copia física de la factura se obtendrá con la impresora disponible para el operador.

#### 3.1.3 Interfaz de software

PHP se comunicará con MySQL para leer y guardar clientes, facturas, productos y detalles, consultar las vistas y ejecutar los procedimientos de reportes de `db.sql`. El servicio se ejecutará localmente en el equipo del operador.

#### 3.1.4 Interfaz de comunicación

Navegador y aplicación se comunicarán mediante solicitudes web. El servidor validará los datos antes de guardarlos y devolverá mensajes claros cuando una operación no pueda completarse.

### 3.2 Historias de usuario

- **Gestión de clientes:** como operador, quiero registrar, buscar y actualizar clientes por `id_cliente` y nombre para seleccionar al comprador y conservar su relación con las facturas.
- **Gestión de productos:** como operador, quiero registrar y actualizar productos con nombre, unidad de medida, precio y stock entero; ver agotados y eliminar solo productos sin detalles asociados.
- **Generación de facturas:** como operador, quiero seleccionar un cliente y agregar productos con cantidades enteras positivas para generar una factura con precios actuales y actualizar existencias.
- **Consulta, impresión y cancelación:** como operador, quiero consultar e imprimir facturas por identificador, cliente o fecha, usando el precio actual, y cancelar solo las `Pagado`, bloqueando la acción si ya están `Cancelado`.
- **Reportes:** como operador, quiero consultar reportes de clientes, ventas, existencias y estados de factura.

### 3.3 Requisitos funcionales

| ID | Requisito | Descripción |
| --- | --- | --- |
| RF-01 | Registrar clientes | Guardar `id_cliente` (`INT` suministrado por la aplicación) y nombre obligatorio (`VARCHAR(100)`). Si no se envía `fecha_registro`, MySQL asignará `CURRENT_DATE`. El esquema no define documento, teléfono, correo, dirección ni unicidad del nombre. |
| RF-02 | Consultar y actualizar clientes | Buscar por `id_cliente` o nombre y permitir actualizar el nombre. Las facturas relacionarán al cliente por `id_cliente` y mostrarán el nombre vigente. La clave foránea impide borrar clientes referenciados. |
| RF-03 | Registrar productos | Guardar nombre (`VARCHAR(100)`), unidad (`VARCHAR(20)`), precio (`DECIMAL(10,2)`) y stock (`INT`). Exigir precio y stock no negativos. El esquema no define códigos, descripciones ni una lista cerrada de unidades. |
| RF-04 | Consultar y actualizar productos | Buscar por `id_producto` o nombre y actualizar unidad, precio o stock dentro de los tipos y límites de `db.sql`. Los reportes de facturas mostrarán el precio vigente. Las claves foráneas impiden borrar productos referenciados. |
| RF-05 | Preparar una factura | Seleccionar un cliente y productos existentes, indicar cantidades enteras positivas, mostrar el precio vigente y validar stock antes de guardar. La unidad de medida es texto y no cambia el tipo entero de stock o cantidad. |
| RF-06 | Generar la factura | Guardar una fila en `facturas` con identificador autogenerado, cliente existente, fecha `CURRENT_TIMESTAMP` por defecto y estado `Pagado` explícito. Guardar uno o más detalles con cantidad entera positiva. Si el estado se omite directamente en MySQL, se aplica `Pendiente`. |
| RF-07 | Calcular importes | Calcular subtotal y total al consultar, multiplicando cantidad por precio vigente. El esquema no guarda precio histórico, subtotal ni total; cambiar el precio puede alterar importes mostrados para facturas anteriores. |
| RF-08 | Controlar existencias | Mostrar el stock entero, validar que alcance para la venta y actualizarlo desde PHP al guardar. El `CHECK` impide stock negativo en motores que lo apliquen; `db.sql` no implementa descuento ni transacción de venta. |
| RF-09 | Consultar e imprimir facturas | Buscar por `id_factura`, `id_cliente`, nombre del cliente o fecha. Mostrar estado, fecha, detalles, precio vigente, subtotales y total calculado. No se generará número `FAC-` ni se presentará precio histórico. |
| RF-10 | Cancelar facturas | Permitir en PHP la transición `Pagado` a `Cancelado` y reponer cantidades una sola vez. Bloquear cancelaciones repetidas. El esquema admite `Pendiente`, cuyo flujo no se define en esta versión. |
| RF-11 | Validar registros | Validar campos obligatorios, tipos y límites: nombre hasta 100 caracteres, unidad hasta 20, precio `DECIMAL(10,2)` no negativo, stock entero no negativo y cantidad entera positiva. No se declara unicidad para nombres ni para el par factura-producto. |
| RF-12 | Eliminar registros | Solicitar confirmación y eliminar clientes o productos sin historial solo si no tienen referencias en claves foráneas. No borrar facturas ni detalles desde la aplicación. `db.sql` no define `ON DELETE CASCADE`. |
| RF-13 | Consultar reportes | Ofrecer reportes de clientes, totales por factura, unidades por producto, detalle, existencias, estado y pendientes mediante las tres vistas y cinco procedimientos de la sección 3.5.1.1, respetando sus cálculos y filtros. |

### 3.4 Requisitos no funcionales

| ID | Categoría | Especificación |
| --- | --- | --- |
| RNF-01 | Usabilidad | Las acciones principales estarán en Clientes, Productos, Facturas y Reportes. Los errores identificarán campo y causa; los productos agotados y acciones bloqueadas por historial o estado serán visibles. |
| RNF-02 | Rendimiento | En el equipo local, el 95 % de consultas por `id_cliente`, `id_producto` o `id_factura` responderá en dos segundos o menos con 1.000 clientes, 1.000 productos y 1.000 facturas de hasta diez detalles. |
| RNF-03 | Exposición del servicio | Aplicación y MySQL escucharán solo en la interfaz de bucle local. No se expondrán rutas de datos ni el puerto MySQL a la red local o Internet. El acceso al equipo corresponde al sistema operativo. |
| RNF-04 | Integridad | PHP coordinará ventas, detalles, ajustes de stock y cancelaciones con transacciones y validaciones para evitar escrituras parciales. `db.sql` define claves foráneas y `CHECK` de stock/cantidad, pero no contiene operaciones de venta, bloqueos ni reposición. |
| RNF-05 | Compatibilidad | La interfaz funcionará en navegadores modernos y permitirá imprimir el comprobante interno con las funciones habituales del navegador, conservando datos e importes. |
| RNF-06 | Mantenibilidad | El código se organizará en PHP y la base se reproducirá con `db.sql`: cuatro tablas, tres vistas y cinco procedimientos. El script no inserta datos ni define charset, collation, motor, índices únicos de negocio o triggers. |

### 3.5 Modelo de datos y reglas de negocio

El modelo coincide con `db.sql`. Las reglas de transacciones, estados de venta y cambios de stock que no están expresadas como restricciones SQL corresponden a la aplicación PHP.

| Tabla | Campos y restricciones | Finalidad |
| --- | --- | --- |
| `clientes` | `id_cliente INT` (PK, no autoincremental), `nombre VARCHAR(100) NOT NULL`, `fecha_registro DATE DEFAULT CURRENT_DATE`. | Registrar clientes y su fecha de registro. |
| `productos` | `id_producto INT AUTO_INCREMENT` (PK), `nombre VARCHAR(100) NOT NULL`, `unidad_medida VARCHAR(20) NOT NULL`, `stock INT NOT NULL CHECK (stock >= 0)`, `precio_unitario DECIMAL(10,2) NOT NULL CHECK (precio_unitario >= 0)`. | Registrar productos, existencias enteras y precio vigente. |
| `facturas` | `id_factura INT AUTO_INCREMENT` (PK), `id_cliente INT NOT NULL` (FK), `estado_factura ENUM('Pagado','Cancelado','Pendiente') DEFAULT 'Pendiente'`, `fecha_factura DATETIME DEFAULT CURRENT_TIMESTAMP`. No hay campo de número comercial ni total. | Registrar una factura asociada a un cliente, con estado y fecha. |
| `detalle_facturas` | `id_detalle_factura INT AUTO_INCREMENT` (PK), `id_factura INT NOT NULL` (FK), `id_producto INT NOT NULL` (FK), `cantidad INT NOT NULL CHECK (cantidad > 0)`. No guarda precio unitario, subtotal ni total. | Relacionar factura y producto. |

#### 3.5.1 Relaciones

- `clientes` 1-N `facturas`: cada factura referencia un cliente existente.
- `facturas` 1-N `detalle_facturas`: cada detalle referencia una factura existente.
- `productos` 1-N `detalle_facturas`: cada detalle referencia un producto existente.
- `facturas` N-N `productos`: `detalle_facturas` resuelve la relación con su propia PK. No hay restricción única para (`id_factura`, `id_producto`), por lo que un producto puede repetirse en una factura.

#### 3.5.1.1 Vistas y procedimientos almacenados

| Objeto | Comportamiento definido en `db.sql` |
| --- | --- |
| `vista_clientes_total_facturas` | Cuenta las facturas de cada cliente, incluye clientes sin facturas y no filtra por estado. |
| `vista_total_facturas` | Suma cantidad por precio vigente por factura; usa uniones internas y no filtra estados. |
| `vista_unidades_productos` | Suma cantidades por producto; no excluye facturas canceladas. |
| `reporte_detalle_facturas()` | Devuelve factura, cliente, estado, fecha, producto, cantidad, precio actual y subtotal; ordena por `id_factura`. |
| `reporte_facturas_clientes()` | Cuenta facturas por cliente, incluye clientes sin facturas y ordena de mayor a menor total. |
| `reporte_stock_productos()` | Devuelve ID, nombre, stock y precio vigente; ordena por stock ascendente. |
| `reporte_facturas_por_estado()` | Cuenta facturas agrupadas por estado. |
| `reporte_facturas_pendientes()` | Lista facturas `Pendiente` con cliente y fecha; ordena por fecha ascendente. |

#### 3.5.2 Reglas de negocio

- Cada factura referencia un cliente existente. Cada detalle referencia una factura y un producto existentes. La aplicación exige al menos un detalle por venta.
- `unidad_medida` es texto de hasta 20 caracteres. Stock y cantidad son enteros; el stock no puede ser negativo y la cantidad debe ser positiva.
- PHP valida nuevamente cliente, precio y stock al guardar. `db.sql` no reserva ni descuenta existencias.
- La factura guarda `id_cliente`; el detalle guarda `id_producto` y cantidad. Los nombres y el precio se consultan desde las tablas relacionadas; no se conserva precio histórico.
- Si cambia `productos.precio_unitario`, pueden cambiar los importes mostrados para facturas anteriores. Las consultas también muestran el nombre vigente del cliente.
- El ajuste manual fija el stock a un entero no negativo. La aplicación coordina ajustes, ventas y cancelaciones con transacciones o bloqueos.
- Al cancelar, PHP suma las cantidades al stock vigente en una operación atómica. La reposición se realiza una sola vez y solo se permite `Pagado` a `Cancelado`.
- El esquema admite `Pendiente` como valor predeterminado, pero esta versión no define su transición. PHP guardará explícitamente `Pagado` al completar la venta.
- El subtotal se calcula como cantidad por precio vigente y el total como suma de subtotales. Ninguno se almacena; se presentan con dos posiciones decimales.
- La venta guardará factura, detalles y actualización de stock en una transacción para evitar cambios parciales.
- La clave de `detalle_facturas` es `id_detalle_factura`; no se declara unicidad por factura y producto, por lo que el esquema permite líneas repetidas del mismo producto.
- La aplicación solo permite eliminar clientes sin facturas y productos sin detalles. Las claves foráneas impiden eliminar registros referenciados; no hay `ON DELETE CASCADE`.
- La aplicación gestiona las modificaciones y eliminaciones de facturas guardadas. No se define un identificador comercial `FAC-`; se usa el `id_factura` autogenerado.
- Un producto con stock cero permanece visible, pero la aplicación impide agregarlo a una venta.

#### 3.5.3 Formatos y límites de los datos

| Grupo | Tipo | Restricción |
| --- | --- | --- |
| Identificadores | `INT` | `id_cliente` es PK suministrada por la aplicación. `id_factura`, `id_producto` e `id_detalle_factura` son autogenerados. Las PK son únicas y no nulas; el esquema no declara `UNSIGNED` ni positividad adicional. |
| Nombre y fecha del cliente | `VARCHAR(100)`, `DATE` | Nombre obligatorio. `fecha_registro` tiene `DEFAULT CURRENT_DATE`, pero no se declara `NOT NULL`. |
| Unidad de medida | `VARCHAR(20)` | Obligatoria. No hay ENUM ni catálogo de unidades. |
| Existencias | `INT` | `stock` obligatorio y `CHECK (stock >= 0)`; no admite fracciones. |
| Precio unitario | `DECIMAL(10,2)` | Obligatorio y `CHECK (precio_unitario >= 0)`; rango de 0.00 a 99,999,999.99. |
| Estado y fecha de factura | `ENUM`, `DATETIME` | Estados `Pagado`, `Cancelado` o `Pendiente`; predeterminado `Pendiente`. Fecha predeterminada `CURRENT_TIMESTAMP`; SQL no define zona horaria. |
| Detalle de factura | `INT` | Cantidad obligatoria y `CHECK (cantidad > 0)`. El detalle usa PK autogenerada, sin clave compuesta ni unicidad por producto. |
| Importes calculados | Expresión | Subtotal y total no son columnas; se calculan con cantidad y precio vigente del producto. |
| Campos no definidos | No existen en `db.sql` | No hay documento, teléfono, correo, dirección, código, descripción, número `FAC-`, precio histórico, subtotal o total almacenado. |
| Configuración | No especificada | `db.sql` no define charset, collation, motor, zona horaria, índices únicos de negocio ni datos iniciales. La base se llama `la_unica`. |

#### 3.5.4 Criterios de aceptación de la alineación con `db.sql`

- **RF-02 y RF-11:** se permiten nombres de cliente repetidos. Un cliente con factura no se puede eliminar y las facturas muestran su nombre vigente.
- **RF-03, RF-05 y RF-08:** stock entero no negativo, precio no negativo con dos decimales y cantidades enteras positivas. Stock insuficiente rechaza la venta sin cambios parciales.
- **RF-04 y RF-10:** venta y cancelación actualizan stock atómicamente. Si se venden tres unidades y luego el stock se ajusta a una, cancelar deja cuatro; una segunda cancelación no altera el stock.
- **RF-08 y RF-10:** un producto agotado no se puede agregar; una factura `Cancelado` no se puede cancelar de nuevo. El servidor también rechaza solicitudes que intenten omitir estas reglas.
- **RF-12:** se puede eliminar tras confirmar un cliente o producto sin referencias; con referencias, la clave foránea bloquea la operación.
- **RF-07, RF-11 y RF-13:** subtotales se calculan con el precio vigente; cambiarlo altera los importes consultados de facturas anteriores. El precio admite dos decimales y un máximo de 99,999,999.99.
- **RF-03, RF-05 y RF-11:** unidad de medida acepta cualquier texto no nulo de hasta 20 caracteres; stock y cantidad son enteros. Se rechaza cantidad cero o negativa y stock negativo.
- **RNF-03 y RNF-04:** el servicio responde solo desde el equipo local. PHP coordina ventas, ajustes y cancelaciones simultáneas mediante transacciones; `db.sql` no realiza descuentos ni reposiciones.
- **RF-13:** reportes de clientes y estados cuentan todos los estados; `vista_unidades_productos` incluye detalles de facturas canceladas; `reporte_facturas_pendientes()` solo devuelve `Pendiente`.

## Referencias

- *Especificación de requisitos de software: La Única*. (2026). Documento del proyecto, versión 1.3.
- Equipo del proyecto. (2026). *Esquema de la base de datos La Única* (`db.sql`), consultado el 26 de octubre de 2026.