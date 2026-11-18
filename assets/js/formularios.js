/* Confirmación visual de envío sin perder el nombre/valor del botón pulsado. */
(() => {
  document.querySelectorAll('form[method="post"]').forEach(form => {
    if (form.id === 'formulario-factura') return;
    form.addEventListener('submit', event => {
      if (form.dataset.enviando === '1') { event.preventDefault(); return; }
      form.dataset.enviando = '1';
      form.setAttribute('aria-busy', 'true');
      const button = event.submitter || form.querySelector('[type="submit"]');
      if (!button) return;
      button.setAttribute('aria-disabled', 'true');
      const label = button.querySelector('span') || button;
      label.textContent = 'Procesando…';
    });
  });
  // Una página restaurada desde el historial debe permitir volver a enviar.
  window.addEventListener('pageshow', () => {
    document.querySelectorAll('form[data-enviando="1"]').forEach(() => location.reload());
  });
  document.querySelector('[aria-invalid="true"]')?.focus();
})();
