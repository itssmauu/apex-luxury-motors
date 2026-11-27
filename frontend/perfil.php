<?php
// Mi perfil (RF3)
// Mismo diseño que iniciar sesión: video a pantalla completa detrás,
// un saludo a la izquierda y el formulario en una tarjeta a la derecha.

// trae rolActual() y requerirSesion(), y abre la sesión
require_once __DIR__ . "/../backend/funciones/acceso.php";
require_once __DIR__ . "/../backend/funciones/usuarios.php";
// textos traducidos (t)
require_once __DIR__ . "/includes/idioma.php";

// sin sesión, lo manda al login
requerirSesion();

// datos actuales para llenar el formulario
$perfil = obtenerPerfil();
if ($perfil === null) {
    header("Location: /apex/frontend/login.php");
    exit;
}

$titulo = t("menu.mi_perfil");
// el logo y el menú van encima del video
$claseBody = "pagina-acceso";

// solo el primer nombre para el saludo ("Hola, Mauro")
$primerNombre = explode(" ", trim($perfil["nombre"]))[0];

require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main>
    <section class="acceso perfil">
        <!-- un solo video de fondo, se repite (sin JavaScript se ve la imagen fija) -->
        <div class="acceso-fondo" aria-hidden="true">
            <video class="acceso-video activo" muted playsinline preload="auto"
                   poster="/apex/img/videos/acceso-detalle.jpg">
                <source src="/apex/img/videos/acceso-detalle.mp4" type="video/mp4">
            </video>
        </div>

        <div class="acceso-contenido">
            <!-- saludo y accesos rápidos -->
            <div class="acceso-texto">
                <p class="sobretitulo"><?php echo htmlspecialchars(t("perfil.mi_cuenta")); ?></p>
                <p class="acceso-titulo"><?php echo htmlspecialchars(t("cuenta.saludo", $primerNombre)); ?></p>
                <p class="acceso-bajada"><?php echo htmlspecialchars(t("perfil.bajada")); ?></p>

                <ul class="perfil-atajos">
                    <?php if (rolActual() === "cliente"): ?>
                        <li><a href="mis_solicitudes.php"><?php echo htmlspecialchars(t("menu.mis_solicitudes")); ?> <span aria-hidden="true">→</span></a></li>
                    <?php elseif (rolActual() === "administrador"): ?>
                        <li><a href="admin/vehiculos.php"><?php echo htmlspecialchars(t("menu.inventario")); ?> <span aria-hidden="true">→</span></a></li>
                        <li><a href="admin/solicitudes.php"><?php echo htmlspecialchars(t("menu.solicitudes")); ?> <span aria-hidden="true">→</span></a></li>
                    <?php endif; ?>
                    <li><a href="catalogo.php"><?php echo htmlspecialchars(t("menu.catalogo")); ?> <span aria-hidden="true">→</span></a></li>
                </ul>
            </div>

            <!-- tarjeta con el formulario -->
            <div class="acceso-tarjeta">
                <h1><?php echo htmlspecialchars(t("menu.mi_perfil")); ?></h1>

                <!-- el formulario va por POST al proceso del backend, que valida, guarda y vuelve aquí -->
                <form method="post" action="/apex/backend/procesos/perfil_guardar.php">
                    <div class="campo">
                        <label for="nombre"><?php echo htmlspecialchars(t("campo.nombre")); ?></label>
                        <input type="text" id="nombre" name="nombre" required autocomplete="name"
                               value="<?php echo htmlspecialchars($perfil["nombre"]); ?>">
                    </div>

                    <div class="campos-dobles">
                        <!-- el correo identifica la cuenta, por eso solo se muestra -->
                        <div class="campo">
                            <p class="campo-etiqueta"><?php echo htmlspecialchars(t("campo.correo")); ?></p>
                            <p class="campo-fijo"><?php echo htmlspecialchars($perfil["correo"]); ?></p>
                        </div>
                        <div class="campo">
                            <label for="telefono"><?php echo htmlspecialchars(t("campo.telefono")); ?></label>
                            <input type="tel" id="telefono" name="telefono" required autocomplete="tel"
                                   value="<?php echo htmlspecialchars($perfil["telefono"]); ?>">
                        </div>
                    </div>

                    <!-- contraseña: opcional -->
                    <fieldset class="perfil-clave">
                        <legend><?php echo htmlspecialchars(t("perfil.cambiar_clave")); ?> <span><?php echo htmlspecialchars(t("perfil.opcional")); ?></span></legend>
                        <p class="campo-ayuda"><?php echo htmlspecialchars(t("perfil.ayuda")); ?></p>
                        <!-- data-texto-*: los dos textos traducidos del botón "Mostrar" -->
                        <div class="campo">
                            <label for="contrasena_actual"><?php echo htmlspecialchars(t("perfil.clave_actual")); ?></label>
                            <div class="campo-clave">
                                <input type="password" id="contrasena_actual" name="contrasena_actual" autocomplete="current-password">
                                <button type="button" class="ver-clave" aria-controls="contrasena_actual"
                                        data-texto-mostrar="<?php echo htmlspecialchars(t("comun.mostrar")); ?>"
                                        data-texto-ocultar="<?php echo htmlspecialchars(t("comun.ocultar")); ?>" hidden><?php echo htmlspecialchars(t("comun.mostrar")); ?></button>
                            </div>
                        </div>
                        <div class="campo">
                            <label for="contrasena_nueva"><?php echo htmlspecialchars(t("perfil.clave_nueva")); ?></label>
                            <div class="campo-clave">
                                <input type="password" id="contrasena_nueva" name="contrasena_nueva" autocomplete="new-password">
                                <button type="button" class="ver-clave" aria-controls="contrasena_nueva"
                                        data-texto-mostrar="<?php echo htmlspecialchars(t("comun.mostrar")); ?>"
                                        data-texto-ocultar="<?php echo htmlspecialchars(t("comun.ocultar")); ?>" hidden><?php echo htmlspecialchars(t("comun.mostrar")); ?></button>
                            </div>
                        </div>
                    </fieldset>

                    <button type="submit" class="acceso-boton"><?php echo htmlspecialchars(t("perfil.guardar")); ?></button>
                </form>
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

<!-- video de fondo y botones para mostrar la contraseña -->
<script src="/apex/frontend/js/acceso.js?v=2"></script>

<?php require_once __DIR__ . "/includes/pie.php"; ?>
