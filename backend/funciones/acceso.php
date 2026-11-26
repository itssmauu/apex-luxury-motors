<?php
// Aquí se revisa quién está usando el sistema y qué rol tiene

// abrir la sesión solo si todavía no está abierta
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// devuelve "visitante", "cliente" o "administrador"
function rolActual() {
    if (isset($_SESSION["rol"])) {
        return $_SESSION["rol"];
    }
    return "visitante";
}

// si no hay sesión iniciada, manda al inicio de sesión
function requerirSesion() {
    if (!isset($_SESSION["usuario_id"])) {
        header("Location: /apex/frontend/login.php");
        exit;
    }
}

// si el rol no es el que se pide, se niega el acceso
function requerirRol($rol) {
    requerirSesion();
    if ($_SESSION["rol"] !== $rol) {
        http_response_code(403);
        echo "Acceso denegado.";
        exit;
    }
}