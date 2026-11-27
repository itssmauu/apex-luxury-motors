<?php
// Inventario de vehículos (RF8)
// Diseño: encabezado grande, cifras que también sirven de filtro
// y una fila por vehículo con su foto, sus datos y las acciones.

// trae requerirRol() y abre la sesión
require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/vehiculos.php";

// solo administrador
requerirRol("administrador");

// todos los vehículos, incluso vendidos y desactivados
$vehiculos = listarInventario();

// cuántos hay de cada tipo (para las cifras de arriba)
$vistas = [
    "todos"        => ["nombre" => "En inventario",       "cantidad" => 0],
    "disponibles"  => ["nombre" => "Disponibles",         "cantidad" => 0],
    "vendidos"     => ["nombre" => "Vendidos",            "cantidad" => 0],
    "desactivados" => ["nombre" => "Fuera del catálogo",  "cantidad" => 0],
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

$titulo = "Inventario";

require_once __DIR__ . "/../includes/encabezado.php";
require_once __DIR__ . "/../includes/menu.php";
?>

<main class="inventario">
    <!-- encabezado: título a la izquierda y "Agregar vehículo" a la derecha -->
    <div class="inventario-cabecera">
        <div>
            <p class="sobretitulo">Panel de administración</p>
            <h1>Inventario</h1>
        </div>
        <a class="boton inventario-agregar" href="/apex/frontend/admin/vehiculo_form.php">
            <span aria-hidden="true">+</span> Agregar vehículo
        </a>
    </div>

    <!-- cifras: cada una es también un filtro -->
    <nav class="inventario-filtros" aria-label="Filtrar inventario">
        <ul>
            <?php foreach ($vistas as $clave => $vista): ?>
                <li>
                    <a href="?vista=<?php echo $clave; ?>"
                       <?php if ($clave === $vistaActual) { echo 'aria-current="page"'; } ?>>
                        <span class="inventario-cifra"><?php echo $vista["cantidad"]; ?></span>
                        <span class="inventario-cifra-nombre"><?php echo $vista["nombre"]; ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <?php if (count($vehiculosVista) === 0): ?>
        <div class="inventario-vacio">
            <p><?php echo (count($vehiculos) === 0) ? "No hay vehículos registrados." : "No hay vehículos en esta vista."; ?></p>
            <?php if ($vistaActual !== "todos"): ?>
                <a href="?vista=todos">Ver todo el inventario</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <h2 class="solo-lector"><?php echo htmlspecialchars($vistas[$vistaActual]["nombre"]); ?></h2>

        <ul class="inventario-lista">
            <?php foreach ($vehiculosVista as $numero => $v): ?>
                <?php
                $nombreVehiculo = $v["marca"] . " " . $v["modelo"];
                $activo = ((int) $v["activo"] === 1);
                ?>
                <li class="inventario-fila<?php if (!$activo) { echo " inventario-fila-inactiva"; } ?>">
                    <div class="inventario-foto">
                        <?php if (!empty($v["foto"])): ?>
                            <!-- alt vacío: el nombre está justo al lado -->
                            <img src="/apex/<?php echo htmlspecialchars($v["foto"]); ?>" alt=""
                                 width="320" height="200" <?php if ($numero > 3) { echo 'loading="lazy"'; } ?>>
                        <?php else: ?>
                            <span>Sin foto</span>
                        <?php endif; ?>
                    </div>

                    <div class="inventario-datos">
                        <p class="inventario-categoria"><?php echo htmlspecialchars($v["categoria"]); ?></p>
                        <h3 class="inventario-nombre">
                            <span class="inventario-marca"><?php echo htmlspecialchars($v["marca"]); ?></span>
                            <?php echo htmlspecialchars($v["modelo"]); ?>
                        </h3>
                        <!-- estado: el texto siempre lo dice, el color solo ayuda -->
                        <p class="inventario-sellos">
                            <?php if ($v["estado"] === "vendido"): ?>
                                <span class="estado-sello estado-sello-vendido">Vendido</span>
                            <?php else: ?>
                                <span class="estado-sello estado-sello-disponible">Disponible</span>
                            <?php endif; ?>
                            <?php if (!$activo): ?>
                                <span class="estado-sello estado-sello-oculto">Fuera del catálogo</span>
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
                               aria-label="Ver <?php echo htmlspecialchars($nombreVehiculo); ?> en el sitio">
                                Ver en el sitio <span aria-hidden="true">→</span>
                            </a>
                        <?php endif; ?>

                        <a class="boton-linea" href="/apex/frontend/admin/vehiculo_form.php?id=<?php echo (int) $v["id"]; ?>"
                           aria-label="Editar <?php echo htmlspecialchars($nombreVehiculo); ?>">Editar</a>

                        <!-- solo se desactiva si está activo; el backend lo vuelve a validar -->
                        <?php if ($activo): ?>
                            <form method="post" action="/apex/backend/procesos/vehiculo_desactivar.php">
                                <input type="hidden" name="id" value="<?php echo (int) $v["id"]; ?>">
                                <button type="submit" class="boton-quitar"
                                        aria-label="Desactivar <?php echo htmlspecialchars($nombreVehiculo); ?>">
                                    Desactivar
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . "/../includes/pie.php"; ?>
