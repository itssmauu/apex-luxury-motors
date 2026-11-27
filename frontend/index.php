<?php
// Inicio (portada): intro con el logo, carrusel de videos por categoría,
// quiénes somos, destacados, cómo comprar y una invitación final según el rol
require_once __DIR__ . "/../backend/funciones/acceso.php";
require_once __DIR__ . "/../backend/funciones/vehiculos.php";

$titulo = "Inicio";
$claseBody = "pagina-inicio";
$rol = rolActual();

// una diapositiva por categoría; "categoria" tiene que escribirse igual que en la base
$diapositivas = [
    [
        "categoria" => "Superdeportivo",
        "etiqueta"  => "Superdeportivos",
        "titulo"    => "Potencia sin límites",
        "bajada"    => "Motores V8 y V12 de motor central para quienes buscan lo más alto.",
        "boton"     => "Ver superdeportivos",
        "video"     => "superdeportivo"
    ],
    [
        "categoria" => "Deportivo alemán",
        "etiqueta"  => "Deportivos alemanes",
        "titulo"    => "Precisión alemana",
        "bajada"    => "Ingeniería pensada para la carretera de todos los días y para la pista.",
        "boton"     => "Ver deportivos alemanes",
        "video"     => "aleman"
    ],
    [
        "categoria" => "Deportivo japonés",
        "etiqueta"  => "Deportivos japoneses",
        "titulo"    => "Leyendas de Japón",
        "bajada"    => "Los íconos que marcaron una generación, nuevos y con cero kilómetros.",
        "boton"     => "Ver deportivos japoneses",
        "video"     => "japones"
    ],
    [
        "categoria" => "Deportivo estadounidense",
        "etiqueta"  => "Deportivos estadounidenses",
        "titulo"    => "Músculo americano",
        "bajada"    => "V8 de gran cilindrada y un carácter que se escucha antes de verse.",
        "boton"     => "Ver deportivos estadounidenses",
        "video"     => "estadounidense"
    ]
];

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
    <h1 class="solo-lector">Apex Luxury Motors: deportivos y autos de lujo nuevos</h1>

    <!-- carrusel: un video por categoría. Sin JavaScript se ve la primera diapositiva -->
    <section class="hero" aria-roledescription="carrusel" aria-label="Categorías del catálogo">
        <div class="hero-diapositivas" aria-live="off">
            <?php foreach ($diapositivas as $i => $d): ?>
                <div class="hero-diapositiva<?php if ($i === 0) { echo " activa"; } ?>"
                     role="group" aria-roledescription="diapositiva"
                     aria-label="<?php echo ($i + 1) . " de " . count($diapositivas); ?>">

                    <video class="hero-video" muted loop playsinline aria-hidden="true"
                           preload="<?php echo ($i === 0) ? "auto" : "none"; ?>"
                           poster="/apex/img/videos/<?php echo $d["video"]; ?>.jpg">
                        <source src="/apex/img/videos/<?php echo $d["video"]; ?>.mp4" type="video/mp4">
                    </video>

                    <div class="hero-texto">
                        <p class="hero-etiqueta">
                            <?php echo htmlspecialchars($d["etiqueta"]); ?>
                            <?php if ($d["cantidad"] > 0): ?>
                                · <?php echo $d["cantidad"]; ?> <?php echo ($d["cantidad"] === 1) ? "modelo" : "modelos"; ?>
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
            <button type="button" class="boton-pausa hero-pausa" aria-label="Pausar el carrusel">
                <span class="icono-pausa" aria-hidden="true"></span>
            </button>
            <ol class="hero-indicadores">
                <?php foreach ($diapositivas as $i => $d): ?>
                    <li>
                        <button type="button" class="hero-indicador"
                                aria-label="Diapositiva <?php echo ($i + 1); ?>: <?php echo htmlspecialchars($d["etiqueta"]); ?>"
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
                <p class="sobretitulo">Quiénes somos</p>
                <h2 id="titulo-quienes" class="titulo-grande">Autos de lujo nuevos, en un solo lugar</h2>
            </div>
            <div class="quienes-texto revelar" style="--i: 1">
                <p>Apex Luxury Motors es una concesionaria de autos deportivos y de lujo nuevos, todos con cero kilómetros. Reunimos superdeportivos y deportivos de Alemania, Japón y Estados Unidos para que no tengas que buscarlos por separado.</p>
                <p>Cada auto tiene su precio a la vista, sus fotos y su descripción. Envías tu solicitud de compra desde donde estés y nuestro equipo te responde con su decisión y un comentario que puedes revisar cuando quieras.</p>
            </div>
        </div>

        <!-- dt es el nombre de la cifra y dd el número; el CSS pone el número arriba -->
        <dl class="contenedor cifras">
            <div class="revelar" style="--i: 0">
                <dt>Modelos en el catálogo</dt>
                <dd><?php echo $totalModelos; ?></dd>
            </div>
            <div class="revelar" style="--i: 1">
                <dt>Categorías</dt>
                <dd><?php echo $totalCategorias; ?></dd>
            </div>
            <div class="revelar" style="--i: 2">
                <dt>Kilómetros en cada auto</dt>
                <dd>0</dd>
            </div>
            <div class="revelar" style="--i: 3">
                <dt>De la solicitud en línea</dt>
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
                        <p class="sobretitulo">Destacados</p>
                        <h2 id="titulo-destacados" class="titulo-grande">Lo más exclusivo del catálogo</h2>
                    </div>
                    <a class="enlace-flecha" href="catalogo.php">Ver todo el catálogo <span aria-hidden="true">→</span></a>
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
                            <p class="pieza-categoria"><?php echo htmlspecialchars($v["categoria"]); ?></p>
                            <h3><?php echo htmlspecialchars($nombreAuto); ?></h3>
                            <p class="pieza-precio">$<?php echo number_format((float) $v["precio"], 0, ".", ","); ?> USD</p>
                            <a class="pieza-enlace" href="vehiculo.php?id=<?php echo (int) $v["id"]; ?>"
                               aria-label="Ver vehículo <?php echo htmlspecialchars($nombreAuto); ?>">
                                Ver vehículo <span aria-hidden="true">→</span>
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
                <p class="sobretitulo">Qué hacemos</p>
                <h2 id="titulo-pasos" class="titulo-grande">Del catálogo a tu garaje, en tres pasos</h2>
            </div>
            <ol class="pasos">
                <li class="revelar" style="--i: 0">
                    <span class="paso-numero" aria-hidden="true">01</span>
                    <h3>Elige</h3>
                    <p>Explora el catálogo y filtra por marca, categoría o precio máximo.</p>
                </li>
                <li class="revelar" style="--i: 1">
                    <span class="paso-numero" aria-hidden="true">02</span>
                    <h3>Solicita</h3>
                    <p>Inicia sesión y envía tu solicitud de compra desde la ficha del auto.</p>
                </li>
                <li class="revelar" style="--i: 2">
                    <span class="paso-numero" aria-hidden="true">03</span>
                    <h3>Sigue tu solicitud</h3>
                    <p>En «Mis solicitudes» ves si fue aprobada, junto con el comentario de nuestro equipo.</p>
                </li>
            </ol>
        </div>
        <button type="button" class="boton-pausa video-pausa" aria-label="Pausar el video de fondo" hidden>
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
                <h2 id="titulo-llamado" class="titulo-grande">Panel de administración</h2>
                <p>Mantén el inventario al día y responde las solicitudes pendientes.</p>
                <div class="llamado-botones">
                    <a class="boton" href="admin/solicitudes.php">Ver solicitudes</a>
                    <a class="boton-borde" href="admin/vehiculos.php">Ir al inventario</a>
                </div>
            <?php elseif ($rol === "cliente"): ?>
                <h2 id="titulo-llamado" class="titulo-grande">Tu próximo auto te espera</h2>
                <p>Revisa en qué va cada solicitud o sigue explorando el catálogo.</p>
                <div class="llamado-botones">
                    <a class="boton" href="mis_solicitudes.php">Mis solicitudes</a>
                    <a class="boton-borde" href="catalogo.php">Ver el catálogo</a>
                </div>
            <?php else: ?>
                <h2 id="titulo-llamado" class="titulo-grande">Tu próximo auto empieza aquí</h2>
                <p>Crea tu cuenta para enviar solicitudes de compra y seguir su estado desde cualquier lugar.</p>
                <div class="llamado-botones">
                    <a class="boton" href="registro.php">Crear cuenta</a>
                    <a class="boton-borde" href="login.php">Iniciar sesión</a>
                </div>
            <?php endif; ?>
        </div>
        <button type="button" class="boton-pausa video-pausa" aria-label="Pausar el video de fondo" hidden>
            <span class="icono-pausa" aria-hidden="true"></span>
        </button>
    </section>
</main>

<script src="/apex/frontend/js/portada.js?v=1"></script>

<?php require_once __DIR__ . "/includes/pie.php"; ?>