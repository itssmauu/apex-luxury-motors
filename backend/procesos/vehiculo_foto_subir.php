<?php
// Recibe la foto del formulario del administrador (RF8)

require_once __DIR__ . "/../funciones/acceso.php";
require_once __DIR__ . "/../funciones/vehiculos.php";

requerirRol("administrador");

// solo se entra por el formulario
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /apex/frontend/admin/vehiculos.php");
    exit;
}

$vehiculoId = (int) ($_POST["vehiculo_id"] ?? 0);

$resultado = subirFotoVehiculo($vehiculoId, $_FILES["foto"] ?? null);

$_SESSION["mensaje"] = [
    "tipo"  => $resultado["ok"] ? "exito" : "error",
    "texto" => $resultado["mensaje"]
];

// vuelve al formulario del mismo vehículo
header("Location: /apex/frontend/admin/vehiculo_form.php?id=" . $vehiculoId);
exit;