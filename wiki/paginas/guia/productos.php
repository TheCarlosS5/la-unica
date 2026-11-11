<?php if (!defined('LA_UNICA_WIKI')) { exit; } ?>
<p class="aviso"><strong>Guía visual actualizada.</strong> Las capturas se tomaron de la interfaz actual con datos ficticios y una base independiente. Haz clic en una imagen o enfócala y pulsa Enter para ampliarla; Escape cierra el visor.</p>
<h2 id="crear">1 Registrar un producto</h2>
<p>En Productos abre Nuevo producto. Completa nombre, unidad de medida, stock entero no negativo y precio no negativo con hasta dos decimales. El ID se genera automáticamente.</p>
<figure class="captura"><img src="img/actuales/productos-crear.jpg" alt="Producto nuevo — Campos completos antes de guardar." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Producto nuevo — Campos completos antes de guardar.</figcaption></figure>
<h2 id="guardar">2 Comprobar el registro</h2>
<p>Guarda el producto y revisa la confirmación. La unidad es una descripción, como paquete o caja; las cantidades vendidas son enteras.</p>
<figure class="captura"><img src="img/actuales/productos-creado.jpg" alt="Confirmación — Producto creado en la base ficticia." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Confirmación — Producto creado en la base ficticia.</figcaption></figure>
<h2 id="listado">3 Consultar y buscar</h2>
<p>El listado permite buscar por ID o nombre y acceder a las acciones de cada producto.</p>
<figure class="captura"><img src="img/actuales/productos-lista.jpg" alt="Catálogo actual — Existencias, precio y acciones por producto." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Catálogo actual — Existencias, precio y acciones por producto.</figcaption></figure>
<h2 id="stock">4 Reconocer el stock bajo</h2>
<p>El umbral de stock bajo es 5. Revisa las marcas de disponibilidad antes de vender; un producto con stock cero no puede completar una venta.</p>
<figure class="captura"><img src="img/actuales/productos-stock.jpg" alt="Existencias — Ejemplo de un producto con stock bajo." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Existencias — Ejemplo de un producto con stock bajo.</figcaption></figure>
<h2 id="editar">5 Editar datos y existencias</h2>
<p>Abre Editar y revisa nombre, unidad, stock y precio. Los cambios no alteran las copias históricas de las facturas anteriores.</p>
<figure class="captura"><img src="img/actuales/productos-editar.jpg" alt="Edición — Datos y existencias actuales del producto." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Edición — Datos y existencias actuales del producto.</figcaption></figure>
<h2 id="validar">6 Corregir valores inválidos</h2>
<p>El servidor también valida los datos y rechaza stock negativo. Corrige el campo señalado y vuelve a guardar.</p>
<figure class="captura"><img src="img/actuales/productos-validacion.jpg" alt="Validación — Una existencia negativa no se guarda." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Validación — Una existencia negativa no se guarda.</figcaption></figure>
<h2 id="eliminar">7 Revisar antes de eliminar</h2>
<p>Solo se eliminan productos sin detalles de factura asociados. La confirmación informa qué registro se pretende borrar; abrirla todavía no elimina el producto.</p>
<figure class="captura"><img src="img/actuales/productos-eliminar.jpg" alt="Eliminación — Confirmación previa para un producto sin ventas." width="1280" height="960" loading="lazy" decoding="async"><figcaption>Eliminación — Confirmación previa para un producto sin ventas.</figcaption></figure>
