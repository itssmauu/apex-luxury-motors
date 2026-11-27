<?php
// Detalle de vehículo (RF5)

// funciones del backend que usa esta página:
// obtenerVehiculo() para los datos y las fotos del vehículo
require_once __DIR__ . "/../backend/funciones/vehiculos.php";
// esta segunda línea es para rolActual() (y también abre la sesión)
require_once __DIR__ . "/../backend/funciones/acceso.php";
// especificaciones técnicas: están en un archivo aparte porque la base no las guarda
require_once __DIR__ . "/datos/especificaciones.php";

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

// especificaciones de este vehículo (si no hay, la sección no se muestra)
$fichaTecnica = [];
if ($vehiculo !== null && isset($especificaciones[(int) $vehiculo["id"]])) {
    $fichaTecnica = $especificaciones[(int) $vehiculo["id"]];
}

// qué filas van en cada tabla
$tablasTecnicas = [
    "Motor y rendimiento" => ["Motor", "Cilindrada", "Potencia", "Torque", "Aceleración", "Velocidad máxima"],
    "Tracción y peso" => ["Tracción", "Peso"],
];

// plantilla común: encabezado con el logo, menú según el rol y mensajes
require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main>
    <?php if ($vehiculo === null): ?>
        <h1>Vehículo no encontrado</h1>
        <p>El vehículo que buscas no existe o ya no está en el catálogo.</p>
        <p><a href="catalogo.php">Volver al catálogo</a></p>
    <?php else: ?>
        <!-- salida hacia el catálogo (va antes del título para no separarlo de la ficha);
             se ve como botón, pero es un enlace. La flecha es decorativa -->
        <a class="volver" href="catalogo.php"><span class="flecha" aria-hidden="true">←</span> Volver al catálogo</a>
        <h1><?php echo htmlspecialchars($vehiculo["marca"] . " " . $vehiculo["modelo"]); ?></h1>

        <!-- dos columnas: a la izquierda fotos y ficha, a la derecha las especificaciones
             (la clase extra solo se pone si este auto tiene especificaciones) -->
        <div class="detalle<?php if (count($fichaTecnica) > 0) { echo " con-especificaciones"; } ?>">
            <div class="columna-principal">
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
                <p class="precio"><strong>Precio:</strong> $<?php echo number_format((float) $vehiculo["precio"], 0, ".", ","); ?> USD</p>
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

            <!-- especificaciones técnicas (solo si este auto tiene datos) -->
            <?php if (count($fichaTecnica) > 0): ?>
                <section class="especificaciones" aria-labelledby="titulo-especificaciones">
                    <h2 id="titulo-especificaciones">Especificaciones técnicas</h2>

                    <?php foreach ($tablasTecnicas as $nombreTabla => $filas): ?>
                        <?php
                        // solo las filas que tienen dato, para no mostrar tablas vacías
                        $filasConDato = [];
                        foreach ($filas as $fila) {
                            if (isset($fichaTecnica[$fila]) && $fichaTecnica[$fila] !== "") {
                                $filasConDato[$fila] = $fichaTecnica[$fila];
                            }
                        }
                        ?>
                        <?php if (count($filasConDato) > 0): ?>
                            <table>
                                <caption><?php echo htmlspecialchars($nombreTabla); ?></caption>
                                <tbody>
                                    <?php foreach ($filasConDato as $dato => $valor): ?>
                                        <tr>
                                            <th scope="row"><?php echo htmlspecialchars($dato); ?></th>
                                            <td><?php echo htmlspecialchars($valor); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <p class="nota">Cifras de referencia obtenidas de fuentes públicas; pueden variar según el mercado y el año del modelo.</p>
                </section>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . "/includes/pie.php"; ?>
