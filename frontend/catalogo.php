<?php
// Catálogo y Buscar vehículos (RF4)
// Diseño: como las páginas de modelos de las marcas de lujo. Arriba rotan los autos
// más exclusivos, las categorías son pestañas, y los autos van agrupados por categoría
// en tarjetas grandes con foto, cifras clave, precio y botones.

// funciones del backend que usa esta página:
// listarVehiculos() para las tarjetas, listarMarcas() y listarCategorias() para los filtros,
// listarFotosVehiculo() para elegir la mejor foto y la de otro ángulo
require_once __DIR__ . "/../backend/funciones/vehiculos.php";
// especificaciones (no están en la base) y las funciones para sacar cifras cortas
require_once __DIR__ . "/datos/especificaciones.php";
require_once __DIR__ . "/includes/cifras.php";

// leemos los filtros de la dirección (catalogo.php?marca=...&categoria=...&precio_max=...)
// si no vienen o vienen raros, quedan vacíos
$marca = isset($_GET["marca"]) && is_string($_GET["marca"]) ? trim($_GET["marca"]) : "";
$categoria = isset($_GET["categoria"]) && is_string($_GET["categoria"]) ? trim($_GET["categoria"]) : "";
$precioMax = isset($_GET["precio_max"]) && is_string($_GET["precio_max"]) ? trim($_GET["precio_max"]) : "";

// el precio máximo solo cuenta si es un número mayor que 0
if (!is_numeric($precioMax) || $precioMax <= 0) {
    $precioMax = "";
}

// para saber qué mensaje mostrar si no hay resultados
$hayFiltros = ($marca !== "" || $categoria !== "" || $precioMax !== "");

$vehiculos = listarVehiculos($marca, $categoria, $precioMax);
$marcas = listarMarcas();
$categorias = listarCategorias();

// ---------- fotos de cada auto ----------
// "portada": la primera foto con buena resolución (si ninguna, la primera).
// "otra": una vista distinta (de perfil si hay) que aparece al pasar el mouse.
function fotosDeTarjeta($vehiculoId)
{
    $fotos = listarFotosVehiculo($vehiculoId);
    $portada = null;
    $otra = null;

    foreach ($fotos as $foto) {
        $archivo = __DIR__ . "/../" . $foto["ruta"];
        $ancho = is_file($archivo) ? (getimagesize($archivo)[0] ?? 0) : 0;
        if ($portada === null && $ancho >= 1200) {
            $portada = $foto["ruta"];
        }
    }
    if ($portada === null && count($fotos) > 0) {
        $portada = $fotos[0]["ruta"];
    }

    // primero se busca la de perfil; si no hay, cualquier otra que no sea del interior
    foreach ($fotos as $foto) {
        if ($foto["ruta"] !== $portada && strpos($foto["ruta"], "-lateral.") !== false) {
            $otra = $foto["ruta"];
        }
    }
    if ($otra === null) {
        foreach ($fotos as $foto) {
            $esInterior = (strpos($foto["ruta"], "-interior.") !== false || strpos($foto["ruta"], "-cabina.") !== false);
            if ($foto["ruta"] !== $portada && !$esInterior) {
                $otra = $foto["ruta"];
                break;
            }
        }
    }

    return ["portada" => $portada, "otra" => $otra, "cantidad" => count($fotos)];
}

// sonido del motor y modelo 3D de cada auto (si el archivo existe):
// audio/motores/8.mp3 y img/vehiculos/modelos/8.glb para el auto con id 8
function mediosDelAuto($vehiculoId)
{
    $sonido = "audio/motores/" . (int) $vehiculoId . ".mp3";
    $modelo = "img/vehiculos/modelos/" . (int) $vehiculoId . ".glb";
    return [
        "sonido" => is_file(__DIR__ . "/../" . $sonido) ? "../" . $sonido : "",
        "modelo" => is_file(__DIR__ . "/../" . $modelo) ? "../" . $modelo : "",
    ];
}

// la primera oración de la descripción, como línea corta debajo del nombre
function primeraOracion($texto)
{
    $texto = trim($texto);
    $punto = strpos($texto, ". ");
    return ($punto === false) ? $texto : substr($texto, 0, $punto + 1);
}

