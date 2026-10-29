Especificación de requisitos de software

La Unica

Centro de la Industria, la Empresa y los Servicios Regional Huila

Programa de formación Análisis y Desarrollo de Software

Ficha 3407799

**Integrantes del grupo:**

Camilo Andrés Tamayo Duran

Carlos Stiven Gutiérrez Ramírez

Kevin Fernando Martínez Pérez

Samuel Santiago Ramírez Fierro

Santiago Ortiz Claros

**Instructor:** José de Jesús Motta Vargas

27 de octubre de 2026

# Control del documento

*Control de la versión*

| Version | Fecha | Estado | Descripcion |
| --- | --- | --- | --- |
| 1.0 | 05/10/2026 | Reemplazada | Creacion del SRS y planteamiento de los requisitos funcionales (RF) y requisitos no funcionales (RNF) del sistema La Unica. |
| 1.1 | 06/10/2026 | Reemplazada | Corrección del modelo de datos: se eliminaron datos redundantes (copias del cliente y del producto, subtotal y total almacenados, id propio de detalle\_factura) y se agregaron tipo\_documento, unidad\_medida, correo, direccion y cantidades decimales. Se ajustaron los requisitos funcionales, las reglas de negocio y los formatos. |
| 1.2 | 26/10/2026 | Reemplazada | Alineación de entidades, campos, estados, límites y reportes con la estructura de db.sql. |
| 1.3 | 26/10/2026 | Reemplazada | Actualización del stack tecnológico a PHP y MySQL. |
| 1.4 | 27/10/2026 | Vigente | Ajustes a reportes, reglas, dependencias y aceptación. |

**Contenido**

Control del documento 2

Resumen ejecutivo 5

1 Introducción 6

1.1 Planteamiento del problema 6

1.2 Propósito 6

1.3 Justificación del proyecto 6

1.4 Objetivo general 6

1.4.1 Objetivos específicos 7

1.5 Alcance del proyecto 7

1.6 Personal involucrado 8

1.7 Definiciones acrónimos y abreviaturas 9

1.7.1 Definiciones 9

1.7.2 Acrónimos 11

1.7.3 Abreviaturas 11

1.8 Referencias 11

1.9 Resumen 11

1.10 Fuentes primarias de información 12

2 Descripción general 13

2.1 Perspectiva del producto 13

2.2 Características de los usuarios 13

2.3 Restricciones 14

2.4 Suposiciones y dependencias 15

2.4.1 Suposiciones 15

2.4.2 Dependencias 15

3 Requisitos específicos 16

3.1 Requisitos comunes de las interfaces 16

3.1.1 Interfaz de usuario 16

3.1.2 Interfaz de hardware 16

3.1.3 Interfaz de software 16

3.1.4 Interfaz de comunicación 17

3.2 Historias de usuario 17

3.2.1 Gestión de clientes 17

3.2.2 Gestión de productos 17

3.2.3 Generación de facturas 17

3.2.4 Consulta impresión y cancelación de facturas 17

3.2.5 Consulta de reportes 18

3.3 Requisitos funcionales 18

3.4 Requisitos no funcionales 21

3.5 Modelo de datos y reglas de negocio 24

3.5.1 Relaciones 25

3.5.1.1 Vistas y procedimientos almacenados 26

3.5.2 Reglas de negocio 27

3.5.3 Formatos y límites de los datos 29

3.5.4 Criterios de aceptación de la alineación con db.sql 31

Referencias 34

# Resumen ejecutivo

La Unica será una aplicación web local para registrar clientes y productos, gestionar facturas y consultar reportes. La base de datos la\_unica se crea con cuatro tablas (clientes, facturas, productos y detalle\_facturas), tres vistas y cinco procedimientos almacenados definidos en db.sql.

La aplicación permitirá gestionar clientes identificados por id\_cliente, productos con unidad de medida en texto, existencias enteras y precios vigentes, y facturas con detalles de cantidades enteras. El esquema no guarda el precio histórico en cada detalle: los subtotales y totales se calculan con el precio actual del producto. La aplicación en PHP implementará la lógica de venta, actualización de existencias y cancelación.

El esquema declara Pendiente como estado predeterminado de facturas. Para conservar el flujo de venta descrito en este SRS, la aplicación en PHP guardará Pagado explícitamente al completar una venta; la cancelación podrá cambiar Pagado a Cancelado. El procedimiento de pendientes consulta cualquier registro que tenga estado Pendiente.

# 1 Introducción

## 1.1 Planteamiento del problema

Los registros manuales o dispersos de clientes, productos y ventas dificultan localizar información y controlar las existencias. Al elaborar una factura de forma manual pueden presentarse errores en las cantidades, los precios y el cálculo del total.

