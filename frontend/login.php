<?php
// Iniciar sesión (RF2)
// Diseño: video a pantalla completa detrás, el logo y el menú encima (como en la portada),
// un título grande a la izquierda y el formulario en una tarjeta a la derecha.

// trae rolActual() y abre la sesión
require_once __DIR__ . "/../backend/funciones/acceso.php";
// textos traducidos (t)
require_once __DIR__ . "/includes/idioma.php";

// si ya inició sesión, no tiene sentido mostrar el formulario
if (rolActual() !== "visitante") {
    header("Location: /apex/frontend/catalogo.php");
    exit;
}

$titulo = t("menu.iniciar_sesion");
// el logo y el menú van encima del video
$claseBody = "pagina-acceso";

// videos de fondo: se turnan, uno después del otro
$videosFondo = ["acceso-calle", "acceso-detalle"];

require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main>
    <section class="acceso">
        <!-- fondo: el primer video arranca solo; el JavaScript pasa al siguiente cuando termina.
             Sin JavaScript se ve la imagen fija (poster) del primero -->
        <div class="acceso-fondo" aria-hidden="true">
            <?php foreach ($videosFondo as $numero => $video): ?>
                <video class="acceso-video<?php if ($numero === 0) { echo " activo"; } ?>"
                       muted playsinline preload="<?php echo ($numero === 0) ? "auto" : "none"; ?>"
                       poster="/apex/img/videos/<?php echo $video; ?>.jpg">
                    <source src="/apex/img/videos/<?php echo $video; ?>.mp4" type="video/mp4">
                </video>
            <?php endforeach; ?>
        </div>

        <div class="acceso-contenido">
            <!-- texto de bienvenida (a la izquierda en computadora) -->
            <div class="acceso-texto">
                <p class="sobretitulo">Apex Luxury Motors</p>
                <p class="acceso-titulo"><?php echo htmlspecialchars(t("acceso.bienvenido")); ?></p>
                <p class="acceso-bajada"><?php echo htmlspecialchars(t("acceso.login_bajada")); ?></p>
            </div>

            <!-- tarjeta con el formulario -->
            <div class="acceso-tarjeta">
                <h1><?php echo htmlspecialchars(t("menu.iniciar_sesion")); ?></h1>

                <!-- el formulario va por POST al proceso del backend, que valida y redirige -->
                <form method="post" action="/apex/backend/procesos/login.php">
                    <div class="campo">
                        <label for="correo"><?php echo htmlspecialchars(t("campo.correo")); ?></label>
                        <input type="email" id="correo" name="correo" required autocomplete="email">
                    </div>
                    <div class="campo">
                        <label for="contrasena"><?php echo htmlspecialchars(t("campo.contrasena")); ?></label>
                        <!-- el botón "Mostrar" lo activa el JavaScript (data-texto-*: sus dos textos traducidos) -->
                        <div class="campo-clave">
                            <input type="password" id="contrasena" name="contrasena" required autocomplete="current-password">
                            <button type="button" class="ver-clave" aria-controls="contrasena"
                                    data-texto-mostrar="<?php echo htmlspecialchars(t("comun.mostrar")); ?>"
                                    data-texto-ocultar="<?php echo htmlspecialchars(t("comun.ocultar")); ?>" hidden><?php echo htmlspecialchars(t("comun.mostrar")); ?></button>
                        </div>
                    </div>
                    <button type="submit" class="acceso-boton"><?php echo htmlspecialchars(t("acceso.entrar")); ?></button>
                </form>

                <p class="acceso-cambio"><?php echo htmlspecialchars(t("acceso.sin_cuenta")); ?> <a href="registro.php"><?php echo htmlspecialchars(t("acceso.registrate")); ?></a></p>
            </div>
        </div>

        <!-- pausa del video de fondo (el JavaScript le quita el hidden) -->
        <button type="button" class="boton-pausa acceso-pausa"
                aria-label="<?php echo htmlspecialchars(t("acceso.pausar_video")); ?>"
                data-texto-pausar="<?php echo htmlspecialchars(t("acceso.pausar_video")); ?>"
                data-texto-reproducir="<?php echo htmlspecialchars(t("acceso.reproducir_video")); ?>" hidden>
            <span class="icono-pausa" aria-hidden="true"></span>
        </button>
    </section>
</main>

<!-- videos que se turnan y botón para mostrar la contraseña -->
<script src="/apex/frontend/js/acceso.js?v=2"></script>

<?php require_once __DIR__ . "/includes/pie.php"; ?>
