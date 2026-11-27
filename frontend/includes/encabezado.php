<?php
// abre la sesión y trae rolActual(), antes de imprimir nada
require_once __DIR__ . "/../../backend/funciones/acceso.php";
// idioma de la interfaz: t() para los textos e idiomaActual() para <html lang>
require_once __DIR__ . "/idioma.php";

// por si una página olvida definir su título
if (!isset($titulo)) {
    $titulo = "Apex Luxury Motors";
}

// clase opcional para el <body>: la portada usa "pagina-inicio"
// para que el logo y el menú vayan encima del video
if (!isset($claseBody)) {
    $claseBody = "";
}

// datos para el bloque de cuenta (arriba a la derecha)
$rolCuenta = rolActual();
$nombreCuenta = $_SESSION["nombre"] ?? "";
// primera letra del nombre para el círculo (mb_ para que funcione con tildes)
$inicialCuenta = ($nombreCuenta !== "") ? mb_strtoupper(mb_substr($nombreCuenta, 0, 1)) : "";
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars(idiomaActual()); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($titulo); ?> - Apex Luxury Motors (demo)</title>
    <!-- estilos de todo el sitio; subir el ?v= cuando cambie el CSS para que el navegador baje la versión nueva -->
    <link rel="stylesheet" href="/apex/frontend/css/estilos.css?v=18">
    <!-- avisa al CSS que hay JavaScript: así lo que aparece al hacer scroll
         solo se oculta si de verdad hay quien lo muestre -->
    <script>document.documentElement.classList.add("con-js");</script>
</head>
<body<?php if ($claseBody !== "") { echo ' class="' . htmlspecialchars($claseBody) . '"'; } ?>>
<header>
    <!-- logo centrado arriba en todas las páginas (PNG con fondo transparente) -->
    <a class="logo" href="/apex/frontend/index.php">
        <img src="/apex/img/logo_apex_transparente.png" alt="<?php echo htmlspecialchars(t("logo.alt")); ?>" width="800" height="394">
    </a>

    <!-- cuenta: saludo y cerrar sesión, solo si hay sesión iniciada -->
    <?php if ($rolCuenta !== "visitante"): ?>
        <div class="cuenta">
            <span class="cuenta-inicial" aria-hidden="true"><?php echo htmlspecialchars($inicialCuenta); ?></span>
            <span class="cuenta-texto">
                <span class="cuenta-saludo"><?php echo htmlspecialchars(t("cuenta.saludo", $nombreCuenta)); ?></span>
                <!-- el rol se traduce por su valor ("cliente" -> "rol.cliente"), nunca se muestra crudo -->
                <span class="cuenta-rol"><?php echo htmlspecialchars(t("rol." . $rolCuenta)); ?></span>
            </span>
            <a class="cuenta-salir" href="/apex/backend/procesos/logout.php"><?php echo htmlspecialchars(t("cuenta.salir")); ?></a>
        </div>
    <?php endif; ?>
</header>
