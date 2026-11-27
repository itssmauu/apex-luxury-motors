<?php
// Detalle de una solicitud y decisión del administrador (RF9)
// Diseño: a la izquierda la foto grande del auto con su precio; a la derecha
// el cliente, los datos de la solicitud y el panel para aprobar o rechazar.

require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/solicitudes.php";
// foto principal del auto, textos traducidos (t) y nombres de los estados
require_once __DIR__ . "/../includes/fotos.php";
require_once __DIR__ . "/../includes/idioma.php";
require_once __DIR__ . "/../datos/valores.php";

requerirRol("administrador");

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$solicitud = ($id > 0) ? obtenerSolicitud($id) : null;

// no existe: aviso y vuelta a la lista
if (!$solicitud) {
    $_SESSION["mensaje"] = ["tipo" => "error", "texto" => t("admin.sol_no_existe")];
    header("Location: /apex/frontend/admin/solicitudes.php");
    exit;
}

$nombreVehiculo = $solicitud["marca"] . " " . $solicitud["modelo"];
$foto = fotoPrincipalVehiculo($solicitud["vehiculo_id"]);
$pendiente = ($solicitud["estado"] === "pendiente");
$vehiculoVendido = ($solicitud["estado_vehiculo"] === "vendido");
$fechaTexto = date("d/m/Y", strtotime((string) $solicitud["fecha"]) ?: time());

// nombre del estado de la solicitud en el idioma elegido (la base guarda "pendiente", "aprobada"...)
$estadoTexto = nombreEstadoSolicitud($solicitud["estado"]);

$titulo = t("solicitudes.numero", (int) $solicitud["id"]);

require_once __DIR__ . "/../includes/encabezado.php";
require_once __DIR__ . "/../includes/menu.php";
?>

