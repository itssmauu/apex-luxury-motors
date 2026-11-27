<?php
// Inventario de vehículos (RF8)
// Diseño: encabezado grande, cifras que también sirven de filtro
// y una fila por vehículo con su foto, sus datos y las acciones.
// Esta pantalla consume la API propia (backend/api/vehiculos.php) con js/inventario.js:
// "Marcar como vendido / disponible" usa PUT y "Desactivar" usa DELETE, sin recargar la página.
// Sin JavaScript, "Desactivar" sigue funcionando con su formulario normal.

// trae requerirRol() y abre la sesión
require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/vehiculos.php";
// textos traducidos (t) y nombres de las categorías en el idioma elegido
require_once __DIR__ . "/../includes/idioma.php";
require_once __DIR__ . "/../datos/valores.php";

// solo administrador
requerirRol("administrador");

// todos los vehículos, incluso vendidos y desactivados
$vehiculos = listarInventario();

// cuántos hay de cada tipo (para las cifras de arriba)
$vistas = [
    "todos"        => ["nombre" => t("admin.en_inventario"),  "cantidad" => 0],
    "disponibles"  => ["nombre" => t("admin.disponibles"),    "cantidad" => 0],
    "vendidos"     => ["nombre" => t("admin.vendidos"),       "cantidad" => 0],
    "desactivados" => ["nombre" => t("admin.fuera_catalogo"), "cantidad" => 0],
];

// a qué vista pertenece un vehículo
function vistasDelVehiculo($v)
{
    $lista = ["todos"];
    if ((int) $v["activo"] === 0) {
        $lista[] = "desactivados";
    } elseif ($v["estado"] === "vendido") {
        $lista[] = "vendidos";
    } else {
        $lista[] = "disponibles";
    }
    return $lista;
}

foreach ($vehiculos as $v) {
    foreach (vistasDelVehiculo($v) as $vista) {
        $vistas[$vista]["cantidad"]++;
    }
}

// la vista elegida viene en la dirección: vehiculos.php?vista=vendidos
$vistaActual = (isset($_GET["vista"]) && is_string($_GET["vista"]) && isset($vistas[$_GET["vista"]]))
    ? $_GET["vista"]
    : "todos";

// solo los vehículos de esa vista
$vehiculosVista = [];
foreach ($vehiculos as $v) {
    if (in_array($vistaActual, vistasDelVehiculo($v), true)) {
        $vehiculosVista[] = $v;
    }
}

$titulo = t("menu.inventario");

require_once __DIR__ . "/../includes/encabezado.php";
require_once __DIR__ . "/../includes/menu.php";
?>

