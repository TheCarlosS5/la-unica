1 

# **Especificación de requisitos de software** 

# **La Unica** 

Centro de la Industria, la Empresa y los Servicios Regional Huila 

Programa de formación Análisis y Desarrollo de Software 

Ficha 3407799 

# **Integrantes del grupo:** 

Camilo Andrés Tamayo Duran 

Carlos Stiven Gutiérrez Ramírez 

Kevin Fernando Martínez Pérez 

Samuel Santiago Ramírez Fierro 

Santiago Ortiz Claros 

**Instructor:** José de Jesús Motta Vargas 

27 de octubre de 2026 

2 

# **Control del documento** 

# _Control de la versión_ 

|Version|Fecha|Estado|Descripcion|
|---|---|---|---|
|1.0|05/10/2026|Reemplazada|Creacion del SRS y planteamiento de los<br>requisitos funcionales (RF) y requisitos<br>no funcionales (RNF) del sistema La<br>Unica.|
|1.1|06/10/2026|Reemplazada|Corrección del modelo de datos: se<br>eliminaron datos redundantes (copias del<br>cliente y del producto, subtotal y total<br>almacenados, id propio de<br>detalle_factura) y se agregaron<br>tipo_documento, unidad_medida, correo,<br>direccion y cantidades decimales. Se<br>ajustaron los requisitos funcionales, las<br>reglas de negocio y los formatos.|
|1.2|26/10/2026|Reemplazada|Alineación de entidades, campos,<br>estados, límites y reportes con la<br>estructura de db.sql.|
|1.3|26/10/2026|Reemplazada|Actualización del stack tecnológico a<br>PHP y MySQL.|
|1.4|27/10/2026|Vigente|Ajustes a reportes, reglas, dependencias<br>y aceptación.|



3 

# **Contenido** 

|Control del documento............................................................................................2|
|---|
|Resumen ejecutivo.................................................................................................5|
|1 Introducción.........................................................................................................6|
|1.1 Planteamiento del problema.........................................................................6|
|1.2 Propósito.......................................................................................................6|
|1.3 Justificación del proyecto..............................................................................6|
|1.4 Objetivo general............................................................................................6|
|1.4.1 Objetivos específicos..............................................................................7|
|1.5 Alcance del proyecto.....................................................................................7|
|1.6 Personal involucrado.....................................................................................8|
|1.7 Definiciones acrónimos y abreviaturas.........................................................9|
|1.7.1 Definiciones............................................................................................9|
|1.7.2 Acrónimos.............................................................................................11|
|1.7.3 Abreviaturas.........................................................................................11|
|1.8 Referencias.................................................................................................11|
|1.9 Resumen.....................................................................................................11|
|1.10 Fuentes primarias de información.............................................................12|
|2 Descripción general...........................................................................................13|
|2.1 Perspectiva del producto.............................................................................13|
|2.2 Características de los usuarios...................................................................13|
|2.3 Restricciones...............................................................................................14|
|2.4 Suposiciones y dependencias....................................................................15|



4 

|2.4.1 Suposiciones........................................................................................15|
|---|
|2.4.2 Dependencias.......................................................................................15|
|3 Requisitos específicos.......................................................................................16|
|3.1 Requisitos comunes de las interfaces........................................................16|
|3.1.1 Interfaz de usuario................................................................................16|
|3.1.2 Interfaz de hardware.............................................................................16|
|3.1.3 Interfaz de software..............................................................................16|
|3.1.4 Interfaz de comunicación.....................................................................17|
|3.2 Historias de usuario....................................................................................17|
|3.2.1 Gestión de clientes...............................................................................17|
|3.2.2 Gestión de productos...........................................................................17|
|3.2.3 Generación de facturas........................................................................17|
|3.2.4 Consulta impresión y cancelación de facturas.....................................17|
|3.2.5 Consulta de reportes............................................................................18|
|3.3 Requisitos funcionales................................................................................18|
|3.4 Requisitos no funcionales...........................................................................21|
|3.5 Modelo de datos y reglas de negocio.........................................................24|
|3.5.1 Relaciones............................................................................................25|
|3.5.1.1 Vistas y procedimientos almacenados..............................................26|
|3.5.2 Reglas de negocio................................................................................27|
|3.5.3 Formatos y límites de los datos............................................................29|
|3.5.4 Criterios de aceptación de la alineación con db.sql.............................31|
|Referencias...........................................................................................................34|



5 

# **Resumen ejecutivo** 

La Unica será una aplicación web local para registrar clientes y productos, gestionar facturas y consultar reportes. La base de datos la_unica se crea con cuatro tablas (clientes, facturas, productos y detalle_facturas), tres vistas y cinco procedimientos almacenados definidos en db.sql. 

La aplicación permitirá gestionar clientes identificados por id_cliente, productos con unidad de medida en texto, existencias enteras y precios vigentes, y facturas con detalles de cantidades enteras. El esquema no guarda el precio histórico en cada detalle: los subtotales y totales se calculan con el precio actual del producto. La aplicación en PHP implementará la lógica de venta, actualización de existencias y cancelación. 

