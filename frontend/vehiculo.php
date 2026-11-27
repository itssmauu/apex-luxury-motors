<?php
// Detalle de vehículo (RF5)
// Idea: como las páginas de modelo de las marcas de lujo. Una foto a pantalla completa,
// después "escenas" donde la foto se queda fija y la información pasa por encima al hacer
// scroll, una galería con todas las vistas, las especificaciones y la solicitud de compra.

// funciones del backend que usa esta página:
// obtenerVehiculo() para los datos y las fotos del vehículo
require_once __DIR__ . "/../backend/funciones/vehiculos.php";
// esta segunda línea es para rolActual() (y también abre la sesión)
require_once __DIR__ . "/../backend/funciones/acceso.php";
// especificaciones técnicas: están en un archivo aparte porque la base no las guarda
require_once __DIR__ . "/datos/especificaciones.php";
// cifras cortas (potencia, aceleración...) sacadas de las especificaciones
require_once __DIR__ . "/includes/cifras.php";
// créditos de los modelos 3D y de los videos
require_once __DIR__ . "/datos/modelos3d.php";
require_once __DIR__ . "/datos/videos.php";
// textos traducidos: interfaz (t), categorías, descripciones y ficha técnica
require_once __DIR__ . "/includes/idioma.php";
require_once __DIR__ . "/datos/valores.php";
require_once __DIR__ . "/datos/descripciones.php";
require_once __DIR__ . "/datos/especificaciones_traducciones.php";

// el id viene en la dirección: vehiculo.php?id=8
$id = isset($_GET["id"]) && is_string($_GET["id"]) ? $_GET["id"] : 0;

// si no existe o está inactivo, llega null
$vehiculo = obtenerVehiculo($id);

// la página solo decide qué mostrar, el backend vuelve a validar al solicitar
$rol = rolActual();

if ($vehiculo === null) {
    http_response_code(404);
    $titulo = t("vehiculo.no_encontrado");
} else {
    $titulo = $vehiculo["marca"] . " " . $vehiculo["modelo"];
    // el logo y el menú van encima de la foto grande (igual que en la portada)
    $claseBody = "pagina-vehiculo";
}

// ---------- fotos ----------
// El nombre del archivo dice qué vista es: "ferrari-458-trasera.jpg" es la trasera.
// Las fotos que sube el administrador no siguen esa regla: salen como "Vista 2", "Vista 3"...
// (el nombre de cada vista sale del diccionario: vista.frontal, vista.lateral...)
$nombresVista = [
    "frontal"  => "vista.frontal",
    "lateral"  => "vista.lateral",
    "trasera"  => "vista.trasera",
    "puertas"  => "vista.puertas",
    "interior" => "vista.interior",
    "cabina"   => "vista.cabina",
];

$fotos = [];
if ($vehiculo !== null) {
    foreach ($vehiculo["fotos"] as $numero => $foto) {
        // última palabra del nombre del archivo, sin la extensión
        $partes = explode("-", pathinfo($foto["ruta"], PATHINFO_FILENAME));
        $clave = strtolower(end($partes));

        if (isset($nombresVista[$clave])) {
            $vista = t($nombresVista[$clave]);
        } elseif ($numero === 0) {
            $vista = t("vista.frontal");       // la primera foto de cada auto es la de frente
            $clave = "frontal";
        } else {
            $vista = t("vista.numero", $numero + 1);
        }

        // medidas reales, para ponerle width y height y que la página no salte al cargar
        $archivo = __DIR__ . "/../" . $foto["ruta"];
        $medidas = is_file($archivo) ? getimagesize($archivo) : false;

        $fotos[] = [
            "ruta"  => "../" . $foto["ruta"],   // la base guarda "img/...", desde frontend/ se sube un nivel
            "clave" => $clave,
            "vista" => $vista,
            "ancho" => $medidas ? (int) $medidas[0] : 0,
            "alto"  => $medidas ? (int) $medidas[1] : 0,
        ];
    }
}

// primera foto de la lista que sea de alguna de estas vistas (o null)
function buscarFoto($fotos, $claves)
{
    foreach ($claves as $clave) {
        foreach ($fotos as $foto) {
            if ($foto["clave"] === $clave) {
                return $foto;
            }
        }
    }
    return null;
}

