<?php
// Registro de clientes (RF1)

// trae rolActual() y abre la sesión
require_once __DIR__ . "/../backend/funciones/acceso.php";

// si ya inició sesión, no tiene sentido registrarse otra vez
if (rolActual() !== "visitante") {
    header("Location: /apex/frontend/catalogo.php");
    exit;
}

$titulo = "Crear cuenta";

require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main class="pagina-formulario">
    <h1>Crear cuenta</h1>

    <!-- el formulario va por POST al proceso del backend, que valida, guarda y redirige -->
    <form method="post" action="/apex/backend/procesos/registro.php">
        <p>
            <label for="nombre">Nombre completo</label><br>
            <input type="text" id="nombre" name="nombre" required autocomplete="name">
        </p>
        <p>
            <label for="correo">Correo electrónico</label><br>
            <input type="email" id="correo" name="correo" required autocomplete="email">
        </p>
        <p>
            <label for="telefono">Teléfono</label><br>
            <input type="tel" id="telefono" name="telefono" required autocomplete="tel">
        </p>
        <p>
            <label for="contrasena">Contraseña</label><br>
            <input type="password" id="contrasena" name="contrasena" required autocomplete="new-password">
        </p>
        <p>
            <button type="submit">Crear cuenta</button>
        </p>
    </form>

    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
</main>

<?php require_once __DIR__ . "/includes/pie.php"; ?>