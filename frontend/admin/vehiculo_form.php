<?php
// Crear o editar vehículo (RF8)
// Diseño: a la izquierda el formulario por secciones; a la derecha una vista previa
// de cómo se verá la tarjeta en el catálogo. Al editar, abajo van las fotos.

require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/vehiculos.php";

requerirRol("administrador");

// id = 0 significa "crear"
$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

// valores vacíos por defecto (modo crear)
$vehiculo = [
    "marca"       => "",
    "modelo"      => "",
    "precio"      => "",
    "categoria"   => "",
    "descripcion" => "",
    "estado"      => "disponible",
    "activo"      => 1
];

// modo editar: cargar los datos actuales
if ($id > 0) {
    $encontrado = obtenerVehiculoAdmin($id);

    if (!$encontrado) {
        $_SESSION["mensaje"] = ["tipo" => "error", "texto" => "El vehículo no existe."];
        header("Location: /apex/frontend/admin/vehiculos.php");
        exit;
    }

    $vehiculo = $encontrado;
}

$categorias = listarCategorias();

// fotos actuales (solo existen al editar)
$fotos = ($id > 0) ? listarFotosVehiculo($id) : [];

// nombre de cada vista según el archivo (igual que en la página del vehículo)
$nombresVista = [
    "lateral"  => "Perfil",
    "trasera"  => "Trasera",
    "puertas"  => "Puertas abiertas",
    "interior" => "Interior",
    "cabina"   => "Cabina",
];
function nombreDeVista($ruta, $numero, $nombresVista)
{
    $partes = explode("-", pathinfo($ruta, PATHINFO_FILENAME));
    $clave = strtolower(end($partes));
    if (isset($nombresVista[$clave])) {
        return $nombresVista[$clave];
    }
    return ($numero === 0) ? "Principal" : "Foto " . ($numero + 1);
}

$editando = ($id > 0);
$tituloPagina = $editando ? "Editar vehículo" : "Agregar vehículo";
$titulo = $tituloPagina;

require_once __DIR__ . "/../includes/encabezado.php";
require_once __DIR__ . "/../includes/menu.php";
?>

