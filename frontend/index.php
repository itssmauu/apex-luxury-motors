<?php
// Inicio (portada): intro con el logo, carrusel de videos por categoría,
// quiénes somos, destacados, cómo comprar y una invitación final según el rol
require_once __DIR__ . "/../backend/funciones/acceso.php";
require_once __DIR__ . "/../backend/funciones/vehiculos.php";
require_once __DIR__ . "/includes/idioma.php";
require_once __DIR__ . "/datos/valores.php";

$titulo = t("menu.inicio");
$claseBody = "pagina-inicio";
$rol = rolActual();

// una diapositiva por categoría; "categoria" tiene que escribirse igual que en la base
// (los textos salen del diccionario: inicio.<clave>.etiqueta, .titulo, .bajada y .boton)
$diapositivas = [
    ["categoria" => "Superdeportivo",           "clave" => "super",   "video" => "superdeportivo"],
    ["categoria" => "Deportivo alemán",         "clave" => "aleman",  "video" => "aleman"],
    ["categoria" => "Deportivo japonés",        "clave" => "japones", "video" => "japones"],
    ["categoria" => "Deportivo estadounidense", "clave" => "eeuu",    "video" => "estadounidense"],
];
foreach ($diapositivas as $i => $d) {
    foreach (["etiqueta", "titulo", "bajada", "boton"] as $parte) {
        $diapositivas[$i][$parte] = t("inicio." . $d["clave"] . "." . $parte);
    }
}

// cuántos modelos tiene cada categoría (para la etiqueta de la diapositiva)
foreach ($diapositivas as $i => $d) {
    $diapositivas[$i]["cantidad"] = count(listarVehiculos("", $d["categoria"]));
}

// cifras de "Quiénes somos", sacadas de la base
$todos = listarVehiculos();
$totalModelos = count($todos);
$totalCategorias = count(listarCategorias());

// destacados: los 5 autos disponibles más caros (uno grande y cuatro chicos)
$disponibles = [];
foreach ($todos as $v) {
    if ($v["estado"] !== "vendido") {
        $disponibles[] = $v;
    }
}
usort($disponibles, function ($a, $b) {
    return (float) $b["precio"] <=> (float) $a["precio"];
});
$destacados = array_slice($disponibles, 0, 5);

require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<!-- intro: el logo aparece sobre negro y se desvanece (es solo decoración) -->
<div class="intro" aria-hidden="true">
    <div class="intro-marca">
        <img src="/apex/img/logo_apex_intro.png" alt="" width="1000" height="375">
        <span class="intro-linea"></span>
    </div>
</div>

