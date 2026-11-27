<?php
// Catálogo y Buscar vehículos (RF4)

// funciones del backend que usa esta página:
// listarVehiculos() para las tarjetas, listarMarcas() y listarCategorias() para los filtros
require_once __DIR__ . "/../backend/funciones/vehiculos.php";

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

$titulo = "Catálogo";

// plantilla común: encabezado con el logo, menú según el rol y mensajes
require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main>
    <!-- cabecera del catálogo, con el mismo estilo de la portada -->
    <div class="catalogo-cabecera">
        <p class="sobretitulo">Catálogo</p>
        <h1>Catálogo de vehículos</h1>
        <p class="catalogo-conteo">
            <?php echo count($vehiculos); ?>
            <?php echo (count($vehiculos) === 1) ? "modelo" : "modelos"; ?>
            <?php if ($hayFiltros) { echo "con estos filtros"; } ?>
        </p>
    </div>

    <!-- Buscar vehículos: el formulario manda los filtros por la dirección -->
    <section class="filtros">
        <h2>Buscar vehículos</h2>
        <form method="get" action="catalogo.php">
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
                Categoría
                <select name="categoria">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $fila): ?>
                        <option value="<?php echo htmlspecialchars($fila["categoria"]); ?>"
                            <?php if ($fila["categoria"] === $categoria) { echo "selected"; } ?>>
                            <?php echo htmlspecialchars($fila["categoria"]); ?>
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
            <a href="catalogo.php">Quitar filtros</a>
        </form>
    </section>

    <!-- Catálogo: una tarjeta por vehículo -->
    <section>
        <?php if (count($vehiculos) === 0): ?>
            <?php if ($hayFiltros): ?>
                <p>No encontramos vehículos con esos filtros.</p>
                <p><a href="catalogo.php">Quitar filtros</a> para ver todo el catálogo.</p>
            <?php else: ?>
                <p>Por ahora no hay vehículos en el catálogo.</p>
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
                                <p class="sin-foto">Foto no disponible</p>
                            <?php endif; ?>

                            <!-- el estado va escrito, el color solo ayuda -->
                            <?php if ($v["estado"] === "vendido"): ?>
                                <span class="sello sello-vendido">Vendido</span>
                            <?php else: ?>
                                <span class="sello">Disponible</span>
                            <?php endif; ?>
                        </div>

                        <div class="tarjeta-cuerpo">
                            <p class="tarjeta-categoria"><?php echo htmlspecialchars($v["categoria"]); ?></p>
                            <h3><?php echo htmlspecialchars($nombreAuto); ?></h3>
                            <p class="precio">$<?php echo number_format((float) $v["precio"], 0, ".", ","); ?> <span>USD</span></p>
                            <!-- el aria-label dice qué auto es; con el CSS toda la tarjeta se vuelve clicable -->
                            <a class="tarjeta-enlace" href="vehiculo.php?id=<?php echo (int) $v["id"]; ?>"
                               aria-label="Ver vehículo <?php echo htmlspecialchars($nombreAuto); ?>">
                                Ver vehículo <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . "/includes/pie.php"; ?>