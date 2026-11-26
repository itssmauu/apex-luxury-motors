<?php
// Detalle de vehículo (RF5)

// funciones del backend que usa esta página:
// obtenerVehiculo() para los datos y las fotos del vehículo
require_once __DIR__ . "/../backend/funciones/vehiculos.php";
// esta segunda línea es para rolActual() (y también abre la sesión)
require_once __DIR__ . "/../backend/funciones/acceso.php";

// el id viene en la dirección: vehiculo.php?id=8
$id = isset($_GET["id"]) && is_string($_GET["id"]) ? $_GET["id"] : 0;

// si no existe o está inactivo, llega null
$vehiculo = obtenerVehiculo($id);

// la página solo decide qué mostrar, el backend vuelve a validar al solicitar
$rol = rolActual();

if ($vehiculo === null) {
    http_response_code(404);
    $titulo = "Vehículo no encontrado";
} else {
    $titulo = $vehiculo["marca"] . " " . $vehiculo["modelo"];
}

// medidas reales de la foto principal, para ponerle width y height y que no salte al cargar
// (solo se lee el archivo, no se cambia)
$medidasFoto = false;
if ($vehiculo !== null && count($vehiculo["fotos"]) > 0) {
    $archivoFoto = __DIR__ . "/../" . $vehiculo["fotos"][0]["ruta"];
    if (is_file($archivoFoto)) {
        $medidasFoto = getimagesize($archivoFoto);
    }
}

// AQUÍ VA LA PLANTILLA DE ENZO (encabezado, menú y mensajes):
// require_once __DIR__ . "/includes/encabezado.php";
// require_once __DIR__ . "/includes/menu.php";

// TEMPORAL: inicio de la página hecho a mano (de aquí hasta FIN TEMPORAL),
// se borra cuando esté la plantilla
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($titulo); ?> - Apex Luxury Motors (demo)</title>
</head>
<body>
<!-- FIN TEMPORAL -->

<!-- estilos básicos TEMPORALES, se pueden borrar cuando haya hoja de estilos -->
<style>
    .detalle { display: flex; flex-wrap: wrap; gap: 24px; }
    .fotos { width: 480px; max-width: 100%; }
    .fotos .principal { width: 100%; height: auto; border-radius: 6px; }
    .miniaturas { display: flex; gap: 8px; margin-top: 8px; }
    .miniaturas img { width: 90px; height: 60px; object-fit: cover; border: 1px solid #ccc; }
    .datos { flex: 1; min-width: 260px; }
    .accion { margin-top: 20px; }
    .vendido { color: #b00000; font-weight: bold; }
</style>

<main>
    <?php if ($vehiculo === null): ?>
        <h1>Vehículo no encontrado</h1>
        <p>El vehículo que buscas no existe o ya no está en el catálogo.</p>
        <p><a href="catalogo.php">Volver al catálogo</a></p>
    <?php else: ?>
        <h1><?php echo htmlspecialchars($vehiculo["marca"] . " " . $vehiculo["modelo"]); ?></h1>

        <div class="detalle">
            <!-- fotos: la primera es la principal y todas salen como miniaturas -->
            <section class="fotos">
                <?php if (count($vehiculo["fotos"]) > 0): ?>
                    <!-- la base guarda "img/archivo.jpg", desde frontend/ solo falta subir un nivel -->
                    <!-- fetchpriority high: es lo primero que se ve, que cargue antes que lo demás -->
                    <img class="principal"
                         src="../<?php echo htmlspecialchars($vehiculo["fotos"][0]["ruta"]); ?>"
                         alt="<?php echo htmlspecialchars($vehiculo["marca"] . " " . $vehiculo["modelo"]); ?>"
                         <?php if ($medidasFoto) { echo 'width="' . (int) $medidasFoto[0] . '" height="' . (int) $medidasFoto[1] . '"'; } ?>
                         fetchpriority="high">

                    <div class="miniaturas">
                        <?php foreach ($vehiculo["fotos"] as $numero => $foto): ?>
                            <!-- cada miniatura abre la foto en grande, en otra pestaña (lo avisa el alt) -->
                            <a href="../<?php echo htmlspecialchars($foto["ruta"]); ?>" target="_blank">
                                <img src="../<?php echo htmlspecialchars($foto["ruta"]); ?>"
                                     alt="Foto <?php echo $numero + 1; ?> de <?php echo htmlspecialchars($vehiculo["marca"] . " " . $vehiculo["modelo"]); ?> (se abre en otra pestaña)"
                                     width="90" height="60">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Foto no disponible</p>
                <?php endif; ?>
            </section>

            <!-- datos del vehículo -->
            <section class="datos">
                <p><strong>Marca:</strong> <?php echo htmlspecialchars($vehiculo["marca"]); ?></p>
                <p><strong>Modelo:</strong> <?php echo htmlspecialchars($vehiculo["modelo"]); ?></p>
                <p><strong>Precio:</strong> $<?php echo number_format((float) $vehiculo["precio"], 0, ".", ","); ?> USD</p>
                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($vehiculo["categoria"]); ?></p>
                <p>
                    <strong>Estado:</strong>
                    <?php if ($vehiculo["estado"] === "vendido"): ?>
                        <span class="vendido">Vendido</span>
                    <?php else: ?>
                        Disponible
                    <?php endif; ?>
                </p>
                <p><strong>Descripción:</strong></p>
                <p><?php echo htmlspecialchars($vehiculo["descripcion"]); ?></p>

                <!-- acción según el rol (el administrador solo lee, no ve nada aquí) -->
                <?php if ($rol !== "administrador"): ?>
                    <div class="accion">
                        <?php if ($vehiculo["estado"] === "vendido"): ?>
                            <p class="vendido">Este vehículo ya fue vendido</p>
                        <?php elseif ($rol === "cliente"): ?>
                            <form method="post" action="../backend/procesos/solicitar.php" id="form-solicitar">
                                <input type="hidden" name="vehiculo_id" value="<?php echo (int) $vehiculo["id"]; ?>">
                                <button type="submit">Solicitar compra</button>
                            </form>
                            <script>
                                // para que un doble clic no mande la solicitud dos veces
                                var formSolicitar = document.getElementById("form-solicitar");
                                formSolicitar.addEventListener("submit", function () {
                                    formSolicitar.querySelector("button").disabled = true;
                                });
                                // si vuelven con el botón Atrás del navegador, el botón se activa otra vez
                                window.addEventListener("pageshow", function () {
                                    formSolicitar.querySelector("button").disabled = false;
                                });
                            </script>
                        <?php else: ?>
                            <a href="login.php">Iniciar sesión para solicitar</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    <?php endif; ?>
</main>

<?php
// AQUÍ VA EL PIE DE LA PLANTILLA DE ENZO:
// require_once __DIR__ . "/includes/pie.php";
?>
<!-- TEMPORAL: cierre de la página hecho a mano, se borra cuando esté la plantilla -->
</body>
</html>