// ---------- autos agrupados por categoría ----------
// los superdeportivos primero; las demás categorías en orden alfabético
$grupos = [];
foreach ($vehiculos as $v) {
    $grupos[$v["categoria"]][] = $v;
}
uksort($grupos, function ($a, $b) {
    if ($a === "Superdeportivo") { return -1; }
    if ($b === "Superdeportivo") { return 1; }
    return strcmp($a, $b);
});

// una línea que presenta cada categoría (si hay una nueva sin texto, no se muestra nada)
$descripcionesCategoria = [
    "Superdeportivo"           => "Los más rápidos y exclusivos del catálogo.",
    "Deportivo alemán"         => "Ingeniería alemana para el día a día y para la pista.",
    "Deportivo japonés"        => "Leyendas japonesas del turbo y la tracción integral.",
    "Deportivo estadounidense" => "V8 americano con carácter propio.",
];

// ---------- destacados de arriba: los 3 más caros que siguen disponibles ----------
$destacados = [];
foreach (listarVehiculos() as $v) {
    if ($v["estado"] === "disponible") {
        $destacados[] = $v;
    }
}
usort($destacados, function ($a, $b) {
    return (float) $b["precio"] <=> (float) $a["precio"];
});
$destacados = array_slice($destacados, 0, 3);

// arma la dirección de una pestaña de categoría sin perder la marca ni el precio elegidos
function enlaceCategoria($nuevaCategoria, $marca, $precioMax)
{
    $parametros = array_filter([
        "marca"      => $marca,
        "categoria"  => $nuevaCategoria,
        "precio_max" => $precioMax,
    ], function ($valor) {
        return $valor !== "";
    });
    return "catalogo.php" . (count($parametros) > 0 ? "?" . http_build_query($parametros) : "");
}

$titulo = "Catálogo";
// el logo y el menú van encima de las fotos de arriba
$claseBody = "pagina-catalogo";

