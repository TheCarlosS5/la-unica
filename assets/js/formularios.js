/* Confirmación visual de envío sin perder el nombre/valor del botón pulsado. */
(() => {
  const formularios = document.querySelectorAll('form[method="post"], form[data-carga]');
  formularios.forEach(form => {
    if (form.id === 'formulario-factura') return;
    form.addEventListener('submit', event => {
      if (form.dataset.enviando === '1') { event.preventDefault(); return; }
      form.dataset.enviando = '1';
      form.setAttribute('aria-busy', 'true');
      const button = event.submitter || form.querySelector('button[type="submit"]');
      if (!button) return;
      button.setAttribute('aria-disabled', 'true');
      const label = button.querySelector('span') || button;
      const original = label.textContent;
      label.textContent = form.hasAttribute('data-carga') ? 'Cargando…' : 'Procesando…';

      window.addEventListener('pageshow', () => {
        delete form.dataset.enviando;
        form.removeAttribute('aria-busy');
        button.removeAttribute('aria-disabled');
        label.textContent = original;
      }, { once: true });
    });
  });
  document.querySelector('[aria-invalid="true"]')?.focus();
})();