<main class="inventario solicitud-detalle">
    <a class="inventario-ver solicitud-volver" href="/apex/frontend/admin/solicitudes.php">
        <span aria-hidden="true">←</span> <?php echo htmlspecialchars(t("admin.sol_pendientes")); ?>
    </a>

    <!-- encabezado: número y fecha, el auto y el estado -->
    <div class="inventario-cabecera revelar">
        <div>
            <p class="sobretitulo"><?php echo htmlspecialchars(t("solicitudes.numero", (int) $solicitud["id"])); ?> · <?php echo htmlspecialchars(t("solicitudes.enviada", $fechaTexto)); ?></p>
            <h1>
                <span class="inventario-marca"><?php echo htmlspecialchars($solicitud["marca"]); ?></span>
                <?php echo htmlspecialchars($solicitud["modelo"]); ?>
            </h1>
        </div>
        <span class="estado-sello estado-sello-grande estado-sello-<?php echo htmlspecialchars($solicitud["estado"]); ?>">
            <?php echo htmlspecialchars($estadoTexto); ?>
        </span>
    </div>

    <div class="solicitud-rejilla">
        <!-- el auto: foto grande, precio y estado -->
        <figure class="solicitud-auto revelar">
            <div class="solicitud-auto-foto">
                <?php if ($foto !== null): ?>
                    <img src="<?php echo htmlspecialchars($foto); ?>" alt="<?php echo htmlspecialchars($nombreVehiculo); ?>" width="1200" height="750">
                <?php else: ?>
                    <span><?php echo htmlspecialchars(t("admin.sin_foto")); ?></span>
                <?php endif; ?>
            </div>
            <figcaption class="solicitud-auto-datos">
                <p class="inventario-precio">
                    $<?php echo number_format((float) $solicitud["precio"], 0, ".", ","); ?> <span>USD</span>
                </p>
                <span class="estado-sello <?php echo $vehiculoVendido ? "estado-sello-vendido" : "estado-sello-disponible"; ?>">
                    <?php echo htmlspecialchars($vehiculoVendido ? t("estado_vehiculo.vendido") : t("estado_vehiculo.disponible")); ?>
                </span>
                <a class="inventario-ver" href="/apex/frontend/vehiculo.php?id=<?php echo (int) $solicitud["vehiculo_id"]; ?>">
                    <?php echo htmlspecialchars(t("admin.ver_sitio")); ?> <span aria-hidden="true">→</span>
                </a>
            </figcaption>
        </figure>

        <div class="solicitud-paneles">
            <!-- el cliente -->
            <section class="solicitud-panel revelar" style="--i: 1" aria-labelledby="titulo-cliente">
                <h2 id="titulo-cliente" class="solicitud-panel-titulo"><?php echo htmlspecialchars(t("rol.cliente")); ?></h2>
                <div class="solicitud-cliente solicitud-cliente-grande">
                    <span class="solicitud-inicial" aria-hidden="true"><?php echo htmlspecialchars(mb_strtoupper(mb_substr($solicitud["nombre"], 0, 1))); ?></span>
                    <p class="solicitud-cliente-nombre"><?php echo htmlspecialchars($solicitud["nombre"]); ?></p>
                </div>
                <dl class="escena-lista solicitud-lista">
                    <div>
                        <dt><?php echo htmlspecialchars(t("campo.correo")); ?></dt>
                        <dd><a href="mailto:<?php echo htmlspecialchars($solicitud["correo"]); ?>"><?php echo htmlspecialchars($solicitud["correo"]); ?></a></dd>
                    </div>
                    <div>
                        <dt><?php echo htmlspecialchars(t("campo.telefono")); ?></dt>
                        <dd>
                            <?php if (!empty($solicitud["telefono"])): ?>
                                <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', (string) $solicitud["telefono"])); ?>"><?php echo htmlspecialchars((string) $solicitud["telefono"]); ?></a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </dd>
                    </div>
                </dl>
            </section>

            <?php if ($pendiente): ?>
                <!-- decisión: aprobar o rechazar, con un comentario opcional -->
                <section class="solicitud-panel solicitud-decision revelar" style="--i: 2" aria-labelledby="titulo-decision">
                    <h2 id="titulo-decision" class="solicitud-panel-titulo"><?php echo htmlspecialchars(t("admin.decision")); ?></h2>

                    <?php if ($vehiculoVendido): ?>
                        <p class="solicitud-aviso solicitud-aviso-error"><?php echo htmlspecialchars(t("admin.aviso_vendido")); ?></p>
                    <?php else: ?>
                        <p class="solicitud-aviso"><?php echo htmlspecialchars(t("admin.aviso_aprobar")); ?></p>
                    <?php endif; ?>

                    <form method="post" action="/apex/backend/procesos/atender_solicitud.php">
                        <input type="hidden" name="solicitud_id" value="<?php echo (int) $solicitud["id"]; ?>">

                        <div class="campo">
                            <label for="comentario"><?php echo htmlspecialchars(t("admin.comentario")); ?> <span><?php echo htmlspecialchars(t("perfil.opcional")); ?></span></label>
                            <!-- el backend acepta hasta 255 caracteres -->
                            <textarea id="comentario" name="comentario" rows="4" maxlength="255"
                                      aria-describedby="comentario-cuenta"
                                      placeholder="<?php echo htmlspecialchars(t("admin.comentario_ejemplo")); ?>"></textarea>
                            <!-- data-texto-*: los textos traducidos que usa solicitud.js para el contador -->
                            <p class="campo-ayuda" id="comentario-cuenta" aria-live="polite"
                               data-texto-uno="<?php echo htmlspecialchars(t("admin.quedan_uno")); ?>"
                               data-texto-varios="<?php echo htmlspecialchars(t("admin.quedan_varios", "%s")); ?>"><?php echo htmlspecialchars(t("admin.hasta_255")); ?></p>
                        </div>

                        <div class="solicitud-botones">
                            <?php if (!$vehiculoVendido): ?>
                                <button type="submit" class="solicitud-aprobar" name="decision" value="aprobada"><?php echo htmlspecialchars(t("admin.aprobar")); ?></button>
                            <?php endif; ?>
                            <button type="submit" class="boton-quitar solicitud-rechazar" name="decision" value="rechazada"><?php echo htmlspecialchars(t("admin.rechazar")); ?></button>
                        </div>
                    </form>
                </section>
            <?php else: ?>
                <!-- ya atendida: cómo terminó y el comentario que se dejó -->
                <section class="solicitud-panel revelar" style="--i: 2" aria-labelledby="titulo-resultado">
                    <h2 id="titulo-resultado" class="solicitud-panel-titulo"><?php echo htmlspecialchars(t("admin.resultado")); ?></h2>
                    <p><?php echo htmlspecialchars(t("admin.ya_atendida", mb_strtolower($estadoTexto))); ?></p>
                    <?php if (!empty($solicitud["comentario"])): ?>
                        <blockquote class="solicitud-respuesta-texto"><p><?php echo htmlspecialchars($solicitud["comentario"]); ?></p></blockquote>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- contador de caracteres del comentario -->
<script src="/apex/frontend/js/solicitud.js?v=2"></script>

<?php require_once __DIR__ . "/../includes/pie.php"; ?>
