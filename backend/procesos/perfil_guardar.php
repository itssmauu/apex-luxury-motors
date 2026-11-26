<?php
require_once __DIR__ . "/../funciones/acceso.php";
require_once __DIR__ . "/../funciones/usuarios.php";

requerirSesion();   // si no hay sesión, lo manda al login

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /apex/frontend/perfil.php");
    exit;
}

$resultado = actualizarPerfil(
    $_POST["nombre"] ?? "",
    $_POST["telefono"] ?? "",
    $_POST["contrasena_actual"] ?? "",
    $_POST["contrasena_nueva"] ?? ""
);

$_SESSION["mensaje"] = [
    "tipo" => $resultado["ok"] ? "exito" : "error",
    "texto" => $resultado["mensaje"]
];

header("Location: /apex/frontend/perfil.php");
exit;