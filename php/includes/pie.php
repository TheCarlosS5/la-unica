<!--
/**
 * Archivo: pie.php
 * Módulo: Plantilla compartida de cierre.
 * Propósito: cerrar el contenedor principal y mostrar el pie común de las páginas.
 * Funcionamiento: imprime el cierre del shell y activa el menú lateral adaptable con JavaScript.
 * Puntos clave: abrir/cerrar menú, cerrar al pulsar el fondo o Escape, actualizar aria-expanded
 * y devolver el foco al botón que lo abrió.
 */
-->
        </main>
        <footer class="pie-app" role="contentinfo">
            <div class="pie-inner">
                <span class="pie-marca">La Única</span>
                <span class="pie-separador" aria-hidden="true">·</span>
                <span>Administración local de ventas y catálogo</span>
            </div>
        </footer>
    </div>
</div>

<script>
(function() {
    var sidebar = document.getElementById('barra-lateral');
    var backdrop = document.getElementById('drawer-backdrop');
    var btnAbrir = document.getElementById('btn-abrir-drawer');
    var btnCerrar = document.getElementById('btn-cerrar-drawer');
    if (!sidebar || !btnAbrir) return;

    function abrirMenu() {
        sidebar.classList.add('abierto');
        if (backdrop) backdrop.classList.add('visible');
        btnAbrir.setAttribute('aria-expanded', 'true');
        document.body.classList.add('drawer-bloqueado');
        if (btnCerrar) btnCerrar.focus();
    }

    function cerrarMenu() {
        sidebar.classList.remove('abierto');
        if (backdrop) backdrop.classList.remove('visible');
        btnAbrir.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('drawer-bloqueado');
        btnAbrir.focus();
    }

    btnAbrir.addEventListener('click', abrirMenu);
    if (btnCerrar) btnCerrar.addEventListener('click', cerrarMenu);
    if (backdrop) backdrop.addEventListener('click', cerrarMenu);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('abierto')) {
            cerrarMenu();
        }
    });
})();
</script>
</body>
</html>
