<?php
// Catálogo y Buscar vehículos (RF4)

// funciones del backend que usa esta página:
// listarVehiculos() para las tarjetas, listarMarcas() y listarCategorias() para los filtros
require_once __DIR__ . "/../backend/funciones/vehiculos.php";
// textos traducidos (t) y nombres de categorías en el idioma elegido
require_once __DIR__ . "/includes/idioma.php";
require_once __DIR__ . "/datos/valores.php";

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

$titulo = t("menu.catalogo");

// plantilla común: encabezado con el logo, menú según el rol y mensajes
require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main>
    <!-- cabecera del catálogo, con el mismo estilo de la portada -->
    <div class="catalogo-cabecera">
        <p class="sobretitulo"><?php echo htmlspecialchars(t("menu.catalogo")); ?></p>
        <h1><?php echo htmlspecialchars(t("catalogo.h1")); ?></h1>
        <p class="catalogo-conteo">
            <?php echo htmlspecialchars(tPlural(count($vehiculos), "plural.modelo.uno", "plural.modelo.varios")); ?>
            <?php if ($hayFiltros) { echo htmlspecialchars(t("catalogo.con_filtros")); } ?>
        </p>
    </div>

    <!-- Buscar vehículos: el formulario manda los filtros por la dirección -->
    <section class="filtros">
        <h2><?php echo htmlspecialchars(t("catalogo.buscar_titulo")); ?></h2>
        <form method="get" action="catalogo.php">
            <label>
                <?php echo htmlspecialchars(t("dato.marca")); ?>
                <select name="marca">
                    <option value=""><?php echo htmlspecialchars(t("catalogo.todas")); ?></option>
                    <?php foreach ($marcas as $fila): ?>
                        <option value="<?php echo htmlspecialchars($fila["marca"]); ?>"
                            <?php if ($fila["marca"] === $marca) { echo "selected"; } ?>>
                            <?php echo htmlspecialchars($fila["marca"]); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                <?php echo htmlspecialchars(t("dato.categoria")); ?>
                <select name="categoria">
                    <option value=""><?php echo htmlspecialchars(t("catalogo.todas")); ?></option>
                    <!-- el value es el de la base (para filtrar); solo el texto se traduce -->
                    <?php foreach ($categorias as $fila): ?>
                        <option value="<?php echo htmlspecialchars($fila["categoria"]); ?>"
                            <?php if ($fila["categoria"] === $categoria) { echo "selected"; } ?>>
                            <?php echo htmlspecialchars(nombreCategoria($fila["categoria"])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                <?php echo htmlspecialchars(t("catalogo.precio_max")); ?>
                <!-- autocomplete off: que el navegador no sugiera precios viejos -->
                <input type="number" name="precio_max" min="1" step="1" autocomplete="off"
                       value="<?php echo htmlspecialchars($precioMax); ?>">
            </label>

            <button type="submit"><?php echo htmlspecialchars(t("catalogo.buscar")); ?></button>
            <a href="catalogo.php"><?php echo htmlspecialchars(t("catalogo.quitar_filtros")); ?></a>
        </form>
    </section>

    <!-- Catálogo: una tarjeta por vehículo -->
    <section>
        <?php if (count($vehiculos) === 0): ?>
            <?php if ($hayFiltros): ?>
                <p><?php echo htmlspecialchars(t("catalogo.sin_resultados")); ?></p>
                <p><a href="catalogo.php"><?php echo htmlspecialchars(t("catalogo.quitar_filtros")); ?></a> <?php echo htmlspecialchars(t("catalogo.para_ver_todo")); ?></p>
            <?php else: ?>
                <p><?php echo htmlspecialchars(t("catalogo.vacio")); ?></p>
            <?php endif; ?>
        <?php else: ?>
            <div class="tarjetas">
                <?php foreach ($vehiculos as $n => $v): ?>
                    <?php $nombreAuto = $v["marca"] . " " . $v["modelo"]; ?>
                    <!-- --i escalona la aparición: las 4 de cada fila salen una tras otra -->
                    <article class="tarjeta revelar" style="--i: <?php echo $n % 4; ?>">
                        <div class="tarjeta-foto">
                            <?php if (!empty($v["foto"])): ?>
                                <!-- la base guarda "img/archivo.jpg", desde frontend/ solo falta subir un nivel;
                                     lazy = la foto se baja cuando se acerca a la pantalla -->
                                <img src="../<?php echo htmlspecialchars($v["foto"]); ?>"
                                     alt="<?php echo htmlspecialchars($nombreAuto); ?>"
                                     width="480" height="300" loading="lazy">
                            <?php else: ?>
                                <p class="sin-foto"><?php echo htmlspecialchars(t("comun.foto_no_disponible")); ?></p>
                            <?php endif; ?>

                            <!-- el estado va escrito, el color solo ayuda -->
                            <?php if ($v["estado"] === "vendido"): ?>
                                <span class="sello sello-vendido"><?php echo htmlspecialchars(t("estado_vehiculo.vendido")); ?></span>
                            <?php else: ?>
                                <span class="sello"><?php echo htmlspecialchars(t("estado_vehiculo.disponible")); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="tarjeta-cuerpo">
                            <p class="tarjeta-categoria"><?php echo htmlspecialchars(nombreCategoria($v["categoria"])); ?></p>
                            <h3><?php echo htmlspecialchars($nombreAuto); ?></h3>
                            <p class="precio">$<?php echo number_format((float) $v["precio"], 0, ".", ","); ?> <span>USD</span></p>
                            <!-- el aria-label dice qué auto es; con el CSS toda la tarjeta se vuelve clicable -->
                            <a class="tarjeta-enlace" href="vehiculo.php?id=<?php echo (int) $v["id"]; ?>"
                               aria-label="<?php echo htmlspecialchars(t("comun.ver_vehiculo_aria", $nombreAuto)); ?>">
                                <?php echo htmlspecialchars(t("comun.ver_vehiculo")); ?> <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . "/includes/pie.php"; ?>