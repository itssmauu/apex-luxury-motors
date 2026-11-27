<?php
// Funciones que comparten los archivos de la API (backend/api/).
// Toda respuesta es JSON con la misma forma:
//   éxito: {"ok": true, "datos": ..., "mensaje": "..."}
//   error: {"ok": false, "error": {"codigo": "...", "mensaje": "...", "campos": {...}}}
// "codigo" es fijo (sirve para programar); "mensaje" es para leerlo una persona.

// la sesión es la misma del sitio: quien inició sesión en la página también la tiene aquí
require_once __DIR__ . "/../funciones/acceso.php";

header("Content-Type: application/json; charset=utf-8");
header("X-Content-Type-Options: nosniff");
header("Cache-Control: no-store");

// envía la respuesta y termina
function responder($codigoHttp, $cuerpo)
{
    http_response_code($codigoHttp);
    echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

function responderExito($codigoHttp, $datos, $mensaje = null)
{
    $cuerpo = ["ok" => true];
    if ($mensaje !== null) {
        $cuerpo["mensaje"] = $mensaje;
    }
    $cuerpo["datos"] = $datos;
    responder($codigoHttp, $cuerpo);
}

function responderError($codigoHttp, $codigo, $mensaje, $campos = null)
{
    $error = ["codigo" => $codigo, "mensaje" => $mensaje];
    if ($campos !== null) {
        $error["campos"] = $campos;   // qué dato está mal y por qué
    }
    responder($codigoHttp, ["ok" => false, "error" => $error]);
}

// lee el cuerpo JSON de POST y PUT; si no es JSON válido, responde 400 o 415
function leerJson()
{
    // pedir JSON también evita que otra página envíe formularios a la API con la sesión del usuario
    $tipo = $_SERVER["CONTENT_TYPE"] ?? "";
    if (stripos($tipo, "application/json") !== 0) {
        responderError(415, "tipo_no_soportado", "Envía los datos en JSON (Content-Type: application/json).");
    }

    $texto = file_get_contents("php://input");
    $datos = json_decode($texto, true);
    // tiene que ser un objeto ({...}), no una lista ([...]) ni un valor suelto
    $esLista = is_array($datos) && count($datos) > 0 && array_keys($datos) === range(0, count($datos) - 1);
    if (!is_array($datos) || $esLista) {
        responderError(400, "json_invalido", "El cuerpo debe ser un objeto JSON, por ejemplo {\"marca\": \"Ferrari\"}.");
    }
    return $datos;
}

// las operaciones restringidas: 401 sin sesión, 403 con otro rol
function requerirAdministrador()
{
    if (!isset($_SESSION["usuario_id"])) {
        responderError(401, "no_autenticado", "Inicia sesión como administrador para usar esta operación.");
    }
    if (($_SESSION["rol"] ?? "") !== "administrador") {
        responderError(403, "sin_permiso", "Solo el administrador puede usar esta operación.");
    }
}

// el id de la dirección (?id=8): null si no viene; 400 si no es un entero positivo
function leerId()
{
    if (!isset($_GET["id"]) || $_GET["id"] === "") {
        return null;
    }
    $id = $_GET["id"];
    if (!is_string($id) || !ctype_digit($id) || (int) $id <= 0) {
        responderError(400, "id_invalido", "El id debe ser un número entero mayor que 0.");
    }
    return (int) $id;
}

// métodos que no existen en un recurso
function metodoNoPermitido($permitidos)
{
    header("Allow: " . implode(", ", $permitidos));
    responderError(405, "metodo_no_permitido", "Este recurso acepta: " . implode(", ", $permitidos) . ".");
}
