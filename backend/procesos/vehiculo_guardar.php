<?php
require_once __DIR__ . "/../funciones/acceso.php";
require_once __DIR__ . "/../funciones/vehiculos.php";

requerirRol("administrador");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /apex/frontend/admin/vehiculos.php");
    exit;
}

$id = (int) ($_POST["id"] ?? 0);

$resultado = guardarVehiculo(
    $id,
    $_POST["marca"] ?? "",
    $_POST["modelo"] ?? "",
    $_POST["precio"] ?? "",
    $_POST["categoria"] ?? "",
    $_POST["descripcion"] ?? "",
    $_POST["estado"] ?? "disponible"
);

$_SESSION["mensaje"] = [
    "tipo" => $resultado["ok"] ? "exito" : "error",
    "texto" => $resultado["mensaje"]
];

if ($resultado["ok"]) {
    header("Location: /apex/frontend/admin/vehiculos.php");
} else {
    // volver al formulario (con el id si estaba editando)
    $destino = "/apex/frontend/admin/vehiculo_form.php";
    if ($id > 0) {
        $destino .= "?id=" . $id;
    }
    header("Location: " . $destino);
}
exit;