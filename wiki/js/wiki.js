/**
 * Archivo: wiki.js
 * Módulo: Wiki — interacciones.
 * Propósito: dar comportamiento a la wiki sin depender de librerías.
 * Funcionamiento:
 * - Menú lateral en celular: abrir con ☰, cerrar con el fondo o Escape.
 * - Pestañas: convierte cada bloque .pestanas en botones; sin JavaScript se ven todas.
 * - Botón «Copiar» en los bloques de código.
 * - Visor: al hacer clic en una captura se muestra en grande.
 * - Índice: resalta la sección que se está leyendo.
 */
(function () {
  'use strict';

  // ── Menú lateral en celular ──
  const botonMenu = document.getElementById('wiki-abrir-menu');
  const menu = document.getElementById('wiki-menu');
  const fondo = document.getElementById('wiki-fondo-menu');

  function abrirMenu(abrir) {
    menu.classList.toggle('abierto', abrir);
    fondo.hidden = !abrir;
    botonMenu.setAttribute('aria-expanded', String(abrir));
    if (abrir) {
      const activo = menu.querySelector('a.activo') || menu.querySelector('a');
      if (activo) activo.focus();
    } else {
      botonMenu.focus();
    }
  }

  if (botonMenu && menu && fondo) {
    botonMenu.addEventListener('click', function () { abrirMenu(!menu.classList.contains('abierto')); });
    fondo.addEventListener('click', function () { abrirMenu(false); });
  }

  // ── Pestañas ──
  document.querySelectorAll('.pestanas').forEach(function (grupo, numeroGrupo) {
    const paneles = Array.from(grupo.querySelectorAll(':scope > .pestana'));
    if (paneles.length === 0) return;
    const botones = document.createElement('div');
    botones.className = 'pestanas-botones';
    botones.setAttribute('role', 'tablist');

    paneles.forEach(function (panel, i) {
      const boton = document.createElement('button');
      boton.type = 'button';
      boton.textContent = panel.dataset.titulo;
      boton.id = 'pestana-' + numeroGrupo + '-' + i;
      boton.setAttribute('role', 'tab');
      panel.setAttribute('role', 'tabpanel');
      panel.setAttribute('aria-labelledby', boton.id);
      boton.addEventListener('click', function () { mostrar(i); });
      botones.appendChild(boton);
    });

    function mostrar(indice) {
      paneles.forEach(function (panel, i) {
        panel.hidden = i !== indice;
        botones.children[i].setAttribute('aria-selected', String(i === indice));
      });
    }

    grupo.prepend(botones);
    grupo.classList.add('lista');
    mostrar(0);
  });

  // ── Copiar código ──
  document.querySelectorAll('.wiki-contenido pre').forEach(function (bloque) {
    const boton = document.createElement('button');
    boton.type = 'button';
    boton.className = 'boton-copiar';
    boton.textContent = 'Copiar';
    boton.addEventListener('click', function () {
      const texto = bloque.querySelector('code') ? bloque.querySelector('code').innerText : bloque.innerText;
      const listo = function () {
        boton.textContent = '¡Copiado!';
        setTimeout(function () { boton.textContent = 'Copiar'; }, 1500);
      };
      if (navigator.clipboard) {
        navigator.clipboard.writeText(texto).then(listo).catch(function () { boton.textContent = 'Selecciona y copia'; });
      }
    });
    bloque.appendChild(boton);
  });

  // ── Visor de imágenes ──
  const visor = document.getElementById('wiki-visor');
  if (visor) {
    const imagenVisor = visor.querySelector('img');
    const pieVisor = visor.querySelector('.wiki-visor-pie');
    let origen = null;

    document.querySelectorAll('.captura img').forEach(function (imagen) {
      imagen.tabIndex = 0;
      const abrir = function () {
        origen = imagen;
        imagenVisor.src = imagen.src;
        imagenVisor.alt = imagen.alt;
        pieVisor.textContent = imagen.alt;
        visor.hidden = false;
        visor.querySelector('.wiki-visor-cerrar').focus();
      };
      imagen.addEventListener('click', abrir);
      imagen.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); abrir(); }
      });
    });

    visor.addEventListener('click', function () {
      visor.hidden = true;
      if (origen) origen.focus();
    });
  }

  // ── Escape cierra el visor y el menú ──
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    if (visor && !visor.hidden) { visor.hidden = true; return; }
    if (menu && menu.classList.contains('abierto')) abrirMenu(false);
  });

  // ── Índice: resaltar la sección visible ──
  const enlacesIndice = Array.from(document.querySelectorAll('.wiki-indice a'));
  if (enlacesIndice.length && 'IntersectionObserver' in window) {
    const porId = new Map(enlacesIndice.map(function (a) { return [decodeURIComponent(a.hash.slice(1)), a]; }));
    const observador = new IntersectionObserver(function (entradas) {
      entradas.forEach(function (entrada) {
        if (!entrada.isIntersecting) return;
        enlacesIndice.forEach(function (a) { a.classList.remove('activo'); });
        const enlace = porId.get(entrada.target.id);
        if (enlace) enlace.classList.add('activo');
      });
    }, { rootMargin: '-80px 0px -70% 0px' });
    document.querySelectorAll('.wiki-contenido h2[id], .wiki-contenido h3[id]').forEach(function (titulo) {
      observador.observe(titulo);
    });
  }
})();