<main class="inventario">
    <!-- encabezado: título a la izquierda y "Agregar vehículo" a la derecha -->
    <div class="inventario-cabecera">
        <div>
            <p class="sobretitulo"><?php echo htmlspecialchars(t("admin.panel")); ?></p>
            <h1><?php echo htmlspecialchars(t("menu.inventario")); ?></h1>
        </div>
        <a class="boton inventario-agregar" href="/apex/frontend/admin/vehiculo_form.php">
            <span aria-hidden="true">+</span> <?php echo htmlspecialchars(t("admin.agregar")); ?>
        </a>
    </div>

    <!-- cifras: cada una es también un filtro -->
    <nav class="inventario-filtros" aria-label="<?php echo htmlspecialchars(t("admin.filtrar_aria")); ?>">
        <ul>
            <?php foreach ($vistas as $clave => $vista): ?>
                <li>
                    <a href="?vista=<?php echo $clave; ?>"
                       <?php if ($clave === $vistaActual) { echo 'aria-current="page"'; } ?>>
                        <span class="inventario-cifra"><?php echo $vista["cantidad"]; ?></span>
                        <span class="inventario-cifra-nombre"><?php echo htmlspecialchars($vista["nombre"]); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- avisos de las acciones hechas con la API (los escribe js/inventario.js) -->
    <div class="inventario-aviso" id="inventario-aviso" aria-live="polite"></div>

    <?php if (count($vehiculosVista) === 0): ?>
        <div class="inventario-vacio">
            <p><?php echo htmlspecialchars((count($vehiculos) === 0) ? t("admin.sin_vehiculos") : t("admin.vista_vacia")); ?></p>
            <?php if ($vistaActual !== "todos"): ?>
                <a href="?vista=todos"><?php echo htmlspecialchars(t("admin.ver_todo_inventario")); ?></a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <h2 class="solo-lector"><?php echo htmlspecialchars($vistas[$vistaActual]["nombre"]); ?></h2>

        <!-- data-api: dirección de la API; data-texto-*: textos traducidos que usa js/inventario.js -->
        <ul class="inventario-lista" data-api="/apex/backend/api/vehiculos.php"
            data-texto-disponible="<?php echo htmlspecialchars(t("estado_vehiculo.disponible")); ?>"
            data-texto-vendido="<?php echo htmlspecialchars(t("estado_vehiculo.vendido")); ?>"
            data-texto-fuera="<?php echo htmlspecialchars(t("admin.fuera_catalogo")); ?>"
            data-texto-marcar-vendido="<?php echo htmlspecialchars(t("admin.marcar_vendido")); ?>"
            data-texto-marcar-disponible="<?php echo htmlspecialchars(t("admin.marcar_disponible")); ?>"
            data-texto-marcar-vendido-aria="<?php echo htmlspecialchars(t("admin.marcar_vendido_aria", "%s")); ?>"
            data-texto-marcar-disponible-aria="<?php echo htmlspecialchars(t("admin.marcar_disponible_aria", "%s")); ?>"
            data-texto-procesando="<?php echo htmlspecialchars(t("admin.procesando")); ?>"
            data-texto-ok-desactivado="<?php echo htmlspecialchars(t("admin.ok_desactivado", "%s")); ?>"
            data-texto-ok-vendido="<?php echo htmlspecialchars(t("admin.ok_vendido", "%s")); ?>"
            data-texto-ok-disponible="<?php echo htmlspecialchars(t("admin.ok_disponible", "%s")); ?>"
            data-texto-err-pendientes="<?php echo htmlspecialchars(t("admin.err_pendientes")); ?>"
            data-texto-err-ya-desactivado="<?php echo htmlspecialchars(t("admin.err_ya_desactivado")); ?>"
            data-texto-err-no-encontrado="<?php echo htmlspecialchars(t("admin.err_no_encontrado")); ?>"
            data-texto-err-sesion="<?php echo htmlspecialchars(t("admin.err_sesion")); ?>"
            data-texto-err-permiso="<?php echo htmlspecialchars(t("admin.err_permiso")); ?>"
            data-texto-error="<?php echo htmlspecialchars(t("admin.api_error")); ?>">
            <?php foreach ($vehiculosVista as $numero => $v): ?>
                <?php
                $nombreVehiculo = $v["marca"] . " " . $v["modelo"];
                $activo = ((int) $v["activo"] === 1);
                ?>
                <li class="inventario-fila<?php if (!$activo) { echo " inventario-fila-inactiva"; } ?>"
                    data-id="<?php echo (int) $v["id"]; ?>"
                    data-nombre="<?php echo htmlspecialchars($nombreVehiculo); ?>"
                    data-estado="<?php echo htmlspecialchars($v["estado"]); ?>"
                    data-activo="<?php echo $activo ? "1" : "0"; ?>">
                    <div class="inventario-foto">
                        <?php if (!empty($v["foto"])): ?>
                            <!-- alt vacío: el nombre está justo al lado -->
                            <img src="/apex/<?php echo htmlspecialchars($v["foto"]); ?>" alt=""
                                 width="320" height="200" <?php if ($numero > 3) { echo 'loading="lazy"'; } ?>>
                        <?php else: ?>
                            <span><?php echo htmlspecialchars(t("admin.sin_foto")); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="inventario-datos">
                        <p class="inventario-categoria"><?php echo htmlspecialchars(nombreCategoria($v["categoria"])); ?></p>
                        <h3 class="inventario-nombre">
                            <span class="inventario-marca"><?php echo htmlspecialchars($v["marca"]); ?></span>
                            <?php echo htmlspecialchars($v["modelo"]); ?>
                        </h3>
                        <!-- estado: el texto siempre lo dice, el color solo ayuda -->
                        <p class="inventario-sellos">
                            <?php if ($v["estado"] === "vendido"): ?>
                                <span class="estado-sello estado-sello-vendido"><?php echo htmlspecialchars(t("estado_vehiculo.vendido")); ?></span>
                            <?php else: ?>
                                <span class="estado-sello estado-sello-disponible"><?php echo htmlspecialchars(t("estado_vehiculo.disponible")); ?></span>
                            <?php endif; ?>
                            <?php if (!$activo): ?>
                                <span class="estado-sello estado-sello-oculto"><?php echo htmlspecialchars(t("admin.fuera_catalogo")); ?></span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <p class="inventario-precio">
                        $<?php echo number_format((float) $v["precio"], 0, ".", ","); ?> <span>USD</span>
                    </p>

                    <div class="inventario-acciones">
                        <?php if ($activo): ?>
                            <!-- la página pública del vehículo (solo existe si está en el catálogo) -->
                            <a class="inventario-ver" href="/apex/frontend/vehiculo.php?id=<?php echo (int) $v["id"]; ?>"
                               aria-label="<?php echo htmlspecialchars(t("admin.ver_sitio_aria", $nombreVehiculo)); ?>">
                                <?php echo htmlspecialchars(t("admin.ver_sitio")); ?> <span aria-hidden="true">→</span>
                            </a>
                        <?php endif; ?>

                        <?php if ($activo): ?>
                            <!-- cambia el estado con la API (PUT); aparece solo con JavaScript -->
                            <button type="button" class="boton-linea inventario-estado" hidden>
                                <?php echo htmlspecialchars(($v["estado"] === "vendido") ? t("admin.marcar_disponible") : t("admin.marcar_vendido")); ?>
                            </button>
                        <?php endif; ?>

                        <a class="boton-linea" href="/apex/frontend/admin/vehiculo_form.php?id=<?php echo (int) $v["id"]; ?>"
                           aria-label="<?php echo htmlspecialchars(t("admin.editar_aria", $nombreVehiculo)); ?>"><?php echo htmlspecialchars(t("admin.editar")); ?></a>

                        <!-- solo se desactiva si está activo; el backend lo vuelve a validar -->
                        <?php if ($activo): ?>
                            <form class="inventario-desactivar" method="post" action="/apex/backend/procesos/vehiculo_desactivar.php">
                                <input type="hidden" name="id" value="<?php echo (int) $v["id"]; ?>">
                                <button type="submit" class="boton-quitar"
                                        aria-label="<?php echo htmlspecialchars(t("admin.desactivar_aria", $nombreVehiculo)); ?>">
                                    <?php echo htmlspecialchars(t("admin.desactivar")); ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>

<!-- acciones del inventario con la API (PUT y DELETE) -->
<script src="/apex/frontend/js/inventario.js?v=1"></script>

<?php require_once __DIR__ . "/../includes/pie.php"; ?>
