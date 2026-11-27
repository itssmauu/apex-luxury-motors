<?php
// abre la sesión y trae rolActual(), antes de imprimir nada
require_once __DIR__ . "/../../backend/funciones/acceso.php";

// por si una página olvida definir su título
if (!isset($titulo)) {
    $titulo = "Apex Luxury Motors";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($titulo); ?> - Apex Luxury Motors (demo)</title>
</head>
<body>
<header>
    <a href="/apex/frontend/catalogo.php">
        <img src="/apex/img/logo_apex.png" alt="Apex Luxury Motors, ir al catálogo" height="40">
    </a>
</header>