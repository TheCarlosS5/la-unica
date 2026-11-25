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
    var principal = document.querySelector('.area-principal');
    var saltoContenido = document.querySelector('.salto-contenido');
    var vistaMovil = window.matchMedia('(max-width: 820px)');
    if (!sidebar || !btnAbrir || !btnCerrar || !principal || !saltoContenido) return;

    function abrirMenu() {
        sidebar.classList.add('abierto');
        if (backdrop) backdrop.classList.add('visible');
        btnAbrir.setAttribute('aria-expanded', 'true');
        document.body.classList.add('drawer-bloqueado');
        btnCerrar.focus();
        principal.inert = true;
        saltoContenido.inert = true;
    }

    function cerrarMenu() {
        sidebar.classList.remove('abierto');
        if (backdrop) backdrop.classList.remove('visible');
        btnAbrir.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('drawer-bloqueado');
        principal.inert = false;
        saltoContenido.inert = false;
        (vistaMovil.matches ? btnAbrir : sidebar.querySelector('.sidebar-marca')).focus();
    }

    btnAbrir.addEventListener('click', abrirMenu);
    btnCerrar.addEventListener('click', cerrarMenu);
    vistaMovil.addEventListener('change', function(e) {
        if (!e.matches && sidebar.classList.contains('abierto')) cerrarMenu();
    });
    if (backdrop) backdrop.addEventListener('click', cerrarMenu);
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('abierto')) {
            cerrarMenu();
        }
    });
})();
</script>
<script src="<?= h($baseUrl) ?>/assets/js/formularios.js" defer></script>
</body>
</html>
