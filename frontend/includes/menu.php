<?php
// el menú cambia según quién esté usando la página
// (el saludo y "Cerrar sesión" están arriba a la derecha, en encabezado.php)
// los textos salen de t() (frontend/idiomas/<idioma>.php)
$rolMenu = rolActual();
$raiz = "/apex/frontend";
?>
<nav aria-label="<?php echo htmlspecialchars(t("menu.aria")); ?>">
    <ul>
        <li><a href="<?php echo $raiz; ?>/index.php"><?php echo htmlspecialchars(t("menu.inicio")); ?></a></li>
        <li><a href="<?php echo $raiz; ?>/catalogo.php"><?php echo htmlspecialchars(t("menu.catalogo")); ?></a></li>
        <li><a href="<?php echo $raiz; ?>/sobre_nosotros.php"><?php echo htmlspecialchars(t("menu.sobre_nosotros")); ?></a></li>

        <?php if ($rolMenu === "visitante"): ?>
            <li><a href="<?php echo $raiz; ?>/login.php"><?php echo htmlspecialchars(t("menu.iniciar_sesion")); ?></a></li>
            <li><a href="<?php echo $raiz; ?>/registro.php"><?php echo htmlspecialchars(t("menu.registrarse")); ?></a></li>

        <?php elseif ($rolMenu === "cliente"): ?>
            <li><a href="<?php echo $raiz; ?>/mis_solicitudes.php"><?php echo htmlspecialchars(t("menu.mis_solicitudes")); ?></a></li>
            <li><a href="<?php echo $raiz; ?>/perfil.php"><?php echo htmlspecialchars(t("menu.mi_perfil")); ?></a></li>

        <?php elseif ($rolMenu === "administrador"): ?>
            <li><a href="<?php echo $raiz; ?>/admin/vehiculos.php"><?php echo htmlspecialchars(t("menu.inventario")); ?></a></li>
            <li><a href="<?php echo $raiz; ?>/admin/solicitudes.php"><?php echo htmlspecialchars(t("menu.solicitudes")); ?></a></li>
            <li><a href="<?php echo $raiz; ?>/perfil.php"><?php echo htmlspecialchars(t("menu.mi_perfil")); ?></a></li>
        <?php endif; ?>
    </ul>
</nav>

<?php require __DIR__ . "/mensajes.php"; ?>
