<?php
require_once __DIR__ . "/../funciones/acceso.php";
require_once __DIR__ . "/../funciones/usuarios.php";

cerrarSesion();

header("Location: /apex/frontend/index.php");
exit;