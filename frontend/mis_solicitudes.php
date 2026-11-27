<?php
// Mis solicitudes (RF7)

// trae requerirRol() y abre la sesión
require_once __DIR__ . "/../backend/funciones/acceso.php";
require_once __DIR__ . "/../backend/funciones/solicitudes.php";
// textos traducidos (t) y nombres de los estados en el idioma elegido
require_once __DIR__ . "/includes/idioma.php";
require_once __DIR__ . "/datos/valores.php";

// solo clientes: sin sesión va al login, y otro rol recibe "Acceso denegado"
requerirRol("cliente");

// solo trae las solicitudes del cliente que inició sesión
$solicitudes = listarMisSolicitudes();

$titulo = t("menu.mis_solicitudes");

require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main>
    <h1><?php echo htmlspecialchars(t("menu.mis_solicitudes")); ?></h1>

    <?php if (count($solicitudes) === 0): ?>
        <p><?php echo htmlspecialchars(t("solicitudes.vacio")); ?></p>
        <p><a href="catalogo.php"><?php echo htmlspecialchars(t("comun.ver_catalogo")); ?></a></p>
    <?php else: ?>
        <!-- en el celular la tabla se desliza de lado dentro de este recuadro (tabindex para moverla con el teclado) -->
        <div class="tabla-desplazable" role="region" aria-labelledby="titulo-tabla" tabindex="0">
        <table>
            <caption id="titulo-tabla"><?php echo htmlspecialchars(t("solicitudes.caption")); ?></caption>
            <thead>
                <tr>
                    <th scope="col"><?php echo htmlspecialchars(t("dato.fecha")); ?></th>
                    <th scope="col"><?php echo htmlspecialchars(t("dato.vehiculo")); ?></th>
                    <th scope="col"><?php echo htmlspecialchars(t("dato.estado")); ?></th>
                    <th scope="col"><?php echo htmlspecialchars(t("dato.comentario")); ?></th>
                    <th scope="col"><?php echo htmlspecialchars(t("dato.accion")); ?></th>
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
                        <!-- la clase solo cambia el color; el estado siempre va escrito (traducido; si no se conoce, tal cual) -->
                        <td class="estado-<?php echo htmlspecialchars($s["estado"]); ?>"><?php echo htmlspecialchars(nombreEstadoSolicitud($s["estado"])); ?></td>
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
                                            aria-label="<?php echo htmlspecialchars(t("solicitudes.cancelar_aria", $nombreVehiculo)); ?>">
                                        <?php echo htmlspecialchars(t("solicitudes.cancelar")); ?>
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
        </div>
        <p><a href="catalogo.php"><?php echo htmlspecialchars(t("comun.ver_catalogo")); ?></a></p>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . "/includes/pie.php"; ?>