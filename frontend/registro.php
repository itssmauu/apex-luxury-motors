<?php
// Registro de clientes (RF1)
// Mismo diseño que iniciar sesión: video detrás, título a la izquierda y el formulario en una tarjeta.

// trae rolActual() y abre la sesión
require_once __DIR__ . "/../backend/funciones/acceso.php";

// si ya inició sesión, no tiene sentido registrarse otra vez
if (rolActual() !== "visitante") {
    header("Location: /apex/frontend/catalogo.php");
    exit;
}

$titulo = "Crear cuenta";
// el logo y el menú van encima del video
$claseBody = "pagina-acceso";

// videos de fondo: se turnan, uno después del otro
$videosFondo = ["registro-trasera", "registro-faro"];

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
                <p class="acceso-titulo">Tu próximo auto empieza aquí</p>
                <p class="acceso-bajada">Crea tu cuenta para solicitar la compra de cualquier vehículo del catálogo y seguir cada solicitud.</p>
            </div>

            <!-- tarjeta con el formulario -->
            <div class="acceso-tarjeta">
                <h1>Crear cuenta</h1>

                <!-- el formulario va por POST al proceso del backend, que valida, guarda y redirige -->
                <form method="post" action="/apex/backend/procesos/registro.php">
                    <div class="campo">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" required autocomplete="name">
                    </div>
                    <!-- correo y teléfono uno al lado del otro en pantallas anchas -->
                    <div class="campos-dobles">
                        <div class="campo">
                            <label for="correo">Correo electrónico</label>
                            <input type="email" id="correo" name="correo" required autocomplete="email">
                        </div>
                        <div class="campo">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" required autocomplete="tel">
                        </div>
                    </div>
                    <div class="campo">
                        <label for="contrasena">Contraseña</label>
                        <!-- el botón "Mostrar" lo activa el JavaScript -->
                        <div class="campo-clave">
                            <input type="password" id="contrasena" name="contrasena" required autocomplete="new-password">
                            <button type="button" class="ver-clave" aria-controls="contrasena" hidden>Mostrar</button>
                        </div>
                    </div>
                    <button type="submit" class="acceso-boton">Crear cuenta</button>
                </form>

                <p class="acceso-cambio">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
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
