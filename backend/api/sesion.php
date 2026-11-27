<?php
// API de sesión: /apex/backend/api/sesion.php
// Sirve para probar la API sin pasar por la página de inicio de sesión (Postman, curl...).
//   GET    → quién tiene la sesión abierta
//   POST   → iniciar sesión con {"correo": "...", "contrasena": "..."}
//   DELETE → cerrar sesión
// Nunca devuelve la contraseña ni otros datos sensibles: solo id, nombre y rol.

require_once __DIR__ . "/respuesta.php";
require_once __DIR__ . "/../funciones/usuarios.php";

function usuarioDeLaSesion()
{
    return [
        "id"     => (int) $_SESSION["usuario_id"],
        "nombre" => $_SESSION["nombre"] ?? "",
        "rol"    => $_SESSION["rol"] ?? "",
    ];
}

try {
    switch ($_SERVER["REQUEST_METHOD"]) {
        case "GET":
            if (!isset($_SESSION["usuario_id"])) {
                responderError(401, "no_autenticado", "No hay una sesión iniciada.");
            }
            responderExito(200, usuarioDeLaSesion());

        case "POST":
            $datos = leerJson();
            $correo = $datos["correo"] ?? "";
            $contrasena = $datos["contrasena"] ?? "";
            if (!is_string($correo) || !is_string($contrasena) || trim($correo) === "" || $contrasena === "") {
                responderError(400, "datos_invalidos", "Escribe el correo y la contraseña.", [
                    "correo"     => "Obligatorio.",
                    "contrasena" => "Obligatoria.",
                ]);
            }
            // misma función que usa la página de inicio de sesión
            $resultado = iniciarSesion($correo, $contrasena);
            if (!$resultado["ok"]) {
                // mismo mensaje para correo o contraseña incorrectos
                responderError(401, "credenciales_invalidas", $resultado["mensaje"]);
            }
            responderExito(200, usuarioDeLaSesion(), $resultado["mensaje"]);

        case "DELETE":
            if (!isset($_SESSION["usuario_id"])) {
                responderError(401, "no_autenticado", "No hay una sesión iniciada.");
            }
            $resultado = cerrarSesion();
            responderExito(200, null, $resultado["mensaje"]);

        default:
            metodoNoPermitido(["GET", "POST", "DELETE"]);
    }
} catch (Throwable $e) {
    // sin detalles técnicos hacia afuera
    responderError(500, "error_servidor", "Ocurrió un error en el servidor. Intenta de nuevo.");
}
