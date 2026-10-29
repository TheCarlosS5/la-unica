<?php
/**
 * Archivo: pie.php
 * Módulo: Wiki — plantilla de cierre.
 * Propósito: imprimir el índice «En esta página», cerrar el contenedor principal y cargar el JavaScript.
 * Funcionamiento: usa $indice preparado por index.php; si la página no tiene títulos, no muestra el índice.
 * Punto clave: el visor de imágenes queda oculto hasta que se hace clic en una captura.
 */

if (!defined('LA_UNICA_WIKI')) {
    exit;
}
?>
            <?php if (!empty($indice)): ?>
                <aside class="wiki-indice" aria-label="En esta página">
                    <p class="wiki-indice-titulo">En esta página</p>
                    <ul>
                        <?php foreach ($indice as $entrada): ?>
                            <li class="nivel-<?= (int)$entrada['nivel'] ?>">
                                <a href="#<?= h($entrada['id']) ?>"><?= h($entrada['texto']) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </aside>
            <?php endif; ?>
        </main>
    </div>

    <footer class="wiki-pie">
        <span>Wiki de La Única · SENA ficha 3407799</span>
        <a href="../main.php">Ir al aplicativo</a>
    </footer>

    <div id="wiki-visor" class="wiki-visor" hidden>
        <button type="button" class="wiki-visor-cerrar" aria-label="Cerrar imagen">✕</button>
        <img src="" alt="">
        <p class="wiki-visor-pie"></p>
    </div>

    <script src="js/wiki.js"></script>
</body>
</html>