// la foto grande de arriba: la primera que tenga buena resolución (si ninguna, la primera)
$fotoPortada = null;
foreach ($fotos as $foto) {
    if ($foto["ancho"] >= 1200) {
        $fotoPortada = $foto;
        break;
    }
}
if ($fotoPortada === null && count($fotos) > 0) {
    $fotoPortada = $fotos[0];
}

// ---------- especificaciones ----------
$fichaTecnica = [];
if ($vehiculo !== null && isset($especificaciones[(int) $vehiculo["id"]])) {
    $fichaTecnica = $especificaciones[(int) $vehiculo["id"]];
}

// las cifras grandes de la escena "Rendimiento" (las funciones están en includes/cifras.php,
// que también traduce los nombres)
$cifras = cifrasDestacadas($fichaTecnica);

// qué filas van en cada tabla de especificaciones (la clave es el título de la tabla en el diccionario)
$tablasTecnicas = [
    "ficha.tabla_motor" => ["Motor", "Cilindrada", "Potencia", "Torque", "Aceleración", "Velocidad máxima"],
    "ficha.tabla_traccion" => ["Tracción", "Peso"],
];

// ---------- escenas (foto fija + información encima) ----------
// cada escena usa la foto de una vista distinta; si el auto no la tiene, usa otra
$escenaDiseno = buscarFoto($fotos, ["lateral", "trasera", "puertas", "frontal"]);
$escenaRendimiento = buscarFoto($fotos, ["trasera", "puertas", "lateral", "frontal"]);
$escenaInterior = buscarFoto($fotos, ["interior", "cabina", "puertas"]);

// los datos se leen en español y se muestran traducidos
$idVehiculo = ($vehiculo !== null) ? (int) $vehiculo["id"] : 0;
$motor = datoFichaTraducido($idVehiculo, "Motor", datoFicha($fichaTecnica, "Motor"));
$cilindrada = datoFichaTraducido($idVehiculo, "Cilindrada", datoFicha($fichaTecnica, "Cilindrada"));
$traccion = datoFichaTraducido($idVehiculo, "Tracción", datoFicha($fichaTecnica, "Tracción"));
$peso = datoFichaTraducido($idVehiculo, "Peso", datoFicha($fichaTecnica, "Peso"));

// ---------- videos (opcionales) ----------
// si existen img/vehiculos/videos/8-1.mp4 y 8-2.mp4, se muestran; si no, no pasa nada
$videos = [];
if ($vehiculo !== null) {
    for ($n = 1; $n <= 2; $n++) {
        $base = "img/vehiculos/videos/" . (int) $vehiculo["id"] . "-" . $n;
        if (is_file(__DIR__ . "/../" . $base . ".mp4")) {
            $videos[$n] = [
                "video"   => "../" . $base . ".mp4",
                "poster"  => is_file(__DIR__ . "/../" . $base . ".jpg") ? "../" . $base . ".jpg" : "",
                "credito" => $creditosVideos[(int) $vehiculo["id"] . "-" . $n] ?? null,
            ];
        }
    }
}

// ---------- sonido del motor y modelo 3D (opcionales) ----------
// audio/motores/8.mp3 y img/vehiculos/modelos/8.glb para el auto con id 8
$sonidoMotor = "";
$modelo3d = "";
$creditoModelo = null;
$pinturaModelo = null;
if ($vehiculo !== null) {
    $idAuto = (int) $vehiculo["id"];
    if (is_file(__DIR__ . "/../audio/motores/" . $idAuto . ".mp3")) {
        $sonidoMotor = "../audio/motores/" . $idAuto . ".mp3";
    }
    if (is_file(__DIR__ . "/../img/vehiculos/modelos/" . $idAuto . ".glb")) {
        $modelo3d = "../img/vehiculos/modelos/" . $idAuto . ".glb";
        $creditoModelo = $modelos3d[$idAuto] ?? null;
        $pinturaModelo = $pinturas3d[$idAuto] ?? null;
    }
}

// crédito de un video que lo pide (licencia CC): "Video «título» de autor, licencia X."
function creditoVideo($credito)
{
    if ($credito === null) {
        return "";
    }
    $enlace = '<a href="' . htmlspecialchars($credito["enlace"]) . '" target="_blank" rel="noopener">«'
        . htmlspecialchars($credito["titulo"]) . '»</a>';
    $texto = htmlspecialchars(t("video.credito", "[enlace]", $credito["autor"], $credito["licencia"]));
    return '<p class="modelo-video-credito">' . str_replace("[enlace]", $enlace, $texto) . '</p>';
}