La Unica centralizará esta información y relacionará cada factura con su cliente y sus productos mediante facturas y detalle\_facturas. El detalle registra la cantidad, pero no guarda el precio aplicado históricamente; las consultas obtienen el precio\_unitario vigente desde productos.

## 1.2 Propósito

Definir los requisitos de una aplicación web básica para registrar clientes y productos, generar facturas, consultar reportes y utilizar las cuatro tablas, tres vistas y cinco procedimientos almacenados creados por db.sql.

## 1.3 Justificación del proyecto

El proyecto permitirá aplicar análisis de requisitos, diseño de bases de datos relacionales y desarrollo web mediante un proceso de venta de alcance controlado. El SRS refleja las columnas y restricciones que define db.sql y separa esas reglas de las validaciones y operaciones que implementará la aplicación en PHP.

## 1.4 Objetivo general

Diseñar y desarrollar La Unica como una aplicación web para gestionar clientes, productos y facturas, con detalle\_facturas como relación entre las facturas y sus productos.

### 1.4.1 Objetivos específicos

* Registrar, consultar y actualizar clientes usando id\_cliente, nombre y fecha\_registro.
* Registrar, consultar y actualizar productos con su unidad de medida, precio vigente y existencias enteras.
* Generar facturas asociadas a un cliente y a uno o varios productos.
* Registrar cantidades enteras en detalle\_facturas y calcular los importes consultados a partir del precio\_unitario vigente en productos.
* Actualizar las existencias al completar o cancelar una factura.
* Consultar e imprimir facturas y conservar el historial de ventas.
* Mantener la integridad de las relaciones entre las cuatro tablas y utilizar las vistas y procedimientos de reporte definidos en db.sql.

## 1.5 Alcance del proyecto

El aplicativo comprenderá los módulos de clientes, productos, facturación y reportes. Un operador podrá realizar las funciones desde el mismo aplicativo. Los clientes son compradores asociados a facturas, no cuentas de acceso.

Los clientes tendrán id\_cliente y nombre; fecha\_registro se asignará con la fecha actual si no se proporciona. Los productos tendrán nombre, unidad\_medida como texto de hasta 20 caracteres, precio\_unitario no negativo y stock entero no negativo. El esquema no define documentos, teléfonos, correos, direcciones, códigos ni descripciones.

Para generar una factura, el operador seleccionará un cliente existente, agregará productos e indicará cantidades enteras positivas. La base asignará id\_factura, fecha\_factura tendrá por defecto la fecha y hora del servidor, y cada detalle tendrá un id\_detalle\_factura propio. Al completar una venta, la aplicación en PHP guardará explícitamente el estado Pagado y actualizará las existencias. El total se calculará con los precios vigentes de productos; no se guardará un número FAC ni un precio histórico en detalle\_facturas.

La Unica es un prototipo formativo. En este alcance, la factura será un comprobante interno de venta calculado como cantidad por precio\_unitario vigente; no incluirá cálculo de IVA u otros impuestos y no se presentará como factura fiscal o electrónica. Esta simplificación delimita el ejercicio académico y no incorpora reglas tributarias al aplicativo.

El alcance no incluirá administración de usuarios o roles, autenticación por cuentas, recuperación de contraseñas, gestión de categorías, tabla de tienda, contabilidad avanzada, descuentos, pasarelas de pago, facturación electrónica ante servicios externos ni aplicaciones móviles nativas. Las operaciones persistentes utilizarán las cuatro tablas; los reportes podrán consultar las vistas y procedimientos incluidos en db.sql.

## 1.6 Personal involucrado

El desarrollo estará a cargo de los cinco integrantes del grupo. Cada integrante tendrá una responsabilidad principal y podrá apoyar otras actividades del proyecto cuando sea necesario.

**Integrantes, roles y responsabilidades del proyecto**

| Nombre | Rol principal | Responsabilidades |
| --- | --- | --- |
| Carlos Stiven Gutiérrez Ramírez | Desarrollador Frontend y UX/UI | Diseñar y desarrollar las interfaces, navegación, componentes visuales y adaptación responsive. |
| Camilo Andrés Tamayo Duran | Desarrollador Backend e Integraciones | Desarrollar la lógica del servidor, servicios y procesos de negocio de la aplicación. |
| Kevin Fernando Martínez Pérez | Analista de Requisitos y Documentación | Mantener el SRS, organizar requisitos, trazabilidad y documentación técnica. |
| Samuel Santiago Ramírez Fierro | Responsable de Base de Datos | Diseñar y mantener la estructura de datos, relaciones, consultas e integridad de la información. |
| Santiago Ortiz Claros | Responsable de Calidad, Pruebas y Seguridad | Diseñar y ejecutar pruebas, registrar errores, validar requisitos y apoyar la revisión de seguridad. |