El esquema declara Pendiente como estado predeterminado de facturas. Para conservar el flujo de venta descrito en este SRS, la aplicación en PHP guardará Pagado explícitamente al completar una venta; la cancelación podrá cambiar Pagado a Cancelado. El procedimiento de pendientes consulta cualquier registro que tenga estado Pendiente. 

6 

# **1 Introducción** 

# **1.1 Planteamiento del problema** 

Los registros manuales o dispersos de clientes, productos y ventas dificultan localizar información y controlar las existencias. Al elaborar una factura de forma manual pueden presentarse errores en las cantidades, los precios y el cálculo del total. 

La Unica centralizará esta información y relacionará cada factura con su cliente y sus productos mediante facturas y detalle_facturas. El detalle registra la cantidad, pero no guarda el precio aplicado históricamente; las consultas obtienen el precio_unitario vigente desde productos. 

# **1.2 Propósito** 

Definir los requisitos de una aplicación web básica para registrar clientes y 

productos, generar facturas, consultar reportes y utilizar las cuatro tablas, tres vistas y cinco procedimientos almacenados creados por db.sql. 

# **1.3 Justificación del proyecto** 

El proyecto permitirá aplicar análisis de requisitos, diseño de bases de datos relacionales y desarrollo web mediante un proceso de venta de alcance controlado. El SRS refleja las columnas y restricciones que define db.sql y separa esas reglas de las validaciones y operaciones que implementará la aplicación en PHP. 

# **1.4 Objetivo general** 

Diseñar y desarrollar La Unica como una aplicación web para gestionar clientes, 

productos y facturas, con detalle_facturas como relación entre las facturas y sus productos. 

7 

# **_1.4.1 Objetivos específicos_** 

- Registrar, consultar y actualizar clientes usando id_cliente, nombre y 

fecha_registro. 

- Registrar, consultar y actualizar productos con su unidad de medida, precio vigente y existencias enteras. 

- Generar facturas asociadas a un cliente y a uno o varios productos. 

- Registrar cantidades enteras en detalle_facturas y calcular los importes 

consultados a partir del precio_unitario vigente en productos. 

- Actualizar las existencias al completar o cancelar una factura. 

- Consultar e imprimir facturas y conservar el historial de ventas. 

- Mantener la integridad de las relaciones entre las cuatro tablas y utilizar las vistas y procedimientos de reporte definidos en db.sql. 

# **1.5 Alcance del proyecto** 

El aplicativo comprenderá los módulos de clientes, productos, facturación y reportes. Un operador podrá realizar las funciones desde el mismo aplicativo. Los clientes son compradores asociados a facturas, no cuentas de acceso. 

Los clientes tendrán id_cliente y nombre; fecha_registro se asignará con la fecha actual si no se proporciona. Los productos tendrán nombre, unidad_medida como texto de hasta 20 caracteres, precio_unitario no negativo y stock entero no negativo. El esquema no define documentos, teléfonos, correos, direcciones, códigos ni descripciones. 

Para generar una factura, el operador seleccionará un cliente existente, agregará productos e indicará cantidades enteras positivas. La base asignará 

8 

id_factura, fecha_factura tendrá por defecto la fecha y hora del servidor, y cada detalle tendrá un id_detalle_factura propio. Al completar una venta, la aplicación en PHP guardará explícitamente el estado Pagado y actualizará las existencias. El total se calculará con los precios vigentes de productos; no se guardará un número FAC ni un precio histórico en detalle_facturas. 

La Unica es un prototipo formativo. En este alcance, la factura será un comprobante interno de venta calculado como cantidad por precio_unitario vigente; no incluirá cálculo de IVA u otros impuestos y no se presentará como factura fiscal o electrónica. Esta simplificación delimita el ejercicio académico y no incorpora reglas tributarias al aplicativo. 

El alcance no incluirá administración de usuarios o roles, autenticación por cuentas, recuperación de contraseñas, gestión de categorías, tabla de tienda, contabilidad avanzada, descuentos, pasarelas de pago, facturación electrónica ante servicios externos ni aplicaciones móviles nativas. Las operaciones persistentes utilizarán las cuatro tablas; los reportes podrán consultar las vistas y procedimientos incluidos en db.sql. 

# **1.6 Personal involucrado** 

El desarrollo estará a cargo de los cinco integrantes del grupo. Cada integrante tendrá una responsabilidad principal y podrá apoyar otras actividades del proyecto cuando sea necesario. 

9 

# **Integrantes, roles y responsabilidades del proyecto** 

|Nombre|Rol principal|Responsabilidades|
|---|---|---|
|Carlos Stiven Gutiérrez|Desarrollador|Diseñar y desarrollar las interfaces,|
|Ramírez|Frontend y UX/UI|navegación, componentes visuales<br>y adaptación responsive.|
|Camilo Andrés Tamayo|Desarrollador|Desarrollar la lógica del servidor,|
|Duran|Backend e<br>Integraciones|servicios y procesos de negocio de<br>la aplicación.|
|Kevin Fernando|Analista de|Mantener el SRS, organizar|
|Martínez Pérez|Requisitos y<br>Documentación|requisitos, trazabilidad y<br>documentación técnica.|
|Samuel Santiago|Responsable de|Diseñar y mantener la estructura de|
|Ramírez Fierro|Base de Datos|datos, relaciones, consultas e<br>integridad de la información.|
|Santiago Ortiz Claros|Responsable de<br>Calidad, Pruebas y<br>Seguridad|Diseñar y ejecutar pruebas, registrar<br>errores, validar requisitos y apoyar<br>la revisión de seguridad.|



