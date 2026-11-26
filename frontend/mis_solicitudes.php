<?php
// Mis solicitudes (RF7)

// trae requerirRol() y abre la sesión
require_once __DIR__ . "/../backend/funciones/acceso.php";
require_once __DIR__ . "/../backend/funciones/solicitudes.php";

// solo clientes: sin sesión va al login, y otro rol recibe "Acceso denegado"
requerirRol("cliente");

// solo trae las solicitudes del cliente que inició sesión
$solicitudes = listarMisSolicitudes();

$titulo = "Mis solicitudes";

require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main>
    <h1>Mis solicitudes</h1>

    <?php if (count($solicitudes) === 0): ?>
        <p>Todavía no has enviado ninguna solicitud.</p>
        <p><a href="catalogo.php">Ver el catálogo</a></p>
    <?php else: ?>
        <table>
            <caption>Solicitudes enviadas</caption>
            <thead>
                <tr>
                    <th scope="col">Fecha</th>
                    <th scope="col">Vehículo</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Comentario</th>
                    <th scope="col">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($solicitudes as $s): ?>
                    <?php $nombreVehiculo = $s["marca"] . " " . $s["modelo"]; ?>
                    <tr>
                        <td><?php echo htmlspecialchars($s["fecha"]); ?></td>
                        <td>
                            <a href="vehiculo.php?id=<?php echo (int) $s["vehiculo_id"]; ?>">
                                <?php echo htmlspecialchars($nombreVehiculo); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars(ucfirst($s["estado"])); ?></td>
                        <td>
                            <?php if ($s["comentario"] !== null && $s["comentario"] !== ""): ?>
                                <?php echo htmlspecialchars($s["comentario"]); ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- solo se puede cancelar mientras esté pendiente; el backend lo vuelve a validar -->
                            <?php if ($s["estado"] === "pendiente"): ?>
                                <form method="post" action="/apex/backend/procesos/cancelar_solicitud.php">
                                    <input type="hidden" name="solicitud_id" value="<?php echo (int) $s["id"]; ?>">
                                    <button type="submit"
                                            aria-label="Cancelar la solicitud de <?php echo htmlspecialchars($nombreVehiculo); ?>">
                                        Cancelar
                                    </button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . "/includes/pie.php"; ?>