## 1.7 Definiciones acrónimos y abreviaturas

### 1.7.1 Definiciones

| Termino | Definicion |
| --- | --- |
| Cliente | Comprador registrado en clientes; puede no tener facturas o estar asociado a una o varias. |
| Operador | Persona de la tienda que registra clientes y productos y realiza las operaciones de facturación. No constituye una tabla del modelo. |
| Producto | Artículo registrado en productos con nombre, unidad\_medida, precio\_unitario y stock. La unidad se almacena como texto; el esquema no fija una lista de valores. |
| Factura | Registro de facturas asociado a un cliente, con id\_factura, fecha\_factura y estado\_factura. El total no se almacena y se calcula en los reportes con el precio vigente del producto. |
| Detalle de factura | Fila de detalle\_facturas que enlaza una factura con un producto mediante id\_detalle\_factura, id\_factura e id\_producto, y almacena cantidad entera. No guarda precio\_unitario ni subtotal. |
| Stock | Existencia entera no negativa del producto, limitada por el tipo INT y la restricción CHECK de db.sql. |
| Unidad de medida | Texto almacenado en unidad\_medida (VARCHAR(20)); db.sql no define una lista cerrada ni habilita cantidades decimales. |
| Tabla pivote | detalle\_facturas relaciona facturas y productos. Cada fila tiene id\_detalle\_factura como clave primaria; no se declara unicidad para el par id\_factura e id\_producto. |
| Vista | Consulta guardada creada por db.sql para resumir información de clientes, facturas o productos. |
| Procedimiento almacenado | Rutina de consulta creada por db.sql para generar reportes. |

### 1.7.2 Acrónimos

* SRS: Software Requirements Specification.
* RF: Requisito funcional.
* RNF: Requisito no funcional.
* BD: Base de datos.

### 1.7.3 Abreviaturas

* PK: Clave primaria.
* FK: Clave foránea.
* 1-N: Relación uno a muchos.
* N-N: Relación muchos a muchos.

## 1.8 Referencias

La organización documental y los datos del grupo e instructor proceden del documento base del proyecto (Especificación de requisitos de software [SRS]: La Unica, 2026).

## 1.9 Resumen

La Unica permitirá gestionar clientes y productos y registrar ventas mediante facturas y detalle\_facturas. Este documento describe las cuatro tablas, las tres vistas y los cinco procedimientos almacenados del esquema, además de las reglas de aplicación necesarias para operar la venta.

## 1.10 Fuentes primarias de información

Los requisitos se fundamentan en la definición del proyecto y en la estructura de db.sql. El archivo crea la base la\_unica, cuatro tablas con sus claves y restricciones, tres vistas y cinco procedimientos almacenados; no inserta datos iniciales.

# 2 Descripción general

## 2.1 Perspectiva del producto

La Unica será una aplicación web local para una tienda y se ejecutará en el equipo del operador. La aplicación se desarrollará con PHP y MySQL: PHP implementará la lógica y MySQL almacenará la base la\_unica, con las tablas, vistas y procedimientos definidos en db.sql. El navegador accederá al servicio por la interfaz de bucle local del mismo equipo.

*Tecnologías seleccionadas*

| Elemento | Tecnologia |
| --- | --- |
| Lenguaje de programación | PHP |
| Motor de base de datos | MySQL |

## 2.2 Características de los usuarios

**Participantes del proceso y su relacion con el aplicativo**

| Participante | Caracteristicas | Funciones o relacion con el aplicativo |
| --- | --- | --- |
| Operador de la tienda | Persona con conocimientos básicos de manejo de formularios y ventas. | Registrar, consultar y actualizar clientes y productos; generar, consultar, imprimir y cancelar facturas. |
| Cliente | Persona que realiza una compra y cuyos datos se registran en clientes. | Quedar asociado a sus facturas y recibir la factura impresa. No opera el aplicativo ni tiene una cuenta. |

## 2.3 Restricciones

* La aplicación será web y utilizará PHP 7.4 o posterior, con mysqli y mysqlnd, y MySQL 8.0.16 o posterior para aplicar las restricciones CHECK del esquema.
* El esquema de persistencia utilizará las cuatro tablas clientes, facturas, productos y detalle\_facturas, además de las vistas y procedimientos que crea db.sql.
* La relación N-N entre facturas y productos se resolverá mediante detalle\_facturas, que tiene una clave primaria propia y no declara unicidad para el par de claves foráneas.
* Cada factura se asociará a un cliente existente por id\_cliente. La regla de que contenga al menos un detalle corresponde a la aplicación en PHP; db.sql no la impone mediante una restricción.
* No se implementarán módulos que requieran tablas adicionales.
* Los reportes y las impresiones calcularán importes con el precio\_unitario actual de productos. db.sql no almacena el precio histórico en detalle\_facturas; por ello, cambiar el precio de un producto puede cambiar los importes mostrados para facturas anteriores.

