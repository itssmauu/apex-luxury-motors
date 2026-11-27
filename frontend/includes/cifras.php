<?php
// Cifras cortas de las especificaciones, para mostrarlas en grande.
// Las usan la página del vehículo (escena "Rendimiento") y el catálogo (cada tarjeta).
// Las cifras se sacan del texto en español y los nombres salen del diccionario (t).
require_once __DIR__ . "/idioma.php";

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

// la cifra corta se saca del texto en español; aquí solo se traduce el "Más de" / "Menos de" del inicio
function cifraTraducida($cifra)
{
    if (strpos($cifra, "Más de ") === 0) {
        return t("cifra.mas_de", substr($cifra, strlen("Más de ")));
    }
    if (strpos($cifra, "Menos de ") === 0) {
        return t("cifra.menos_de", substr($cifra, strlen("Menos de ")));
    }
    return $cifra;
}

// las cifras de un vehículo (solo las que tienen dato), en este orden:
// potencia, aceleración, velocidad máxima y torque.
// "nombre" es el texto largo y "corto" el de las tarjetas del catálogo.
function cifrasDestacadas($ficha)
{
    $cifras = [];

    $potencia = cifraCorta(datoFicha($ficha, "Potencia"), ["(?:PS|CV|bhp|hp)\b", "kW\b"]);
    if ($potencia !== "") {
        $cifras[] = ["valor" => cifraTraducida($potencia), "nombre" => t("cifra.potencia"), "corto" => t("cifra.potencia_corta")];
    }

    $aceleracionTexto = datoFicha($ficha, "Aceleración");
    $aceleracion = cifraCorta($aceleracionTexto, ["s\b"]);
    if ($aceleracion !== "") {
        // la fuente da la cifra de 0 a 60 mph o de 0 a 100 km/h: se dice cuál es
        $tramo = (strpos($aceleracionTexto, "0–60") !== false) ? "0–60 mph" : "0–100 km/h";
        $cifras[] = ["valor" => cifraTraducida($aceleracion), "nombre" => t("cifra.aceleracion", $tramo), "corto" => $tramo];
    }

    $velocidad = cifraCorta(datoFicha($ficha, "Velocidad máxima"), ["km\/h"]);
    if ($velocidad !== "") {
        $cifras[] = ["valor" => cifraTraducida($velocidad), "nombre" => t("cifra.velocidad"), "corto" => t("cifra.velocidad")];
    }

    $torque = cifraCorta(datoFicha($ficha, "Torque"), ["N·m"]);
    if ($torque !== "") {
        $cifras[] = ["valor" => cifraTraducida($torque), "nombre" => t("cifra.torque"), "corto" => t("cifra.torque_corto")];
    }

    return $cifras;
}
