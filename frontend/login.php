<?php
// Iniciar sesión (RF2)

// trae rolActual() y abre la sesión
require_once __DIR__ . "/../backend/funciones/acceso.php";

// si ya inició sesión, no tiene sentido mostrar el formulario
if (rolActual() !== "visitante") {
    header("Location: /apex/frontend/catalogo.php");
    exit;
}

$titulo = "Iniciar sesión";

require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main class="pagina-formulario">
    <h1>Iniciar sesión</h1>

    <!-- el formulario va por POST al proceso del backend, que valida y redirige -->
    <form method="post" action="/apex/backend/procesos/login.php">
        <p>
            <label for="correo">Correo electrónico</label><br>
            <input type="email" id="correo" name="correo" required autocomplete="email">
        </p>
        <p>
            <label for="contrasena">Contraseña</label><br>
            <input type="password" id="contrasena" name="contrasena" required autocomplete="current-password">
        </p>
        <p>
            <button type="submit">Entrar</button>
        </p>
    </form>

    <p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
</main>

<?php require_once __DIR__ . "/includes/pie.php"; ?>