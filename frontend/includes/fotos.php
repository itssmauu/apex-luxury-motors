<?php
// Foto principal de un vehículo, para las listas de solicitudes (cliente y administrador).
// Es la primera foto con buena resolución; si ninguna llega, la primera que haya.
// Devuelve la ruta lista para usar en src (/apex/img/...) o null si no tiene fotos.
require_once __DIR__ . "/../../backend/funciones/vehiculos.php";

function fotoPrincipalVehiculo($vehiculoId)
{
    static $guardadas = [];   // un mismo auto puede salir varias veces en la lista
    $vehiculoId = (int) $vehiculoId;
    if (array_key_exists($vehiculoId, $guardadas)) {
        return $guardadas[$vehiculoId];
    }

    $fotos = listarFotosVehiculo($vehiculoId);
    $elegida = null;
    foreach ($fotos as $foto) {
        $archivo = __DIR__ . "/../../" . $foto["ruta"];
        $ancho = is_file($archivo) ? (getimagesize($archivo)[0] ?? 0) : 0;
        if ($ancho >= 1200) {
            $elegida = $foto["ruta"];
            break;
        }
    }
    if ($elegida === null && count($fotos) > 0) {
        $elegida = $fotos[0]["ruta"];
    }

    $guardadas[$vehiculoId] = ($elegida !== null) ? "/apex/" . $elegida : null;
    return $guardadas[$vehiculoId];
}
