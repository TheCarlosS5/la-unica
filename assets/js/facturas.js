(function () {
  'use strict';

  const formulario = document.getElementById('formulario-factura');
  const lineas = document.getElementById('lineas-factura');
  const plantilla = document.getElementById('plantilla-linea-factura');
  const botonAgregar = document.getElementById('agregar-linea-factura');
  const salidaTotal = document.getElementById('total-factura');

  if (!formulario || !lineas || !plantilla || !botonAgregar || !salidaTotal) return;

  let siguienteId = 0;

  function filas() {
    return Array.from(lineas.querySelectorAll('[data-linea-factura]'));
  }

  function centavos(precio) {
    const coincidencia = String(precio || '').match(/^(\d+)(?:\.(\d{1,2}))?$/);
    if (!coincidencia) return 0n;
    return BigInt(coincidencia[1]) * 100n + BigInt((coincidencia[2] || '').padEnd(2, '0'));
  }

  function monedaDesdeCentavos(valor) {
    const pesos = valor / 100n;
    const fraccion = String(valor % 100n).padStart(2, '0');
    const pesosFormateados = new Intl.NumberFormat('es-CO', { maximumFractionDigits: 0 }).format(pesos);
    return '$' + pesosFormateados + ',' + fraccion + ' COP';
  }

  function asignarIds(fila) {
    const select = fila.querySelector('[data-producto-select]');
    const cantidad = fila.querySelector('[data-cantidad]');
    const etiquetaProducto = fila.querySelector('.factura-producto-campo label');
    const etiquetaCantidad = fila.querySelector('.factura-cantidad-campo label');
    const sufijo = String(siguienteId++);

    select.id = 'id_producto_' + sufijo;
    cantidad.id = 'cantidad_' + sufijo;
    etiquetaProducto.htmlFor = select.id;
    etiquetaCantidad.htmlFor = cantidad.id;
  }

  function actualizarDetalle(fila) {
    const select = fila.querySelector('[data-producto-select]');
    const cantidad = fila.querySelector('[data-cantidad]');
    const detalle = fila.querySelector('[data-detalle-producto]');
    const opcion = select.selectedOptions[0];

    if (!opcion || !opcion.value) {
      cantidad.max = '2147483647';
      detalle.textContent = 'Selecciona un producto para consultar precio y existencias.';
      return;
    }

    const stock = Number(opcion.dataset.stock || 0);
    cantidad.max = String(stock);
    detalle.textContent = 'Precio unitario: ' + monedaDesdeCentavos(centavos(opcion.dataset.precio))
      + ' · Existencias disponibles: ' + stock;
  }

  function actualizarOpciones() {
    const todasLasFilas = filas();

    todasLasFilas.forEach(function (fila) {
      const select = fila.querySelector('[data-producto-select]');
      const actual = select.value;
      Array.from(select.options).forEach(function (opcion) {
        if (!opcion.value) return;
        const duplicadoEnOtraFila = todasLasFilas.some(function (otraFila) {
          return otraFila !== fila && otraFila.querySelector('[data-producto-select]').value === opcion.value;
        });
        opcion.disabled = Number(opcion.dataset.stock || 0) < 1 || duplicadoEnOtraFila;
      });
      actualizarDetalle(fila);
    });
  }

  function actualizarTotal() {
    let total = 0n;
    filas().forEach(function (fila) {
      const select = fila.querySelector('[data-producto-select]');
      const cantidadInput = fila.querySelector('[data-cantidad]');
      const opcion = select.selectedOptions[0];
      const cantidad = Number(cantidadInput.value);
      if (!opcion || !opcion.value || !Number.isSafeInteger(cantidad) || cantidad < 1) return;
      if (cantidad > Number(opcion.dataset.stock || 0)) return;
      total += centavos(opcion.dataset.precio) * BigInt(cantidad);
    });
    salidaTotal.textContent = monedaDesdeCentavos(total);
  }

  function actualizarControles() {
    const seleccionados = new Set(filas().map(function (fila) {
      return fila.querySelector('[data-producto-select]').value;
    }).filter(Boolean));
    const opcionesDisponibles = Array.from(filas()[0].querySelector('[data-producto-select]').options)
      .filter(function (opcion) { return opcion.value && Number(opcion.dataset.stock || 0) > 0; }).length;

    botonAgregar.disabled = seleccionados.size >= opcionesDisponibles;
    filas().forEach(function (fila) {
      fila.querySelector('[data-quitar-linea]').disabled = filas().length <= 1;
    });
  }

  function refrescar() {
    actualizarOpciones();
    actualizarTotal();
    actualizarControles();
  }

  filas().forEach(asignarIds);
  refrescar();

  botonAgregar.addEventListener('click', function () {
    const nuevaFila = plantilla.content.firstElementChild.cloneNode(true);
    asignarIds(nuevaFila);
    lineas.appendChild(nuevaFila);
    refrescar();
    nuevaFila.querySelector('[data-producto-select]').focus();
  });

  lineas.addEventListener('change', function (evento) {
    if (evento.target.matches('[data-producto-select]')) refrescar();
  });

  lineas.addEventListener('input', function (evento) {
    if (evento.target.matches('[data-cantidad]')) actualizarTotal();
  });

  lineas.addEventListener('click', function (evento) {
    const botonQuitar = evento.target.closest('[data-quitar-linea]');
    if (!botonQuitar || filas().length <= 1) return;
    botonQuitar.closest('[data-linea-factura]').remove();
    refrescar();
  });

  formulario.addEventListener('submit', function () {
    const botonGuardar = formulario.querySelector('button[type="submit"]');
    if (botonGuardar) {
      botonGuardar.disabled = true;
      botonGuardar.querySelector('span').textContent = 'Guardando…';
    }
  });
})();
