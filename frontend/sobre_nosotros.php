<?php
// Sobre nosotros: quiénes somos, cifras, lo que ofrecemos, categorías,
// cómo trabajamos y una invitación final según el rol.
// Reutiliza las secciones de la portada (estilos en estilos.css).
require_once __DIR__ . "/../backend/funciones/acceso.php";
require_once __DIR__ . "/../backend/funciones/vehiculos.php";
require_once __DIR__ . "/includes/idioma.php";
require_once __DIR__ . "/datos/valores.php";

$titulo = t("menu.sobre_nosotros");
$rol = rolActual();

// cifras sacadas de la base, igual que en la portada
$totalModelos = count(listarVehiculos());

// categorías: si la consulta falla, la sección "Explora por categoría" no se muestra
$categorias = [];
try {
    $categorias = listarCategorias();
} catch (Throwable $e) {
    $categorias = [];
}
$totalCategorias = count($categorias);

require_once __DIR__ . "/includes/encabezado.php";
require_once __DIR__ . "/includes/menu.php";
?>

<main class="sobre-pagina">
    <!-- encabezado de la página y cifras -->
    <section class="seccion" aria-labelledby="titulo-sobre">
        <div class="contenedor quienes">
            <div class="revelar">
                <p class="sobretitulo"><?php echo htmlspecialchars(t("menu.sobre_nosotros")); ?></p>
                <h1 id="titulo-sobre" class="titulo-grande"><?php echo htmlspecialchars(t("sobre.h1")); ?></h1>
            </div>
            <div class="quienes-texto revelar" style="--i: 1">
                <p><?php echo htmlspecialchars(t("sobre.p1")); ?></p>
                <p><?php echo htmlspecialchars(t("sobre.p2")); ?></p>
            </div>
        </div>

        <!-- dt es el nombre de la cifra y dd el número; el CSS pone el número arriba -->
        <dl class="contenedor cifras sobre-cifras">
            <div class="revelar" style="--i: 0">
                <dt><?php echo htmlspecialchars(t("cifra.modelos")); ?></dt>
                <dd><?php echo $totalModelos; ?></dd>
            </div>
            <div class="revelar" style="--i: 1">
                <dt><?php echo htmlspecialchars(t("cifra.categorias")); ?></dt>
                <dd><?php echo $totalCategorias; ?></dd>
            </div>
            <div class="revelar" style="--i: 2">
                <dt><?php echo htmlspecialchars(t("cifra.kilometros")); ?></dt>
                <dd>0</dd>
            </div>
        </dl>
    </section>

    <!-- lo que ofrecemos: tres bloques (no son pasos, por eso van sin número) -->
    <section class="seccion seccion-oscura" aria-labelledby="titulo-ofrecemos">
        <div class="contenedor">
            <h2 id="titulo-ofrecemos" class="titulo-grande revelar"><?php echo htmlspecialchars(t("sobre.ofrecemos")); ?></h2>
            <ul class="pasos">
                <li class="revelar" style="--i: 0">
                    <h3><?php echo htmlspecialchars(t("sobre.catalogo_claro")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.catalogo_claro_texto")); ?></p>
                </li>
                <li class="revelar" style="--i: 1">
                    <h3><?php echo htmlspecialchars(t("sobre.autos_nuevos")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.autos_nuevos_texto")); ?></p>
                </li>
                <li class="revelar" style="--i: 2">
                    <h3><?php echo htmlspecialchars(t("sobre.en_linea")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.en_linea_texto")); ?></p>
                </li>
            </ul>
        </div>
    </section>

    <!-- explora por categoría (se oculta si no hay categorías).
         El enlace usa el valor de la base; solo el texto se traduce -->
    <?php if ($totalCategorias > 0): ?>
        <section class="seccion" aria-labelledby="titulo-categorias">
            <div class="contenedor">
                <h2 id="titulo-categorias" class="titulo-grande revelar"><?php echo htmlspecialchars(t("sobre.explora")); ?></h2>
                <ul class="sobre-categorias">
                    <?php foreach ($categorias as $n => $fila): ?>
                        <li class="revelar" style="--i: <?php echo $n; ?>">
                            <a class="boton-borde" href="catalogo.php?categoria=<?php echo urlencode($fila["categoria"]); ?>">
                                <?php echo htmlspecialchars(nombreCategoria($fila["categoria"])); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <!-- cómo trabajamos: tres pasos -->
    <section class="seccion seccion-oscura" aria-labelledby="titulo-trabajamos">
        <div class="contenedor">
            <h2 id="titulo-trabajamos" class="titulo-grande revelar"><?php echo htmlspecialchars(t("sobre.trabajamos")); ?></h2>
            <ol class="pasos">
                <li class="revelar" style="--i: 0">
                    <span class="paso-numero" aria-hidden="true">01</span>
                    <h3><?php echo htmlspecialchars(t("paso.elige")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.elige_texto")); ?></p>
                </li>
                <li class="revelar" style="--i: 1">
                    <span class="paso-numero" aria-hidden="true">02</span>
                    <h3><?php echo htmlspecialchars(t("paso.solicita")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.solicita_texto")); ?></p>
                </li>
                <li class="revelar" style="--i: 2">
                    <span class="paso-numero" aria-hidden="true">03</span>
                    <h3><?php echo htmlspecialchars(t("sobre.recibe")); ?></h3>
                    <p><?php echo htmlspecialchars(t("sobre.recibe_texto")); ?></p>
                </li>
            </ol>
        </div>
    </section>

    <!-- invitación final: cambia según quién visita -->
    <section class="seccion llamado sobre-llamado" aria-labelledby="titulo-llamado">
        <div class="contenedor revelar">
            <?php if ($rol === "administrador"): ?>
                <h2 id="titulo-llamado" class="titulo-grande"><?php echo htmlspecialchars(t("llamado.admin_titulo")); ?></h2>
                <div class="llamado-botones">
                    <a class="boton" href="admin/solicitudes.php"><?php echo htmlspecialchars(t("llamado.ver_solicitudes")); ?></a>
                    <a class="boton-borde" href="admin/vehiculos.php"><?php echo htmlspecialchars(t("llamado.ir_inventario")); ?></a>
                </div>
            <?php elseif ($rol === "cliente"): ?>
                <h2 id="titulo-llamado" class="titulo-grande"><?php echo htmlspecialchars(t("llamado.visitante_titulo")); ?></h2>
                <div class="llamado-botones">
                    <a class="boton" href="mis_solicitudes.php"><?php echo htmlspecialchars(t("menu.mis_solicitudes")); ?></a>
                    <a class="boton-borde" href="catalogo.php"><?php echo htmlspecialchars(t("comun.ver_catalogo")); ?></a>
                </div>
            <?php else: ?>
                <h2 id="titulo-llamado" class="titulo-grande"><?php echo htmlspecialchars(t("llamado.visitante_titulo")); ?></h2>
                <div class="llamado-botones">
                    <a class="boton" href="registro.php"><?php echo htmlspecialchars(t("comun.crear_cuenta")); ?></a>
                    <a class="boton-borde" href="catalogo.php"><?php echo htmlspecialchars(t("comun.ver_catalogo")); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . "/includes/pie.php"; ?>
