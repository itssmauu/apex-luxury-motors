<?php
require_once __DIR__ . "/../funciones/acceso.php";
require_once __DIR__ . "/../funciones/vehiculos.php";

requerirRol("administrador");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /apex/frontend/admin/vehiculos.php");
    exit;
}

$resultado = desactivarVehiculo($_POST["id"] ?? 0);

$_SESSION["mensaje"] = [
    "tipo" => $resultado["ok"] ? "exito" : "error",
    "texto" => $resultado["mensaje"]
];

header("Location: /apex/frontend/admin/vehiculos.php");
exit;