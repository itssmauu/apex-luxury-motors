<?php
// Guarda las preferencias de idioma y de cookies y vuelve a la página de origen.
// No muestra nada: solo recibe formularios POST.
//   accion=idioma  + lang          -> cambia el idioma
//   accion=cookies + valor=aceptar | rechazar | reabrir
//   volver = la página a la que se regresa (solo dentro de /apex/frontend/)
require_once __DIR__ . "/includes/idioma.php";

const DURACION_COOKIE = 60 * 60 * 24 * 365;   // 12 meses
const DESTINO_POR_DEFECTO = "/apex/frontend/index.php";

// la cookie solo viaja por HTTPS si la página se abrió con HTTPS
function conexionSegura()
{
    return (!empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off")
        || (($_SERVER["SERVER_PORT"] ?? "") == 443);
}

function guardarCookie($nombre, $valor)
{
    setcookie($nombre, $valor, [
        "expires"  => time() + DURACION_COOKIE,
        "path"     => "/",
        "secure"   => conexionSegura(),
        "httponly" => true,
        "samesite" => "Lax",
    ]);
}

function borrarCookie($nombre)
{
    setcookie($nombre, "", [
        "expires"  => time() - 3600,
        "path"     => "/",
        "secure"   => conexionSegura(),
        "httponly" => true,
        "samesite" => "Lax",
    ]);
}

// solo se vuelve a páginas del sitio; cualquier otra cosa va al inicio
function destinoSeguro($volver)
{
    if (!is_string($volver) || strpos($volver, "/apex/frontend/") !== 0) {
        return DESTINO_POR_DEFECTO;
    }
    foreach (["//", "://", "\\", "\r", "\n"] as $prohibido) {
        if (strpos($volver, $prohibido) !== false) {
            return DESTINO_POR_DEFECTO;
        }
    }
    // ningún otro carácter de control
    if (preg_match('/[\x00-\x1F\x7F]/', $volver)) {
        return DESTINO_POR_DEFECTO;
    }
    return $volver;
}

// si no es POST, no se hace nada
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . DESTINO_POR_DEFECTO);
    exit;
}

$accion = $_POST["accion"] ?? "";
$destino = destinoSeguro($_POST["volver"] ?? "");

if ($accion === "idioma") {
    $lang = $_POST["lang"] ?? "";
    // un código que no está en la lista se ignora
    if (is_string($lang) && isset(idiomasDisponibles()[$lang])) {
        $_SESSION["idioma"] = $lang;
        if (decisionCookies() === "aceptadas") {
            guardarCookie("apex_idioma", $lang);
        }
    }
} elseif ($accion === "cookies") {
    $valor = $_POST["valor"] ?? "";
    if ($valor === "aceptar") {
        guardarCookie("apex_cookies", "aceptadas");
        guardarCookie("apex_idioma", idiomaActual());
    } elseif ($valor === "rechazar") {
        guardarCookie("apex_cookies", "rechazadas");
        if (isset($_COOKIE["apex_idioma"])) {
            borrarCookie("apex_idioma");
        }
    } elseif ($valor === "reabrir") {
        // se borra la decisión: el aviso vuelve a aparecer
        borrarCookie("apex_cookies");
        borrarCookie("apex_idioma");
    }
}

header("Location: " . $destino, true, 303);
exit;