## 2.4 Suposiciones y dependencias

### 2.4.1 Suposiciones

* El aplicativo y la base de datos se ejecutarán en el equipo del operador, sin acceso desde otros equipos ni publicación en Internet. El acceso al equipo se administrará mediante el sistema operativo; el aplicativo no autenticará al operador.
* El operador registrará datos válidos y mantendrá actualizados los clientes, productos y existencias. La aplicación validará formatos y reglas que db.sql no impone.
* Los clientes se registrarán antes de completar una factura.
* Los equipos dispondrán de un navegador compatible; la impresión utilizará las opciones del navegador.
* db.sql no fija el juego de caracteres, la intercalación, el motor de almacenamiento ni la zona horaria de MySQL. El despliegue configurará estos valores; para conservar texto en español se utilizará utf8mb4, y para presentar fechas de Bogotá la aplicación en PHP configurará explícitamente la zona horaria de la aplicación y de la sesión de base de datos.

### 2.4.2 Dependencias

* Servidor local capaz de ejecutar PHP 7.4 o posterior con las extensiones mysqli y mysqlnd habilitadas.
* Servicio MySQL 8.0.16 o posterior, con soporte para las restricciones CHECK y DEFAULT (CURRENT\_DATE) de db.sql.

# 3 Requisitos específicos

## 3.1 Requisitos comunes de las interfaces

### 3.1.1 Interfaz de usuario

La interfaz ofrecerá acceso a Clientes, Productos, Facturas y Reportes. La sección Reportes mostrará un resumen con los siete indicadores definidos en RF-13 y acceso a cada reporte individual. Los formularios de clientes solicitarán id\_cliente y nombre; los de productos, nombre, unidad\_medida, precio\_unitario y stock. La preparación de una factura mostrará cliente, productos, cantidades e importes basados en el precio vigente. Los productos con stock cero podrán mostrarse como agotados. La cancelación estará deshabilitada para facturas Cancelado; la eliminación estará deshabilitada cuando haya referencias asociadas.

### 3.1.2 Interfaz de hardware

El sistema podrá utilizarse desde un equipo con navegador web. No requerirá hardware especializado. Para obtener una copia física de la factura se utilizará una impresora disponible en el equipo del operador.

### 3.1.3 Interfaz de software

La aplicación desarrollada en PHP se comunicará con MySQL para leer y guardar clientes, facturas, productos y detalle\_facturas, así como para consultar las vistas y ejecutar los procedimientos de reporte definidos en db.sql. El servicio se ejecutará localmente en el equipo del operador.

### 3.1.4 Interfaz de comunicación

La comunicación entre navegador y aplicación se realizará mediante solicitudes web. El servidor validará los datos antes de guardarlos y devolverá mensajes claros si una operación no puede completarse.

## 3.2 Historias de usuario

### 3.2.1 Gestión de clientes

Como operador, quiero registrar, buscar y actualizar clientes por id\_cliente y nombre para seleccionar al comprador de una venta y conservar su relación con las facturas.

### 3.2.2 Gestión de productos

Como operador, quiero registrar y actualizar productos con nombre, unidad\_medida, precio\_unitario y stock entero para preparar ventas con datos vigentes. Quiero ver los productos agotados y eliminar solo productos que no tengan detalles asociados.

### 3.2.3 Generación de facturas

Como operador, quiero seleccionar un cliente registrado y agregar productos con cantidades enteras positivas para generar una factura cuyo total se calcule con los precios actuales y cuyas existencias se actualicen al completar la venta.

### 3.2.4 Consulta impresión y cancelación de facturas

Como operador, quiero consultar e imprimir facturas por su id\_factura, cliente o fecha. Los importes utilizarán el precio actual del producto. Quiero cancelar únicamente facturas Pagado y bloquear la acción si ya están Cancelado.

### 3.2.5 Consulta de reportes

Como operador, quiero consultar reportes de clientes, ventas, existencias y estados de factura para revisar los datos consolidados por el esquema.

## 3.3 Requisitos funcionales

Los requisitos funcionales describen las operaciones que el aplicativo ofrecerá al operador. Cada identificador corresponde al alcance de esta versión.

**Requisitos funcionales del sistema**

