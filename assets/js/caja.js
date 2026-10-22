/**
 * La Única - Caja (nueva venta)
 * Archivo: assets/js/caja.js  ·  Lo carga php/facturas/nueva_factura.php
 *
 * JavaScript simple, sin librerías. Todo pasa en el navegador:
 *   - Cada botón .producto-boton trae sus datos en atributos data-*
 *     (id, nombre, unidad, precio, stock) que escribe el PHP.
 *   - "ticket" es un objeto { idProducto: { ...datos, cantidad } }.
 *   - Cada cambio llama a dibujar(), que vuelve a pintar el ticket completo.
 *
 * Nada se guarda en la base de datos desde aquí. Para guardar, el backend
 * recibe el <form> (ver el comentario al inicio de nueva_factura.php).
 */
(function () {
    'use strict';

    var form = document.getElementById('form-venta');
    if (!form) return;

    // --- Elementos de la página -------------------------------------------
    var grid = document.getElementById('productos-grid');
    var buscador = document.getElementById('buscar-producto');
    var sinResultados = document.getElementById('sin-resultados');
    var lista = document.getElementById('ticket-lineas');
    var camposOcultos = document.getElementById('campos-ocultos');
    var totalEl = document.getElementById('ticket-total');
    var efectivo = document.getElementById('efectivo');
    var cambioEl = document.getElementById('ticket-cambio');
    var btnCobrar = document.getElementById('cobrar');
    var btnVaciar = document.getElementById('vaciar-ticket');
    var cliente = document.getElementById('id_cliente');
    var aviso = document.getElementById('aviso-caja');
    var avisoTexto = document.getElementById('aviso-caja-texto');

    var botones = grid ? Array.prototype.slice.call(grid.querySelectorAll('.producto-boton')) : [];

    // Dinero con el mismo formato de las otras pantallas: $ 3.500,00
    var formatoDinero = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    function dinero(valor) {
        return formatoDinero.format(valor);
    }

    // Trabajamos en centavos (enteros) para evitar errores de decimales (0.1 + 0.2).
    function aCentavos(valor) {
        return Math.round(parseFloat(valor) * 100);
    }

    // --- Estado del ticket -------------------------------------------------
    var ticket = {};

    function productoDeBoton(boton) {
        return {
            id: boton.dataset.id,
            nombre: boton.dataset.nombre,
            unidad: boton.dataset.unidad,
            precio: aCentavos(boton.dataset.precio),
            stock: parseInt(boton.dataset.stock, 10),
            cantidad: 0
        };
    }

    function agregar(boton) {
        var id = boton.dataset.id;
        if (!ticket[id]) ticket[id] = productoDeBoton(boton);
        cambiarCantidad(id, ticket[id].cantidad + 1);
    }

    // Cambia la cantidad respetando el stock. Si llega a 0, se quita del ticket.
    function cambiarCantidad(id, nueva) {
        var linea = ticket[id];
        if (!linea) return;
        nueva = Math.floor(Number(nueva));
        if (isNaN(nueva) || nueva < 0) nueva = 0;

        if (nueva > linea.stock) {
            nueva = linea.stock;
            mostrarAviso('Solo hay ' + linea.stock + ' de ' + linea.nombre + ' en stock.');
        } else {
            ocultarAviso();
        }

        if (nueva === 0) {
            delete ticket[id];
        } else {
            linea.cantidad = nueva;
        }
        dibujar();
    }

    function vaciar() {
        ticket = {};
        efectivo.value = '';
        ocultarAviso();
        dibujar();
    }

    function total() {
        var suma = 0;
        Object.keys(ticket).forEach(function (id) {
            suma += ticket[id].precio * ticket[id].cantidad;
        });
        return suma;
    }

    // --- Avisos -------------------------------------------------------------
    var temporizadorAviso = null;
    function mostrarAviso(texto) {
        avisoTexto.textContent = texto;
        aviso.hidden = false;
        clearTimeout(temporizadorAviso);
        temporizadorAviso = setTimeout(ocultarAviso, 6000);
    }
    function ocultarAviso() {
        aviso.hidden = true;
    }

    // --- Dibujar ------------------------------------------------------------
    function crear(etiqueta, clase, texto) {
        var el = document.createElement(etiqueta);
        if (clase) el.className = clase;
        if (texto !== undefined) el.textContent = texto;
        return el;
    }

    function dibujarLinea(linea) {
        var li = crear('li', 'ticket-linea');

        var nombre = crear('span', 'linea-nombre', linea.nombre);
        nombre.appendChild(crear('small', '', dinero(linea.precio / 100) + ' · ' + linea.unidad));

        var subtotal = crear('span', 'linea-subtotal', dinero((linea.precio * linea.cantidad) / 100));

        var grupo = crear('div', 'cantidad');
        grupo.setAttribute('role', 'group');
        grupo.setAttribute('aria-label', 'Cantidad de ' + linea.nombre);

        var menos = crear('button', '', '−');
        menos.type = 'button';
        menos.setAttribute('aria-label', 'Quitar uno de ' + linea.nombre);
        menos.addEventListener('click', function () {
            cambiarCantidad(linea.id, linea.cantidad - 1);
        });

        var campo = crear('input');
        campo.type = 'number';
        campo.min = '1';
        campo.max = String(linea.stock);
        campo.step = '1';
        campo.value = String(linea.cantidad);
        campo.setAttribute('inputmode', 'numeric');
        campo.setAttribute('aria-label', 'Cantidad de ' + linea.nombre);
        campo.addEventListener('change', function () {
            cambiarCantidad(linea.id, campo.value);
        });

        var mas = crear('button', '', '+');
        mas.type = 'button';
        mas.setAttribute('aria-label', 'Agregar uno de ' + linea.nombre);
        mas.addEventListener('click', function () {
            cambiarCantidad(linea.id, linea.cantidad + 1);
        });

        grupo.appendChild(menos);
        grupo.appendChild(campo);
        grupo.appendChild(mas);

        var quitar = crear('button', 'linea-quitar', 'Quitar');
        quitar.type = 'button';
        quitar.setAttribute('aria-label', 'Quitar ' + linea.nombre + ' del ticket');
        quitar.addEventListener('click', function () {
            cambiarCantidad(linea.id, 0);
        });

        li.appendChild(nombre);
        li.appendChild(subtotal);
        li.appendChild(grupo);
        li.appendChild(quitar);
        return li;
    }

    function dibujar() {
        var ids = Object.keys(ticket);

        // Líneas del ticket
        lista.textContent = '';
        if (ids.length === 0) {
            lista.appendChild(crear('li', 'ticket-vacio', 'Aún no hay productos. Toca uno de la izquierda.'));
        } else {
            ids.forEach(function (id) {
                lista.appendChild(dibujarLinea(ticket[id]));
            });
        }

        // Campos ocultos que recibirá el backend: cantidades[ID] = cantidad
        camposOcultos.textContent = '';
        ids.forEach(function (id) {
            var oculto = document.createElement('input');
            oculto.type = 'hidden';
            oculto.name = 'cantidades[' + id + ']';
            oculto.value = String(ticket[id].cantidad);
            camposOcultos.appendChild(oculto);
        });

        // Globitos con la cantidad en cada producto
        botones.forEach(function (boton) {
            var linea = ticket[boton.dataset.id];
            var globito = boton.querySelector('.producto-cantidad');
            boton.classList.toggle('en-ticket', !!linea);
            globito.textContent = linea ? String(linea.cantidad) : '';
        });

        // Totales y botones
        totalEl.textContent = dinero(total() / 100);
        btnCobrar.disabled = ids.length === 0;
        btnVaciar.disabled = ids.length === 0;
        calcularCambio();
    }

    function calcularCambio() {
        var recibido = efectivo.value === '' ? null : aCentavos(efectivo.value);
        var diferencia = recibido === null ? 0 : recibido - total();
        cambioEl.classList.toggle('falta', recibido !== null && diferencia < 0);
        if (recibido === null || total() === 0) {
            cambioEl.textContent = dinero(0);
        } else if (diferencia >= 0) {
            cambioEl.textContent = dinero(diferencia / 100);
        } else {
            cambioEl.textContent = 'Faltan ' + dinero(-diferencia / 100);
        }
    }

    // --- Buscador -----------------------------------------------------------
    function filtrar() {
        var texto = buscador.value.trim().toLowerCase();
        var visibles = 0;
        botones.forEach(function (boton) {
            var coincide = boton.dataset.nombre.toLowerCase().indexOf(texto) !== -1;
            boton.hidden = !coincide;
            if (coincide) visibles++;
        });
        if (sinResultados) sinResultados.hidden = visibles !== 0;
    }

    function primerVisibleDisponible() {
        for (var i = 0; i < botones.length; i++) {
            if (!botones[i].hidden && !botones[i].disabled) return botones[i];
        }
        return null;
    }

    // --- Eventos ------------------------------------------------------------
    botones.forEach(function (boton) {
        boton.addEventListener('click', function () {
            agregar(boton);
        });
    });

    if (buscador) {
        buscador.addEventListener('input', filtrar);
        // Enter agrega el primer producto que coincide y deja el buscador listo para el siguiente
        buscador.addEventListener('keydown', function (e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();
            var boton = primerVisibleDisponible();
            if (boton) {
                agregar(boton);
                buscador.select();
            }
        });
    }

    efectivo.addEventListener('input', calcularCambio);
    btnVaciar.addEventListener('click', vaciar);

    form.addEventListener('submit', function (e) {
        if (!cliente.value) {
            e.preventDefault();
            mostrarAviso('Elige el cliente antes de cobrar.');
            cliente.focus();
            return;
        }
        // Mientras el backend no esté conectado, el formulario no se envía.
        // Para conectarlo: en nueva_factura.php cambiar data-conectado="no" por "si".
        if (form.dataset.conectado !== 'si') {
            e.preventDefault();
            mostrarAviso('Esta pantalla es solo el diseño: falta conectar el guardado de la factura con el backend.');
        }
    });

    dibujar();
})();
