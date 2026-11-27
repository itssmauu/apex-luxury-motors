<?php
// Sobre nosotros: quiénes somos, cifras, un video que se agranda al hacer scroll,
// lo que ofrecemos, categorías, cómo trabajamos y una invitación final según el rol.
// Cada sección tiene su video (img/videos/sobre-*.mp4): de fondo en las bandas
// y en una ventana vertical en "Explora por categoría" y "Cómo trabajamos".
// Lo que lleva data-zoom crece al entrar en la pantalla y se achica al salir (js/zoom_scroll.js).
// Reutiliza las secciones de la portada (estilos en estilos.css).
require_once __DIR__ . "/../backend/funciones/acceso.php";
require_once __DIR__ . "/../backend/funciones/vehiculos.php";
require_once __DIR__ . "/includes/idioma.php";
require_once __DIR__ . "/datos/valores.php";

$titulo = t("menu.sobre_nosotros");
$rol = rolActual();

// cifras sacadas de la base, igual que en la portada
$totalModelos = count(listarVehiculos());

// categorías: si la consulta falla, la sección "Explora por categoría" no se muestra
$categorias = [];
try {
    $categorias = listarCategorias();
} catch (Throwable $e) {
    $categorias = [];
}
$totalCategorias = count($categorias);

// video de una sección: sin sonido, en bucle, y solo se reproduce cuando se ve (zoom_scroll.js).
// aria-hidden: es decoración, el contenido está en el texto de la sección
function videoSobre($nombre, $clase)
{
    $ruta = "../img/videos/" . $nombre;
    return '<video class="' . $clase . ' video-al-ver" muted loop playsinline preload="none" aria-hidden="true" poster="'
        . htmlspecialchars($ruta) . '.jpg"><source src="' . htmlspecialchars($ruta) . '.mp4" type="video/mp4"></video>';
}

// botón de pausa de ese video (el JavaScript le quita el hidden)
function pausaSobre()
{
    return '<button type="button" class="boton-pausa video-pausa"'
        . ' aria-label="' . htmlspecialchars(t("vehiculo.pausar_video")) . '"'
        . ' data-texto-pausar="' . htmlspecialchars(t("vehiculo.pausar_video")) . '"'
        . ' data-texto-reproducir="' . htmlspecialchars(t("vehiculo.reproducir_video")) . '" hidden>'
        . '<span class="icono-pausa" aria-hidden="true"></span></button>';
}