| ID | Requisito funcional | Descripcion |
| --- | --- | --- |
| RF-01 | Registrar clientes | Guardar id\_cliente (INT suministrado por la aplicación) y nombre (VARCHAR(100) obligatorio). Si no se envía fecha\_registro, MySQL asignará CURRENT\_DATE. El esquema no define documento, teléfono, correo, dirección ni unicidad del nombre. |
| RF-02 | Consultar y actualizar clientes | Buscar por id\_cliente o nombre y permitir actualizar el nombre. Las facturas relacionarán al cliente por id\_cliente; al consultar, se mostrará el nombre vigente. La clave foránea impide borrar clientes referenciados. |
| RF-03 | Registrar productos | Guardar nombre (VARCHAR(100)), unidad\_medida (VARCHAR(20)), precio\_unitario (DECIMAL(10,2)) y stock (INT). Exigir precio y stock no negativos; el esquema no define códigos, descripciones ni una lista cerrada de unidades. |
| RF-04 | Consultar y actualizar productos | Buscar por id\_producto o nombre y actualizar unidad\_medida, precio\_unitario o stock con los tipos y límites de db.sql. El precio mostrado en reportes de facturas será el vigente. Las claves foráneas impiden borrar productos referenciados. |
| RF-05 | Preparar una factura | Seleccionar un cliente y productos existentes, e indicar cantidades enteras positivas. Mostrar precio\_unitario vigente y validar stock disponible antes de guardar. La unidad\_medida es texto y no modifica el tipo entero de stock o cantidad. |
| RF-06 | Generar la factura | Guardar una fila en facturas con id\_factura autogenerado, id\_cliente existente, fecha\_factura por defecto CURRENT\_TIMESTAMP y estado Pagado explícito al completar una venta. Guardar una o más filas en detalle\_facturas con cantidad entera positiva. Si el estado se omite directamente en MySQL, se aplica Pendiente. |
| RF-07 | Calcular importes | Calcular subtotal y total al consultar, multiplicando cantidad por el precio\_unitario vigente de productos. El esquema no guarda precio histórico, subtotal ni total; una actualización de precio puede modificar los importes que muestran facturas anteriores. |
| RF-08 | Controlar existencias | Mostrar el stock entero vigente, validar que alcance para la venta y actualizarlo en la aplicación en PHP al guardar. La restricción CHECK de db.sql impide stock negativo en motores que apliquen CHECK, pero db.sql no implementa el descuento ni una transacción de venta. |
| RF-09 | Consultar e imprimir facturas | Buscar por id\_factura, id\_cliente o nombre del cliente, y fecha\_factura. Mostrar el estado, la fecha, los detalles, el precio\_unitario vigente, los subtotales y el total calculado. No se generará un número FAC ni se presentará un precio histórico. |
| RF-10 | Cancelar facturas | Permitir en la aplicación en PHP la transición Pagado a Cancelado y reponer las cantidades una sola vez. Bloquear la cancelación repetida. El esquema admite también Pendiente, cuyo flujo de transición no se define en esta versión. |
| RF-11 | Validar los registros | Validar los campos obligatorios, tipos y límites definidos: nombre de hasta 100 caracteres, unidad de medida de hasta 20, precio DECIMAL(10,2) no negativo, stock entero no negativo y cantidad entera positiva. El esquema no declara unicidad para nombres ni para el par factura-producto. |
| RF-12 | Eliminar registros sin historial | Solicitar confirmación y eliminar clientes o productos solo cuando no tengan referencias en las claves foráneas. No borrar facturas ni detalles desde la aplicación. db.sql no define reglas ON DELETE CASCADE. |
| RF-13 | Consultar reportes | Incluir una portada con resumen y enlaces a siete reportes individuales: «Clientes y cantidad de facturas», «Total vigente por factura», «Unidades facturadas por producto», «Detalle de facturas», «Existencias de productos», «Facturas por estado» y «Facturas pendientes». El resumen mostrará la cantidad de clientes y de sus facturas (todos los estados); el importe vigente de las facturas con detalle; las unidades facturadas; las líneas de detalle y facturas distintas; las existencias totales; el total y desglose por estado; y la cantidad de facturas pendientes. Mantener los cálculos y filtros de 3.5.1.1. |

## 3.4 Requisitos no funcionales

Los requisitos no funcionales establecen las condiciones de calidad y funcionamiento del aplicativo.

**Requisitos no funcionales del sistema**

