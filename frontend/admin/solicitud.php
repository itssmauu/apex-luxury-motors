<?php
// Detalle de una solicitud y decisión del administrador (RF9)

require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/solicitudes.php";

requerirRol("administrador");

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$solicitud = ($id > 0) ? obtenerSolicitud($id) : null;

// no existe: aviso y vuelta a la lista
if (!$solicitud) {
    $_SESSION["mensaje"] = ["tipo" => "error", "texto" => "La solicitud no existe."];
    header("Location: /apex/frontend/admin/solicitudes.php");
    exit;
}

$nombreVehiculo = $solicitud["marca"] . " " . $solicitud["modelo"];

$titulo = "Solicitud #" . (int) $solicitud["id"];

require_once __DIR__ . "/../includes/encabezado.php";
require_once __DIR__ . "/../includes/menu.php";
?>

<main>
    <h1>Solicitud #<?php echo (int) $solicitud["id"]; ?></h1>

    <h2>Solicitud</h2>
    <ul>
        <li>Fecha: <?php echo htmlspecialchars($solicitud["fecha"]); ?></li>
        <li>Estado: <?php echo htmlspecialchars(ucfirst($solicitud["estado"])); ?></li>
    </ul>

    <h2>Cliente</h2>
    <ul>
        <li>Nombre: <?php echo htmlspecialchars($solicitud["nombre"]); ?></li>
        <li>Correo: <?php echo htmlspecialchars($solicitud["correo"]); ?></li>
        <li>Teléfono: <?php echo htmlspecialchars((string) $solicitud["telefono"]); ?></li>
    </ul>

    <h2>Vehículo</h2>
    <ul>
        <li>Vehículo: <?php echo htmlspecialchars($nombreVehiculo); ?></li>
        <li>Precio: $<?php echo number_format((float) $solicitud["precio"], 0, ".", ","); ?> USD</li>
        <li>Estado del vehículo: <?php echo htmlspecialchars(ucfirst($solicitud["estado_vehiculo"])); ?></li>
    </ul>

    <?php if ($solicitud["estado"] === "pendiente"): ?>
        <h2>Decisión</h2>

        <form method="post" action="/apex/backend/procesos/atender_solicitud.php">
            <input type="hidden" name="solicitud_id" value="<?php echo (int) $solicitud["id"]; ?>">

            <p>
                <label for="comentario">Comentario para el cliente (opcional)</label><br>
                <textarea id="comentario" name="comentario" rows="4" cols="40"></textarea>
            </p>

            <p>
                <button type="submit" name="decision" value="aprobada">Aprobar</button>
                <button type="submit" name="decision" value="rechazada">Rechazar</button>
                <a href="/apex/frontend/admin/solicitudes.php">Volver</a>
            </p>
        </form>
    <?php else: ?>
        <p>Esta solicitud ya fue atendida.</p>
        <?php if (!empty($solicitud["comentario"])): ?>
            <p>Comentario: <?php echo htmlspecialchars($solicitud["comentario"]); ?></p>
        <?php endif; ?>
        <p><a href="/apex/frontend/admin/solicitudes.php">Volver a pendientes</a></p>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . "/../includes/pie.php"; ?>