# **1.7 Definiciones acrónimos y abreviaturas** 

# **_1.7.1 Definiciones_** 

|Termino|Definicion|
|---|---|
|Cliente|Comprador registrado en clientes; puede no tener facturas o|
||estar asociado a una o varias.|



10 

|Termino|Definicion|
|---|---|
|Operador|Persona de la tienda que registra clientes y productos y realiza<br>las operaciones de facturación. No constituye una tabla del<br>modelo.|
|Producto|Artículo registrado en productos con nombre, unidad_medida,<br>precio_unitario y stock. La unidad se almacena como texto; el<br>esquema no fija una lista de valores.|
|Factura|Registro de facturas asociado a un cliente, con id_factura,<br>fecha_factura y estado_factura. El total no se almacena y se<br>calcula en los reportes con el precio vigente del producto.|
|Detalle de factura|Fila de detalle_facturas que enlaza una factura con un producto<br>mediante id_detalle_factura, id_factura e id_producto, y<br>almacena cantidad entera. No guarda precio_unitario ni subtotal.|
|Stock|Existencia entera no negativa del producto, limitada por el tipo<br>INT y la restricción CHECK de db.sql.|
|Unidad de medida|Texto almacenado en unidad_medida (VARCHAR(20)); db.sql<br>no define una lista cerrada ni habilita cantidades decimales.|
|Tabla pivote|detalle_facturas relaciona facturas y productos. Cada fila tiene<br>id_detalle_factura como clave primaria; no se declara unicidad<br>para el par id_factura e id_producto.|
|Vista|Consulta guardada creada por db.sql para resumir información|
||de clientes, facturas o productos.|



11 

|Termino|Definicion|
|---|---|
|Procedimiento|Rutina de consulta creada por db.sql para generar reportes.|
|almacenado||



# **_1.7.2 Acrónimos_** 

- SRS: Software Requirements Specification. 

- RF: Requisito funcional. 

- RNF: Requisito no funcional. 

- BD: Base de datos. 

# **_1.7.3 Abreviaturas_** 

- PK: Clave primaria. 

- FK: Clave foránea. 

- 1-N: Relación uno a muchos. 

- N-N: Relación muchos a muchos. 

# **1.8 Referencias** 

La organización documental y los datos del grupo e instructor proceden del 

documento base del proyecto (Especificación de requisitos de software [SRS]: La Unica, 2026). 

# **1.9 Resumen** 

La Unica permitirá gestionar clientes y productos y registrar ventas mediante 

facturas y detalle_facturas. Este documento describe las cuatro tablas, las tres vistas y 

los cinco procedimientos almacenados del esquema, además de las reglas de 

aplicación necesarias para operar la venta. 

12 

# **1.10 Fuentes primarias de información** 

Los requisitos se fundamentan en la definición del proyecto y en la estructura de db.sql. El archivo crea la base la_unica, cuatro tablas con sus claves y restricciones, 

tres vistas y cinco procedimientos almacenados; no inserta datos iniciales. 

13 

# **2 Descripción general** 

# **2.1 Perspectiva del producto** 

La Unica será una aplicación web local para una tienda y se ejecutará en el equipo del operador. La aplicación se desarrollará con PHP y MySQL: PHP 

implementará la lógica y MySQL almacenará la base la_unica, con las tablas, vistas y procedimientos definidos en db.sql. El navegador accederá al servicio por la interfaz de bucle local del mismo equipo. 

# _Tecnologías seleccionadas_ 

|Elemento|Tecnologia|
|---|---|
|Lenguaje de|PHP|
|programación||
|Motor de base de datos|MySQL|



# **2.2 Características de los usuarios** 

# **Participantes del proceso y su relacion con el aplicativo** 

|Participante|Caracteristicas|Funciones o relacion con el aplicativo|
|---|---|---|
|Operador de la|Persona con|Registrar, consultar y actualizar clientes y|
|tienda|conocimientos<br>básicos de manejo|productos; generar, consultar, imprimir y<br>cancelar facturas.|
||de formularios y<br>ventas.||



14 

|Participante|Caracteristicas|Funciones o relacion con el aplicativo|
|---|---|---|
|Cliente|Persona que realiza<br>una compra y cuyos<br>datos se registran<br>en clientes.|Quedar asociado a sus facturas y recibir la<br>factura impresa. No opera el aplicativo ni<br>tiene una cuenta.|



# **2.3 Restricciones** 

- La aplicación será web y utilizará PHP 7.4 o posterior, con mysqli y mysqlnd, y 

   - MySQL 8.0.16 o posterior para aplicar las restricciones CHECK del esquema. 

- El esquema de persistencia utilizará las cuatro tablas clientes, facturas, 

   - productos y detalle_facturas, además de las vistas y procedimientos que crea db.sql. 

