<?php
require_once __DIR__ . "/../funciones/acceso.php";
require_once __DIR__ . "/../funciones/usuarios.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /apex/frontend/login.php");
    exit;
}

$resultado = iniciarSesion(
    $_POST["correo"] ?? "",
    $_POST["contrasena"] ?? ""
);

if (!$resultado["ok"]) {
    $_SESSION["mensaje"] = ["tipo" => "error", "texto" => $resultado["mensaje"]];
    header("Location: /apex/frontend/login.php");
    exit;
}

// el administrador va a su panel; el cliente, al inicio
if ($_SESSION["rol"] === "administrador") {
    header("Location: /apex/frontend/admin/vehiculos.php");
} else {
    header("Location: /apex/frontend/index.php");
}
exit;