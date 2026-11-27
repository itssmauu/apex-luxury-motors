<?php
// Sobre nosotros: quiénes somos, cifras, lo que ofrecemos, categorías,
// cómo trabajamos y una invitación final según el rol.
// Reutiliza las secciones de la portada (estilos en estilos.css).
require_once __DIR__ . "/../backend/funciones/acceso.php";
require_once __DIR__ . "/../backend/funciones/vehiculos.php";

$titulo = "Sobre nosotros";
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
                <p class="sobretitulo">Sobre nosotros</p>
                <h1 id="titulo-sobre" class="titulo-grande">Autos deportivos y de lujo, todos nuevos</h1>
            </div>
            <div class="quienes-texto revelar" style="--i: 1">
                <p>Apex Luxury Motors es una concesionaria de autos deportivos y de lujo nuevos, todos con cero kilómetros. Reunimos superdeportivos y deportivos de Alemania, Japón y Estados Unidos en un solo catálogo.</p>
                <p>Cada auto tiene su precio a la vista, sus fotos, su descripción y su ficha técnica. Envías tu solicitud de compra desde tu cuenta y nuestro equipo la revisa y te responde con su decisión y un comentario.</p>
            </div>
        </div>

        <!-- dt es el nombre de la cifra y dd el número; el CSS pone el número arriba -->
        <dl class="contenedor cifras sobre-cifras">
            <div class="revelar" style="--i: 0">
                <dt>Modelos en el catálogo</dt>
                <dd><?php echo $totalModelos; ?></dd>
            </div>
            <div class="revelar" style="--i: 1">
                <dt>Categorías</dt>
                <dd><?php echo $totalCategorias; ?></dd>
            </div>
            <div class="revelar" style="--i: 2">
                <dt>Kilómetros en cada auto</dt>
                <dd>0</dd>
            </div>
        </dl>
    </section>

    <!-- lo que ofrecemos: tres bloques (no son pasos, por eso van sin número) -->
    <section class="seccion seccion-oscura" aria-labelledby="titulo-ofrecemos">
        <div class="contenedor">
            <h2 id="titulo-ofrecemos" class="titulo-grande revelar">Lo que ofrecemos</h2>
            <ul class="pasos">
                <li class="revelar" style="--i: 0">
                    <h3>Catálogo claro</h3>
                    <p>Filtra por marca, categoría o precio máximo y encuentra tu auto sin perder tiempo.</p>
                </li>
                <li class="revelar" style="--i: 1">
                    <h3>Autos nuevos</h3>
                    <p>Todos los vehículos son nuevos, con cero kilómetros.</p>
                </li>
                <li class="revelar" style="--i: 2">
                    <h3>Solicitud en línea</h3>
                    <p>Envías tu solicitud desde la ficha del auto y sigues su estado en «Mis solicitudes».</p>
                </li>
            </ul>
        </div>
    </section>

    <!-- explora por categoría (se oculta si no hay categorías) -->
    <?php if ($totalCategorias > 0): ?>
        <section class="seccion" aria-labelledby="titulo-categorias">
            <div class="contenedor">
                <h2 id="titulo-categorias" class="titulo-grande revelar">Explora por categoría</h2>
                <ul class="sobre-categorias">
                    <?php foreach ($categorias as $n => $fila): ?>
                        <li class="revelar" style="--i: <?php echo $n; ?>">
                            <a class="boton-borde" href="catalogo.php?categoria=<?php echo urlencode($fila["categoria"]); ?>">
                                <?php echo htmlspecialchars($fila["categoria"]); ?>
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
            <h2 id="titulo-trabajamos" class="titulo-grande revelar">Cómo trabajamos</h2>
            <ol class="pasos">
                <li class="revelar" style="--i: 0">
                    <span class="paso-numero" aria-hidden="true">01</span>
                    <h3>Elige</h3>
                    <p>Explora el catálogo y revisa la ficha de cada auto.</p>
                </li>
                <li class="revelar" style="--i: 1">
                    <span class="paso-numero" aria-hidden="true">02</span>
                    <h3>Solicita</h3>
                    <p>Inicia sesión y envía tu solicitud de compra.</p>
                </li>
                <li class="revelar" style="--i: 2">
                    <span class="paso-numero" aria-hidden="true">03</span>
                    <h3>Recibe respuesta</h3>
                    <p>Nuestro equipo aprueba o rechaza tu solicitud y deja un comentario que puedes leer en «Mis solicitudes».</p>
                </li>
            </ol>
        </div>
    </section>

    <!-- invitación final: cambia según quién visita -->
    <section class="seccion llamado sobre-llamado" aria-labelledby="titulo-llamado">
        <div class="contenedor revelar">
            <?php if ($rol === "administrador"): ?>
                <h2 id="titulo-llamado" class="titulo-grande">Panel de administración</h2>
                <div class="llamado-botones">
                    <a class="boton" href="admin/solicitudes.php">Ver solicitudes</a>
                    <a class="boton-borde" href="admin/vehiculos.php">Ir al inventario</a>
                </div>
            <?php elseif ($rol === "cliente"): ?>
                <h2 id="titulo-llamado" class="titulo-grande">Tu próximo auto empieza aquí</h2>
                <div class="llamado-botones">
                    <a class="boton" href="mis_solicitudes.php">Mis solicitudes</a>
                    <a class="boton-borde" href="catalogo.php">Ver el catálogo</a>
                </div>
            <?php else: ?>
                <h2 id="titulo-llamado" class="titulo-grande">Tu próximo auto empieza aquí</h2>
                <div class="llamado-botones">
                    <a class="boton" href="registro.php">Crear cuenta</a>
                    <a class="boton-borde" href="catalogo.php">Ver el catálogo</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . "/includes/pie.php"; ?>