- La relación N-N entre facturas y productos se resolverá mediante 

detalle_facturas, que tiene una clave primaria propia y no declara unicidad para el par de claves foráneas. 

- Cada factura se asociará a un cliente existente por id_cliente. La regla de que 

   - contenga al menos un detalle corresponde a la aplicación en PHP; db.sql no la impone mediante una restricción. 

- No se implementarán módulos que requieran tablas adicionales. 

- Los reportes y las impresiones calcularán importes con el precio_unitario actual de productos. db.sql no almacena el precio histórico en detalle_facturas; por ello, cambiar el precio de un producto puede cambiar los importes mostrados para facturas anteriores. 

15 

# **2.4 Suposiciones y dependencias** 

# **_2.4.1 Suposiciones_** 

- El aplicativo y la base de datos se ejecutarán en el equipo del operador, sin 

acceso desde otros equipos ni publicación en Internet. El acceso al equipo se administrará mediante el sistema operativo; el aplicativo no autenticará al operador. 

- El operador registrará datos válidos y mantendrá actualizados los clientes, 

productos y existencias. La aplicación validará formatos y reglas que db.sql no impone. 

- Los clientes se registrarán antes de completar una factura. 

- Los equipos dispondrán de un navegador compatible; la impresión utilizará las opciones del navegador. 

- db.sql no fija el juego de caracteres, la intercalación, el motor de 

almacenamiento ni la zona horaria de MySQL. El despliegue configurará estos valores; para conservar texto en español se utilizará utf8mb4, y para presentar 

fechas de Bogotá la aplicación en PHP configurará explícitamente la zona 

horaria de la aplicación y de la sesión de base de datos. 

# **_2.4.2 Dependencias_** 

- Servidor local capaz de ejecutar PHP 7.4 o posterior con las extensiones mysqli y mysqlnd habilitadas. 

- Servicio MySQL 8.0.16 o posterior, con soporte para las restricciones CHECK y DEFAULT (CURRENT_DATE) de db.sql. 

16 

# **3 Requisitos específicos** 

# **3.1 Requisitos comunes de las interfaces** 

# **_3.1.1 Interfaz de usuario_** 

La interfaz ofrecerá acceso a Clientes, Productos, Facturas y Reportes. La sección Reportes mostrará un resumen con los siete indicadores definidos en RF-13 y acceso a cada reporte individual. Los formularios de clientes solicitarán id_cliente y nombre; los de productos, nombre, unidad_medida, precio_unitario y stock. La preparación de una factura mostrará cliente, productos, cantidades e importes basados en el precio vigente. Los productos con stock cero podrán mostrarse como agotados. La cancelación estará deshabilitada para facturas Cancelado; la eliminación estará deshabilitada cuando haya referencias asociadas. 

# **_3.1.2 Interfaz de hardware_** 

El sistema podrá utilizarse desde un equipo con navegador web. No requerirá hardware especializado. Para obtener una copia física de la factura se utilizará una impresora disponible en el equipo del operador. 

# **_3.1.3 Interfaz de software_** 

La aplicación desarrollada en PHP se comunicará con MySQL para leer y guardar clientes, facturas, productos y detalle_facturas, así como para consultar las vistas y ejecutar los procedimientos de reporte definidos en db.sql. El servicio se ejecutará localmente en el equipo del operador. 

17 

# **_3.1.4 Interfaz de comunicación_** 

La comunicación entre navegador y aplicación se realizará mediante solicitudes web. El servidor validará los datos antes de guardarlos y devolverá mensajes claros si una operación no puede completarse. 

# **3.2 Historias de usuario** 

# **_3.2.1 Gestión de clientes_** 

Como operador, quiero registrar, buscar y actualizar clientes por id_cliente y nombre para seleccionar al comprador de una venta y conservar su relación con las facturas. 

# **_3.2.2 Gestión de productos_** 

Como operador, quiero registrar y actualizar productos con nombre, 

unidad_medida, precio_unitario y stock entero para preparar ventas con datos vigentes. Quiero ver los productos agotados y eliminar solo productos que no tengan detalles asociados. 

# **_3.2.3 Generación de facturas_** 

Como operador, quiero seleccionar un cliente registrado y agregar productos con cantidades enteras positivas para generar una factura cuyo total se calcule con los precios actuales y cuyas existencias se actualicen al completar la venta. 

# **_3.2.4 Consulta impresión y cancelación de facturas_** 

Como operador, quiero consultar e imprimir facturas por su id_factura, cliente o fecha. Los importes utilizarán el precio actual del producto. Quiero cancelar únicamente facturas Pagado y bloquear la acción si ya están Cancelado. 

18 

# **_3.2.5 Consulta de reportes_** 

Como operador, quiero consultar reportes de clientes, ventas, existencias y 

estados de factura para revisar los datos consolidados por el esquema. 

# **3.3 Requisitos funcionales** 

Los requisitos funcionales describen las operaciones que el aplicativo ofrecerá al 

operador. Cada identificador corresponde al alcance de esta versión. 

# **Requisitos funcionales del sistema** 

