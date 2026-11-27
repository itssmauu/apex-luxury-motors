<?php
// Crear o editar vehículo (RF8)
// Diseño: a la izquierda el formulario por secciones; a la derecha una vista previa
// de cómo se verá la tarjeta en el catálogo. Al editar, abajo van las fotos.

require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/vehiculos.php";
// textos traducidos (t) y nombres de las categorías en el idioma elegido
require_once __DIR__ . "/../includes/idioma.php";
require_once __DIR__ . "/../datos/valores.php";

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
        $_SESSION["mensaje"] = ["tipo" => "error", "texto" => t("admin.vehiculo_no_existe")];
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
    "lateral"  => t("vista.lateral"),
    "trasera"  => t("vista.trasera"),
    "puertas"  => t("vista.puertas"),
    "interior" => t("vista.interior"),
    "cabina"   => t("vista.cabina"),
];
function nombreDeVista($ruta, $numero, $nombresVista)
{
    $partes = explode("-", pathinfo($ruta, PATHINFO_FILENAME));
    $clave = strtolower(end($partes));
    if (isset($nombresVista[$clave])) {
        return $nombresVista[$clave];
    }
    return ($numero === 0) ? t("admin.principal") : t("admin.foto_n", $numero + 1);
}

$editando = ($id > 0);
$tituloPagina = $editando ? t("admin.editar_vehiculo") : t("admin.agregar");
$titulo = $tituloPagina;

require_once __DIR__ . "/../includes/encabezado.php";
require_once __DIR__ . "/../includes/menu.php";
?>

