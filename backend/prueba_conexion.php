<?php
require_once __DIR__ . "/config/conexion.php";

echo "Conexión correcta.<br>";

// contar los vehículos
$consulta = $conexion->query("SELECT COUNT(*) AS total FROM vehiculos");
$fila = $consulta->fetch();
echo "Vehículos en la base: " . $fila["total"] . "<br><br>";

// listarlos
$consulta = $conexion->query("SELECT marca, modelo, precio FROM vehiculos ORDER BY id");
foreach ($consulta as $v) {
    echo $v["marca"] . " " . $v["modelo"] . " - $" . number_format($v["precio"], 2) . "<br>";
}