|ID|Requisito funcional|Descripcion|
|---|---|---|
|RF-01|Registrar clientes|Guardar id_cliente (INT suministrado por la<br>aplicación) y nombre (VARCHAR(100) obligatorio). Si<br>no se envía fecha_registro, MySQL asignará<br>CURRENT_DATE. El esquema no define documento,<br>teléfono, correo, dirección ni unicidad del nombre.|
|RF-02|Consultar y<br>actualizar clientes|Buscar por id_cliente o nombre y permitir actualizar el<br>nombre. Las facturas relacionarán al cliente por<br>id_cliente; al consultar, se mostrará el nombre<br>vigente. La clave foránea impide borrar clientes<br>referenciados.|
|RF-03|Registrar productos|Guardar nombre (VARCHAR(100)), unidad_medida<br>(VARCHAR(20)), precio_unitario (DECIMAL(10,2)) y<br>stock (INT). Exigir precio y stock no negativos; el<br>esquema no define códigos, descripciones ni una lista<br>cerrada de unidades.|



19 

|ID|Requisito funcional|Descripcion|
|---|---|---|
|RF-04|Consultar y<br>actualizar productos|Buscar por id_producto o nombre y actualizar<br>unidad_medida, precio_unitario o stock con los tipos<br>y límites de db.sql. El precio mostrado en reportes de<br>facturas será el vigente. Las claves foráneas impiden<br>borrar productos referenciados.|
|RF-05|Preparar una<br>factura|Seleccionar un cliente y productos existentes, e<br>indicar cantidades enteras positivas. Mostrar<br>precio_unitario vigente y validar stock disponible<br>antes de guardar. La unidad_medida es texto y no<br>modifica el tipo entero de stock o cantidad.|
|RF-06|Generar la factura|Guardar una fila en facturas con id_factura<br>autogenerado, id_cliente existente, fecha_factura por<br>defecto CURRENT_TIMESTAMP y estado Pagado<br>explícito al completar una venta. Guardar una o más<br>filas en detalle_facturas con cantidad entera positiva.<br>Si el estado se omite directamente en MySQL, se<br>aplica Pendiente.|
|RF-07|Calcular importes|Calcular subtotal y total al consultar, multiplicando<br>cantidad por el precio_unitario vigente de productos.<br>El esquema no guarda precio histórico, subtotal ni<br>total; una actualización de precio puede modificar los<br>importes que muestran facturas anteriores.|



20 

|ID|Requisito funcional|Descripcion|
|---|---|---|
|RF-08|Controlar<br>existencias|Mostrar el stock entero vigente, validar que alcance<br>para la venta y actualizarlo en la aplicación en PHP al<br>guardar. La restricción CHECK de db.sql impide stock<br>negativo en motores que apliquen CHECK, pero<br>db.sql no implementa el descuento ni una transacción<br>de venta.|
|RF-09|Consultar e imprimir<br>facturas|Buscar por id_factura, id_cliente o nombre del cliente,<br>y fecha_factura. Mostrar el estado, la fecha, los<br>detalles, el precio_unitario vigente, los subtotales y el<br>total calculado. No se generará un número FAC ni se<br>presentará un precio histórico.|
|RF-10|Cancelar facturas|Permitir en la aplicación en PHP la transición Pagado<br>a Cancelado y reponer las cantidades una sola vez.<br>Bloquear la cancelación repetida. El esquema admite<br>también Pendiente, cuyo flujo de transición no se<br>define en esta versión.|
|RF-11|Validar los registros|Validar los campos obligatorios, tipos y límites<br>definidos: nombre de hasta 100 caracteres, unidad de<br>medida de hasta 20, precio DECIMAL(10,2) no<br>negativo, stock entero no negativo y cantidad entera<br>positiva. El esquema no declara unicidad para<br>nombres ni para el par factura-producto.|



21 

|ID|Requisito funcional|Descripcion|
|---|---|---|
|RF-12|Eliminar registros<br>sin historial|Solicitar confirmación y eliminar clientes o productos<br>solo cuando no tengan referencias en las claves<br>foráneas. No borrar facturas ni detalles desde la<br>aplicación. db.sql no define reglas ON DELETE<br>CASCADE.|
|RF-13|Consultar reportes|Incluir una portada con resumen y enlaces a siete<br>reportes individuales: «Clientes y cantidad de<br>facturas», «Total vigente por factura», «Unidades<br>facturadas por producto», «Detalle de facturas»,<br>«Existencias de productos», «Facturas por estado» y<br>«Facturas pendientes». El resumen mostrará la<br>cantidad de clientes y de sus facturas (todos los<br>estados); el importe vigente de las facturas con<br>detalle; las unidades facturadas; las líneas de detalle<br>y facturas distintas; las existencias totales; el total y<br>desglose por estado; y la cantidad de facturas<br>pendientes. Mantener los cálculos y filtros de 3.5.1.1.|



# **3.4 Requisitos no funcionales** 

Los requisitos no funcionales establecen las condiciones de calidad y 

funcionamiento del aplicativo. 

22 

# **Requisitos no funcionales del sistema** 