<main class="inventario ficha-admin">
    <!-- encabezado -->
    <div class="inventario-cabecera">
        <div>
            <a class="modelo-volver ficha-admin-volver" href="/apex/frontend/admin/vehiculos.php">
                <span aria-hidden="true">←</span> <?php echo htmlspecialchars(t("menu.inventario")); ?>
            </a>
            <p class="sobretitulo"><?php echo htmlspecialchars(t("admin.panel")); ?></p>
            <h1><?php echo htmlspecialchars($tituloPagina); ?></h1>
            <?php if ($editando): ?>
                <p class="ficha-admin-nombre"><?php echo htmlspecialchars($vehiculo["marca"] . " " . $vehiculo["modelo"]); ?></p>
            <?php endif; ?>
        </div>
        <?php if ($editando && (int) $vehiculo["activo"] === 1): ?>
            <a class="inventario-ver" href="/apex/frontend/vehiculo.php?id=<?php echo $id; ?>">
                <?php echo htmlspecialchars(t("admin.ver_sitio")); ?> <span aria-hidden="true">→</span>
            </a>
        <?php endif; ?>
    </div>

    <div class="ficha-admin-cuerpo">
        <!-- formulario -->
        <!-- data-texto-*: los textos traducidos que usa vehiculo_form.js en la vista previa -->
        <form class="ficha-admin-form" method="post" action="/apex/backend/procesos/vehiculo_guardar.php"
              data-texto-nombre="<?php echo htmlspecialchars(t("admin.marca_modelo")); ?>"
              data-texto-categoria="<?php echo htmlspecialchars(t("dato.categoria")); ?>"
              data-texto-disponible="<?php echo htmlspecialchars(t("estado_vehiculo.disponible")); ?>"
              data-texto-vendido="<?php echo htmlspecialchars(t("estado_vehiculo.vendido")); ?>">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <fieldset class="ficha-admin-seccion">
                <legend><?php echo htmlspecialchars(t("admin.datos")); ?></legend>

                <div class="campos-dobles">
                    <div class="campo">
                        <label for="marca"><?php echo htmlspecialchars(t("dato.marca")); ?></label>
                        <input type="text" id="marca" name="marca" required
                               value="<?php echo htmlspecialchars($vehiculo["marca"]); ?>">
                    </div>
                    <div class="campo">
                        <label for="modelo"><?php echo htmlspecialchars(t("dato.modelo")); ?></label>
                        <input type="text" id="modelo" name="modelo" required
                               value="<?php echo htmlspecialchars($vehiculo["modelo"]); ?>">
                    </div>
                </div>

                <div class="campos-dobles">
                    <div class="campo">
                        <label for="categoria"><?php echo htmlspecialchars(t("dato.categoria")); ?></label>
                        <select id="categoria" name="categoria" required>
                            <option value=""><?php echo htmlspecialchars(t("admin.elige_categoria")); ?></option>
                            <?php foreach ($categorias as $c): ?>
                                <?php $nombreCategoria = is_array($c) ? reset($c) : $c; ?>
                                <option value="<?php echo htmlspecialchars($nombreCategoria); ?>"
                                    <?php echo ($nombreCategoria === $vehiculo["categoria"]) ? "selected" : ""; ?>>
                                    <?php echo htmlspecialchars(nombreCategoria($nombreCategoria)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="precio"><?php echo htmlspecialchars(t("admin.precio")); ?></label>
                        <input type="number" id="precio" name="precio" required min="1" step="0.01"
                               value="<?php echo htmlspecialchars((string) $vehiculo["precio"]); ?>">
                    </div>
                </div>

                <div class="campo">
                    <label for="descripcion"><?php echo htmlspecialchars(t("admin.descripcion")); ?></label>
                    <textarea id="descripcion" name="descripcion" rows="5" aria-describedby="ayuda-descripcion"><?php
                        echo htmlspecialchars($vehiculo["descripcion"]);
                    ?></textarea>
                    <p class="campo-ayuda" id="ayuda-descripcion"><?php echo htmlspecialchars(t("admin.descripcion_ayuda", t("vehiculo.diseno"))); ?></p>
                </div>
            </fieldset>

            <!-- estado: dos opciones grandes en vez de una lista -->
            <fieldset class="ficha-admin-seccion">
                <legend><?php echo htmlspecialchars(t("dato.estado")); ?></legend>
                <div class="opciones-estado">
                    <label class="opcion-estado">
                        <input type="radio" name="estado" value="disponible" required
                            <?php echo ($vehiculo["estado"] === "disponible") ? "checked" : ""; ?>>
                        <span class="opcion-estado-nombre"><?php echo htmlspecialchars(t("estado_vehiculo.disponible")); ?></span>
                        <span class="opcion-estado-ayuda"><?php echo htmlspecialchars(t("admin.disponible_ayuda")); ?></span>
                    </label>
                    <label class="opcion-estado">
                        <input type="radio" name="estado" value="vendido"
                            <?php echo ($vehiculo["estado"] === "vendido") ? "checked" : ""; ?>>
                        <span class="opcion-estado-nombre"><?php echo htmlspecialchars(t("estado_vehiculo.vendido")); ?></span>
                        <span class="opcion-estado-ayuda"><?php echo htmlspecialchars(t("admin.vendido_ayuda")); ?></span>
                    </label>
                </div>
            </fieldset>

            <div class="ficha-admin-botones">
                <button type="submit" class="acceso-boton"><?php echo htmlspecialchars($editando ? t("perfil.guardar") : t("admin.agregar")); ?></button>
                <a class="boton-linea" href="/apex/frontend/admin/vehiculos.php"><?php echo htmlspecialchars(t("solicitudes.cancelar")); ?></a>
            </div>
        </form>

        <!-- vista previa: así se verá en el catálogo (el JavaScript la actualiza al escribir) -->
        <aside class="ficha-admin-previa" aria-labelledby="titulo-previa">
            <p class="sobretitulo" id="titulo-previa"><?php echo htmlspecialchars(t("admin.vista_previa")); ?></p>
            <article class="tarjeta" aria-hidden="true">
                <div class="tarjeta-foto">
                    <?php if (count($fotos) > 0): ?>
                        <img src="/apex/<?php echo htmlspecialchars($fotos[0]["ruta"]); ?>" alt="">
                    <?php else: ?>
                        <p class="sin-foto"><?php echo htmlspecialchars(t("admin.sin_foto_aun")); ?></p>
                    <?php endif; ?>
                    <span class="sello" id="previa-sello"><?php echo htmlspecialchars(($vehiculo["estado"] === "vendido") ? t("estado_vehiculo.vendido") : t("estado_vehiculo.disponible")); ?></span>
                </div>
                <div class="tarjeta-cuerpo">
                    <p class="tarjeta-categoria" id="previa-categoria"><?php echo htmlspecialchars($vehiculo["categoria"] !== "" ? nombreCategoria($vehiculo["categoria"]) : t("dato.categoria")); ?></p>
                    <h3 id="previa-nombre"><?php echo htmlspecialchars(trim($vehiculo["marca"] . " " . $vehiculo["modelo"]) !== "" ? $vehiculo["marca"] . " " . $vehiculo["modelo"] : t("admin.marca_modelo")); ?></h3>
                    <p class="precio"><span id="previa-precio"><?php echo ($vehiculo["precio"] !== "") ? "$" . number_format((float) $vehiculo["precio"], 0, ".", ",") : "$0"; ?></span> <span>USD</span></p>
                    <span class="tarjeta-enlace"><?php echo htmlspecialchars(t("comun.ver_vehiculo")); ?> <span>→</span></span>
                </div>
            </article>
            <?php if (!$editando): ?>
                <p class="campo-ayuda"><?php echo htmlspecialchars(t("admin.fotos_despues")); ?></p>
            <?php endif; ?>
        </aside>
    </div>

    <?php if ($editando): ?>
        <!-- fotos: las actuales en cuadrícula y la zona para subir otra -->
        <section class="ficha-admin-fotos" aria-labelledby="titulo-fotos">
            <div class="ficha-admin-fotos-cabecera">
                <h2 id="titulo-fotos"><?php echo htmlspecialchars(t("admin.fotos")); ?></h2>
                <p><?php echo htmlspecialchars(tPlural(count($fotos), "admin.n_fotos_uno", "admin.n_fotos_varios")); ?></p>
            </div>

            <ul class="fotos-admin">
                <?php foreach ($fotos as $numero => $f): ?>
                    <li>
                        <img src="/apex/<?php echo htmlspecialchars($f["ruta"]); ?>"
                             alt="<?php echo htmlspecialchars(t("admin.foto_alt", nombreDeVista($f["ruta"], $numero, $nombresVista), $vehiculo["marca"] . " " . $vehiculo["modelo"])); ?>"
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
                            <span class="zona-foto-texto" id="nombre-foto" data-texto-elegir="<?php echo htmlspecialchars(t("admin.elegir_foto")); ?>"><?php echo htmlspecialchars(t("admin.elegir_foto")); ?></span>
                            <span class="zona-foto-ayuda"><?php echo htmlspecialchars(t("admin.foto_ayuda")); ?></span>
                        </label>
                        <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" required>
                        <button type="submit" class="boton-linea"><?php echo htmlspecialchars(t("admin.subir_foto")); ?></button>
                    </form>
                </li>
            </ul>
        </section>
    <?php endif; ?>
</main>

<!-- vista previa en vivo y nombre del archivo elegido -->
<script src="/apex/frontend/js/vehiculo_form.js?v=2"></script>

<?php require_once __DIR__ . "/../includes/pie.php"; ?>
