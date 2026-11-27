<?php
// abre la sesión y trae rolActual(), antes de imprimir nada
require_once __DIR__ . "/../../backend/funciones/acceso.php";

// por si una página olvida definir su título
if (!isset($titulo)) {
    $titulo = "Apex Luxury Motors";
}

// clase opcional para el <body>: la portada usa "pagina-inicio"
// para que el logo y el menú vayan encima del video
if (!isset($claseBody)) {
    $claseBody = "";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($titulo); ?> - Apex Luxury Motors (demo)</title>
    <!-- estilos de todo el sitio; subir el ?v= cuando cambie el CSS para que el navegador baje la versión nueva -->
    <link rel="stylesheet" href="/apex/frontend/css/estilos.css?v=11">
</head>
<body<?php if ($claseBody !== "") { echo ' class="' . htmlspecialchars($claseBody) . '"'; } ?>>
<header>
    <!-- logo centrado arriba en todas las páginas (PNG con fondo transparente) -->
    <a class="logo" href="/apex/frontend/index.php">
        <img src="/apex/img/logo_apex_transparente.png" alt="Apex Luxury Motors, ir al inicio" width="800" height="394">
    </a>
</header>