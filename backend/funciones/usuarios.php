<?php
// conexión a la base de datos
require_once __DIR__ . "/../config/conexion.php";

function registrarCliente($nombre, $correo, $contrasena, $telefono) {
    global $conexion;

    // quitar espacios de los lados
    $nombre = trim($nombre);
    $correo = trim($correo);
    $telefono = trim($telefono);

    // ningún campo puede ir vacío
    if ($nombre === "" || $correo === "" || $contrasena === "" || $telefono === "") {
        return ["ok" => false, "mensaje" => "Completa todos los campos."];
    }

    // ver si el correo ya está registrado
    $consulta = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $consulta->execute([$correo]);
    if ($consulta->fetch()) {
        return ["ok" => false, "mensaje" => "Ese correo ya está registrado."];
    }

    // la contraseña se guarda cifrada, nunca en texto plano
    $clave = password_hash($contrasena, PASSWORD_DEFAULT);

    $insertar = $conexion->prepare(
        "INSERT INTO usuarios (nombre, correo, contrasena, telefono, rol) VALUES (?, ?, ?, ?, 'cliente')"
    );
    $insertar->execute([$nombre, $correo, $clave, $telefono]);

    return ["ok" => true, "mensaje" => "Cuenta creada. Ya puedes iniciar sesión."];
}

function iniciarSesion($correo, $contrasena) {
    global $conexion;

    $correo = trim($correo);

    if ($correo === "" || $contrasena === "") {
        return ["ok" => false, "mensaje" => "Escribe tu correo y tu contraseña."];
    }

    // buscar al usuario activo con ese correo
    $consulta = $conexion->prepare(
        "SELECT id, nombre, contrasena, rol FROM usuarios WHERE correo = ? AND activo = 1"
    );
    $consulta->execute([$correo]);
    $usuario = $consulta->fetch();

    // mismo mensaje si el correo no existe o la contraseña está mal
    if (!$usuario || !password_verify($contrasena, $usuario["contrasena"])) {
        return ["ok" => false, "mensaje" => "Correo o contraseña incorrectos."];
    }

    // cambia el id de sesión al entrar (evita robo de sesión)
    session_regenerate_id(true);

    $_SESSION["usuario_id"] = $usuario["id"];
    $_SESSION["nombre"] = $usuario["nombre"];
    $_SESSION["rol"] = $usuario["rol"];

    return ["ok" => true, "mensaje" => "Bienvenido, " . $usuario["nombre"] . "."];
}

function cerrarSesion() {
    // vaciar los datos de la sesión y destruirla
    $_SESSION = [];
    session_destroy();

    return ["ok" => true, "mensaje" => "Sesión cerrada."];
}

function actualizarPerfil($nombre, $telefono, $actual, $nueva) {
    return ["ok" => true, "mensaje" => "Perfil actualizado (ejemplo)."];
}