require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main class="sobre-pagina">
    <!-- encabezado de la página y cifras, sobre el video del Corvette -->
    <section class="seccion banda sobre-inicio" data-zoom aria-labelledby="titulo-sobre">
        <?php echo videoSobre("sobre-inicio", "banda-video"); ?>
        <div class="contenedor quienes">
            <div class="revelar">
                <p class="sobretitulo"><?php echo htmlspecialchars(t("menu.sobre_nosotros")); ?></p>
                <h1 id="titulo-sobre" class="titulo-grande"><?php echo htmlspecialchars(t("sobre.h1")); ?></h1>
            </div>
            <div class="quienes-texto revelar" style="--i: 1">
                <p><?php echo htmlspecialchars(t("sobre.p1")); ?></p>
                <p><?php echo htmlspecialchars(t("sobre.p2")); ?></p>
            </div>
        </div>

        <!-- dt es el nombre de la cifra y dd el número; el CSS pone el número arriba -->
        <dl class="contenedor cifras sobre-cifras" data-zoom>
            <div class="revelar" style="--i: 0">
                <dt><?php echo htmlspecialchars(t("cifra.modelos")); ?></dt>
                <dd><?php echo $totalModelos; ?></dd>
            </div>
            <div class="revelar" style="--i: 1">
                <dt><?php echo htmlspecialchars(t("cifra.categorias")); ?></dt>
                <dd><?php echo $totalCategorias; ?></dd>
            </div>
            <div class="revelar" style="--i: 2">
                <dt><?php echo htmlspecialchars(t("cifra.kilometros")); ?></dt>
                <dd>0</dd>
            </div>
        </dl>
        <?php echo pausaSobre(); ?>
    </section>

    <!-- video: empieza como una ventana con bordes redondeados y, al bajar, se agranda
         hasta ocupar toda la pantalla; se queda fijo un rato y al salir se vuelve a achicar -->
    <section class="sobre-video" data-zoom aria-label="<?php echo htmlspecialchars(t("sobre.video_aria")); ?>">
        <div class="sobre-video-marco">
            <?php echo videoSobre("sobre-nosotros", "sobre-video-archivo"); ?>
            <div class="sobre-video-texto">
                <p class="sobretitulo"><?php echo htmlspecialchars(t("sobre.video_sobretitulo")); ?></p>
                <p class="sobre-video-titulo"><?php echo htmlspecialchars(t("sobre.video_titulo")); ?></p>
            </div>
            <?php echo pausaSobre(); ?>
        </div>
    </section>

    <!-- lo que ofrecemos: tres bloques (no son pasos, por eso van sin número) -->
    <section class="seccion banda sobre-ofrecemos" data-zoom aria-labelledby="titulo-ofrecemos">
        <?php echo videoSobre("sobre-ofrecemos", "banda-video"); ?>
        <div class="contenedor">
            <h2 id="titulo-ofrecemos" class="titulo-grande revelar"><?php echo htmlspecialchars(t("sobre.ofrecemos")); ?></h2>
            <ul class="pasos">
                <li class="revelar" style="--i: 0">
                    <h3><?php echo htmlspecialchars(t("sobre.catalogo_claro")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.catalogo_claro_texto")); ?></p>
                </li>
                <li class="revelar" style="--i: 1">
                    <h3><?php echo htmlspecialchars(t("sobre.autos_nuevos")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.autos_nuevos_texto")); ?></p>
                </li>
                <li class="revelar" style="--i: 2">
                    <h3><?php echo htmlspecialchars(t("sobre.en_linea")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.en_linea_texto")); ?></p>
                </li>
            </ul>
        </div>
        <?php echo pausaSobre(); ?>
    </section>

    <!-- explora por categoría (se oculta si no hay categorías).
         El enlace usa el valor de la base; solo el texto se traduce -->
    <?php if ($totalCategorias > 0): ?>
        <section class="seccion" aria-labelledby="titulo-categorias">
            <div class="contenedor sobre-dividido sobre-dividido-invertido">
                <!-- ventana vertical con video: crece al entrar en la pantalla -->
                <div class="sobre-ventana" data-zoom>
                    <?php echo videoSobre("sobre-categorias", "sobre-ventana-video"); ?>
                    <?php echo pausaSobre(); ?>
                </div>
                <div>
                <h2 id="titulo-categorias" class="titulo-grande revelar"><?php echo htmlspecialchars(t("sobre.explora")); ?></h2>
                <ul class="sobre-categorias">
                    <?php foreach ($categorias as $n => $fila): ?>
                        <li class="revelar" style="--i: <?php echo $n; ?>">
                            <a class="boton-borde" href="catalogo.php?categoria=<?php echo urlencode($fila["categoria"]); ?>">
                                <?php echo htmlspecialchars(nombreCategoria($fila["categoria"])); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- cómo trabajamos: tres pasos -->
    <section class="seccion seccion-oscura" aria-labelledby="titulo-trabajamos">
        <div class="contenedor sobre-dividido">
            <div>
            <h2 id="titulo-trabajamos" class="titulo-grande revelar"><?php echo htmlspecialchars(t("sobre.trabajamos")); ?></h2>
            <ol class="pasos">
                <li class="revelar" style="--i: 0">
                    <span class="paso-numero" aria-hidden="true">01</span>
                    <h3><?php echo htmlspecialchars(t("paso.elige")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.elige_texto")); ?></p>
                </li>
                <li class="revelar" style="--i: 1">
                    <span class="paso-numero" aria-hidden="true">02</span>
                    <h3><?php echo htmlspecialchars(t("paso.solicita")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.solicita_texto")); ?></p>
                </li>
                <li class="revelar" style="--i: 2">
                    <span class="paso-numero" aria-hidden="true">03</span>
                    <h3><?php echo htmlspecialchars(t("sobre.recibe")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.recibe_texto")); ?></p>
                </li>
            </ol>
            </div>
            <div class="sobre-ventana" data-zoom>
                <?php echo videoSobre("sobre-trabajamos", "sobre-ventana-video"); ?>
                <?php echo pausaSobre(); ?>
            </div>
        </div>
    </section>

    <!-- invitación final: cambia según quién visita -->
    <section class="seccion banda llamado sobre-llamado" data-zoom aria-labelledby="titulo-llamado">
        <?php echo videoSobre("sobre-final", "banda-video"); ?>
        <div class="contenedor revelar">
            <?php if ($rol === "administrador"): ?>
                <h2 id="titulo-llamado" class="titulo-grande"><?php echo htmlspecialchars(t("llamado.admin_titulo")); ?></h2>
                <div class="llamado-botones">
                    <a class="boton" href="admin/solicitudes.php"><?php echo htmlspecialchars(t("llamado.ver_solicitudes")); ?></a>
                    <a class="boton-borde" href="admin/vehiculos.php"><?php echo htmlspecialchars(t("llamado.ir_inventario")); ?></a>
                </div>
            <?php elseif ($rol === "cliente"): ?>
                <h2 id="titulo-llamado" class="titulo-grande"><?php echo htmlspecialchars(t("llamado.visitante_titulo")); ?></h2>
                <div class="llamado-botones">
                    <a class="boton" href="mis_solicitudes.php"><?php echo htmlspecialchars(t("menu.mis_solicitudes")); ?></a>
                    <a class="boton-borde" href="catalogo.php"><?php echo htmlspecialchars(t("comun.ver_catalogo")); ?></a>
                </div>
            <?php else: ?>
                <h2 id="titulo-llamado" class="titulo-grande"><?php echo htmlspecialchars(t("llamado.visitante_titulo")); ?></h2>
                <div class="llamado-botones">
                    <a class="boton" href="registro.php"><?php echo htmlspecialchars(t("comun.crear_cuenta")); ?></a>
                    <a class="boton-borde" href="catalogo.php"><?php echo htmlspecialchars(t("comun.ver_catalogo")); ?></a>
                </div>
            <?php endif; ?>
        </div>
        <?php echo pausaSobre(); ?>
    </section>
</main>

<!-- video y elementos que se agrandan al hacer scroll -->
<script src="/apex/frontend/js/zoom_scroll.js?v=2"></script>

<?php require_once __DIR__ . "/includes/pie.php"; ?>