| ID | Categoria | Especificacion |
| --- | --- | --- |
| RNF-01 | Usabilidad | Las acciones principales estarán en Clientes, Productos, Facturas y Reportes. Los errores identificarán el campo y la causa. Los productos agotados y las acciones bloqueadas por historial o estado serán visibles. |
| RNF-02 | Rendimiento | En el equipo local y sin operaciones concurrentes, se medirán al menos 100 ejecuciones completas de cada consulta por id\_cliente, id\_producto e id\_factura, desde el envío hasta mostrar los resultados. La base de prueba tendrá 1.000 clientes, 1.000 productos y 1.000 facturas, con un máximo de diez detalles por factura. Cumple si el percentil 95 del tiempo de respuesta de cada consulta es de dos segundos o menos. |
| RNF-03 | Exposición del servicio | La aplicación y MySQL escucharán únicamente en la interfaz de bucle local. No se expondrán rutas de datos ni el puerto de MySQL a la red local o a Internet. El control de acceso al equipo corresponde al sistema operativo. |
| RNF-04 | Integridad | La aplicación en PHP guardará ventas y cancelaciones con sus cambios de stock en transacciones y validaciones para evitar escrituras parciales. Los ajustes manuales se aplicarán en una operación atómica por producto y se coordinarán con ventas y cancelaciones concurrentes para evitar actualizaciones perdidas o stock negativo. db.sql define claves foráneas y CHECK de stock/cantidad, pero no contiene procedimientos de venta, bloqueo ni reposición de existencias. |
| RNF-05 | Compatibilidad | La interfaz funcionará en navegadores web modernos y permitirá imprimir el comprobante interno con las funciones habituales del navegador, conservando sus datos e importes. |
| RNF-06 | Mantenibilidad | El código se organizará en PHP y la estructura de la base de datos se reproducirá con db.sql: cuatro tablas, tres vistas y cinco procedimientos almacenados. El archivo no inserta datos y no define charset, collation, motor, índices únicos de negocio ni triggers. |

## 3.5 Modelo de datos y reglas de negocio

El modelo relacional coincide con db.sql: cuatro tablas con las columnas y tipos que aparecen a continuación. El archivo también crea tres vistas y cinco procedimientos almacenados. Las reglas sobre transacciones, estados de la venta y cambios de stock que no estén expresadas como restricciones en SQL corresponden a la aplicación en PHP.

| Tabla | Campos | Finalidad |
| --- | --- | --- |
| clientes | id\_cliente INT (PK, sin autoincremento), nombre VARCHAR(100) NOT NULL, fecha\_registro DATE DEFAULT CURRENT\_DATE. | Registrar clientes y su fecha de registro. |
| productos | id\_producto INT AUTO\_INCREMENT (PK), nombre VARCHAR(100) NOT NULL, unidad\_medida VARCHAR(20) NOT NULL, stock INT NOT NULL CHECK (stock >= 0), precio\_unitario DECIMAL(10,2) NOT NULL CHECK (precio\_unitario >= 0). | Registrar productos, existencias enteras y precio vigente. |
| facturas | id\_factura INT AUTO\_INCREMENT (PK), id\_cliente INT NOT NULL (FK), estado\_factura ENUM(Pagado, Cancelado, Pendiente) DEFAULT Pendiente, fecha\_factura DATETIME DEFAULT CURRENT\_TIMESTAMP. | Registrar una factura asociada a un cliente, con estado y fecha. No hay campo de número ni total. |
| detalle\_facturas | id\_detalle\_factura INT AUTO\_INCREMENT (PK), id\_factura INT NOT NULL (FK), id\_producto INT NOT NULL (FK), cantidad INT NOT NULL CHECK (cantidad > 0). | Relacionar factura y producto. No almacena precio\_unitario, subtotal ni total. |

### 3.5.1 Relaciones

* clientes 1-N facturas: un cliente puede tener varias facturas y cada factura referencia un id\_cliente existente.
* facturas 1-N detalle\_facturas: cada fila de detalle\_facturas referencia una factura existente.
* productos 1-N detalle\_facturas: un producto puede aparecer en varias filas de detalle\_facturas.
* facturas N-N productos: detalle\_facturas relaciona ambas tablas y utiliza id\_detalle\_factura como clave primaria. db.sql no declara unicidad para (id\_factura, id\_producto), por lo que permite más de una fila del mismo producto en una factura.

### 3.5.1.1 Vistas y procedimientos almacenados

| Objeto | Datos y comportamiento definidos en db.sql |
| --- | --- |
| vista\_clientes\_total\_facturas | Cuenta todas las facturas de cada cliente; incluye clientes sin facturas y no filtra por estado. |
| vista\_total\_facturas | Calcula el total por factura sumando cantidad por precio\_unitario actual. Usa uniones internas y no filtra estados. |
| vista\_unidades\_productos | Suma cantidades de todas las filas de detalle por producto; no excluye facturas Cancelado. |
| reporte\_detalle\_facturas() | Devuelve factura, cliente, estado, fecha, producto, cantidad, precio actual y subtotal; ordena por id\_factura. |
| reporte\_facturas\_clientes() | Cuenta todas las facturas por cliente, incluye clientes sin facturas y ordena de mayor a menor total. |
| reporte\_stock\_productos() | Devuelve id, nombre, stock y precio vigente; ordena por stock ascendente. |
| reporte\_facturas\_por\_estado() | Cuenta facturas agrupadas por estado, incluidos los estados presentes en la tabla. |
| reporte\_facturas\_pendientes() | Lista facturas con estado Pendiente, junto con cliente y fecha; ordena por fecha ascendente. |

