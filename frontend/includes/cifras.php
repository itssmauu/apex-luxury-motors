<?php
// Cifras cortas de las especificaciones, para mostrarlas en grande.
// Las usan la página del vehículo (escena "Rendimiento") y el catálogo (cada tarjeta).

// devuelve el dato si existe y no dice "No disponible"
function datoFicha($ficha, $nombre)
{
    if (!isset($ficha[$nombre]) || $ficha[$nombre] === "" || $ficha[$nombre] === "No disponible") {
        return "";
    }
    return $ficha[$nombre];
}

// saca la cifra corta de un texto largo: de "570 CV a 9,000 rpm (≈562 hp)" queda "570 CV".
// Prueba los patrones en orden y devuelve lo primero que encuentre.
function cifraCorta($texto, $patrones)
{
    foreach ($patrones as $patron) {
        if (preg_match('/((?:≈|Más de |Menos de )?\d[\d.,–]*\s?' . $patron . ')/u', $texto, $encontrado)) {
            return $encontrado[1];
        }
    }
    return "";
}

// las cifras de un vehículo (solo las que tienen dato), en este orden:
// potencia, aceleración, velocidad máxima y torque.
// "nombre" es el texto largo y "corto" el de las tarjetas del catálogo.
function cifrasDestacadas($ficha)
{
    $cifras = [];

    $potencia = cifraCorta(datoFicha($ficha, "Potencia"), ["(?:PS|CV|bhp|hp)\b", "kW\b"]);
    if ($potencia !== "") {
        $cifras[] = ["valor" => $potencia, "nombre" => "Potencia máxima", "corto" => "Potencia"];
    }

    $aceleracionTexto = datoFicha($ficha, "Aceleración");
    $aceleracion = cifraCorta($aceleracionTexto, ["s\b"]);
    if ($aceleracion !== "") {
        // la fuente da la cifra de 0 a 60 mph o de 0 a 100 km/h: se dice cuál es
        $tramo = (strpos($aceleracionTexto, "0–60") !== false) ? "0–60 mph" : "0–100 km/h";
        $cifras[] = ["valor" => $aceleracion, "nombre" => "Aceleración " . $tramo, "corto" => $tramo];
    }

    $velocidad = cifraCorta(datoFicha($ficha, "Velocidad máxima"), ["km\/h"]);
    if ($velocidad !== "") {
        $cifras[] = ["valor" => $velocidad, "nombre" => "Velocidad máxima", "corto" => "Velocidad máxima"];
    }

    $torque = cifraCorta(datoFicha($ficha, "Torque"), ["N·m"]);
    if ($torque !== "") {
        $cifras[] = ["valor" => $torque, "nombre" => "Torque máximo", "corto" => "Torque"];
    }

    return $cifras;
}
