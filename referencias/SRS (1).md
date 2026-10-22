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

5 de octubre de 2026

# Control del documento

*Control de la versión*

|  |  |  |  |
| --- | --- | --- | --- |
| Version | Fecha | Estado | Descripcion |
| 1.0 | 05/10/2026 | Reemplazada | Creacion del SRS y planteamiento de los requisitos funcionales (RF) y requisitos no funcionales (RNF) del sistema La Unica. |
| 1.1 | 06/10/2026 | Vigente | Corrección del modelo de datos: se eliminaron datos redundantes (copias del cliente y del producto, subtotal y total almacenados, id propio de detalle\_factura) y se agregaron tipo\_documento, unidad\_medida, correo, direccion y cantidades decimales. Se ajustaron los requisitos funcionales, las reglas de negocio y los formatos. |

**Contenido**

[Control del documento 2](#_Toc242092147)

[Resumen ejecutivo 5](#_Toc242092148)

[1 Introducción 6](#_Toc242092149)

[1.1 Planteamiento del problema 6](#_Toc242092150)

[1.2 Propósito 6](#_Toc242092151)

[1.3 Justificación del proyecto 6](#_Toc242092152)

[1.4 Objetivo general 6](#_Toc242092153)

[1.4.1 Objetivos específicos 6](#_Toc242092154)

[1.5 Alcance del proyecto 7](#_Toc242092155)

[1.6 Personal involucrado 8](#_Toc242092156)

[1.7 Definiciones acrónimos y abreviaturas 9](#_Toc242092157)

[1.7.1 Definiciones 9](#_Toc242092158)

[1.7.2 Acrónimos 10](#_Toc242092159)

[1.7.3 Abreviaturas 10](#_Toc242092160)

[1.8 Referencias 11](#_Toc242092161)

[1.9 Resumen 11](#_Toc242092162)

[1.10 Fuentes primarias de información 11](#_Toc242092163)

[2 Descripción general 12](#_Toc242092164)

[2.1 Perspectiva del producto 12](#_Toc242092165)

[2.2 Características de los usuarios 12](#_Toc242092166)

[2.3 Restricciones 13](#_Toc242092167)

[2.4 Suposiciones y dependencias 13](#_Toc242092168)

[2.4.1 Suposiciones 13](#_Toc242092169)

[2.4.2 Dependencias 14](#_Toc242092170)

[3 Requisitos específicos 15](#_Toc242092171)

[3.1 Requisitos comunes de las interfaces 15](#_Toc242092172)

[3.1.1 Interfaz de usuario 15](#_Toc242092173)

[3.1.2 Interfaz de hardware 15](#_Toc242092174)

[3.1.3 Interfaz de software 15](#_Toc242092175)

[3.1.4 Interfaz de comunicación 15](#_Toc242092176)

[3.2 Historias de usuario 16](#_Toc242092177)

[3.2.1 Gestión de clientes 16](#_Toc242092178)

[3.2.2 Gestión de productos 16](#_Toc242092179)

[3.2.3 Generación de facturas 16](#_Toc242092180)

[3.2.4 Consulta impresión y cancelación de facturas 16](#_Toc242092181)

[3.3 Requisitos funcionales 16](#_Toc242092182)

[3.4 Requisitos no funcionales 19](#_Toc242092183)

[3.5 Modelo de datos y reglas de negocio 21](#_Toc242092184)

[3.5.1 Relaciones 22](#_Toc242092185)

[3.5.2 Reglas de negocio 22](#_Toc242092186)

[3.5.3 Formatos y límites de los datos 25](#_Toc242092187)

[3.5.4 Criterios de aceptación de las correcciones 27](#_Toc242092188)

[Referencias 30](#_Toc242092189)

# Resumen ejecutivo

La Unica será una aplicación web sencilla para registrar clientes y productos y gestionar las ventas de una tienda. El modelo de datos estará compuesto únicamente por cuatro tablas: clientes, productos, factura y detalle\_factura. Cada factura pertenecerá a un cliente y contendrá uno o varios productos mediante la tabla pivote detalle\_factura.

El aplicativo permitirá registrar, consultar y actualizar clientes y productos; eliminar los registros sin facturas asociadas; generar facturas; calcular importes; controlar existencias; consultar e imprimir facturas; y cancelarlas conservando el historial. Cada línea de factura guardará la cantidad vendida y el precio unitario aplicado en la venta. Un operador utilizará el aplicativo en el mismo equipo donde se ejecute, sin cuentas o roles propios.

El desarrollo utilizará Laravel, MySQL y Laragon. El alcance priorizará un proceso de venta claro y un modelo relacional reducido que mantenga la consistencia entre clientes, productos, facturas y detalles.

# 1 Introducción

## 1.1 Planteamiento del problema

Los registros manuales o dispersos de clientes, productos y ventas dificultan localizar información y controlar las existencias. Al elaborar una factura de forma manual pueden presentarse errores en las cantidades, los precios y el cálculo del total.

La Unica centralizará esta información y relacionará cada venta con su cliente y sus productos. La tabla detalle\_factura permitirá registrar varios productos en una misma factura y conservar el precio aplicado a cada uno.

## 1.2 Propósito

Definir los requisitos de una aplicación web básica para registrar clientes y productos, generar facturas y consultar las ventas, utilizando exclusivamente las tablas clientes, productos, factura y detalle\_factura.

## 1.3 Justificación del proyecto

El proyecto permitirá aplicar análisis de requisitos, diseño de bases de datos relacionales y desarrollo web mediante un proceso de venta de alcance controlado. La estructura de cuatro tablas facilitará comprender y mantener las relaciones entre la información registrada y las facturas.

## 1.4 Objetivo general

Diseñar y desarrollar La Unica como una aplicación web para gestionar clientes, productos y facturas, con detalle\_factura como tabla pivote entre las facturas y sus productos.

### 1.4.1 Objetivos específicos

* Registrar, consultar y actualizar los datos de los clientes.
* Registrar, consultar y actualizar productos, con su unidad de medida, precio y existencias.
* Generar facturas asociadas a un cliente y a uno o varios productos.
* Registrar cantidades y precios de venta en detalle\_factura y calcular subtotales y totales.
* Actualizar las existencias al completar o cancelar una factura.
* Consultar e imprimir facturas y conservar el historial de ventas.
* Mantener la integridad de las relaciones entre las cuatro tablas.

## 1.5 Alcance del proyecto

El aplicativo comprenderá los módulos de clientes, productos y facturación. Un operador podrá realizar las funciones de estos módulos desde el mismo aplicativo. Los clientes serán registros de compradores asociados a las facturas y no cuentas de acceso al sistema.

Los clientes tendrán tipo y número de documento, nombre y, opcionalmente, teléfono, correo y dirección. Los productos tendrán código único, nombre, descripción opcional, unidad de medida, precio por unidad de medida y stock. El operador podrá actualizar estos datos con las restricciones definidas en 3.5.2, sin modificar los precios aplicados en facturas anteriores. Los productos agotados se mostrarán con el aviso Agotado y la acción de agregarlos a una factura deshabilitada. Solo se permitirá eliminar clientes sin facturas y productos sin detalles de factura asociados, incluidos los de facturas canceladas.

Para generar una factura, el operador seleccionará un cliente registrado, agregará productos e indicará cantidades. El sistema revalidará los datos y las existencias al guardar, calculará los importes y generará un número único. Conservará en detalle\_factura la cantidad y el precio unitario aplicado a cada producto. El estado inicial será Pagado; solo las facturas en ese estado podrán cancelarse y devolver existencias una sola vez.

La Unica es un prototipo formativo. En este alcance, la factura será un comprobante interno de venta calculado como cantidad por precio unitario; no incluirá cálculo de IVA u otros impuestos y no se presentará como factura fiscal o electrónica. Esta simplificación delimita el ejercicio académico y no incorpora reglas tributarias al aplicativo.

El alcance no incluirá administración de usuarios o roles, autenticación por cuentas, recuperación de contraseñas, gestión de categorías, tabla de tienda, contabilidad avanzada, descuentos, pasarelas de pago, facturación electrónica ante servicios externos ni aplicaciones móviles nativas. Las funciones utilizarán únicamente las cuatro tablas del modelo.

## 1.6 Personal involucrado

El desarrollo estará a cargo de los cinco integrantes del grupo. Cada integrante tendrá una responsabilidad principal y podrá apoyar otras actividades del proyecto cuando sea necesario.

**Integrantes, roles y responsabilidades del proyecto**

|  |  |  |
| --- | --- | --- |
| Nombre | Rol principal | Responsabilidades |
| Carlos Stiven Gutiérrez Ramírez | Desarrollador Frontend y UX/UI | Diseñar y desarrollar las interfaces, navegación, componentes visuales y adaptación responsive. |
| Camilo Andrés Tamayo Duran | Desarrollador Backend e Integraciones | Desarrollar la lógica del servidor, servicios y procesos de negocio de la aplicación. |
| Kevin Fernando Martínez Pérez | Analista de Requisitos y Documentación | Mantener el SRS, organizar requisitos, trazabilidad y documentación técnica. |
| Samuel Santiago Ramírez Fierro | Responsable de Base de Datos | Diseñar y mantener la estructura de datos, relaciones, consultas e integridad de la información. |
| Santiago Ortiz Claros | Responsable de Calidad, Pruebas y Seguridad | Diseñar y ejecutar pruebas, registrar errores, validar requisitos y apoyar la revisión de seguridad. |

## 1.7 Definiciones acrónimos y abreviaturas

### 1.7.1 Definiciones

|  |  |
| --- | --- |
| Termino | Definicion |
| Cliente | Comprador registrado en clientes y asociado a una o varias facturas. |
| Operador | Persona de la tienda que registra clientes y productos y realiza las operaciones de facturación. No constituye una tabla del modelo. |
| Producto | Artículo registrado en productos con código único, unidad de medida, precio por unidad de medida y cantidad disponible. |
| Factura | Registro de una venta asociada a un cliente, con número único, fecha y estado. Su total se calcula a partir de los detalles. |
| Detalle de factura | Registro de detalle\_factura que enlaza una factura con un producto y conserva la cantidad y el precio unitario aplicado. Su subtotal se calcula. |
| Stock | Cantidad disponible de un producto, expresada en su unidad de medida. |
| Unidad de medida | Forma en que se vende un producto (UND, KG, G, LB, L o ML); define si el stock y la cantidad admiten decimales. |
| Tabla pivote | Tabla que resuelve la relación muchos a muchos entre factura y productos y almacena los datos propios de cada línea de venta. |

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

La Unica permitirá gestionar clientes y productos y registrar ventas mediante factura y detalle\_factura. El documento define el alcance, los usuarios, las interfaces, los requisitos y las reglas del modelo de cuatro tablas.

## 1.10 Fuentes primarias de información

Los requisitos se fundamentan en la definición del proyecto y en la indicación de utilizar exclusivamente clientes, productos, factura y detalle\_factura. El funcionamiento se organiza alrededor del registro de clientes, el registro de productos y la facturación básica.

# 2 Descripción general

## 2.1 Perspectiva del producto

La Unica será una aplicación web independiente para una tienda y se ejecutará localmente en el equipo del operador. Laravel implementará la lógica, MySQL almacenará las cuatro tablas y Laragon proporcionará el entorno de desarrollo. El navegador accederá al servicio por la interfaz de bucle local del mismo equipo.

*Tecnologías seleccionadas*

|  |  |
| --- | --- |
| Elemento | Tecnologia |
| Framework | Laravel |
| Motor de base de datos | MySQL |
| Entorno de desarrollo | Laragon |

## 2.2 Características de los usuarios

**Participantes del proceso y su relacion con el aplicativo**

|  |  |  |
| --- | --- | --- |
| Participante | Caracteristicas | Funciones o relacion con el aplicativo |
| Operador de la tienda | Persona con conocimientos básicos de manejo de formularios y ventas. | Registrar, consultar y actualizar clientes y productos; generar, consultar, imprimir y cancelar facturas. |
| Cliente | Persona que realiza una compra y cuyos datos se registran en clientes. | Quedar asociado a sus facturas y recibir la factura impresa. No opera el aplicativo ni tiene una cuenta. |

## 2.3 Restricciones

* La aplicación será web y utilizará Laravel, MySQL y Laragon.
* El esquema del aplicativo utilizará únicamente clientes, productos, factura y detalle\_factura.
* La relación N-N entre factura y productos se resolverá mediante detalle\_factura.
* Cada factura se asociará a un cliente registrado y contendrá al menos un producto.
* No se implementarán módulos que requieran tablas adicionales.
* Las facturas guardadas conservarán el precio unitario aplicado a cada producto, aunque se actualice el precio del producto.

## 2.4 Suposiciones y dependencias

### 2.4.1 Suposiciones

* El aplicativo y la base de datos se ejecutarán en el equipo del operador, sin acceso desde otros equipos ni publicación en Internet. El acceso al equipo se administrará mediante el sistema operativo; el aplicativo no autenticará al operador.
* El operador registrará datos válidos y mantendrá actualizados los clientes, productos y existencias.
* Los clientes se registrarán antes de completar una factura.
* Los equipos dispondrán de un navegador compatible; la impresión utilizará las opciones del navegador.

### 2.4.2 Dependencias

* Servidor capaz de ejecutar Laravel.
* Servicio MySQL disponible.
* Entorno Laragon para desarrollo local.
* Disponibilidad de la interfaz de bucle local para comunicar el navegador, la aplicación y la base de datos en el mismo equipo.

# 3 Requisitos específicos

## 3.1 Requisitos comunes de las interfaces

### 3.1.1 Interfaz de usuario

La interfaz ofrecerá acceso a Clientes, Productos y Facturas, con campos obligatorios, errores de validación y confirmación de resultados. La preparación de facturas mostrará cliente, productos, cantidades e importes. Los productos con stock cero mostrarán Agotado y no podrán seleccionarse. La cancelación estará deshabilitada para facturas Cancelado; la eliminación estará deshabilitada en registros con historial asociado.

### 3.1.2 Interfaz de hardware

El sistema podrá utilizarse desde un equipo con navegador web. No requerirá hardware especializado. Para obtener una copia física de la factura se utilizará una impresora disponible en el equipo del operador.

### 3.1.3 Interfaz de software

Laravel gestionará la aplicación y se comunicará con MySQL para leer y guardar clientes, productos, factura y detalle\_factura. Laragon se utilizará como entorno local de desarrollo.

### 3.1.4 Interfaz de comunicación

La comunicación entre navegador y aplicación se realizará mediante solicitudes web. El servidor validará los datos antes de guardarlos y devolverá mensajes claros si una operación no puede completarse.

## 3.2 Historias de usuario

### 3.2.1 Gestión de clientes

Como operador, quiero registrar, buscar y actualizar clientes para seleccionar al comprador de una venta. Quiero que las facturas ya emitidas sigan identificando al mismo comprador y poder eliminar únicamente clientes sin facturas asociadas.

### 3.2.2 Gestión de productos

Como operador, quiero registrar y actualizar productos, con su unidad de medida, precio y stock, para preparar ventas con datos vigentes. Quiero ver los productos agotados sin poder agregarlos a una factura y eliminar únicamente productos sin detalles de factura asociados.

### 3.2.3 Generación de facturas

Como operador, quiero seleccionar un cliente registrado y agregar productos con sus cantidades para generar una factura cuyo total se calcule automáticamente y cuyo registro actualice las existencias.

### 3.2.4 Consulta impresión y cancelación de facturas

Como operador, quiero consultar e imprimir facturas con los precios aplicados en la venta. Quiero cancelar únicamente las que estén en estado Pagado, devolver sus cantidades al stock vigente y ver bloqueada la acción cuando ya estén en estado Cancelado.

## 3.3 Requisitos funcionales

Los requisitos funcionales describen las operaciones que el aplicativo ofrecerá al operador. Cada identificador corresponde al alcance de esta versión.

**Requisitos funcionales del sistema**

|  |  |  |
| --- | --- | --- |
| ID | Requisito funcional | Descripcion |
| RF-01 | Registrar clientes | Guardar tipo y número de documento (únicos en conjunto), nombre y, opcionalmente, teléfono, correo y dirección. Validar los formatos definidos en 3.5.3 antes de registrar. |
| RF-02 | Consultar y actualizar clientes | Buscar por nombre o documento y actualizar datos sin cambiar el id. No modificar el tipo ni el número de documento si el cliente tiene facturas. Las facturas muestran los datos vigentes del cliente por su id\_cliente. |
| RF-03 | Registrar productos | Guardar código único, nombre, descripción opcional, unidad de medida, precio por unidad de medida y stock en esa unidad. Validar los formatos definidos en 3.5.3. |
| RF-04 | Consultar y actualizar productos | Buscar por código o nombre y actualizar datos, precio o stock actual no negativo. No modificar el código ni la unidad de medida si el producto tiene ventas. Los ajustes no modificarán ventas previas. Los cambios de stock se coordinarán con ventas y cancelaciones. |
| RF-05 | Preparar una factura | Seleccionar cliente y productos disponibles e indicar cantidades positivas (enteras si la unidad es UND; hasta tres decimales en las demás). Antes de guardar, revalidar datos, precios y stock; si cambian los datos del cliente o precios, actualizar la vista y pedir confirmación. |
| RF-06 | Generar la factura | Guardar número único, cliente, fecha y estado Pagado con sus detalles (producto, cantidad y precio unitario aplicado) en una transacción. Exigir cliente existente y al menos un detalle válido. |
| RF-07 | Calcular importes | Calcular el subtotal de cada línea (cantidad por precio unitario) y el total (suma de subtotales) con aritmética decimal y dos decimales, sin almacenarlos. Rechazar importes fuera del rango definido, sin guardar ni descontar stock. |
| RF-08 | Controlar existencias | Mostrar Agotado y deshabilitar productos con stock cero. Revalidar en el servidor y descontar al guardar. Rechazar cantidades superiores al stock (en la unidad del producto), incluso si este cambia durante la preparación. |
| RF-09 | Consultar e imprimir facturas | Buscar por número, cliente (nombre o documento) o fecha. Mostrar e imprimir el cliente y los productos con su unidad de medida, cantidades, precios aplicados, subtotales, total y estado. |
| RF-10 | Cancelar facturas | Permitir solo Pagado a Cancelado y sumar las cantidades al stock vigente una sola vez. Deshabilitar y rechazar la acción si ya está Cancelado, sin cambios. Bloquear cancelaciones concurrentes duplicadas. |
| RF-11 | Validar los registros | Validar campos obligatorios, unicidad, formatos, límites e importes. Rechazar stock negativo y cantidades no positivas; exigir cantidades enteras cuando la unidad sea UND y hasta tres decimales en las demás; mostrar el motivo y conservar los datos previamente guardados. |
| RF-12 | Eliminar registros sin historial | Pedir confirmación y eliminar solo clientes sin facturas o productos sin detalle\_factura. Rechazar la eliminación si existen referencias, también en facturas canceladas. No eliminar facturas o detalles guardados. |
| RF-13 | Conservar el precio histórico | Copiar el precio vigente del producto en precio\_unitario de detalle\_factura al facturar y mantenerlo inmutable. No copiar datos del cliente ni del producto: obtenerlos por relación. No modificar el tipo y número de documento, el código ni la unidad de medida si existen facturas. |

## 3.4 Requisitos no funcionales

Los requisitos no funcionales establecen las condiciones de calidad y funcionamiento del aplicativo.

**Requisitos no funcionales del sistema**

|  |  |  |
| --- | --- | --- |
| ID | Categoria | Especificacion |
| RNF-01 | Usabilidad | Las acciones principales estarán en Clientes, Productos y Facturas. Los errores identificarán el campo y la causa. Los productos agotados y las acciones bloqueadas por historial o estado serán visibles. |
| RNF-02 | Rendimiento | En el equipo local de desarrollo, el 95 % de las consultas por número, documento o código deberá responder en dos segundos o menos con 1.000 clientes, 1.000 productos y 1.000 facturas de hasta diez detalles. |
| RNF-03 | Exposición del servicio | La aplicación y MySQL escucharán únicamente en la interfaz de bucle local. No se expondrán rutas de datos ni el puerto de MySQL a la red local o a Internet. El control de acceso al equipo corresponde al sistema operativo. |
| RNF-04 | Integridad | Venta, detalles y stock se guardarán en una transacción. Ajustes manuales, ventas y cancelaciones se coordinarán con bloqueos de registros. La cancelación bloqueará la factura y verificará Pagado antes de reponer stock. |
| RNF-05 | Compatibilidad | La interfaz funcionará en navegadores web modernos y permitirá imprimir el comprobante interno con las funciones habituales del navegador, conservando sus datos e importes. |
| RNF-06 | Mantenibilidad | El código se organizará con Laravel y el esquema será reproducible. Se mantendrán exclusivamente las cuatro tablas y las relaciones, índices únicos y restricciones definidos. |

## 3.5 Modelo de datos y reglas de negocio

El modelo se compondrá únicamente de las cuatro tablas siguientes. Los campos de identificación y las claves foráneas permitirán mantener las relaciones entre clientes, facturas y productos.

|  |  |  |
| --- | --- | --- |
| Tabla | Campos | Finalidad |
| clientes | id\_cliente (PK), tipo\_documento, numero\_documento, nombre, telefono opcional, correo opcional, direccion opcional. Tipo y número de documento únicos en conjunto. | Registrar compradores y sus datos actuales. |
| productos | id\_producto (PK), codigo único, nombre, descripcion opcional, unidad\_medida, precio, stock | Registrar artículos, su unidad de medida, el precio vigente por unidad y las existencias. |
| factura | id\_factura (PK), numero único, fecha, id\_cliente (FK), estado | Registrar una venta y su cliente. Estado Pagado o Cancelado. El total se calcula a partir de los detalles. |
| detalle\_factura | id\_factura (PK, FK), id\_producto (PK, FK), cantidad, precio\_unitario | Resolver la relación N-N y conservar la cantidad y el precio aplicados a cada producto. El subtotal se calcula. |

### 3.5.1 Relaciones

* clientes 1-N factura: un cliente puede tener varias facturas y cada factura pertenece a un cliente.
* factura 1-N detalle\_factura: cada factura contiene uno o varios detalles y cada detalle pertenece a una factura.
* productos 1-N detalle\_factura: un producto puede aparecer en varios detalles y cada detalle corresponde a un producto.
* factura N-N productos: detalle\_factura es la tabla pivote que resuelve esta relación; su clave primaria es compuesta (id\_factura, id\_producto).

### 3.5.2 Reglas de negocio

* Una factura tendrá un cliente existente y al menos un producto con cantidad positiva. La clave primaria compuesta de detalle\_factura garantizará una única línea por producto en cada factura; si el producto se agrega nuevamente, se acumulará la cantidad y se revalidará el stock.
* Cada producto se venderá en una unidad de medida (UND, KG, G, LB, L o ML). El precio será el valor de una unidad de medida, y el stock y la cantidad se expresarán en esa misma unidad. Si la unidad es UND, stock y cantidad serán enteros; en las demás unidades admitirán hasta tres decimales.
* Al guardar, el servidor tomará los datos actuales del cliente y los precios vigentes. Si difieren de la vista preparada, actualizará el resumen y solicitará confirmación antes de emitir. La preparación no reservará stock; las existencias se comprobarán otra vez dentro de la transacción.
* La factura no copiará datos del cliente ni del producto: guardará el id\_cliente y, en cada línea de detalle\_factura, el id\_producto, la cantidad y el precio\_unitario aplicado en la venta. Las consultas e impresiones obtendrán el nombre, documento y teléfono del cliente y el código, nombre y unidad de medida del producto mediante las relaciones. El precio\_unitario es el único dato que se conserva, porque el precio de un producto cambia con el tiempo y la factura debe mantener el valor cobrado.
* Actualizar el precio de un producto no modificará el precio\_unitario de las facturas anteriores. No se podrán modificar el tipo ni el número de documento de un cliente con facturas, ni el código ni la unidad de medida de un producto con detalles de factura, para que cada factura siga identificando a la misma persona y al mismo artículo. La búsqueda por id\_cliente recuperará todas sus facturas; la búsqueda por nombre o documento utilizará los datos vigentes del cliente.
* El ajuste manual fijará el stock actual a una cantidad válida. No modificará ventas previas y se coordinará con la facturación y cancelación mediante bloqueo del producto. No se exigirá que el stock actual iguale o supere las cantidades vendidas en facturas anteriores.
* Cancelar una factura sumará sus cantidades al stock vigente, aunque ese stock haya sido ajustado manualmente. No restaurará un valor anterior del stock ni lo sustituirá por la cantidad vendida. Toda reposición deberá respetar el límite del campo stock; si lo excede, se rechazará la cancelación completa.
* Solo se permitirá la transición Pagado a Cancelado. La acción se deshabilitará en la interfaz y se rechazará en el servidor si el estado ya es Cancelado. Dos solicitudes simultáneas no podrán reponer existencias dos veces. No se reactivarán facturas canceladas.
* El subtotal de cada línea será cantidad por precio\_unitario, redondeado a dos decimales, y el total será la suma de los subtotales. Ninguno de los dos se almacenará: se calcularán al consultar o imprimir la factura. Los cálculos usarán valores decimales exactos, no coma flotante binaria. Las entradas monetarias tendrán como máximo dos decimales; se rechazarán valores o resultados que excedan el rango definido.
* Completar una venta guardará factura, detalles y descuento de stock como una sola operación. Cancelarla actualizará estado y stock como una sola operación. Si falla cualquier validación o escritura, no se aplicará ningún cambio parcial.
* La clave primaria de detalle\_factura será la combinación de id\_factura e id\_producto, que no podrá repetirse. Las claves foráneas apuntarán a registros existentes y rechazarán eliminaciones que rompan relaciones.
* Solo se podrán eliminar físicamente clientes sin ninguna factura y productos sin ningún detalle\_factura. Las facturas canceladas también conservarán esas referencias. La eliminación requerirá confirmación y una comprobación en el servidor inmediatamente antes de ejecutarse.
* Las facturas y sus detalles guardados no se eliminarán ni se modificarán para corregir una venta; se cancelará la factura y, cuando corresponda, se emitirá otra. No se reutilizarán los números de facturas canceladas.
* Un producto con stock cero permanecerá visible con el aviso Agotado y no podrá agregarse a una factura. Si se le asignan existencias positivas, volverá a estar disponible.

### 3.5.3 Formatos y límites de los datos

Los identificadores de campo se escribirán como se indica en el modelo. Los campos obligatorios no admitirán valores vacíos. Los textos se guardarán en UTF-8; los campos opcionales sin valor se guardarán como NULL. Las siguientes restricciones se aplicarán a los campos indicados en cada fila.

|  |  |  |
| --- | --- | --- |
| Grupo de campos | Tipo | Restriccion |
| Identificadores y FK | BIGINT UNSIGNED | id autogenerado y positivo en clientes, productos y factura. Las FK serán obligatorias y referenciarán el id correspondiente. detalle\_factura usa clave primaria compuesta (id\_factura, id\_producto). |
| Nombres | VARCHAR(120) | nombre (clientes y productos): de 2 a 120 caracteres Unicode, sin espacios al inicio o al final. |
| Tipo de documento | ENUM | tipo\_documento: únicamente CC, CE, NIT o PAS. Junto con numero\_documento forma una combinación única en clientes. |
| Número de documento | VARCHAR(20) | numero\_documento: de 5 a 20 letras o dígitos ASCII. Recortar espacios externos y convertir letras a mayúsculas. Único junto con tipo\_documento. |
| Teléfono | VARCHAR(15), NULL | telefono: opcional; si se proporciona, de 7 a 15 dígitos, sin separadores. |
| Correo | VARCHAR(120), NULL | correo: opcional; si se proporciona, debe tener formato de correo electrónico válido, máximo 120 caracteres. |
| Dirección | VARCHAR(150), NULL | direccion: opcional, máximo 150 caracteres Unicode. |
| Código | VARCHAR(30) | codigo: de 3 a 30 letras ASCII, dígitos, guion o guion bajo; recortar espacios externos y convertir a mayúsculas. Único solo en productos. |
| Descripción | VARCHAR(500), NULL | descripcion: opcional, máximo 500 caracteres Unicode. |
| Unidad de medida | ENUM | unidad\_medida: únicamente UND, KG, G, LB, L o ML. No se puede modificar si el producto tiene detalles de factura. |
| Existencias y cantidad | DECIMAL(12,3) | stock: de 0,000 a 999.999.999,999. cantidad: de 0,001 a 999.999.999,999. Hasta tres decimales; enteros cuando la unidad de medida sea UND. |
| Precio unitario | DECIMAL(12,2) | precio (por unidad de medida) y precio\_unitario: de 0,00 a 9.999.999.999,99; máximo dos decimales. |
| Importes de venta | Calculado (no se almacena) | subtotal y total se calculan como cantidad por precio\_unitario y suma de subtotales, con dos decimales. Se rechaza la venta si un subtotal o el total excede 9.999.999.999.999.999,99. |
| Número de factura | VARCHAR(20) | numero: prefijo FAC- seguido de 16 dígitos. Generación automática, índice único y nuevo número si se detecta colisión. No se exige consecutividad. |
| Fecha de venta | DATETIME | fecha: asignada por el servidor al guardar, en la zona America/Bogota. Almacenamiento AAAA-MM-DD HH:MM:SS; presentación DD/MM/AAAA HH:MM:SS. |
| Estado | ENUM | estado: únicamente Pagado o Cancelado. Inicial Pagado y transición única a Cancelado. |

### 3.5.4 Criterios de aceptación de las correcciones

* RF-02 y RF-13: si se actualiza el precio de un producto, las facturas anteriores conservarán su precio\_unitario. Si se intenta cambiar el tipo o el número de documento de un cliente con facturas, o el código o la unidad de medida de un producto con ventas, el sistema rechazará el cambio y conservará los datos guardados.
* RF-04, RF-05 y RF-08: si el precio cambia durante la preparación, se actualizará el resumen y se pedirá confirmación. Si el stock resulta insuficiente, la factura se rechazará sin guardar detalles ni descontar existencias.
* RF-04 y RF-10: si una venta descontó tres unidades y luego el stock se ajustó a una unidad, cancelar esa factura dejará cuatro unidades. Una segunda solicitud de cancelación será rechazada y mantendrá cuatro unidades.
* RF-08 y RF-10: un producto agotado mostrará Agotado sin acción de agregado disponible; una factura Cancelado mostrará la cancelación bloqueada. El servidor también rechazará solicitudes que intenten omitir esas restricciones.
* RF-12: eliminar un cliente o producto sin referencias será posible tras confirmar. Si tiene referencias en una factura Pagado o Cancelado, el sistema rechazará la eliminación y conservará el registro.
* RF-07 y RF-11: dos unidades a 0,10 producirán un subtotal de 0,20 y 2,500 kg a 4.200,00 producirán 10.500,00. Se rechazarán precios con más de dos decimales, resultados fuera de rango, formatos inválidos y documentos, códigos o números duplicados.
* RF-03, RF-05 y RF-11: un producto con unidad UND solo aceptará stock y cantidades enteras; uno con unidad KG, LB, L u otra unidad medible aceptará hasta tres decimales (por ejemplo, 2,500 kg). Se rechazarán cantidades cero, negativas o con más de tres decimales.
* RNF-03 y RNF-04: el servicio responderá desde el mismo equipo por bucle local y no admitirá conexiones entrantes desde otro equipo. Solicitudes simultáneas de venta, ajuste o cancelación mantendrán stock válido y una sola reposición por factura.

# Referencias

*Especificación de requisitos de software (SRS): La Unica*. (2026). [Documento de requisitos del proyecto, versión 1.0].