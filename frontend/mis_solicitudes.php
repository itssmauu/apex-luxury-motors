<?php
// Mis solicitudes (RF7)
// Diseño: una banda con video arriba, cifras que también filtran por estado
// y una fila por solicitud con la foto del auto, su estado, la respuesta del equipo y la acción.

// trae requerirRol() y abre la sesión
require_once __DIR__ . "/../backend/funciones/acceso.php";
require_once __DIR__ . "/../backend/funciones/solicitudes.php";
// textos traducidos (t) y nombres de los estados en el idioma elegido
require_once __DIR__ . "/includes/idioma.php";
require_once __DIR__ . "/datos/valores.php";
// foto principal de cada auto
require_once __DIR__ . "/includes/fotos.php";

// solo clientes: sin sesión va al login, y otro rol recibe "Acceso denegado"
requerirRol("cliente");

// solo trae las solicitudes del cliente que inició sesión
$solicitudes = listarMisSolicitudes();

// cifras de arriba: cada una es también un filtro (mis_solicitudes.php?estado=aprobadas)
$vistas = [
    "todas"      => ["nombre" => t("solicitudes.todas"),       "estados" => null],
    "pendientes" => ["nombre" => t("solicitudes.f_pendientes"), "estados" => ["pendiente"]],
    "aprobadas"  => ["nombre" => t("solicitudes.f_aprobadas"),  "estados" => ["aprobada"]],
    "cerradas"   => ["nombre" => t("solicitudes.f_cerradas"),   "estados" => ["rechazada", "cancelada"]],
];
foreach ($vistas as $clave => $vista) {
    $vistas[$clave]["cantidad"] = 0;
    foreach ($solicitudes as $s) {
        if ($vista["estados"] === null || in_array($s["estado"], $vista["estados"], true)) {
            $vistas[$clave]["cantidad"]++;
        }
    }
}

$vistaActual = (isset($_GET["estado"]) && is_string($_GET["estado"]) && isset($vistas[$_GET["estado"]]))
    ? $_GET["estado"]
    : "todas";

$solicitudesVista = [];
foreach ($solicitudes as $s) {
    $estados = $vistas[$vistaActual]["estados"];
    if ($estados === null || in_array($s["estado"], $estados, true)) {
        $solicitudesVista[] = $s;
    }
}

// fecha corta: 27/11/2026
function fechaCorta($fecha)
{
    $tiempo = strtotime((string) $fecha);
    return $tiempo ? date("d/m/Y", $tiempo) : (string) $fecha;
}

$titulo = t("menu.mis_solicitudes");

