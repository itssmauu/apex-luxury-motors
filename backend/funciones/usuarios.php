<?php
// Esqueleto: por ahora solo devuelven respuestas de ejemplo
// Después se reemplaza el contenido por la versión real

function registrarCliente($nombre, $correo, $contrasena, $telefono) {
    return ["ok" => true, "mensaje" => "Cuenta creada (ejemplo)."];
}

function iniciarSesion($correo, $contrasena) {
    return ["ok" => true, "mensaje" => "Sesión iniciada (ejemplo)."];
}

function cerrarSesion() {
    return ["ok" => true, "mensaje" => "Sesión cerrada (ejemplo)."];
}

function actualizarPerfil($nombre, $telefono, $actual, $nueva) {
    return ["ok" => true, "mensaje" => "Perfil actualizado (ejemplo)."];
}