|ID|Categoria|Especificacion|
|---|---|---|
|RNF-01|Usabilidad|Las acciones principales estarán en Clientes,<br>Productos, Facturas y Reportes. Los errores<br>identificarán el campo y la causa. Los productos<br>agotados y las acciones bloqueadas por historial o<br>estado serán visibles.|
|RNF-02|Rendimiento|En el equipo local y sin operaciones concurrentes, se<br>medirán al menos 100 ejecuciones completas de<br>cada consulta por id_cliente, id_producto e id_factura,<br>desde el envío hasta mostrar los resultados. La base<br>de prueba tendrá 1.000 clientes, 1.000 productos y<br>1.000 facturas, con un máximo de diez detalles por<br>factura. Cumple si el percentil 95 del tiempo de<br>respuesta de cada consulta es de dos segundos o<br>menos.|
|RNF-03|Exposición del<br>servicio|La aplicación y MySQL escucharán únicamente en la<br>interfaz de bucle local. No se expondrán rutas de<br>datos ni el puerto de MySQL a la red local o a<br>Internet. El control de acceso al equipo corresponde<br>al sistema operativo.|



23 

|ID|Categoria|Especificacion|
|---|---|---|
|RNF-04|Integridad|La aplicación en PHP guardará ventas y<br>cancelaciones con sus cambios de stock en<br>transacciones y validaciones para evitar escrituras<br>parciales. Los ajustes manuales se aplicarán en una<br>operación atómica por producto y se coordinarán con<br>ventas y cancelaciones concurrentes para evitar<br>actualizaciones perdidas o stock negativo. db.sql<br>define claves foráneas y CHECK de stock/cantidad,<br>pero no contiene procedimientos de venta, bloqueo ni<br>reposición de existencias.|
|RNF-05|Compatibilidad|La interfaz funcionará en navegadores web modernos<br>y permitirá imprimir el comprobante interno con las<br>funciones habituales del navegador, conservando sus<br>datos e importes.|
|RNF-06|Mantenibilidad|El código se organizará en PHP y la estructura de la<br>base de datos se reproducirá con db.sql: cuatro<br>tablas, tres vistas y cinco procedimientos<br>almacenados. El archivo no inserta datos y no define<br>charset, collation, motor, índices únicos de negocio ni<br>triggers.|



24 

# **3.5 Modelo de datos y reglas de negocio** 

El modelo relacional coincide con db.sql: cuatro tablas con las columnas y tipos que aparecen a continuación. El archivo también crea tres vistas y cinco 

procedimientos almacenados. Las reglas sobre transacciones, estados de la venta y 

cambios de stock que no estén expresadas como restricciones en SQL corresponden a 

la aplicación en PHP. 

|Tabla|Campos|Finalidad|
|---|---|---|
|clientes|id_cliente INT (PK, sin|Registrar clientes y su fecha|
||autoincremento), nombre<br>VARCHAR(100) NOT NULL,<br>fecha_registro DATE DEFAULT<br>CURRENT_DATE.|de registro.|
|productos|id_producto INT<br>AUTO_INCREMENT (PK), nombre<br>VARCHAR(100) NOT NULL,<br>unidad_medida VARCHAR(20)<br>NOT NULL, stock INT NOT NULL<br>CHECK (stock >= 0),<br>precio_unitario DECIMAL(10,2)<br>NOT NULL CHECK (precio_unitario<br>>= 0).|Registrar productos,<br>existencias enteras y precio<br>vigente.|



25 

|Tabla|Campos|Finalidad|
|---|---|---|
|facturas|id_factura INT AUTO_INCREMENT<br>(PK), id_cliente INT NOT NULL<br>(FK), estado_factura<br>ENUM(Pagado, Cancelado,<br>Pendiente) DEFAULT Pendiente,<br>fecha_factura DATETIME<br>DEFAULT<br>CURRENT_TIMESTAMP.|Registrar una factura asociada<br>a un cliente, con estado y<br>fecha. No hay campo de<br>número ni total.|
|detalle_facturas|id_detalle_factura INT<br>AUTO_INCREMENT (PK),<br>id_factura INT NOT NULL (FK),<br>id_producto INT NOT NULL (FK),<br>cantidad INT NOT NULL CHECK<br>(cantidad > 0).|Relacionar factura y producto.<br>No almacena precio_unitario,<br>subtotal ni total.|



# **_3.5.1 Relaciones_** 

- clientes 1-N facturas: un cliente puede tener varias facturas y cada factura referencia un id_cliente existente. 

- facturas 1-N detalle_facturas: cada fila de detalle_facturas referencia una factura existente. 

- productos 1-N detalle_facturas: un producto puede aparecer en varias filas de detalle_facturas. 

26 

- facturas N-N productos: detalle_facturas relaciona ambas tablas y utiliza 

id_detalle_factura como clave primaria. db.sql no declara unicidad para 

(id_factura, id_producto), por lo que permite más de una fila del mismo producto en una factura. 

# **_3.5.1.1 Vistas y procedimientos almacenados_** 

