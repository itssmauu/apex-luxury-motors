<?php
require_once __DIR__ . "/../funciones/acceso.php";   // también abre la sesión
require_once __DIR__ . "/../funciones/usuarios.php";

// solo se acepta si llegó desde un formulario
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /apex/frontend/registro.php");
    exit;
}

// el ?? "" evita errores si algún campo no llega
$resultado = registrarCliente(
    $_POST["nombre"] ?? "",
    $_POST["correo"] ?? "",
    $_POST["contrasena"] ?? "",
    $_POST["telefono"] ?? ""
);

// guardar el mensaje para que la página lo muestre
$_SESSION["mensaje"] = [
    "tipo" => $resultado["ok"] ? "exito" : "error",
    "texto" => $resultado["mensaje"]
];

// si salió bien, va a iniciar sesión; si no, vuelve al formulario
if ($resultado["ok"]) {
    header("Location: /apex/frontend/login.php");
} else {
    header("Location: /apex/frontend/registro.php");
}
exit;