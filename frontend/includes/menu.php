<?php
// el menú cambia según quién esté usando la página
// (el saludo y "Cerrar sesión" están arriba a la derecha, en encabezado.php)
$rolMenu = rolActual();
$raiz = "/apex/frontend";
?>
<nav aria-label="Menú principal">
    <ul>
        <li><a href="<?php echo $raiz; ?>/index.php">Inicio</a></li>
        <li><a href="<?php echo $raiz; ?>/catalogo.php">Catálogo</a></li>
        <li><a href="<?php echo $raiz; ?>/sobre_nosotros.php">Sobre nosotros</a></li>

        <?php if ($rolMenu === "visitante"): ?>
            <li><a href="<?php echo $raiz; ?>/login.php">Iniciar sesión</a></li>
            <li><a href="<?php echo $raiz; ?>/registro.php">Registrarse</a></li>

        <?php elseif ($rolMenu === "cliente"): ?>
            <li><a href="<?php echo $raiz; ?>/mis_solicitudes.php">Mis solicitudes</a></li>
            <li><a href="<?php echo $raiz; ?>/perfil.php">Mi perfil</a></li>

        <?php elseif ($rolMenu === "administrador"): ?>
            <li><a href="<?php echo $raiz; ?>/admin/vehiculos.php">Inventario</a></li>
            <li><a href="<?php echo $raiz; ?>/admin/solicitudes.php">Solicitudes</a></li>
            <li><a href="<?php echo $raiz; ?>/perfil.php">Mi perfil</a></li>
        <?php endif; ?>
    </ul>
</nav>

<?php require __DIR__ . "/mensajes.php"; ?>