// plantilla común: encabezado con el logo, menú según el rol y mensajes
require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main>
    <!-- ===== 1. arriba: los autos más exclusivos, uno tras otro ===== -->
    <section class="gama-portada" aria-labelledby="titulo-catalogo">
        <?php foreach ($destacados as $n => $d): ?>
            <?php $fotosDestacado = fotosDeTarjeta((int) $d["id"]); ?>
            <div class="gama-diapositiva<?php if ($n === 0) { echo " activa"; } ?>"
                 <?php if ($n > 0) { echo 'aria-hidden="true"'; } ?>>
                <?php if ($fotosDestacado["portada"] !== null): ?>
                    <img src="../<?php echo htmlspecialchars($fotosDestacado["portada"]); ?>" alt=""
                         <?php echo ($n === 0) ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
                <?php endif; ?>
                <p class="gama-destacado">
                    <span>Destacado</span>
                    <a href="vehiculo.php?id=<?php echo (int) $d["id"]; ?>" <?php if ($n > 0) { echo 'tabindex="-1"'; } ?>>
                        <?php echo htmlspecialchars($d["marca"] . " " . $d["modelo"]); ?> <span aria-hidden="true">→</span>
                    </a>
                </p>
            </div>
        <?php endforeach; ?>

        <div class="gama-portada-texto">
            <p class="sobretitulo">Catálogo</p>
            <h1 id="titulo-catalogo" class="gama-titulo">Nuestra gama</h1>
            <p class="gama-conteo">
                <?php echo count($vehiculos); ?>
                <?php echo (count($vehiculos) === 1) ? "modelo" : "modelos"; ?>
                <?php if ($hayFiltros) { echo "con estos filtros"; } else { echo "nuevos, con cero kilómetros"; } ?>
            </p>
        </div>

        <!-- pausa del cambio de destacados (el JavaScript le quita el hidden) -->
        <?php if (count($destacados) > 1): ?>
            <button type="button" class="boton-pausa gama-pausa" aria-label="Pausar destacados" hidden>
                <span class="icono-pausa" aria-hidden="true"></span>
            </button>
        <?php endif; ?>
    </section>

    <!-- ===== 2. filtros: pestañas de categoría y una barra con marca y precio ===== -->
    <section class="gama-filtros" aria-labelledby="titulo-filtros">
        <h2 id="titulo-filtros" class="solo-lector">Buscar vehículos</h2>

        <nav class="gama-pestanas" aria-label="Categorías">
            <ul>
                <li>
                    <a href="<?php echo htmlspecialchars(enlaceCategoria("", $marca, $precioMax)); ?>"
                       <?php if ($categoria === "") { echo 'aria-current="page"'; } ?>>Todos</a>
                </li>
                <?php foreach ($categorias as $fila): ?>
                    <li>
                        <a href="<?php echo htmlspecialchars(enlaceCategoria($fila["categoria"], $marca, $precioMax)); ?>"
                           <?php if ($fila["categoria"] === $categoria) { echo 'aria-current="page"'; } ?>>
                            <?php echo htmlspecialchars($fila["categoria"]); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <!-- el formulario manda los filtros por la dirección; la categoría elegida va escondida -->
        <form class="gama-barra" method="get" action="catalogo.php">
            <?php if ($categoria !== ""): ?>
                <input type="hidden" name="categoria" value="<?php echo htmlspecialchars($categoria); ?>">
            <?php endif; ?>

            <label>
                Marca
                <select name="marca">
                    <option value="">Todas</option>
                    <?php foreach ($marcas as $fila): ?>
                        <option value="<?php echo htmlspecialchars($fila["marca"]); ?>"
                            <?php if ($fila["marca"] === $marca) { echo "selected"; } ?>>
                            <?php echo htmlspecialchars($fila["marca"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Precio máximo (USD)
                <!-- autocomplete off: que el navegador no sugiera precios viejos -->
                <input type="number" name="precio_max" min="1" step="1" autocomplete="off"
                       value="<?php echo htmlspecialchars($precioMax); ?>">
            </label>

            <button type="submit">Buscar</button>
            <?php if ($hayFiltros): ?>
                <a class="gama-quitar" href="catalogo.php">Quitar filtros</a>
            <?php endif; ?>
        </form>
    </section>

    <!-- ===== 3. los autos, agrupados por categoría ===== -->
    <?php if (count($vehiculos) === 0): ?>
        <section class="gama-vacio">
            <?php if ($hayFiltros): ?>
                <p>No encontramos vehículos con esos filtros.</p>
                <p><a href="catalogo.php">Quitar filtros</a> para ver todo el catálogo.</p>
            <?php else: ?>
                <p>Por ahora no hay vehículos en el catálogo.</p>
            <?php endif; ?>
        </section>
    <?php else: ?>
        <?php $numeroGrupo = 0; ?>
        <?php foreach ($grupos as $nombreGrupo => $autos): ?>
            <?php $numeroGrupo++; $idGrupo = "grupo-" . $numeroGrupo; ?>
            <section class="gama-grupo" aria-labelledby="<?php echo $idGrupo; ?>">
                <div class="gama-grupo-cabecera revelar">
                    <h2 id="<?php echo $idGrupo; ?>"><?php echo htmlspecialchars($nombreGrupo); ?></h2>
                    <p>
                        <?php if (isset($descripcionesCategoria[$nombreGrupo])) { echo htmlspecialchars($descripcionesCategoria[$nombreGrupo]) . " · "; } ?>
                        <?php echo count($autos); ?> <?php echo (count($autos) === 1) ? "modelo" : "modelos"; ?>
                    </p>
                </div>

                <div class="gama-tarjetas">
                    <?php foreach ($autos as $n => $v): ?>
                        <?php
                        $nombreAuto = $v["marca"] . " " . $v["modelo"];
                        $fotosAuto = fotosDeTarjeta((int) $v["id"]);
                        $ficha = $especificaciones[(int) $v["id"]] ?? [];
                        // en la tarjeta van 3 cifras: potencia, aceleración y velocidad (o torque si falta alguna)
                        $cifrasAuto = array_slice(cifrasDestacadas($ficha), 0, 3);
                        $traccion = datoFicha($ficha, "Tracción");
                        $vendido = ($v["estado"] === "vendido");
                        $medios = mediosDelAuto((int) $v["id"]);
                        ?>
                        <article class="modelo-tarjeta revelar" style="--i: <?php echo $n % 2; ?>"
                                 aria-labelledby="nombre-<?php echo (int) $v["id"]; ?>">
                            <div class="modelo-tarjeta-medios">
                            <!-- la foto también lleva a la página del auto -->
                            <a class="modelo-tarjeta-foto" href="vehiculo.php?id=<?php echo (int) $v["id"]; ?>"
                               tabindex="-1" aria-hidden="true">
                                <?php if ($fotosAuto["portada"] !== null): ?>
                                    <img src="../<?php echo htmlspecialchars($fotosAuto["portada"]); ?>" alt=""
                                         width="800" height="500" loading="lazy">
                                    <?php if ($fotosAuto["otra"] !== null): ?>
                                        <!-- otro ángulo: aparece al pasar el mouse -->
                                        <img class="modelo-tarjeta-otra" src="../<?php echo htmlspecialchars($fotosAuto["otra"]); ?>" alt=""
                                             width="800" height="500" loading="lazy">
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="sin-foto">Foto no disponible</span>
                                <?php endif; ?>
                            </a>

                            <?php if ($medios["sonido"] !== ""): ?>
                                <!-- botón de encendido: suena el motor de este auto (lo activa el JavaScript) -->
                                <button type="button" class="motor" data-sonido="<?php echo htmlspecialchars($medios["sonido"]); ?>"
                                        aria-pressed="false" aria-label="Encender el motor del <?php echo htmlspecialchars($nombreAuto); ?>" hidden>
                                    <span class="motor-icono" aria-hidden="true"></span>
                                    <span class="motor-texto" aria-hidden="true">Engine<br>Start</span>
                                </button>
                            <?php endif; ?>
                            </div>

                            <div class="modelo-tarjeta-cuerpo">
                                <!-- etiquetas: estado, tracción y cuántas vistas tiene -->
                                <ul class="modelo-etiquetas" aria-label="Datos rápidos">
                                    <li class="<?php echo $vendido ? "etiqueta-vendido" : "etiqueta-disponible"; ?>">
                                        <?php echo $vendido ? "Vendido" : "Disponible"; ?>
                                    </li>
                                    <?php if ($traccion !== ""): ?>
                                        <li>Tracción <?php echo htmlspecialchars(mb_strtolower($traccion)); ?></li>
                                    <?php endif; ?>
                                    <?php if ($fotosAuto["cantidad"] > 1): ?>
                                        <li><?php echo $fotosAuto["cantidad"]; ?> vistas</li>
                                    <?php endif; ?>
                                </ul>

                                <h3 class="modelo-tarjeta-nombre" id="nombre-<?php echo (int) $v["id"]; ?>">
                                    <span class="modelo-tarjeta-marca"><?php echo htmlspecialchars($v["marca"]); ?></span>
                                    <?php echo htmlspecialchars($v["modelo"]); ?>
                                </h3>
                                <p class="modelo-tarjeta-linea"><?php echo htmlspecialchars(primeraOracion($v["descripcion"])); ?></p>

                                <?php if (count($cifrasAuto) > 0): ?>
                                    <!-- número grande arriba y su nombre abajo -->
                                    <dl class="modelo-tarjeta-cifras">
                                        <?php foreach ($cifrasAuto as $cifra): ?>
                                            <div>
                                                <dt><?php echo htmlspecialchars($cifra["corto"]); ?></dt>
                                                <dd><?php echo htmlspecialchars($cifra["valor"]); ?></dd>
                                            </div>
                                        <?php endforeach; ?>
                                    </dl>
                                <?php endif; ?>

                                <div class="modelo-tarjeta-pie">
                                    <p class="modelo-tarjeta-precio">
                                        $<?php echo number_format((float) $v["precio"], 0, ".", ","); ?> <span>USD</span>
                                    </p>
                                    <div class="modelo-tarjeta-botones">
                                        <a class="boton" href="vehiculo.php?id=<?php echo (int) $v["id"]; ?>"
                                           aria-label="Explorar <?php echo htmlspecialchars($nombreAuto); ?>">Explorar</a>
                                        <?php if ($medios["modelo"] !== ""): ?>
                                            <!-- tiene modelo 3D: lleva directo a esa sección de su página -->
                                            <a class="boton-linea" href="vehiculo.php?id=<?php echo (int) $v["id"]; ?>#modelo-3d"
                                               aria-label="Ver el <?php echo htmlspecialchars($nombreAuto); ?> en 3D">Ver en 3D</a>
                                        <?php elseif ($fotosAuto["cantidad"] > 1): ?>
                                            <a class="boton-linea" href="vehiculo.php?id=<?php echo (int) $v["id"]; ?>#vistas"
                                               aria-label="Ver las vistas del <?php echo htmlspecialchars($nombreAuto); ?>">Ver vistas</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<!-- destacados de arriba y botón de encendido del motor -->
<script src="/apex/frontend/js/catalogo.js?v=3"></script>
<script src="/apex/frontend/js/motor.js?v=1"></script>

<?php require_once __DIR__ . "/includes/pie.php"; ?>
