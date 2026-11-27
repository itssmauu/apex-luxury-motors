<?php
// Mi perfil (RF3)

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

require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main class="pagina-formulario">
    <h1>Mi perfil</h1>

    <!-- el formulario va por POST al proceso del backend, que valida, guarda y vuelve aquí -->
    <form method="post" action="/apex/backend/procesos/perfil_guardar.php">
        <p>
            <label for="nombre">Nombre completo</label><br>
            <input type="text" id="nombre" name="nombre" required autocomplete="name"
                   value="<?php echo htmlspecialchars($perfil["nombre"]); ?>">
        </p>

        <!-- el correo identifica la cuenta, por eso solo se muestra -->
        <p><strong>Correo electrónico:</strong> <?php echo htmlspecialchars($perfil["correo"]); ?></p>

        <p>
            <label for="telefono">Teléfono</label><br>
            <input type="tel" id="telefono" name="telefono" required
                   value="<?php echo htmlspecialchars($perfil["telefono"]); ?>">
        </p>

        <h2>Cambiar contraseña (opcional)</h2>
        <p>Deja estos dos campos vacíos si no quieres cambiarla.</p>
        <p>
            <label for="contrasena_actual">Contraseña actual</label><br>
            <input type="password" id="contrasena_actual" name="contrasena_actual" autocomplete="current-password">
        </p>
        <p>
            <label for="contrasena_nueva">Contraseña nueva</label><br>
            <input type="password" id="contrasena_nueva" name="contrasena_nueva" autocomplete="new-password">
        </p>

        <p>
            <button type="submit">Guardar cambios</button>
        </p>
    </form>

    <!-- salidas de la página ("Mis solicitudes" solo es para clientes) -->
    <p class="enlaces-salida">
        <?php if (rolActual() === "cliente"): ?>
            <a href="mis_solicitudes.php">Mis solicitudes</a>
        <?php endif; ?>
        <a class="volver" href="catalogo.php"><span class="flecha" aria-hidden="true">←</span> Volver al catálogo</a>
    </p>
</main>

<?php require_once __DIR__ . "/includes/pie.php"; ?>