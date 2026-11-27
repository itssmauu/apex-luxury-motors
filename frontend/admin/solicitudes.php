<?php
// Solicitudes pendientes (RF9)
// Diseño: banda con video arriba, tres cifras y una fila por solicitud con la foto del auto,
// el cliente, cuánto lleva esperando y el botón para revisarla. Las más antiguas van primero.

require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/solicitudes.php";
// foto principal de cada auto y textos traducidos (t)
require_once __DIR__ . "/../includes/fotos.php";
require_once __DIR__ . "/../includes/idioma.php";

requerirRol("administrador");

$pendientes = listarPendientes();

// cifras: cuántas hay, de cuántos clientes y por cuántos autos
$clientes = [];
$porVehiculo = [];
foreach ($pendientes as $s) {
    $clientes[(int) $s["usuario_id"]] = true;
    $porVehiculo[(int) $s["vehiculo_id"]] = ($porVehiculo[(int) $s["vehiculo_id"]] ?? 0) + 1;
}

// "Hoy", "Hace 1 día", "Hace 5 días" (en el idioma elegido)
function tiempoEsperando($fecha)
{
    $tiempo = strtotime((string) $fecha);
    if (!$tiempo) {
        return "";
    }
    $dias = (int) floor((strtotime("today") - strtotime(date("Y-m-d", $tiempo))) / 86400);
    if ($dias <= 0) {
        return t("admin.hoy");
    }
    return tPlural($dias, "admin.hace_uno", "admin.hace_varios");
}

$titulo = t("admin.sol_pendientes");

require_once __DIR__ . "/../includes/encabezado.php";
require_once __DIR__ . "/../includes/menu.php";
?>

<main class="solicitudes-pagina">
    <!-- banda con video: el título de la página encima -->
    <section class="banda solicitudes-portada" data-zoom aria-labelledby="titulo-solicitudes">
        <video class="banda-video video-al-ver" muted loop playsinline preload="none" aria-hidden="true"
               poster="/apex/img/videos/acceso-calle.jpg">
            <source src="/apex/img/videos/acceso-calle.mp4" type="video/mp4">
        </video>
        <div class="contenedor revelar">
            <p class="sobretitulo"><?php echo htmlspecialchars(t("admin.panel")); ?></p>
            <h1 id="titulo-solicitudes" class="titulo-grande"><?php echo htmlspecialchars(t("menu.solicitudes")); ?></h1>
            <p class="solicitudes-bajada"><?php echo htmlspecialchars(t("admin.sol_bajada")); ?></p>
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
        <!-- cifras: número grande arriba y su nombre abajo -->
        <dl class="cifras cifras-3 solicitudes-cifras" data-zoom>
            <div class="revelar" style="--i: 0">
                <dt><?php echo htmlspecialchars(t("admin.por_atender")); ?></dt>
                <dd><?php echo count($pendientes); ?></dd>
            </div>
            <div class="revelar" style="--i: 1">
                <dt><?php echo htmlspecialchars(t("admin.clientes")); ?></dt>
                <dd><?php echo count($clientes); ?></dd>
            </div>
            <div class="revelar" style="--i: 2">
                <dt><?php echo htmlspecialchars(t("admin.vehiculos_solicitados")); ?></dt>
                <dd><?php echo count($porVehiculo); ?></dd>
            </div>
        </dl>

        <?php if (count($pendientes) === 0): ?>
            <div class="solicitudes-vacio revelar">
                <p><?php echo htmlspecialchars(t("admin.sin_pendientes")); ?></p>
                <a class="boton-linea" href="/apex/frontend/admin/vehiculos.php"><?php echo htmlspecialchars(t("llamado.ir_inventario")); ?></a>
            </div>
        <?php else: ?>
            <h2 class="solo-lector"><?php echo htmlspecialchars(t("admin.sol_caption")); ?></h2>

            <ul class="inventario-lista">
                <?php foreach ($pendientes as $numero => $s): ?>
                    <?php
                    $nombreVehiculo = $s["marca"] . " " . $s["modelo"];
                    $foto = fotoPrincipalVehiculo($s["vehiculo_id"]);
                    $otras = $porVehiculo[(int) $s["vehiculo_id"]] - 1;
                    $enlace = "/apex/frontend/admin/solicitud.php?id=" . (int) $s["id"];
                    ?>
                    <li class="inventario-fila solicitud-fila-admin revelar" style="--i: <?php echo $numero % 4; ?>">
                        <a class="inventario-foto" href="<?php echo $enlace; ?>" tabindex="-1" aria-hidden="true">
                            <?php if ($foto !== null): ?>
                                <img src="<?php echo htmlspecialchars($foto); ?>" alt="" width="320" height="200"
                                     <?php if ($numero > 3) { echo 'loading="lazy"'; } ?>>
                            <?php else: ?>
                                <span><?php echo htmlspecialchars(t("admin.sin_foto")); ?></span>
                            <?php endif; ?>
                        </a>

                        <div class="inventario-datos">
                            <p class="inventario-categoria"><?php echo htmlspecialchars(t("solicitudes.numero", (int) $s["id"])); ?></p>
                            <h3 class="inventario-nombre">
                                <span class="inventario-marca"><?php echo htmlspecialchars($s["marca"]); ?></span>
                                <?php echo htmlspecialchars($s["modelo"]); ?>
                            </h3>
                            <?php if ($otras > 0): ?>
                                <!-- aviso: al aprobar una, las demás de este auto se rechazan solas -->
                                <p class="inventario-sellos">
                                    <span class="estado-sello estado-sello-oculto">
                                        <?php echo htmlspecialchars(tPlural($otras, "admin.otra_uno", "admin.otra_varios")); ?>
                                    </span>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- quién la envió -->
                        <div class="solicitud-cliente">
                            <span class="solicitud-inicial" aria-hidden="true"><?php echo htmlspecialchars(mb_strtoupper(mb_substr($s["nombre"], 0, 1))); ?></span>
                            <div>
                                <p class="solicitud-cliente-nombre"><?php echo htmlspecialchars($s["nombre"]); ?></p>
                                <p class="solicitud-cliente-correo"><?php echo htmlspecialchars($s["correo"]); ?></p>
                            </div>
                        </div>

                        <p class="solicitud-espera">
                            <span><?php echo htmlspecialchars(tiempoEsperando($s["fecha"])); ?></span>
                            <?php echo htmlspecialchars(date("d/m/Y", strtotime((string) $s["fecha"]) ?: time())); ?>
                        </p>

                        <div class="inventario-acciones">
                            <a class="boton solicitud-revisar" href="<?php echo $enlace; ?>"
                               aria-label="<?php echo htmlspecialchars(t("admin.revisar_aria", $s["nombre"], $nombreVehiculo)); ?>">
                                <?php echo htmlspecialchars(t("admin.revisar")); ?>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</main>

<!-- video de la banda y cifras que crecen al hacer scroll -->
<script src="/apex/frontend/js/zoom_scroll.js?v=2"></script>

<?php require_once __DIR__ . "/../includes/pie.php"; ?>