|Objeto|Datos y comportamiento definidos en db.sql|
|---|---|
|vista_clientes_total_f|Cuenta todas las facturas de cada cliente; incluye|
|acturas|clientes sin facturas y no filtra por estado.|
|vista_total_facturas|Calcula el total por factura sumando cantidad por<br>precio_unitario actual. Usa uniones internas y no filtra<br>estados.|
|vista_unidades_prod|Suma cantidades de todas las filas de detalle por|
|uctos|producto; no excluye facturas Cancelado.|
|reporte_detalle_factu|Devuelve factura, cliente, estado, fecha, producto,|
|ras()|cantidad, precio actual y subtotal; ordena por id_factura.|
|reporte_facturas_clie|Cuenta todas las facturas por cliente, incluye|
|ntes()|clientes sin facturas y ordena de mayor a menor total.|
|reporte_stock_produ|Devuelve id, nombre, stock y precio vigente;|
|ctos()|ordena por stock ascendente.|
|reporte_facturas_por<br>_estado()|Cuenta facturas agrupadas por estado, incluidos<br>los estados presentes en la tabla.|
|reporte_facturas_pen|Lista facturas con estado Pendiente, junto con|
|dientes()|cliente y fecha; ordena por fecha ascendente.|



27 

# **_3.5.2 Reglas de negocio_** 

- Cada fila de facturas debe referenciar un cliente mediante id_cliente y cada fila de detalle_facturas debe referenciar una factura y un producto existentes. Las claves foráneas de db.sql protegen estas relaciones. La exigencia de al menos un detalle por venta se validará en la aplicación en PHP, ya que el esquema no la impone. 

- unidad_medida se almacena como VARCHAR(20); db.sql no enumera valores permitidos ni relaciona la unidad con cantidades decimales. stock y cantidad son INT: stock no puede ser negativo y cantidad debe ser mayor que cero según los CHECK definidos. La aplicación validará estas reglas antes de guardar. 

- Al guardar, la aplicación en PHP volverá a validar cliente, precio vigente y stock 

disponible. db.sql no contiene lógica para reservar o descontar existencias; esas operaciones se implementarán en la aplicación. 

- facturas guarda id_cliente y detalle_facturas guarda id_producto y cantidad. El esquema no copia nombre del cliente, nombre del producto ni precio_unitario a los detalles. Las consultas muestran los nombres y el precio vigentes en las tablas relacionadas. 

- Actualizar productos.precio_unitario afecta el subtotal y el total que las vistas, procedimientos y pantallas calculan para facturas anteriores. El esquema no conserva un precio histórico. Las consultas de cliente también muestran el nombre vigente del registro asociado. 

28 

- El ajuste manual fijará stock a un entero no negativo en una actualización atómica. La aplicación coordinará esa operación con ventas y cancelaciones concurrentes para evitar actualizaciones perdidas y stock negativo; db.sql no implementa esa coordinación. 

- Al cancelar, la aplicación en PHP sumará al stock vigente las cantidades enteras del detalle, dentro de una operación atómica. La regla Pagado a Cancelado y la reposición única son reglas de aplicación, no restricciones del esquema. 

 facturas.estado_factura admite Pagado, Cancelado y Pendiente, y el valor predeterminado del esquema es Pendiente. la aplicación en PHP escribirá Pagado al completar la venta según el flujo de este SRS y solo permitirá la transición Pagado a Cancelado. No se define en esta versión el flujo de pago ni la transición de facturas Pendiente; el procedimiento de pendientes permite consultarlas. 

- Los reportes calculan subtotal como cantidad por el precio_unitario vigente del producto y total como suma de subtotales. Ninguno se almacena. Por tanto, si cambia el precio_unitario, pueden cambiar los importes consultados de ventas anteriores. La aplicación usará valores decimales con dos posiciones para presentar los importes. 

- la aplicación en PHP guardará factura, detalles y actualización de stock dentro de una transacción para evitar cambios parciales. db.sql define restricciones de claves y CHECK, pero no implementa esa transacción. 

29 

- detalle_facturas usa id_detalle_factura INT AUTO_INCREMENT como clave primaria. Las claves foráneas apuntan a factura y producto existentes. No se declara una restricción UNIQUE para el par (id_factura, id_producto), por lo que el esquema permite líneas repetidas del mismo producto. 

- La aplicación solo ofrecerá eliminar clientes sin facturas y productos sin detalles. Las claves foráneas impiden eliminar registros referenciados; db.sql no declara ON DELETE CASCADE. 

- La aplicación no permitirá modificar ni eliminar facturas guardadas ni sus detalles. Solo permitirá cancelar una factura en estado Pagado, cambiarla a Cancelado y reponer el stock según las cantidades de su detalle una sola vez. db.sql no define un identificador comercial de factura ni reglas de reutilización: id_factura es la clave primaria autogenerada. 

- Un producto con stock cero permanecerá visible y no se podrá agregar a la venta por decisión de la aplicación. La columna stock permite cero y la base de datos no define una etiqueta de interfaz. 

# **_3.5.3 Formatos y límites de los datos_** 

Los identificadores de campo se escribirán como se indica en el modelo. La aplicación manejará texto Unicode y el despliegue configurará MySQL con utf8mb4, porque db.sql no declara el juego de caracteres ni la intercalación. Si fecha_registro se omite o se deja vacía al crear un cliente, la aplicación omitirá la columna en la inserción para que MySQL aplique DEFAULT CURRENT_DATE; no enviará NULL en ese caso. Las siguientes restricciones se aplicarán a los campos indicados en cada fila. 