<main>
    <h1 class="solo-lector"><?php echo htmlspecialchars(t("inicio.h1")); ?></h1>

    <!-- carrusel: un video por categoría. Sin JavaScript se ve la primera diapositiva -->
    <section class="hero" aria-roledescription="<?php echo htmlspecialchars(t("inicio.carrusel")); ?>"
             aria-label="<?php echo htmlspecialchars(t("inicio.carrusel_aria")); ?>">
        <div class="hero-diapositivas" aria-live="off">
            <?php foreach ($diapositivas as $i => $d): ?>
                <div class="hero-diapositiva<?php if ($i === 0) { echo " activa"; } ?>"
                     role="group" aria-roledescription="<?php echo htmlspecialchars(t("inicio.diapositiva")); ?>"
                     aria-label="<?php echo htmlspecialchars(t("inicio.diapositiva_n", $i + 1, count($diapositivas))); ?>">

                    <video class="hero-video" muted loop playsinline aria-hidden="true"
                           preload="<?php echo ($i === 0) ? "auto" : "none"; ?>"
                           poster="/apex/img/videos/<?php echo $d["video"]; ?>.jpg">
                        <source src="/apex/img/videos/<?php echo $d["video"]; ?>.mp4" type="video/mp4">
                    </video>

                    <div class="hero-texto">
                        <p class="hero-etiqueta">
                            <?php echo htmlspecialchars($d["etiqueta"]); ?>
                            <?php if ($d["cantidad"] > 0): ?>
                                · <?php echo htmlspecialchars(tPlural($d["cantidad"], "plural.modelo.uno", "plural.modelo.varios")); ?>
                            <?php endif; ?>
                        </p>
                        <h2 class="hero-titulo"><?php echo htmlspecialchars($d["titulo"]); ?></h2>
                        <p class="hero-bajada"><?php echo htmlspecialchars($d["bajada"]); ?></p>
                        <a class="boton" href="catalogo.php?categoria=<?php echo urlencode($d["categoria"]); ?>">
                            <?php echo htmlspecialchars($d["boton"]); ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- controles: el JavaScript los muestra (sin él no servirían) -->
        <div class="hero-controles" hidden>
            <!-- data-texto-*: los textos traducidos que usa portada.js al pausar o seguir -->
            <button type="button" class="boton-pausa hero-pausa"
                    aria-label="<?php echo htmlspecialchars(t("inicio.pausar_carrusel")); ?>"
                    data-texto-pausar="<?php echo htmlspecialchars(t("inicio.pausar_carrusel")); ?>"
                    data-texto-reproducir="<?php echo htmlspecialchars(t("inicio.reproducir_carrusel")); ?>">
                <span class="icono-pausa" aria-hidden="true"></span>
            </button>
            <ol class="hero-indicadores">
                <?php foreach ($diapositivas as $i => $d): ?>
                    <li>
                        <button type="button" class="hero-indicador"
                                aria-label="<?php echo htmlspecialchars(t("inicio.indicador", $i + 1, $d["etiqueta"])); ?>"
                                <?php if ($i === 0) { echo 'aria-current="true"'; } ?>>
                            <span class="hero-barra"><span class="hero-progreso"></span></span>
                            <span class="hero-nombre" aria-hidden="true"><?php echo htmlspecialchars($d["etiqueta"]); ?></span>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <!-- quiénes somos -->
    <section class="seccion" id="quienes-somos" aria-labelledby="titulo-quienes">
        <div class="contenedor quienes">
            <div class="revelar">
                <p class="sobretitulo"><?php echo htmlspecialchars(t("inicio.quienes.sobretitulo")); ?></p>
                <h2 id="titulo-quienes" class="titulo-grande"><?php echo htmlspecialchars(t("inicio.quienes.titulo")); ?></h2>
            </div>
            <div class="quienes-texto revelar" style="--i: 1">
                <p><?php echo htmlspecialchars(t("inicio.quienes.p1")); ?></p>
                <p><?php echo htmlspecialchars(t("inicio.quienes.p2")); ?></p>
            </div>
        </div>

        <!-- dt es el nombre de la cifra y dd el número; el CSS pone el número arriba -->
        <dl class="contenedor cifras">
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
            <div class="revelar" style="--i: 3">
                <dt><?php echo htmlspecialchars(t("cifra.en_linea")); ?></dt>
                <dd>100&nbsp;%</dd>
            </div>
        </dl>
    </section>

    <!-- destacados: mosaico con el primero grande; toda la pieza es un enlace -->
    <?php if (count($destacados) > 0): ?>
        <section class="seccion seccion-oscura" aria-labelledby="titulo-destacados">
            <div class="contenedor">
                <div class="seccion-cabecera revelar">
                    <div>
                        <p class="sobretitulo"><?php echo htmlspecialchars(t("inicio.destacados.sobretitulo")); ?></p>
                        <h2 id="titulo-destacados" class="titulo-grande"><?php echo htmlspecialchars(t("inicio.destacados.titulo")); ?></h2>
                    </div>
                    <a class="enlace-flecha" href="catalogo.php"><?php echo htmlspecialchars(t("inicio.destacados.ver_todo")); ?> <span aria-hidden="true">→</span></a>
                </div>

                <div class="mosaico">
                    <?php foreach ($destacados as $n => $v): ?>
                        <?php $nombreAuto = $v["marca"] . " " . $v["modelo"]; ?>
                        <article class="pieza revelar" style="--i: <?php echo $n; ?>">
                            <?php if (!empty($v["foto"])): ?>
                                <!-- alt vacío: el nombre del auto ya está escrito abajo -->
                                <img src="../<?php echo htmlspecialchars($v["foto"]); ?>" alt=""
                                     width="800" height="500" loading="lazy">
                            <?php endif; ?>
                            <p class="pieza-categoria"><?php echo htmlspecialchars(nombreCategoria($v["categoria"])); ?></p>
                            <h3><?php echo htmlspecialchars($nombreAuto); ?></h3>
                            <p class="pieza-precio">$<?php echo number_format((float) $v["precio"], 0, ".", ","); ?> USD</p>
                            <a class="pieza-enlace" href="vehiculo.php?id=<?php echo (int) $v["id"]; ?>"
                               aria-label="<?php echo htmlspecialchars(t("comun.ver_vehiculo_aria", $nombreAuto)); ?>">
                                <?php echo htmlspecialchars(t("comun.ver_vehiculo")); ?> <span aria-hidden="true">→</span>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- qué hacemos: tres pasos sobre un video de carretera -->
    <section class="seccion banda" aria-labelledby="titulo-pasos">
        <video class="banda-video video-al-ver" muted loop playsinline preload="none" aria-hidden="true"
               poster="/apex/img/videos/carretera.jpg">
            <source src="/apex/img/videos/carretera.mp4" type="video/mp4">
        </video>
        <div class="contenedor">
            <div class="revelar">
                <p class="sobretitulo"><?php echo htmlspecialchars(t("inicio.pasos.sobretitulo")); ?></p>
                <h2 id="titulo-pasos" class="titulo-grande"><?php echo htmlspecialchars(t("inicio.pasos.titulo")); ?></h2>
            </div>
            <ol class="pasos">
                <li class="revelar" style="--i: 0">
                    <span class="paso-numero" aria-hidden="true">01</span>
                    <h3><?php echo htmlspecialchars(t("paso.elige")); ?></h3>
                    <p><?php echo htmlspecialchars(t("inicio.pasos.elige_texto")); ?></p>
                </li>
                <li class="revelar" style="--i: 1">
                    <span class="paso-numero" aria-hidden="true">02</span>
                    <h3><?php echo htmlspecialchars(t("paso.solicita")); ?></h3>
                    <p><?php echo htmlspecialchars(t("inicio.pasos.solicita_texto")); ?></p>
                </li>
                <li class="revelar" style="--i: 2">
                    <span class="paso-numero" aria-hidden="true">03</span>
                    <h3><?php echo htmlspecialchars(t("inicio.pasos.sigue")); ?></h3>
                    <p><?php echo htmlspecialchars(t("inicio.pasos.sigue_texto")); ?></p>
                </li>
            </ol>
        </div>
        <button type="button" class="boton-pausa video-pausa"
                aria-label="<?php echo htmlspecialchars(t("inicio.pausar_video")); ?>"
                data-texto-pausar="<?php echo htmlspecialchars(t("inicio.pausar_video")); ?>"
                data-texto-reproducir="<?php echo htmlspecialchars(t("inicio.reproducir_video")); ?>" hidden>
            <span class="icono-pausa" aria-hidden="true"></span>
        </button>
    </section>

    <!-- invitación final: cambia según quién visita -->
    <section class="seccion banda llamado" aria-labelledby="titulo-llamado">
        <video class="banda-video video-al-ver" muted loop playsinline preload="none" aria-hidden="true"
               poster="/apex/img/videos/autopista.jpg">
            <source src="/apex/img/videos/autopista.mp4" type="video/mp4">
        </video>
        <div class="contenedor revelar">
            <?php if ($rol === "administrador"): ?>
                <h2 id="titulo-llamado" class="titulo-grande"><?php echo htmlspecialchars(t("llamado.admin_titulo")); ?></h2>
                <p><?php echo htmlspecialchars(t("inicio.llamado.admin_texto")); ?></p>
                <div class="llamado-botones">
                    <a class="boton" href="admin/solicitudes.php"><?php echo htmlspecialchars(t("llamado.ver_solicitudes")); ?></a>
                    <a class="boton-borde" href="admin/vehiculos.php"><?php echo htmlspecialchars(t("llamado.ir_inventario")); ?></a>
                </div>
            <?php elseif ($rol === "cliente"): ?>
                <h2 id="titulo-llamado" class="titulo-grande"><?php echo htmlspecialchars(t("inicio.llamado.cliente_titulo")); ?></h2>
                <p><?php echo htmlspecialchars(t("inicio.llamado.cliente_texto")); ?></p>
                <div class="llamado-botones">
                    <a class="boton" href="mis_solicitudes.php"><?php echo htmlspecialchars(t("menu.mis_solicitudes")); ?></a>
                    <a class="boton-borde" href="catalogo.php"><?php echo htmlspecialchars(t("comun.ver_catalogo")); ?></a>
                </div>
            <?php else: ?>
                <h2 id="titulo-llamado" class="titulo-grande"><?php echo htmlspecialchars(t("llamado.visitante_titulo")); ?></h2>
                <p><?php echo htmlspecialchars(t("inicio.llamado.visitante_texto")); ?></p>
                <div class="llamado-botones">
                    <a class="boton" href="registro.php"><?php echo htmlspecialchars(t("comun.crear_cuenta")); ?></a>
                    <a class="boton-borde" href="login.php"><?php echo htmlspecialchars(t("menu.iniciar_sesion")); ?></a>
                </div>
            <?php endif; ?>
        </div>
        <button type="button" class="boton-pausa video-pausa"
                aria-label="<?php echo htmlspecialchars(t("inicio.pausar_video")); ?>"
                data-texto-pausar="<?php echo htmlspecialchars(t("inicio.pausar_video")); ?>"
                data-texto-reproducir="<?php echo htmlspecialchars(t("inicio.reproducir_video")); ?>" hidden>
            <span class="icono-pausa" aria-hidden="true"></span>
        </button>
    </section>
</main>

<script src="/apex/frontend/js/portada.js?v=2"></script>

<?php require_once __DIR__ . "/includes/pie.php"; ?>