require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main class="solicitudes-pagina">
    <!-- banda con video: el título de la página encima -->
    <section class="banda solicitudes-portada" data-zoom aria-labelledby="titulo-solicitudes">
        <video class="banda-video video-al-ver" muted loop playsinline preload="none" aria-hidden="true"
               poster="/apex/img/videos/carretera.jpg">
            <source src="/apex/img/videos/carretera.mp4" type="video/mp4">
        </video>
        <div class="contenedor revelar">
            <p class="sobretitulo"><?php echo htmlspecialchars(t("perfil.mi_cuenta")); ?></p>
            <h1 id="titulo-solicitudes" class="titulo-grande"><?php echo htmlspecialchars(t("menu.mis_solicitudes")); ?></h1>
            <p class="solicitudes-bajada"><?php echo htmlspecialchars(t("solicitudes.bajada")); ?></p>
        </div>
        <!-- data-texto-*: los textos traducidos que usa zoom_scroll.js -->
        <button type="button" class="boton-pausa video-pausa"
                aria-label="<?php echo htmlspecialchars(t("vehiculo.pausar_video")); ?>"
                data-texto-pausar="<?php echo htmlspecialchars(t("vehiculo.pausar_video")); ?>"
                data-texto-reproducir="<?php echo htmlspecialchars(t("vehiculo.reproducir_video")); ?>" hidden>
            <span class="icono-pausa" aria-hidden="true"></span>
        </button>
    </section>

    <div class="contenedor solicitudes-contenido">
        <?php if (count($solicitudes) === 0): ?>
            <!-- todavía no hay solicitudes: una invitación al catálogo -->
            <div class="solicitudes-vacio revelar">
                <p><?php echo htmlspecialchars(t("solicitudes.vacio")); ?></p>
                <a class="boton" href="catalogo.php"><?php echo htmlspecialchars(t("comun.ver_catalogo")); ?></a>
            </div>
        <?php else: ?>
            <!-- cifras: cada una es también un filtro -->
            <nav class="inventario-filtros" aria-label="<?php echo htmlspecialchars(t("solicitudes.filtro_aria")); ?>">
                <ul>
                    <?php foreach ($vistas as $clave => $vista): ?>
                        <li>
                            <a href="?estado=<?php echo $clave; ?>"
                               <?php if ($clave === $vistaActual) { echo 'aria-current="page"'; } ?>>
                                <span class="inventario-cifra"><?php echo $vista["cantidad"]; ?></span>
                                <span class="inventario-cifra-nombre"><?php echo htmlspecialchars($vista["nombre"]); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <?php if (count($solicitudesVista) === 0): ?>
                <div class="inventario-vacio">
                    <p><?php echo htmlspecialchars(t("solicitudes.vacio_filtro")); ?></p>
                    <a href="?estado=todas"><?php echo htmlspecialchars(t("solicitudes.ver_todas")); ?></a>
                </div>
            <?php else: ?>
                <h2 class="solo-lector"><?php echo htmlspecialchars(t("solicitudes.caption")); ?></h2>

                <ul class="inventario-lista">
                    <?php foreach ($solicitudesVista as $numero => $s): ?>
                        <?php
                        $nombreVehiculo = $s["marca"] . " " . $s["modelo"];
                        $foto = fotoPrincipalVehiculo($s["vehiculo_id"]);
                        $comentario = ($s["comentario"] !== null) ? trim($s["comentario"]) : "";
                        ?>
                        <li class="inventario-fila solicitud-fila revelar" style="--i: <?php echo $numero % 4; ?>">
                            <a class="inventario-foto" href="vehiculo.php?id=<?php echo (int) $s["vehiculo_id"]; ?>"
                               tabindex="-1" aria-hidden="true">
                                <?php if ($foto !== null): ?>
                                    <img src="<?php echo htmlspecialchars($foto); ?>" alt="" width="320" height="200"
                                         <?php if ($numero > 3) { echo 'loading="lazy"'; } ?>>
                                <?php else: ?>
                                    <span><?php echo htmlspecialchars(t("comun.foto_no_disponible")); ?></span>
                                <?php endif; ?>
                            </a>

                            <div class="inventario-datos">
                                <p class="inventario-categoria">
                                    <?php echo htmlspecialchars(t("solicitudes.numero", (int) $s["id"])); ?> ·
                                    <?php echo htmlspecialchars(t("solicitudes.enviada", fechaCorta($s["fecha"]))); ?>
                                </p>
                                <h3 class="inventario-nombre">
                                    <span class="inventario-marca"><?php echo htmlspecialchars($s["marca"]); ?></span>
                                    <?php echo htmlspecialchars($s["modelo"]); ?>
                                </h3>
                                <!-- el texto dice el estado; el color solo ayuda -->
                                <p class="inventario-sellos">
                                    <span class="estado-sello estado-sello-<?php echo htmlspecialchars($s["estado"]); ?>">
                                        <?php echo htmlspecialchars(nombreEstadoSolicitud($s["estado"])); ?>
                                    </span>
                                </p>
                            </div>

                            <!-- respuesta del equipo (o aviso de que todavía se revisa) -->
                            <div class="solicitud-respuesta">
                                <p class="solicitud-respuesta-titulo"><?php echo htmlspecialchars(t("solicitudes.respuesta")); ?></p>
                                <?php if ($s["estado"] === "pendiente"): ?>
                                    <p class="solicitud-respuesta-texto solicitud-respuesta-espera"><?php echo htmlspecialchars(t("solicitudes.sin_respuesta")); ?></p>
                                <?php elseif ($comentario !== ""): ?>
                                    <blockquote class="solicitud-respuesta-texto"><p><?php echo htmlspecialchars($comentario); ?></p></blockquote>
                                <?php else: ?>
                                    <p class="solicitud-respuesta-texto solicitud-respuesta-espera"><?php echo htmlspecialchars(t("solicitudes.sin_comentario")); ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="inventario-acciones">
                                <a class="inventario-ver" href="vehiculo.php?id=<?php echo (int) $s["vehiculo_id"]; ?>"
                                   aria-label="<?php echo htmlspecialchars(t("comun.ver_vehiculo_aria", $nombreVehiculo)); ?>">
                                    <?php echo htmlspecialchars(t("comun.ver_vehiculo")); ?> <span aria-hidden="true">→</span>
                                </a>
                                <!-- solo se puede cancelar mientras esté pendiente; el backend lo vuelve a validar -->
                                <?php if ($s["estado"] === "pendiente"): ?>
                                    <form method="post" action="/apex/backend/procesos/cancelar_solicitud.php">
                                        <input type="hidden" name="solicitud_id" value="<?php echo (int) $s["id"]; ?>">
                                        <button type="submit" class="boton-quitar"
                                                aria-label="<?php echo htmlspecialchars(t("solicitudes.cancelar_aria", $nombreVehiculo)); ?>">
                                            <?php echo htmlspecialchars(t("solicitudes.cancelar")); ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <p class="solicitudes-mas">
                <a class="boton-linea" href="catalogo.php"><?php echo htmlspecialchars(t("comun.ver_catalogo")); ?></a>
            </p>
        <?php endif; ?>
    </div>
</main>

<!-- video de la banda (se reproduce solo cuando se ve) -->
<script src="/apex/frontend/js/zoom_scroll.js?v=2"></script>

<?php require_once __DIR__ . "/includes/pie.php"; ?>