### 3.5.2 Reglas de negocio

* Cada fila de facturas debe referenciar un cliente mediante id\_cliente y cada fila de detalle\_facturas debe referenciar una factura y un producto existentes. Las claves foráneas de db.sql protegen estas relaciones. La exigencia de al menos un detalle por venta se validará en la aplicación en PHP, ya que el esquema no la impone.
* unidad\_medida se almacena como VARCHAR(20); db.sql no enumera valores permitidos ni relaciona la unidad con cantidades decimales. stock y cantidad son INT: stock no puede ser negativo y cantidad debe ser mayor que cero según los CHECK definidos. La aplicación validará estas reglas antes de guardar.
* Al guardar, la aplicación en PHP volverá a validar cliente, precio vigente y stock disponible. db.sql no contiene lógica para reservar o descontar existencias; esas operaciones se implementarán en la aplicación.
* facturas guarda id\_cliente y detalle\_facturas guarda id\_producto y cantidad. El esquema no copia nombre del cliente, nombre del producto ni precio\_unitario a los detalles. Las consultas muestran los nombres y el precio vigentes en las tablas relacionadas.
* Actualizar productos.precio\_unitario afecta el subtotal y el total que las vistas, procedimientos y pantallas calculan para facturas anteriores. El esquema no conserva un precio histórico. Las consultas de cliente también muestran el nombre vigente del registro asociado.
* El ajuste manual fijará stock a un entero no negativo en una actualización atómica. La aplicación coordinará esa operación con ventas y cancelaciones concurrentes para evitar actualizaciones perdidas y stock negativo; db.sql no implementa esa coordinación.
* Al cancelar, la aplicación en PHP sumará al stock vigente las cantidades enteras del detalle, dentro de una operación atómica. La regla Pagado a Cancelado y la reposición única son reglas de aplicación, no restricciones del esquema.
* facturas.estado\_factura admite Pagado, Cancelado y Pendiente, y el valor predeterminado del esquema es Pendiente. la aplicación en PHP escribirá Pagado al completar la venta según el flujo de este SRS y solo permitirá la transición Pagado a Cancelado. No se define en esta versión el flujo de pago ni la transición de facturas Pendiente; el procedimiento de pendientes permite consultarlas.
* Los reportes calculan subtotal como cantidad por el precio\_unitario vigente del producto y total como suma de subtotales. Ninguno se almacena. Por tanto, si cambia el precio\_unitario, pueden cambiar los importes consultados de ventas anteriores. La aplicación usará valores decimales con dos posiciones para presentar los importes.
* la aplicación en PHP guardará factura, detalles y actualización de stock dentro de una transacción para evitar cambios parciales. db.sql define restricciones de claves y CHECK, pero no implementa esa transacción.
* detalle\_facturas usa id\_detalle\_factura INT AUTO\_INCREMENT como clave primaria. Las claves foráneas apuntan a factura y producto existentes. No se declara una restricción UNIQUE para el par (id\_factura, id\_producto), por lo que el esquema permite líneas repetidas del mismo producto.
* La aplicación solo ofrecerá eliminar clientes sin facturas y productos sin detalles. Las claves foráneas impiden eliminar registros referenciados; db.sql no declara ON DELETE CASCADE.
* La aplicación no permitirá modificar ni eliminar facturas guardadas ni sus detalles. Solo permitirá cancelar una factura en estado Pagado, cambiarla a Cancelado y reponer el stock según las cantidades de su detalle una sola vez. db.sql no define un identificador comercial de factura ni reglas de reutilización: id\_factura es la clave primaria autogenerada.
* Un producto con stock cero permanecerá visible y no se podrá agregar a la venta por decisión de la aplicación. La columna stock permite cero y la base de datos no define una etiqueta de interfaz.

### 3.5.3 Formatos y límites de los datos

Los identificadores de campo se escribirán como se indica en el modelo. La aplicación manejará texto Unicode y el despliegue configurará MySQL con utf8mb4, porque db.sql no declara el juego de caracteres ni la intercalación. Si fecha\_registro se omite o se deja vacía al crear un cliente, la aplicación omitirá la columna en la inserción para que MySQL aplique DEFAULT CURRENT\_DATE; no enviará NULL en ese caso. Las siguientes restricciones se aplicarán a los campos indicados en cada fila.