30 

|Grupo de|Tipo|Restriccion|
|---|---|---|
|campos|||
|Identificadores|INT|id_cliente es PK y lo suministra la aplicación;<br>id_factura, id_producto e id_detalle_factura son<br>AUTO_INCREMENT. Las claves primarias son<br>únicas y no nulas; db.sql no declara UNSIGNED<br>ni restricciones de positividad.|
|Nombre y fecha<br>de cliente|VARCHAR(100)<br>, DATE|clientes.nombre es obligatorio. fecha_registro<br>tiene DEFAULT CURRENT_DATE, pero no se<br>declara NOT NULL.|
|Unidad de<br>medida|VARCHAR(20)|productos.unidad_medida es obligatoria. No hay<br>ENUM ni catálogo de unidades en db.sql.|
|Existencias|INT|productos.stock es obligatorio y CHECK (stock<br>>= 0). No admite fracciones en el esquema.|
|Precio unitario|DECIMAL(10,2)|productos.precio_unitario es obligatorio y<br>CHECK (precio_unitario >= 0); rango<br>representable: 0.00 a 99,999,999.99.|
|Estado y fecha<br>de factura|ENUM,<br>DATETIME|estado_factura admite Pagado, Cancelado o<br>Pendiente y tiene DEFAULT Pendiente.<br>fecha_factura tiene DEFAULT<br>CURRENT_TIMESTAMP; el script no define<br>zona horaria.|



31 

|Grupo de|Tipo|Restriccion|
|---|---|---|
|campos|||
|Detalle de|INT|cantidad es obligatoria y CHECK (cantidad > 0).|
|factura||id_detalle_factura es la PK autogenerada. No<br>existe una clave compuesta ni una restricción de<br>unicidad por producto.|
|Importes<br>calculados|Expresión de<br>consulta|subtotal y total no son columnas: los reportes<br>los calculan usando la cantidad y el<br>precio_unitario vigente de productos.|
|Campos no<br>definidos|No existen en<br>db.sql|El esquema no contiene documento, teléfono,<br>correo, dirección, código de producto,<br>descripción, número FAC, precio histórico,<br>subtotal almacenado ni total almacenado.|
|Configuración<br>del esquema|No especificada|db.sql no declara charset, collation, motor de<br>almacenamiento, zona horaria, índices únicos<br>de negocio ni datos iniciales. La base se llama<br>la_unica.|



# **_3.5.4 Criterios de aceptación de la alineación con db.sql_** 

- RF-02 y RF-11: el sistema permitirá clientes con nombres repetidos, porque 

db.sql no declara unicidad para nombre. Un cliente referenciado por una factura 

no podrá eliminarse; al consultar esa factura se verá el nombre vigente del 

cliente. 

32 

 RF-03, RF-05 y RF-08: productos aceptarán stock entero no negativo y precio DECIMAL(10,2) no negativo; cantidades fraccionarias, cero o negativas se rechazarán. Si el stock disponible es insuficiente, la aplicación en PHP rechazará la venta sin cambios parciales. 

 RF-04 y RF-10: la aplicación en PHP actualizará el stock de manera atómica al vender y cancelar. Si una venta descontó tres unidades y luego el stock se ajustó a una unidad, cancelar la venta dejará cuatro; una segunda cancelación no cambiará el stock. 

- RF-08 y RF-10: un producto con stock cero no se podrá agregar por la interfaz; una factura Cancelado tendrá bloqueada la cancelación. El servidor también rechazará solicitudes que intenten omitir estas reglas. 

- RF-12: eliminar un cliente o producto sin referencias será posible tras confirmar. 

Si hay referencias en facturas o detalle_facturas, la clave foránea impedirá la eliminación. 

 RF-07, RF-11 y RF-13: las consultas calcularán subtotales con el precio vigente. Si el precio de un producto cambia, cambiará el importe presentado para facturas anteriores; el detalle no guarda un precio histórico. Precio acepta hasta dos decimales y un máximo de 99,999,999.99. 

- RF-03, RF-05 y RF-11: productos.unidad_medida es texto de hasta 20 

   - caracteres. Tanto stock como cantidad son enteros; se rechazará cantidad cero 

o negativa y stock negativo. La base permite cualquier texto no nulo como unidad de medida. 

33 

- RNF-03 y RNF-04: el servicio atenderá en bucle local; ventas y cancelaciones usarán transacciones; los ajustes manuales se serializarán con movimientos concurrentes por producto y conservarán stock no negativo. 

- RF-13: las vistas y procedimientos devolverán los datos descritos en 3.5.1.1. Los reportes de facturas por cliente y estado cuentan registros de cualquier estado; vista_unidades_productos también incluye detalles de facturas canceladas; reporte_facturas_pendientes solo devuelve estado Pendiente. 

34 

# **Referencias** 

Especificación de requisitos de software (SRS): La Unica. (2026). [Documento de 

requisitos del proyecto, versión 1.4]. 

Equipo del proyecto. (2026). Esquema de la base de datos La Unica [Archivo db.sql, consultado el 27 de octubre de 2026]. 

