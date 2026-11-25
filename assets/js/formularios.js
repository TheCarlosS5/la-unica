/* Mantiene el botón disponible para que el navegador incluya su nombre y valor. */
(() => {
  const envios = new Map();
  const anuncio = document.createElement('div');
  anuncio.className = 'estado-envio-lector';
  anuncio.setAttribute('role', 'status');
  document.body.append(anuncio);

  document.querySelectorAll('form[method="post"], form.filtros-facturas, form.filtro-busqueda').forEach(form => {
    form.addEventListener('submit', event => {
      if (event.defaultPrevented) return;
      if (envios.has(form)) {
        event.preventDefault();
        return;
      }

      const button = event.submitter || form.querySelector('button[type="submit"]');
      if (!button) return;

      const label = button.querySelector('span') || button;
      const mensaje = button.dataset.textoEnvio || (form.method === 'get' ? 'Consultando…' : 'Procesando…');
      envios.set(form, { button, label, textoOriginal: label.textContent });
      form.setAttribute('aria-busy', 'true');
      button.setAttribute('aria-disabled', 'true');
      label.textContent = mensaje;
      anuncio.textContent = mensaje;
    });
  });

  window.addEventListener('pageshow', () => {
    envios.forEach(({ button, label, textoOriginal }, form) => {
      form.removeAttribute('aria-busy');
      button.removeAttribute('aria-disabled');
      label.textContent = textoOriginal;
    });
    envios.clear();
    anuncio.textContent = '';
  });

  document.querySelector('[aria-invalid="true"]')?.focus();
})();
