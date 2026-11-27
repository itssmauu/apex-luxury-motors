<?php
// Iniciar sesión (RF2)
// Diseño: video a pantalla completa detrás, el logo y el menú encima (como en la portada),
// un título grande a la izquierda y el formulario en una tarjeta a la derecha.

// trae rolActual() y abre la sesión
require_once __DIR__ . "/../backend/funciones/acceso.php";

// si ya inició sesión, no tiene sentido mostrar el formulario
if (rolActual() !== "visitante") {
    header("Location: /apex/frontend/catalogo.php");
    exit;
}

$titulo = "Iniciar sesión";
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
                <p class="acceso-titulo">Bienvenido de vuelta</p>
                <p class="acceso-bajada">Entra a tu cuenta para solicitar la compra de tu próximo auto y seguir tus solicitudes.</p>
            </div>

            <!-- tarjeta con el formulario -->
            <div class="acceso-tarjeta">
                <h1>Iniciar sesión</h1>

                <!-- el formulario va por POST al proceso del backend, que valida y redirige -->
                <form method="post" action="/apex/backend/procesos/login.php">
                    <div class="campo">
                        <label for="correo">Correo electrónico</label>
                        <input type="email" id="correo" name="correo" required autocomplete="email">
                    </div>
                    <div class="campo">
                        <label for="contrasena">Contraseña</label>
                        <!-- el botón "Mostrar" lo activa el JavaScript -->
                        <div class="campo-clave">
                            <input type="password" id="contrasena" name="contrasena" required autocomplete="current-password">
                            <button type="button" class="ver-clave" aria-controls="contrasena" hidden>Mostrar</button>
                        </div>
                    </div>
                    <button type="submit" class="acceso-boton">Entrar</button>
                </form>

                <p class="acceso-cambio">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
            </div>
        </div>

        <!-- pausa del video de fondo (el JavaScript le quita el hidden) -->
        <button type="button" class="boton-pausa acceso-pausa" aria-label="Pausar video de fondo" hidden>
            <span class="icono-pausa" aria-hidden="true"></span>
        </button>
    </section>
</main>

<!-- videos que se turnan y botón para mostrar la contraseña -->
<script src="/apex/frontend/js/acceso.js?v=1"></script>

<?php require_once __DIR__ . "/includes/pie.php"; ?>
