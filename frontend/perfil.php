<?php
// Mi perfil (RF3)
// Mismo diseño que iniciar sesión: video a pantalla completa detrás,
// un saludo a la izquierda y el formulario en una tarjeta a la derecha.

// trae rolActual() y requerirSesion(), y abre la sesión
require_once __DIR__ . "/../backend/funciones/acceso.php";
require_once __DIR__ . "/../backend/funciones/usuarios.php";

// sin sesión, lo manda al login
requerirSesion();

// datos actuales para llenar el formulario
$perfil = obtenerPerfil();
if ($perfil === null) {
    header("Location: /apex/frontend/login.php");
    exit;
}

$titulo = "Mi perfil";
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
                <p class="sobretitulo">Mi cuenta</p>
                <p class="acceso-titulo">Hola, <?php echo htmlspecialchars($primerNombre); ?></p>
                <p class="acceso-bajada">Mantén tus datos de contacto al día para que el equipo de Apex pueda responder tus solicitudes.</p>

                <ul class="perfil-atajos">
                    <?php if (rolActual() === "cliente"): ?>
                        <li><a href="mis_solicitudes.php">Mis solicitudes <span aria-hidden="true">→</span></a></li>
                    <?php elseif (rolActual() === "administrador"): ?>
                        <li><a href="admin/vehiculos.php">Inventario <span aria-hidden="true">→</span></a></li>
                        <li><a href="admin/solicitudes.php">Solicitudes <span aria-hidden="true">→</span></a></li>
                    <?php endif; ?>
                    <li><a href="catalogo.php">Catálogo <span aria-hidden="true">→</span></a></li>
                </ul>
            </div>

            <!-- tarjeta con el formulario -->
            <div class="acceso-tarjeta">
                <h1>Mi perfil</h1>

                <!-- el formulario va por POST al proceso del backend, que valida, guarda y vuelve aquí -->
                <form method="post" action="/apex/backend/procesos/perfil_guardar.php">
                    <div class="campo">
                        <label for="nombre">Nombre completo</label>
                        <input type="text" id="nombre" name="nombre" required autocomplete="name"
                               value="<?php echo htmlspecialchars($perfil["nombre"]); ?>">
                    </div>

                    <div class="campos-dobles">
                        <!-- el correo identifica la cuenta, por eso solo se muestra -->
                        <div class="campo">
                            <p class="campo-etiqueta">Correo electrónico</p>
                            <p class="campo-fijo"><?php echo htmlspecialchars($perfil["correo"]); ?></p>
                        </div>
                        <div class="campo">
                            <label for="telefono">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" required autocomplete="tel"
                                   value="<?php echo htmlspecialchars($perfil["telefono"]); ?>">
                        </div>
                    </div>

                    <!-- contraseña: opcional -->
                    <fieldset class="perfil-clave">
                        <legend>Cambiar contraseña <span>(opcional)</span></legend>
                        <p class="campo-ayuda">Deja estos dos campos vacíos si no quieres cambiarla.</p>
                        <div class="campo">
                            <label for="contrasena_actual">Contraseña actual</label>
                            <div class="campo-clave">
                                <input type="password" id="contrasena_actual" name="contrasena_actual" autocomplete="current-password">
                                <button type="button" class="ver-clave" aria-controls="contrasena_actual" hidden>Mostrar</button>
                            </div>
                        </div>
                        <div class="campo">
                            <label for="contrasena_nueva">Contraseña nueva</label>
                            <div class="campo-clave">
                                <input type="password" id="contrasena_nueva" name="contrasena_nueva" autocomplete="new-password">
                                <button type="button" class="ver-clave" aria-controls="contrasena_nueva" hidden>Mostrar</button>
                            </div>
                        </div>
                    </fieldset>

                    <button type="submit" class="acceso-boton">Guardar cambios</button>
                </form>
            </div>
        </div>

        <!-- pausa del video de fondo (el JavaScript le quita el hidden) -->
        <button type="button" class="boton-pausa acceso-pausa" aria-label="Pausar video de fondo" hidden>
            <span class="icono-pausa" aria-hidden="true"></span>
        </button>
    </section>
</main>

<!-- video de fondo y botones para mostrar la contraseña -->
<script src="/apex/frontend/js/acceso.js?v=1"></script>

<?php require_once __DIR__ . "/includes/pie.php"; ?>
