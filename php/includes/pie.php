<?php
// ===================================================================
// pie.php
// Parte de abajo de todas las páginas. Cierra lo que abrió menu.php
// y carga los scripts.
// ===================================================================
?>
</main>

<script>
    // Botón de modo claro / oscuro: cambia el tema y lo recuerda.
    document.getElementById('cambiar-tema').addEventListener('click', function () {
        var html = document.documentElement;
        var nuevo = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-bs-theme', nuevo);
        try { localStorage.setItem('tema', nuevo); } catch (e) {}
    });

    // Buscador: oculta las filas/tarjetas cuyo data-buscar no contiene el texto.
    var buscador = document.querySelector('[data-buscador]');
    if (buscador) {
        buscador.addEventListener('input', function () {
            var texto = buscador.value.toLowerCase().trim();
            var visibles = 0;
            document.querySelectorAll('[data-buscar]').forEach(function (elemento) {
                var coincide = elemento.dataset.buscar.toLowerCase().includes(texto);
                elemento.hidden = !coincide;
                if (coincide) visibles++;
            });
            document.querySelector('.sin-resultados').hidden = visibles > 0;
        });
    }
</script>

</body>
</html>
