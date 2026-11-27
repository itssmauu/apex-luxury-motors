<?php
// Pie de página común: enlaces en columnas y una barra final con el copyright.
// Todos los enlaces van a páginas que ya existen.

// rolActual() para la columna "Cuenta" y listarCategorias() para "Por categoría"
require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/vehiculos.php";

$rolPie = rolActual();
$raizPie = "/apex/frontend";

// si la consulta de categorías falla, la columna simplemente no se muestra
$categoriasPie = [];
try {
    $categoriasPie = listarCategorias();
} catch (Throwable $e) {
    $categoriasPie = [];
}
?>
<footer>
    <nav class="pie-columnas" aria-label="Pie de página">
        <div class="pie-columna">
            <h2>Explorar</h2>
            <ul>
                <li><a href="<?php echo $raizPie; ?>/index.php">Inicio</a></li>
                <li><a href="<?php echo $raizPie; ?>/catalogo.php">Catálogo</a></li>
                <li><a href="<?php echo $raizPie; ?>/sobre_nosotros.php">Sobre nosotros</a></li>
            </ul>
        </div>

        <!-- la columna "Cuenta" cambia según quién visita -->
        <div class="pie-columna">
            <h2>Cuenta</h2>
            <ul>
                <?php if ($rolPie === "cliente"): ?>
                    <li><a href="<?php echo $raizPie; ?>/mis_solicitudes.php">Mis solicitudes</a></li>
                    <li><a href="<?php echo $raizPie; ?>/perfil.php">Mi perfil</a></li>
                    <li><a href="/apex/backend/procesos/logout.php">Cerrar sesión</a></li>
                <?php elseif ($rolPie === "administrador"): ?>
                    <li><a href="<?php echo $raizPie; ?>/admin/vehiculos.php">Inventario</a></li>
                    <li><a href="<?php echo $raizPie; ?>/admin/solicitudes.php">Solicitudes</a></li>
                    <li><a href="<?php echo $raizPie; ?>/perfil.php">Mi perfil</a></li>
                    <li><a href="/apex/backend/procesos/logout.php">Cerrar sesión</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $raizPie; ?>/login.php">Iniciar sesión</a></li>
                    <li><a href="<?php echo $raizPie; ?>/registro.php">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- un enlace por categoría al catálogo ya filtrado (se oculta si no hay categorías) -->
        <?php if (count($categoriasPie) > 0): ?>
            <div class="pie-columna">
                <h2>Por categoría</h2>
                <ul>
                    <?php foreach ($categoriasPie as $fila): ?>
                        <li>
                            <a href="<?php echo $raizPie; ?>/catalogo.php?categoria=<?php echo urlencode($fila["categoria"]); ?>">
                                <?php echo htmlspecialchars($fila["categoria"]); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </nav>

    <!-- barra final: copyright y avisos -->
    <div class="pie-legal">
        <p>Copyright © <?php echo date("Y"); ?> Apex Luxury Motors (demo).</p>
        <p>Este sitio usa solo una cookie de sesión necesaria para iniciar sesión.</p>
    </div>
</footer>
<!-- aparición suave al hacer scroll (elementos con la clase "revelar") -->
<script src="/apex/frontend/js/revelar.js?v=1"></script>
</body>
</html>
