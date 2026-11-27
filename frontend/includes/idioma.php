<?php
// Idiomas de la interfaz: español (por defecto), inglés, portugués y francés.
// El idioma elegido se guarda en la sesión; en una cookie solo si la persona aceptó cookies.
// Los textos están en frontend/idiomas/<código>.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// los idiomas que hay, cada uno con su nombre en su propio idioma
function idiomasDisponibles()
{
    return [
        "es" => "Español",
        "en" => "English",
        "pt" => "Português",
        "fr" => "Français",
    ];
}

// qué decidió la persona sobre las cookies: "aceptadas", "rechazadas" o "" (todavía nada)
function decisionCookies()
{
    $valor = $_COOKIE["apex_cookies"] ?? "";
    if ($valor === "aceptadas" || $valor === "rechazadas") {
        return $valor;
    }
    return "";
}

function cookiesAceptadas()
{
    return decisionCookies() === "aceptadas";
}

// idioma en uso: primero la cookie (solo si aceptó cookies), después la sesión y si no, español.
// Siempre se revisa que sea uno de la lista
function idiomaActual()
{
    $lista = idiomasDisponibles();

    $deCookie = $_COOKIE["apex_idioma"] ?? "";
    if (cookiesAceptadas() && is_string($deCookie) && isset($lista[$deCookie])) {
        return $deCookie;
    }

    $deSesion = $_SESSION["idioma"] ?? "";
    if (is_string($deSesion) && isset($lista[$deSesion])) {
        return $deSesion;
    }

    return "es";
}

// carga el diccionario de un idioma una sola vez por página
function diccionarioIdioma($codigo)
{
    static $cargados = [];

    // solo códigos de la lista (así nunca se arma una ruta rara)
    if (!isset(idiomasDisponibles()[$codigo])) {
        return [];
    }

    if (!isset($cargados[$codigo])) {
        $archivo = __DIR__ . "/../idiomas/" . $codigo . ".php";
        $cargados[$codigo] = is_file($archivo) ? (array) require $archivo : [];
    }
    return $cargados[$codigo];
}

// texto traducido: si falta en el idioma actual usa el español, y si falta ahí, la clave.
// Con valores, los mete en los %s del texto. Devuelve texto plano: al imprimir, usar htmlspecialchars
function t($clave, ...$valores)
{
    $texto = diccionarioIdioma(idiomaActual())[$clave]
        ?? diccionarioIdioma("es")[$clave]
        ?? $clave;

    if (count($valores) > 0) {
        $texto = vsprintf($texto, $valores);
    }
    return $texto;
}

// singular o plural: una clave para 1 y otra para los demás números.
// En francés el 0 también va en singular ("0 modèle")
function tPlural($cantidad, $claveUno, $claveVarios)
{
    $uno = ($cantidad == 1) || (idiomaActual() === "fr" && $cantidad == 0);
    return t($uno ? $claveUno : $claveVarios, $cantidad);
}