<main class="inventario ficha-admin">
    <!-- encabezado -->
    <div class="inventario-cabecera">
        <div>
            <a class="modelo-volver ficha-admin-volver" href="/apex/frontend/admin/vehiculos.php">
                <span aria-hidden="true">←</span> Inventario
            </a>
            <p class="sobretitulo">Panel de administración</p>
            <h1><?php echo $tituloPagina; ?></h1>
            <?php if ($editando): ?>
                <p class="ficha-admin-nombre"><?php echo htmlspecialchars($vehiculo["marca"] . " " . $vehiculo["modelo"]); ?></p>
            <?php endif; ?>
        </div>
        <?php if ($editando && (int) $vehiculo["activo"] === 1): ?>
            <a class="inventario-ver" href="/apex/frontend/vehiculo.php?id=<?php echo $id; ?>">
                Ver en el sitio <span aria-hidden="true">→</span>
            </a>
        <?php endif; ?>
    </div>

    <div class="ficha-admin-cuerpo">
        <!-- formulario -->
        <form class="ficha-admin-form" method="post" action="/apex/backend/procesos/vehiculo_guardar.php">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <fieldset class="ficha-admin-seccion">
                <legend>Datos del vehículo</legend>

                <div class="campos-dobles">
                    <div class="campo">
                        <label for="marca">Marca</label>
                        <input type="text" id="marca" name="marca" required
                               value="<?php echo htmlspecialchars($vehiculo["marca"]); ?>">
                    </div>
                    <div class="campo">
                        <label for="modelo">Modelo</label>
                        <input type="text" id="modelo" name="modelo" required
                               value="<?php echo htmlspecialchars($vehiculo["modelo"]); ?>">
                    </div>
                </div>

                <div class="campos-dobles">
                    <div class="campo">
                        <label for="categoria">Categoría</label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Selecciona una categoría</option>
                            <?php foreach ($categorias as $c): ?>
                                <?php $nombreCategoria = is_array($c) ? reset($c) : $c; ?>
                                <option value="<?php echo htmlspecialchars($nombreCategoria); ?>"
                                    <?php echo ($nombreCategoria === $vehiculo["categoria"]) ? "selected" : ""; ?>>
                                    <?php echo htmlspecialchars($nombreCategoria); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="precio">Precio (USD)</label>
                        <input type="number" id="precio" name="precio" required min="1" step="0.01"
                               value="<?php echo htmlspecialchars((string) $vehiculo["precio"]); ?>">
                    </div>
                </div>

                <div class="campo">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="5" aria-describedby="ayuda-descripcion"><?php
                        echo htmlspecialchars($vehiculo["descripcion"]);
                    ?></textarea>
                    <p class="campo-ayuda" id="ayuda-descripcion">Se muestra en la página del vehículo, en la sección «Diseño».</p>
                </div>
            </fieldset>

            <!-- estado: dos opciones grandes en vez de una lista -->
            <fieldset class="ficha-admin-seccion">
                <legend>Estado</legend>
                <div class="opciones-estado">
                    <label class="opcion-estado">
                        <input type="radio" name="estado" value="disponible" required
                            <?php echo ($vehiculo["estado"] === "disponible") ? "checked" : ""; ?>>
                        <span class="opcion-estado-nombre">Disponible</span>
                        <span class="opcion-estado-ayuda">Los clientes pueden solicitar la compra.</span>
                    </label>
                    <label class="opcion-estado">
                        <input type="radio" name="estado" value="vendido"
                            <?php echo ($vehiculo["estado"] === "vendido") ? "checked" : ""; ?>>
                        <span class="opcion-estado-nombre">Vendido</span>
                        <span class="opcion-estado-ayuda">Se sigue viendo, pero ya no se puede solicitar.</span>
                    </label>
                </div>
            </fieldset>

            <div class="ficha-admin-botones">
                <button type="submit" class="acceso-boton"><?php echo $editando ? "Guardar cambios" : "Agregar vehículo"; ?></button>
                <a class="boton-linea" href="/apex/frontend/admin/vehiculos.php">Cancelar</a>
            </div>
        </form>

        <!-- vista previa: así se verá en el catálogo (el JavaScript la actualiza al escribir) -->
        <aside class="ficha-admin-previa" aria-labelledby="titulo-previa">
            <p class="sobretitulo" id="titulo-previa">Vista previa en el catálogo</p>
            <article class="tarjeta" aria-hidden="true">
                <div class="tarjeta-foto">
                    <?php if (count($fotos) > 0): ?>
                        <img src="/apex/<?php echo htmlspecialchars($fotos[0]["ruta"]); ?>" alt="">
                    <?php else: ?>
                        <p class="sin-foto">Sin foto todavía</p>
                    <?php endif; ?>
                    <span class="sello" id="previa-sello"><?php echo ($vehiculo["estado"] === "vendido") ? "Vendido" : "Disponible"; ?></span>
                </div>
                <div class="tarjeta-cuerpo">
                    <p class="tarjeta-categoria" id="previa-categoria"><?php echo htmlspecialchars($vehiculo["categoria"] !== "" ? $vehiculo["categoria"] : "Categoría"); ?></p>
                    <h3 id="previa-nombre"><?php echo htmlspecialchars(trim($vehiculo["marca"] . " " . $vehiculo["modelo"]) !== "" ? $vehiculo["marca"] . " " . $vehiculo["modelo"] : "Marca y modelo"); ?></h3>
                    <p class="precio"><span id="previa-precio"><?php echo ($vehiculo["precio"] !== "") ? "$" . number_format((float) $vehiculo["precio"], 0, ".", ",") : "$0"; ?></span> <span>USD</span></p>
                    <span class="tarjeta-enlace">Ver vehículo <span>→</span></span>
                </div>
            </article>
            <?php if (!$editando): ?>
                <p class="campo-ayuda">Las fotos se agregan después de guardar el vehículo.</p>
            <?php endif; ?>
        </aside>
    </div>

    <?php if ($editando): ?>
        <!-- fotos: las actuales en cuadrícula y la zona para subir otra -->
        <section class="ficha-admin-fotos" aria-labelledby="titulo-fotos">
            <div class="ficha-admin-fotos-cabecera">
                <h2 id="titulo-fotos">Fotos</h2>
                <p><?php echo count($fotos); ?> <?php echo (count($fotos) === 1) ? "foto" : "fotos"; ?> · la primera es la del catálogo</p>
            </div>

            <ul class="fotos-admin">
                <?php foreach ($fotos as $numero => $f): ?>
                    <li>
                        <img src="/apex/<?php echo htmlspecialchars($f["ruta"]); ?>"
                             alt="<?php echo htmlspecialchars(nombreDeVista($f["ruta"], $numero, $nombresVista)); ?> del <?php echo htmlspecialchars($vehiculo["marca"] . " " . $vehiculo["modelo"]); ?>"
                             width="320" height="200" loading="lazy">
                        <span><?php echo htmlspecialchars(nombreDeVista($f["ruta"], $numero, $nombresVista)); ?></span>
                    </li>
                <?php endforeach; ?>

                <!-- última casilla: subir una foto nueva -->
                <li class="fotos-admin-subir">
                    <form method="post" action="/apex/backend/procesos/vehiculo_foto_subir.php" enctype="multipart/form-data">
                        <input type="hidden" name="vehiculo_id" value="<?php echo $id; ?>">
                        <label for="foto" class="zona-foto">
                            <span class="zona-foto-mas" aria-hidden="true">+</span>
                            <span class="zona-foto-texto" id="nombre-foto">Elegir foto</span>
                            <span class="zona-foto-ayuda">JPG, PNG o WEBP, máximo 5 MB</span>
                        </label>
                        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" required>
                        <button type="submit" class="boton-linea">Subir foto</button>
                    </form>
                </li>
            </ul>
        </section>
    <?php endif; ?>
</main>

<!-- vista previa en vivo y nombre del archivo elegido -->
<script src="/apex/frontend/js/vehiculo_form.js?v=1"></script>

<?php require_once __DIR__ . "/../includes/pie.php"; ?>
