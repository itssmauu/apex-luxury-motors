<?php
// Solicitudes pendientes (RF9)

require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/solicitudes.php";

requerirRol("administrador");

$pendientes = listarPendientes();

$titulo = "Solicitudes pendientes";

require_once __DIR__ . "/../includes/encabezado.php";
require_once __DIR__ . "/../includes/menu.php";
?>

<main>
    <h1>Solicitudes pendientes</h1>

    <?php if (count($pendientes) === 0): ?>
        <p>No hay solicitudes pendientes.</p>
    <?php else: ?>
        <table>
            <caption>Solicitudes de compra por atender</caption>
            <thead>
                <tr>
                    <th scope="col">Fecha</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Correo</th>
                    <th scope="col">Vehículo</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendientes as $s): ?>
                    <?php $nombreVehiculo = $s["marca"] . " " . $s["modelo"]; ?>
                    <tr>
                        <td><?php echo htmlspecialchars($s["fecha"]); ?></td>
                        <td><?php echo htmlspecialchars($s["nombre"]); ?></td>
                        <td><?php echo htmlspecialchars($s["correo"]); ?></td>
                        <td><?php echo htmlspecialchars($nombreVehiculo); ?></td>
                        <td>
                            <a href="/apex/frontend/admin/solicitud.php?id=<?php echo (int) $s["id"]; ?>"
                               aria-label="Revisar la solicitud de <?php echo htmlspecialchars($s["nombre"]); ?> por <?php echo htmlspecialchars($nombreVehiculo); ?>">Revisar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . "/../includes/pie.php"; ?>