// texto de apoyo que se repite
$nombreAuto = ($vehiculo !== null) ? $vehiculo["marca"] . " " . $vehiculo["modelo"] : "";
$precioTexto = ($vehiculo !== null) ? "$" . number_format((float) $vehiculo["precio"], 0, ".", ",") : "";
$vendido = ($vehiculo !== null && $vehiculo["estado"] === "vendido");

// plantilla común: encabezado con el logo, menú según el rol y mensajes
require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<?php if ($vehiculo === null): ?>
<main>
    <h1><?php echo htmlspecialchars(t("vehiculo.no_encontrado")); ?></h1>
    <p><?php echo htmlspecialchars(t("vehiculo.no_existe")); ?></p>
    <p><a href="catalogo.php"><?php echo htmlspecialchars(t("vehiculo.volver_catalogo")); ?></a></p>
</main>
<?php else: ?>
<main>
    <!-- ===== 1. portada: la foto a pantalla completa con el nombre encima ===== -->
    <section class="modelo-portada" aria-labelledby="nombre-auto">
        <?php if ($fotoPortada !== null): ?>
            <!-- fetchpriority high: es lo primero que se ve, que cargue antes que lo demás -->
            <img class="modelo-portada-foto"
                 src="<?php echo htmlspecialchars($fotoPortada["ruta"]); ?>"
                 alt="<?php echo htmlspecialchars(t("vehiculo.alt_vista", $nombreAuto, mb_strtolower($fotoPortada["vista"]))); ?>"
                 <?php if ($fotoPortada["ancho"] > 0) { echo 'width="' . $fotoPortada["ancho"] . '" height="' . $fotoPortada["alto"] . '"'; } ?>
                 fetchpriority="high">
        <?php endif; ?>

        <div class="modelo-portada-texto">
            <a class="modelo-volver" href="catalogo.php"><span aria-hidden="true">←</span> <?php echo htmlspecialchars(t("menu.catalogo")); ?></a>
            <p class="sobretitulo"><?php echo htmlspecialchars(nombreCategoria($vehiculo["categoria"])); ?></p>
            <h1 id="nombre-auto" class="modelo-nombre">
                <span class="modelo-marca"><?php echo htmlspecialchars($vehiculo["marca"]); ?></span>
                <?php echo htmlspecialchars($vehiculo["modelo"]); ?>
            </h1>
            <p class="modelo-precio">
                <?php echo $precioTexto; ?> <span>USD</span>
                <?php if ($vendido): ?>
                    <span class="sello sello-vendido"><?php echo htmlspecialchars(t("estado_vehiculo.vendido")); ?></span>
                <?php else: ?>
                    <span class="sello"><?php echo htmlspecialchars(t("estado_vehiculo.disponible")); ?></span>
                <?php endif; ?>
            </p>

            <?php if ($modelo3d !== "" || $sonidoMotor !== ""): ?>
                <div class="modelo-portada-acciones">
                    <?php if ($modelo3d !== ""): ?>
                        <a class="boton-borde" href="#modelo-3d"><span aria-hidden="true">↻</span> <?php echo htmlspecialchars(t("catalogo.ver_3d")); ?></a>
                    <?php endif; ?>
                    <?php if ($sonidoMotor !== ""): ?>
                        <!-- botón de encendido: suena el motor (lo activa js/motor.js) -->
                        <span class="motor-con-texto">
                            <button type="button" class="motor" data-sonido="<?php echo htmlspecialchars($sonidoMotor); ?>"
                                    aria-pressed="false" aria-label="<?php echo htmlspecialchars(t("motor.encender_aria", $nombreAuto)); ?>" hidden>
                                <span class="motor-icono" aria-hidden="true"></span>
                                <span class="motor-texto" aria-hidden="true">Engine<br>Start</span>
                            </button>
                            <span class="motor-leyenda" aria-hidden="true"><?php echo htmlspecialchars(t("motor.escucha")); ?></span>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- invitación a bajar (decorativa) -->
        <span class="modelo-bajar" aria-hidden="true"></span>
    </section>

    <!-- ===== barra del modelo: se queda pegada arriba al hacer scroll ===== -->
    <nav class="modelo-barra" aria-label="<?php echo htmlspecialchars(t("vehiculo.secciones")); ?>">
        <p class="modelo-barra-nombre" aria-hidden="true"><?php echo htmlspecialchars($nombreAuto); ?></p>
        <ul>
            <li><a href="#diseno"><?php echo htmlspecialchars(t("vehiculo.diseno")); ?></a></li>
            <?php if ($modelo3d !== ""): ?>
                <li><a href="#modelo-3d"><?php echo htmlspecialchars(t("vehiculo.3d")); ?></a></li>
            <?php endif; ?>
            <?php if (count($cifras) > 0): ?>
                <li><a href="#rendimiento"><?php echo htmlspecialchars(t("vehiculo.rendimiento")); ?></a></li>
            <?php endif; ?>
            <?php if (count($fotos) > 1): ?>
                <li><a href="#vistas"><?php echo htmlspecialchars(t("vehiculo.vistas")); ?></a></li>
            <?php endif; ?>
            <?php if (count($fichaTecnica) > 0): ?>
                <li><a href="#especificaciones"><?php echo htmlspecialchars(t("vehiculo.especificaciones")); ?></a></li>
            <?php endif; ?>
        </ul>
        <?php if ($rol !== "administrador" && !$vendido): ?>
            <a class="modelo-barra-boton" href="#solicitar"><?php echo htmlspecialchars(t("vehiculo.solicitar_compra")); ?></a>
        <?php endif; ?>
    </nav>

    <!-- ===== 2. escena "Diseño": la foto de perfil se queda fija ===== -->
    <section class="escena" id="diseno" aria-labelledby="titulo-diseno">
        <?php if ($escenaDiseno !== null): ?>
            <!-- alt vacío: la misma foto está en la galería con su nombre -->
            <div class="escena-fondo"><img src="<?php echo htmlspecialchars($escenaDiseno["ruta"]); ?>" alt="" loading="lazy"></div>
        <?php endif; ?>
        <div class="escena-contenido">
            <div class="escena-panel revelar">
                <p class="sobretitulo"><?php echo htmlspecialchars(t("vehiculo.diseno")); ?></p>
                <h2 id="titulo-diseno" class="escena-titulo"><?php echo htmlspecialchars($vehiculo["modelo"]); ?></h2>
                <p><?php echo htmlspecialchars(descripcionVehiculo($vehiculo["id"], $vehiculo["descripcion"])); ?></p>
            </div>
            <?php if (count($fotos) > 1): ?>
                <div class="escena-panel escena-panel-derecha revelar">
                    <p class="sobretitulo"><?php echo htmlspecialchars(t("vehiculo.angulos")); ?></p>
                    <?php
                    // "frontal, perfil, trasera e interior": las vistas que tiene este auto
                    $listaVistas = array_map("mb_strtolower", array_column($fotos, "vista"));
                    $ultimaVista = array_pop($listaVistas);
                    $conector = t("lista.y");
                    // en español: "trasera e interior", no "trasera y interior"
                    if (idiomaActual() === "es" && mb_substr($ultimaVista, 0, 1) === "i") {
                        $conector = "e";
                    }
                    $textoVistas = implode(", ", $listaVistas) . " " . $conector . " " . $ultimaVista;
                    ?>
                    <p><?php echo htmlspecialchars(t("vehiculo.vistas_resumen", count($fotos), $nombreAuto, $textoVistas)); ?></p>
                    <a class="enlace-flecha" href="#vistas"><?php echo htmlspecialchars(t("vehiculo.ver_vistas")); ?> <span aria-hidden="true">→</span></a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($modelo3d !== ""): ?>
        <!-- ===== 3D: el modelo del auto, se gira con el mouse o el dedo =====
             model-viewer es el visor 3D de Google (se carga al final de la página) -->
        <section class="modelo-3d" id="modelo-3d" aria-labelledby="titulo-3d">
            <div class="modelo-3d-cabecera">
                <div>
                    <p class="sobretitulo"><?php echo htmlspecialchars(t("modelo3d.titulo")); ?></p>
                    <h2 id="titulo-3d" class="escena-titulo"><?php echo htmlspecialchars($vehiculo["modelo"]); ?></h2>
                </div>
                <p class="modelo-3d-pista"><?php echo htmlspecialchars(t("modelo3d.pista")); ?></p>
            </div>

            <model-viewer class="modelo-3d-visor"
                          src="<?php echo htmlspecialchars($modelo3d); ?>"
                          alt="<?php echo htmlspecialchars(t("modelo3d.alt", $nombreAuto)); ?>"
                          camera-controls auto-rotate auto-rotate-delay="1500" rotation-per-second="18deg"
                          interaction-prompt="none" touch-action="pan-y" loading="lazy"
                          shadow-intensity="1" shadow-softness="0.8" exposure="1.05"
                          environment-image="neutral" field-of-view="28deg"
                          camera-orbit="<?php echo htmlspecialchars($creditoModelo["angulo"] ?? "-40deg"); ?> 75deg 75%"
                          <?php if ($pinturaModelo !== null): ?>data-pintura="<?php echo htmlspecialchars(json_encode($pinturaModelo)); ?>"<?php endif; ?>>
                <?php if ($fotoPortada !== null): ?>
                    <!-- mientras carga el modelo se ve esta foto; si el visor no puede cargar
                         (por ejemplo, sin internet), la foto se queda en su lugar -->
                    <img slot="poster" class="modelo-3d-foto" src="<?php echo htmlspecialchars($fotoPortada["ruta"]); ?>" alt="">
                <?php endif; ?>
            </model-viewer>

            <div class="modelo-3d-pie">
                <?php if ($sonidoMotor !== ""): ?>
                    <span class="motor-con-texto">
                        <button type="button" class="motor" data-sonido="<?php echo htmlspecialchars($sonidoMotor); ?>"
                                aria-pressed="false" aria-label="<?php echo htmlspecialchars(t("motor.encender_aria", $nombreAuto)); ?>" hidden>
                            <span class="motor-icono" aria-hidden="true"></span>
                            <span class="motor-texto" aria-hidden="true">Engine<br>Start</span>
                        </button>
                        <span class="motor-leyenda" aria-hidden="true"><?php echo htmlspecialchars(t("motor.escucha")); ?></span>
                    </span>
                <?php endif; ?>
                <?php if ($creditoModelo !== null): ?>
                    <!-- crédito que pide la licencia del modelo -->
                    <?php
                    // el texto viene del diccionario ("Modelo 3D %s de %s, licencia %s.");
                    // el primer %s es el enlace, que se pone después de escapar el texto
                    $enlaceModelo = '<a href="' . htmlspecialchars($creditoModelo["enlace"]) . '" target="_blank" rel="noopener">«'
                        . htmlspecialchars($creditoModelo["titulo"] ?? t("modelo3d.ver_original")) . '»</a>';
                    $textoCredito = htmlspecialchars(t("modelo3d.credito", "[enlace]", $creditoModelo["autor"], $creditoModelo["licencia"]));
                    ?>
                    <p class="modelo-3d-credito"><?php echo str_replace("[enlace]", $enlaceModelo, $textoCredito); ?></p>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (isset($videos[1])): ?>
        <!-- video 1 (solo si el archivo existe) -->
        <section class="modelo-video" aria-label="<?php echo htmlspecialchars(t("vehiculo.video_aria", $nombreAuto)); ?>">
            <video class="modelo-video-archivo" muted loop playsinline preload="none"
                   <?php if ($videos[1]["poster"] !== "") { echo 'poster="' . htmlspecialchars($videos[1]["poster"]) . '"'; } ?>>
                <source src="<?php echo htmlspecialchars($videos[1]["video"]); ?>" type="video/mp4">
            </video>
            <?php echo creditoVideo($videos[1]["credito"]); ?>
            <!-- data-texto-*: los textos traducidos que usa vehiculo.js -->
            <button type="button" class="boton-pausa video-pausa"
                    aria-label="<?php echo htmlspecialchars(t("vehiculo.pausar_video")); ?>"
                    data-texto-pausar="<?php echo htmlspecialchars(t("vehiculo.pausar_video")); ?>"
                    data-texto-reproducir="<?php echo htmlspecialchars(t("vehiculo.reproducir_video")); ?>" hidden>
                <span class="icono-pausa" aria-hidden="true"></span>
            </button>
        </section>
    <?php endif; ?>

    <?php if (count($cifras) > 0): ?>
        <!-- ===== 3. escena "Rendimiento": la trasera fija y las cifras encima ===== -->
        <section class="escena escena-oscura" id="rendimiento" aria-labelledby="titulo-rendimiento">
            <?php if ($escenaRendimiento !== null): ?>
                <div class="escena-fondo"><img src="<?php echo htmlspecialchars($escenaRendimiento["ruta"]); ?>" alt="" loading="lazy"></div>
            <?php endif; ?>
            <div class="escena-contenido">
                <div class="escena-panel escena-panel-ancho revelar">
                    <p class="sobretitulo"><?php echo htmlspecialchars(t("vehiculo.rendimiento")); ?></p>
                    <h2 id="titulo-rendimiento" class="escena-titulo"><?php echo htmlspecialchars(t("vehiculo.en_cifras")); ?></h2>
                    <!-- número grande arriba y su nombre abajo -->
                    <dl class="modelo-cifras">
                        <?php foreach ($cifras as $numero => $cifra): ?>
                            <div class="revelar" style="--i: <?php echo $numero; ?>">
                                <dt><?php echo htmlspecialchars($cifra["nombre"]); ?></dt>
                                <dd><?php echo htmlspecialchars($cifra["valor"]); ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                </div>
                <?php if ($motor !== ""): ?>
                    <div class="escena-panel revelar">
                        <p class="sobretitulo"><?php echo htmlspecialchars(t("ficha.motor")); ?></p>
                        <p class="escena-dato"><?php echo htmlspecialchars($motor); ?></p>
                        <?php if ($cilindrada !== ""): ?>
                            <p><?php echo htmlspecialchars(t("vehiculo.cilindrada", $cilindrada)); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (isset($videos[2])): ?>
        <!-- video 2 (solo si el archivo existe) -->
        <section class="modelo-video" aria-label="<?php echo htmlspecialchars(t("vehiculo.video2_aria", $nombreAuto)); ?>">
            <video class="modelo-video-archivo" muted loop playsinline preload="none"
                   <?php if ($videos[2]["poster"] !== "") { echo 'poster="' . htmlspecialchars($videos[2]["poster"]) . '"'; } ?>>
                <source src="<?php echo htmlspecialchars($videos[2]["video"]); ?>" type="video/mp4">
            </video>
            <?php echo creditoVideo($videos[2]["credito"]); ?>
            <button type="button" class="boton-pausa video-pausa"
                    aria-label="<?php echo htmlspecialchars(t("vehiculo.pausar_video")); ?>"
                    data-texto-pausar="<?php echo htmlspecialchars(t("vehiculo.pausar_video")); ?>"
                    data-texto-reproducir="<?php echo htmlspecialchars(t("vehiculo.reproducir_video")); ?>" hidden>
                <span class="icono-pausa" aria-hidden="true"></span>
            </button>
        </section>
    <?php endif; ?>

    <?php if ($escenaInterior !== null && ($traccion !== "" || $peso !== "")): ?>
        <!-- ===== 4. escena "Al volante": el interior fijo ===== -->
        <section class="escena" id="interior" aria-labelledby="titulo-interior">
            <div class="escena-fondo"><img src="<?php echo htmlspecialchars($escenaInterior["ruta"]); ?>" alt="" loading="lazy"></div>
            <div class="escena-contenido">
                <div class="escena-panel escena-panel-derecha revelar">
                    <p class="sobretitulo"><?php echo htmlspecialchars(t("vehiculo.al_volante")); ?></p>
                    <h2 id="titulo-interior" class="escena-titulo"><?php echo htmlspecialchars($escenaInterior["vista"]); ?></h2>
                    <dl class="escena-lista">
                        <?php if ($traccion !== ""): ?>
                            <div><dt><?php echo htmlspecialchars(t("ficha.traccion")); ?></dt><dd><?php echo htmlspecialchars($traccion); ?></dd></div>
                        <?php endif; ?>
                        <?php if ($peso !== ""): ?>
                            <div><dt><?php echo htmlspecialchars(t("ficha.peso")); ?></dt><dd><?php echo htmlspecialchars($peso); ?></dd></div>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (count($fotos) > 1): ?>
        <!-- ===== 5. galería: una foto grande y abajo una miniatura por vista =====
             Sin JavaScript, cada miniatura abre su foto; con JavaScript cambia la foto grande -->
        <section class="seccion seccion-oscura vistas" id="vistas" aria-labelledby="titulo-vistas">
            <div class="contenedor">
                <div class="seccion-cabecera revelar">
                    <div>
                        <p class="sobretitulo"><?php echo htmlspecialchars(t("vehiculo.galeria")); ?></p>
                        <h2 id="titulo-vistas" class="titulo-grande"><?php echo htmlspecialchars(t("vehiculo.todas_vistas")); ?></h2>
                    </div>
                    <!-- el lector de pantalla avisa qué vista se está mostrando -->
                    <p class="visor-estado" aria-live="polite">
                        <span id="visor-nombre"><?php echo htmlspecialchars($fotos[0]["vista"]); ?></span>
                        <span class="visor-cuenta"><span id="visor-numero">1</span> / <?php echo count($fotos); ?></span>
                    </p>
                </div>

                <figure class="visor revelar">
                    <img id="visor-foto" src="<?php echo htmlspecialchars($fotos[0]["ruta"]); ?>"
                         alt="<?php echo htmlspecialchars(t("vehiculo.alt_vista", $nombreAuto, mb_strtolower($fotos[0]["vista"]))); ?>"
                         <?php if ($fotos[0]["ancho"] > 0) { echo 'width="' . $fotos[0]["ancho"] . '" height="' . $fotos[0]["alto"] . '"'; } ?>
                         loading="lazy">
                    <!-- flechas: el JavaScript les quita el hidden -->
                    <button type="button" class="visor-flecha visor-anterior" aria-label="<?php echo htmlspecialchars(t("vehiculo.vista_anterior")); ?>" hidden>
                        <span aria-hidden="true">‹</span>
                    </button>
                    <button type="button" class="visor-flecha visor-siguiente" aria-label="<?php echo htmlspecialchars(t("vehiculo.vista_siguiente")); ?>" hidden>
                        <span aria-hidden="true">›</span>
                    </button>
                </figure>

                <!-- data-alt y data-vista ya van traducidos: vehiculo.js solo los copia -->
                <ul class="visor-miniaturas" aria-label="<?php echo htmlspecialchars(t("vehiculo.elegir_vista")); ?>">
                    <?php foreach ($fotos as $numero => $foto): ?>
                        <li>
                            <a class="visor-miniatura" href="<?php echo htmlspecialchars($foto["ruta"]); ?>"
                               data-alt="<?php echo htmlspecialchars(t("vehiculo.alt_vista", $nombreAuto, mb_strtolower($foto["vista"]))); ?>"
                               data-vista="<?php echo htmlspecialchars($foto["vista"]); ?>"
                               <?php if ($numero === 0) { echo 'aria-current="true"'; } ?>>
                                <img src="<?php echo htmlspecialchars($foto["ruta"]); ?>" alt="" width="160" height="100" loading="lazy">
                                <span><?php echo htmlspecialchars($foto["vista"]); ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <?php if (count($fichaTecnica) > 0): ?>
        <!-- ===== 6. especificaciones técnicas ===== -->
        <section class="seccion ficha-tecnica" id="especificaciones" aria-labelledby="titulo-especificaciones">
            <div class="contenedor">
                <p class="sobretitulo revelar"><?php echo htmlspecialchars(t("vehiculo.ficha_tecnica")); ?></p>
                <h2 id="titulo-especificaciones" class="titulo-grande revelar"><?php echo htmlspecialchars(t("vehiculo.especificaciones_tecnicas")); ?></h2>

                <div class="ficha-tablas">
                    <?php foreach ($tablasTecnicas as $claveTabla => $filas): ?>
                        <?php
                        // solo las filas que tienen dato, para no mostrar tablas vacías
                        $filasConDato = [];
                        foreach ($filas as $fila) {
                            if (isset($fichaTecnica[$fila]) && $fichaTecnica[$fila] !== "") {
                                $filasConDato[$fila] = $fichaTecnica[$fila];
                            }
                        }
                        ?>
                        <?php if (count($filasConDato) > 0): ?>
                            <table class="revelar">
                                <caption><?php echo htmlspecialchars(t($claveTabla)); ?></caption>
                                <tbody>
                                    <?php foreach ($filasConDato as $dato => $valor): ?>
                                        <tr>
                                            <th scope="row"><?php echo htmlspecialchars(nombreDatoFicha($dato)); ?></th>
                                            <td><?php echo htmlspecialchars(datoFichaTraducido($idVehiculo, $dato, $valor)); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <p class="nota"><?php echo htmlspecialchars(t("ficha.nota")); ?></p>
            </div>
        </section>
    <?php endif; ?>

    <!-- ===== 7. solicitud de compra: datos a la izquierda, acción a la derecha ===== -->
    <section class="seccion seccion-oscura solicitar" id="solicitar" aria-labelledby="titulo-solicitar">
        <div class="contenedor solicitar-cuadro">
            <div class="revelar">
                <p class="sobretitulo">Apex Luxury Motors</p>
                <h2 id="titulo-solicitar" class="titulo-grande">
                    <?php echo htmlspecialchars($vendido ? t("vehiculo.vendido_titulo") : t("vehiculo.hazlo_tuyo")); ?>
                </h2>
                <p class="solicitar-precio"><?php echo $precioTexto; ?> <span>USD</span></p>
            </div>

            <div class="solicitar-ficha revelar" style="--i: 1">
                <dl class="escena-lista">
                    <div><dt><?php echo htmlspecialchars(t("dato.marca")); ?></dt><dd><?php echo htmlspecialchars($vehiculo["marca"]); ?></dd></div>
                    <div><dt><?php echo htmlspecialchars(t("dato.modelo")); ?></dt><dd><?php echo htmlspecialchars($vehiculo["modelo"]); ?></dd></div>
                    <div><dt><?php echo htmlspecialchars(t("dato.categoria")); ?></dt><dd><?php echo htmlspecialchars(nombreCategoria($vehiculo["categoria"])); ?></dd></div>
                    <div>
                        <dt><?php echo htmlspecialchars(t("dato.estado")); ?></dt>
                        <dd><?php if ($vendido): ?><span class="vendido"><?php echo htmlspecialchars(t("estado_vehiculo.vendido")); ?></span><?php else: ?><?php echo htmlspecialchars(t("estado_vehiculo.disponible")); ?><?php endif; ?></dd>
                    </div>
                </dl>

                <!-- acción según el rol (el administrador solo lee, no ve nada aquí) -->
                <?php if ($rol !== "administrador"): ?>
                    <div class="accion">
                        <?php if ($vendido): ?>
                            <p class="vendido"><?php echo htmlspecialchars(t("vehiculo.ya_vendido")); ?></p>
                            <a href="catalogo.php"><?php echo htmlspecialchars(t("vehiculo.ver_otros")); ?></a>
                        <?php elseif ($rol === "cliente"): ?>
                            <form method="post" action="../backend/procesos/solicitar.php" id="form-solicitar">
                                <input type="hidden" name="vehiculo_id" value="<?php echo (int) $vehiculo["id"]; ?>">
                                <button type="submit"><?php echo htmlspecialchars(t("vehiculo.solicitar_compra")); ?></button>
                            </form>
                            <script>
                                // para que un doble clic no mande la solicitud dos veces
                                var formSolicitar = document.getElementById("form-solicitar");
                                formSolicitar.addEventListener("submit", function () {
                                    formSolicitar.querySelector("button").disabled = true;
                                });
                                // si vuelven con el botón Atrás del navegador, el botón se activa otra vez
                                window.addEventListener("pageshow", function () {
                                    formSolicitar.querySelector("button").disabled = false;
                                });
                            </script>
                        <?php else: ?>
                            <a href="login.php"><?php echo htmlspecialchars(t("vehiculo.login_para_solicitar")); ?></a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<!-- galería y videos de esta página -->
<script src="/apex/frontend/js/vehiculo.js?v=2"></script>
<?php if ($sonidoMotor !== ""): ?>
    <!-- botón de encendido del motor -->
    <script src="/apex/frontend/js/motor.js?v=1"></script>
<?php endif; ?>
<?php if ($modelo3d !== ""): ?>
    <!-- color de la pintura del modelo 3D, y después el visor 3D de Google (necesita internet) -->
    <script src="/apex/frontend/js/modelo3d.js?v=2"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/@google/model-viewer@4.1.0/dist/model-viewer.min.js"></script>
<?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . "/includes/pie.php"; ?>
