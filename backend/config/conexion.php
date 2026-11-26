<?php
// datos para conectarse a la base (los de XAMPP por defecto)
$servidor = "localhost";
$usuario = "root";
$clave = "";
$base = "apex_luxury_motors";

try {
    $conexion = new PDO("mysql:host=$servidor;dbname=$base;charset=utf8mb4", $usuario, $clave);

    // que los errores se lancen como excepciones
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // que cada fila llegue como arreglo con los nombres de las columnas
    $conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // mensaje general, sin mostrar datos de la conexión
    die("No se pudo conectar a la base de datos.");
}