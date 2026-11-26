<?php
// Inventario de vehículos (RF8)

// trae requerirRol() y abre la sesión
require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/vehiculos.php";

// solo administrador
requerirRol("administrador");

// todos los vehículos, incluso vendidos y desactivados
$vehiculos = listarInventario();

$titulo = "Inventario";

require_once __DIR__ . "/../includes/encabezado.php";
require_once __DIR__ . "/../includes/menu.php";
?>

<main>
    <h1>Inventario de vehículos</h1>

    <p><a href="/apex/frontend/admin/vehiculo_form.php">Agregar vehículo</a></p>

    <?php if (count($vehiculos) === 0): ?>
        <p>No hay vehículos registrados.</p>
    <?php else: ?>
        <table>
            <caption>Todos los vehículos</caption>
            <thead>
                <tr>
                    <th scope="col">Foto</th>
                    <th scope="col">Vehículo</th>
                    <th scope="col">Categoría</th>
                    <th scope="col">Precio</th>
                    <th scope="col">Estado</th>
                    <th scope="col">En el catálogo</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehiculos as $v): ?>
                    <?php $nombreVehiculo = $v["marca"] . " " . $v["modelo"]; ?>
                    <tr>
                        <td>
                            <?php if (!empty($v["foto"])): ?>
                                <img src="/apex/<?php echo htmlspecialchars($v["foto"]); ?>"
                                     alt="<?php echo htmlspecialchars($nombreVehiculo); ?>"
                                     width="90" height="60">
                            <?php else: ?>
                                Sin foto
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($nombreVehiculo); ?></td>
                        <td><?php echo htmlspecialchars($v["categoria"]); ?></td>
                        <td>$<?php echo number_format((float) $v["precio"], 0, ".", ","); ?> USD</td>
                        <td><?php echo htmlspecialchars(ucfirst($v["estado"])); ?></td>
                        <td><?php echo ((int) $v["activo"] === 1) ? "Sí" : "No"; ?></td>
                        <td>
                            <a href="/apex/frontend/admin/vehiculo_form.php?id=<?php echo (int) $v["id"]; ?>"
                               aria-label="Editar <?php echo htmlspecialchars($nombreVehiculo); ?>">Editar</a>

                            <!-- solo se desactiva si está activo; el backend lo vuelve a validar -->
                            <?php if ((int) $v["activo"] === 1): ?>
                                <form method="post" action="/apex/backend/procesos/vehiculo_desactivar.php">
                                    <input type="hidden" name="id" value="<?php echo (int) $v["id"]; ?>">
                                    <button type="submit"
                                            aria-label="Desactivar <?php echo htmlspecialchars($nombreVehiculo); ?>">
                                        Desactivar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . "/../includes/pie.php"; ?>