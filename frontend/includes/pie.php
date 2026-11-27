<?php
// Pie de página común: enlaces en columnas, una barra final con el copyright,
// las cookies y el selector de idioma, y el aviso de cookies (si todavía no se decidió).
// Todos los enlaces van a páginas que ya existen. Los textos salen de t().

// rolActual() para la columna "Cuenta" y listarCategorias() para "Por categoría"
require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/vehiculos.php";
require_once __DIR__ . "/idioma.php";
require_once __DIR__ . "/../datos/valores.php";

$rolPie = rolActual();
$raizPie = "/apex/frontend";

// si la consulta de categorías falla, la columna simplemente no se muestra
$categoriasPie = [];
try {
    $categoriasPie = listarCategorias();
} catch (Throwable $e) {
    $categoriasPie = [];
}

// a dónde vuelve la persona después de elegir idioma o cookies (la página actual, con su query)
$volverPie = $_SERVER["REQUEST_URI"] ?? $raizPie . "/index.php";
$idiomaPie = idiomaActual();
$idiomasPie = idiomasDisponibles();
$mostrarAvisoCookies = (decisionCookies() === "");
?>
<footer<?php if ($mostrarAvisoCookies) { echo ' class="con-aviso-cookies"'; } ?>>
    <nav class="pie-columnas" aria-label="<?php echo htmlspecialchars(t("pie.aria")); ?>">
        <div class="pie-columna">
            <h2><?php echo htmlspecialchars(t("pie.explorar")); ?></h2>
            <ul>
                <li><a href="<?php echo $raizPie; ?>/index.php"><?php echo htmlspecialchars(t("menu.inicio")); ?></a></li>
                <li><a href="<?php echo $raizPie; ?>/catalogo.php"><?php echo htmlspecialchars(t("menu.catalogo")); ?></a></li>
                <li><a href="<?php echo $raizPie; ?>/sobre_nosotros.php"><?php echo htmlspecialchars(t("menu.sobre_nosotros")); ?></a></li>
            </ul>
        </div>

        <!-- la columna "Cuenta" cambia según quién visita -->
        <div class="pie-columna">
            <h2><?php echo htmlspecialchars(t("pie.cuenta")); ?></h2>
            <ul>
                <?php if ($rolPie === "cliente"): ?>
                    <li><a href="<?php echo $raizPie; ?>/mis_solicitudes.php"><?php echo htmlspecialchars(t("menu.mis_solicitudes")); ?></a></li>
                    <li><a href="<?php echo $raizPie; ?>/perfil.php"><?php echo htmlspecialchars(t("menu.mi_perfil")); ?></a></li>
                    <li><a href="/apex/backend/procesos/logout.php"><?php echo htmlspecialchars(t("cuenta.salir")); ?></a></li>
                <?php elseif ($rolPie === "administrador"): ?>
                    <li><a href="<?php echo $raizPie; ?>/admin/vehiculos.php"><?php echo htmlspecialchars(t("menu.inventario")); ?></a></li>
                    <li><a href="<?php echo $raizPie; ?>/admin/solicitudes.php"><?php echo htmlspecialchars(t("menu.solicitudes")); ?></a></li>
                    <li><a href="<?php echo $raizPie; ?>/perfil.php"><?php echo htmlspecialchars(t("menu.mi_perfil")); ?></a></li>
                    <li><a href="/apex/backend/procesos/logout.php"><?php echo htmlspecialchars(t("cuenta.salir")); ?></a></li>
                <?php else: ?>
                    <li><a href="<?php echo $raizPie; ?>/login.php"><?php echo htmlspecialchars(t("menu.iniciar_sesion")); ?></a></li>
                    <li><a href="<?php echo $raizPie; ?>/registro.php"><?php echo htmlspecialchars(t("menu.registrarse")); ?></a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- un enlace por categoría al catálogo ya filtrado (se oculta si no hay categorías).
             El enlace usa el valor de la base; solo el nombre que se lee se traduce -->
        <?php if (count($categoriasPie) > 0): ?>
            <div class="pie-columna">
                <h2><?php echo htmlspecialchars(t("pie.por_categoria")); ?></h2>
                <ul>
                    <?php foreach ($categoriasPie as $fila): ?>
                        <li>
                            <a href="<?php echo $raizPie; ?>/catalogo.php?categoria=<?php echo urlencode($fila["categoria"]); ?>">
                                <?php echo htmlspecialchars(nombreCategoria($fila["categoria"])); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </nav>

    <!-- barra final, de borde a borde: copyright y cookies a la izquierda, idioma a la derecha -->
    <div class="pie-legal">
        <div class="pie-legal-contenido">
            <div class="pie-legal-texto">
                <p><?php echo htmlspecialchars(t("pie.copyright", date("Y"))); ?></p>
                <p><?php echo htmlspecialchars(t("pie.cookies")); ?></p>
                <!-- vuelve a mostrar el aviso de cookies para cambiar la decisión -->
                <form class="pie-form-cookies" method="post" action="<?php echo $raizPie; ?>/preferencias.php">
                    <input type="hidden" name="accion" value="cookies">
                    <input type="hidden" name="valor" value="reabrir">
                    <input type="hidden" name="volver" value="<?php echo htmlspecialchars($volverPie); ?>">
                    <button type="submit" class="pie-boton-enlace"><?php echo htmlspecialchars(t("pie.preferencias_cookies")); ?></button>
                </form>
            </div>

            <!-- selector de idioma: funciona sin JavaScript (details); el panel se abre hacia arriba.
                 Cada idioma va escrito en su propio idioma -->
            <details class="pie-idioma">
                <summary>
                    <span class="solo-lector"><?php echo htmlspecialchars(t("idioma.etiqueta")); ?>:</span>
                    <span class="pie-idioma-actual"><?php echo htmlspecialchars($idiomasPie[$idiomaPie]); ?></span>
                    <span class="pie-idioma-flecha" aria-hidden="true">⌃</span>
                </summary>
                <ul class="pie-idioma-panel">
                    <?php foreach ($idiomasPie as $codigo => $nombre): ?>
                        <li>
                            <form method="post" action="<?php echo $raizPie; ?>/preferencias.php">
                                <input type="hidden" name="accion" value="idioma">
                                <input type="hidden" name="lang" value="<?php echo $codigo; ?>">
                                <input type="hidden" name="volver" value="<?php echo htmlspecialchars($volverPie); ?>">
                                <button type="submit" lang="<?php echo $codigo; ?>"
                                        <?php if ($codigo === $idiomaPie) { echo 'aria-current="true"'; } ?>>
                                    <?php echo htmlspecialchars($nombre); ?>
                                    <?php if ($codigo === $idiomaPie): ?>
                                        <span class="pie-idioma-marca" aria-hidden="true">✓</span>
                                    <?php endif; ?>
                                </button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </details>
        </div>
    </div>

    <!-- aviso de cookies: solo si todavía no se decidió. Lo arma PHP (funciona sin JavaScript),
         no bloquea la página ni atrapa el foco. Los dos botones pesan lo mismo -->
    <?php if ($mostrarAvisoCookies): ?>
        <section class="aviso-cookies" aria-label="<?php echo htmlspecialchars(t("cookies.aviso")); ?>">
            <p><?php echo htmlspecialchars(t("cookies.texto")); ?></p>
            <div class="aviso-cookies-botones">
                <form method="post" action="<?php echo $raizPie; ?>/preferencias.php">
                    <input type="hidden" name="accion" value="cookies">
                    <input type="hidden" name="valor" value="aceptar">
                    <input type="hidden" name="volver" value="<?php echo htmlspecialchars($volverPie); ?>">
                    <button type="submit" class="aviso-cookies-boton"><?php echo htmlspecialchars(t("cookies.aceptar")); ?></button>
                </form>
                <form method="post" action="<?php echo $raizPie; ?>/preferencias.php">
                    <input type="hidden" name="accion" value="cookies">
                    <input type="hidden" name="valor" value="rechazar">
                    <input type="hidden" name="volver" value="<?php echo htmlspecialchars($volverPie); ?>">
                    <button type="submit" class="aviso-cookies-boton"><?php echo htmlspecialchars(t("cookies.rechazar")); ?></button>
                </form>
            </div>
        </section>
    <?php endif; ?>
</footer>
<!-- aparición suave al hacer scroll (elementos con la clase "revelar") -->
<script src="/apex/frontend/js/revelar.js?v=1"></script>
<!-- selector de idioma: cerrar el panel con Esc o con un clic fuera -->
<script src="/apex/frontend/js/idioma.js?v=1"></script>
</body>
</html>