| Grupo de campos | Tipo | Restriccion |
| --- | --- | --- |
| Identificadores | INT | id\_cliente es PK y lo suministra la aplicación; id\_factura, id\_producto e id\_detalle\_factura son AUTO\_INCREMENT. Las claves primarias son únicas y no nulas; db.sql no declara UNSIGNED ni restricciones de positividad. |
| Nombre y fecha de cliente | VARCHAR(100), DATE | clientes.nombre es obligatorio. fecha\_registro tiene DEFAULT CURRENT\_DATE, pero no se declara NOT NULL. |
| Unidad de medida | VARCHAR(20) | productos.unidad\_medida es obligatoria. No hay ENUM ni catálogo de unidades en db.sql. |
| Existencias | INT | productos.stock es obligatorio y CHECK (stock >= 0). No admite fracciones en el esquema. |
| Precio unitario | DECIMAL(10,2) | productos.precio\_unitario es obligatorio y CHECK (precio\_unitario >= 0); rango representable: 0.00 a 99,999,999.99. |
| Estado y fecha de factura | ENUM, DATETIME | estado\_factura admite Pagado, Cancelado o Pendiente y tiene DEFAULT Pendiente. fecha\_factura tiene DEFAULT CURRENT\_TIMESTAMP; el script no define zona horaria. |
| Detalle de factura | INT | cantidad es obligatoria y CHECK (cantidad > 0). id\_detalle\_factura es la PK autogenerada. No existe una clave compuesta ni una restricción de unicidad por producto. |
| Importes calculados | Expresión de consulta | subtotal y total no son columnas: los reportes los calculan usando la cantidad y el precio\_unitario vigente de productos. |
| Campos no definidos | No existen en db.sql | El esquema no contiene documento, teléfono, correo, dirección, código de producto, descripción, número FAC, precio histórico, subtotal almacenado ni total almacenado. |
| Configuración del esquema | No especificada | db.sql no declara charset, collation, motor de almacenamiento, zona horaria, índices únicos de negocio ni datos iniciales. La base se llama la\_unica. |

### 3.5.4 Criterios de aceptación de la alineación con db.sql

* RF-02 y RF-11: el sistema permitirá clientes con nombres repetidos, porque db.sql no declara unicidad para nombre. Un cliente referenciado por una factura no podrá eliminarse; al consultar esa factura se verá el nombre vigente del cliente.
* RF-03, RF-05 y RF-08: productos aceptarán stock entero no negativo y precio DECIMAL(10,2) no negativo; cantidades fraccionarias, cero o negativas se rechazarán. Si el stock disponible es insuficiente, la aplicación en PHP rechazará la venta sin cambios parciales.
* RF-04 y RF-10: la aplicación en PHP actualizará el stock de manera atómica al vender y cancelar. Si una venta descontó tres unidades y luego el stock se ajustó a una unidad, cancelar la venta dejará cuatro; una segunda cancelación no cambiará el stock.
* RF-08 y RF-10: un producto con stock cero no se podrá agregar por la interfaz; una factura Cancelado tendrá bloqueada la cancelación. El servidor también rechazará solicitudes que intenten omitir estas reglas.
* RF-12: eliminar un cliente o producto sin referencias será posible tras confirmar. Si hay referencias en facturas o detalle\_facturas, la clave foránea impedirá la eliminación.
* RF-07, RF-11 y RF-13: las consultas calcularán subtotales con el precio vigente. Si el precio de un producto cambia, cambiará el importe presentado para facturas anteriores; el detalle no guarda un precio histórico. Precio acepta hasta dos decimales y un máximo de 99,999,999.99.
* RF-03, RF-05 y RF-11: productos.unidad\_medida es texto de hasta 20 caracteres. Tanto stock como cantidad son enteros; se rechazará cantidad cero o negativa y stock negativo. La base permite cualquier texto no nulo como unidad de medida.
* RNF-03 y RNF-04: el servicio atenderá en bucle local; ventas y cancelaciones usarán transacciones; los ajustes manuales se serializarán con movimientos concurrentes por producto y conservarán stock no negativo.
* RF-13: las vistas y procedimientos devolverán los datos descritos en 3.5.1.1. Los reportes de facturas por cliente y estado cuentan registros de cualquier estado; vista\_unidades\_productos también incluye detalles de facturas canceladas; reporte\_facturas\_pendientes solo devuelve estado Pendiente.

# Referencias

Especificación de requisitos de software (SRS): La Unica. (2026). [Documento de requisitos del proyecto, versión 1.4].

Equipo del proyecto. (2026). Esquema de la base de datos La Unica [Archivo db.sql, consultado el 27 de octubre de 2026].