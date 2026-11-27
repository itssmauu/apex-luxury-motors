<?php
// API de vehículos: /apex/backend/api/vehiculos.php
// Entidad principal del sistema. Usa las mismas funciones que el sitio (funciones/vehiculos.php).
//
//   GET    vehiculos.php                → catálogo (filtros: marca, categoria, precio_max)   público
//   GET    vehiculos.php?id=8           → un vehículo con todas sus fotos                     público
//   POST   vehiculos.php                → crear un vehículo                                    administrador
//   PUT    vehiculos.php?id=8           → actualizar uno o varios datos                        administrador
//   DELETE vehiculos.php?id=8           → desactivar (no se borra, para conservar el historial) administrador
//
// La documentación completa con ejemplos está en backend/API.md

require_once __DIR__ . "/respuesta.php";
require_once __DIR__ . "/../funciones/vehiculos.php";

// datos que se pueden enviar al crear o actualizar
const CAMPOS_VEHICULO = ["marca", "modelo", "precio", "categoria", "descripcion", "estado"];

// un vehículo tal como sale de la API: tipos correctos y rutas de fotos listas para usar
function formatearVehiculo($v, $fotos = null)
{
    $vehiculo = [
        "id"          => (int) $v["id"],
        "marca"       => $v["marca"],
        "modelo"      => $v["modelo"],
        "precio"      => (float) $v["precio"],
        "categoria"   => $v["categoria"],
        "descripcion" => $v["descripcion"] ?? "",
        "estado"      => $v["estado"],
        "activo"      => (int) $v["activo"] === 1,
    ];
    if (array_key_exists("foto", $v)) {
        $vehiculo["foto"] = ($v["foto"] !== null && $v["foto"] !== "") ? "/apex/" . $v["foto"] : null;
    }
    if ($fotos !== null) {
        $vehiculo["fotos"] = array_map(function ($f) {
            return ["id" => (int) $f["id"], "ruta" => "/apex/" . $f["ruta"], "orden" => (int) $f["orden"]];
        }, $fotos);
    }
    return $vehiculo;
}

// revisa los datos de un vehículo completo; devuelve [campo => problema] (vacío si todo está bien)
function validarVehiculo($v)
{
    $errores = [];
    $textos = ["marca" => 50, "modelo" => 80, "categoria" => 50];
    foreach ($textos as $campo => $maximo) {
        if (!is_string($v[$campo]) || trim($v[$campo]) === "") {
            $errores[$campo] = "Obligatorio.";
        } elseif (mb_strlen(trim($v[$campo])) > $maximo) {
            $errores[$campo] = "Máximo " . $maximo . " caracteres.";
        }
    }
    if (!is_numeric($v["precio"]) || (float) $v["precio"] <= 0) {
        $errores["precio"] = "Debe ser un número mayor que 0.";
    } elseif ((float) $v["precio"] >= 10000000000) {
        $errores["precio"] = "Es demasiado alto.";
    }
    if (!is_string($v["descripcion"])) {
        $errores["descripcion"] = "Debe ser texto.";
    }
    if (!in_array($v["estado"], ["disponible", "vendido"], true)) {
        $errores["estado"] = "Debe ser \"disponible\" o \"vendido\".";
    }
    return $errores;
}

// rechaza campos que la API no conoce (por ejemplo "activo" o un "id" en el cuerpo)
function revisarCamposDesconocidos($datos)
{
    $desconocidos = array_diff(array_keys($datos), CAMPOS_VEHICULO);
    if (count($desconocidos) > 0) {
        $campos = [];
        foreach ($desconocidos as $campo) {
            $campos[$campo] = "Este campo no se puede enviar.";
        }
        responderError(400, "datos_invalidos", "Hay campos que la API no acepta.", $campos);
    }
}

// guarda con la función del backend (crear si $id es 0) y responde el vehículo guardado
function guardarYResponder($id, $v, $codigoHttp)
{
    $resultado = guardarVehiculo(
        $id,
        trim($v["marca"]),
        trim($v["modelo"]),
        (string) $v["precio"],
        trim($v["categoria"]),
        trim($v["descripcion"]),
        $v["estado"]
    );
    if (!$resultado["ok"]) {
        responderError(400, "datos_invalidos", $resultado["mensaje"]);
    }
    $guardado = obtenerVehiculoAdmin($resultado["id"]);
    if ($codigoHttp === 201) {
        header("Location: /apex/backend/api/vehiculos.php?id=" . (int) $resultado["id"]);
    }
    responderExito($codigoHttp, formatearVehiculo($guardado, listarFotosVehiculo($resultado["id"])), $resultado["mensaje"]);
}

try {
    $id = leerId();
    $esAdministrador = (($_SESSION["rol"] ?? "") === "administrador");

    switch ($_SERVER["REQUEST_METHOD"]) {
        case "GET":
            if ($id !== null) {
                // el público solo ve vehículos activos; el administrador ve también los desactivados
                $vehiculo = $esAdministrador ? obtenerVehiculoAdmin($id) : obtenerVehiculo($id);
                if (!$vehiculo) {
                    responderError(404, "no_encontrado", "No existe un vehículo con el id " . $id . ".");
                }
                responderExito(200, formatearVehiculo($vehiculo, listarFotosVehiculo($id)));
            }

            // colección: filtros opcionales, iguales a los del catálogo
            $marca = isset($_GET["marca"]) && is_string($_GET["marca"]) ? trim($_GET["marca"]) : "";
            $categoria = isset($_GET["categoria"]) && is_string($_GET["categoria"]) ? trim($_GET["categoria"]) : "";
            $precioMax = isset($_GET["precio_max"]) && is_string($_GET["precio_max"]) ? trim($_GET["precio_max"]) : "";
            if ($precioMax !== "" && (!is_numeric($precioMax) || (float) $precioMax <= 0)) {
                responderError(400, "datos_invalidos", "El filtro precio_max debe ser un número mayor que 0.", [
                    "precio_max" => "Debe ser un número mayor que 0.",
                ]);
            }
            $lista = listarVehiculos($marca, $categoria, $precioMax === "" ? null : $precioMax);
            $datos = array_map("formatearVehiculo", $lista);
            responder(200, ["ok" => true, "total" => count($datos), "datos" => $datos]);

        case "POST":
            requerirAdministrador();
            if ($id !== null) {
                responderError(400, "datos_invalidos", "Para crear no se envía id; para editar usa PUT con ?id=.");
            }
            $datos = leerJson();
            revisarCamposDesconocidos($datos);

            $nuevo = [
                "marca"       => $datos["marca"] ?? "",
                "modelo"      => $datos["modelo"] ?? "",
                "precio"      => $datos["precio"] ?? "",
                "categoria"   => $datos["categoria"] ?? "",
                "descripcion" => $datos["descripcion"] ?? "",
                "estado"      => $datos["estado"] ?? "disponible",
            ];
            $errores = validarVehiculo($nuevo);
            if (count($errores) > 0) {
                responderError(400, "datos_invalidos", "Revisa los datos del vehículo.", $errores);
            }
            guardarYResponder(0, $nuevo, 201);

        case "PUT":
            requerirAdministrador();
            if ($id === null) {
                responderError(400, "id_requerido", "Indica qué vehículo actualizar: vehiculos.php?id=8");
            }
            $actual = obtenerVehiculoAdmin($id);
            if (!$actual) {
                responderError(404, "no_encontrado", "No existe un vehículo con el id " . $id . ".");
            }
            $datos = leerJson();
            revisarCamposDesconocidos($datos);
            if (count($datos) === 0) {
                responderError(400, "datos_invalidos", "Envía al menos un dato para actualizar.");
            }

            // solo cambia lo que llega; lo demás queda como estaba
            $actualizado = [];
            foreach (CAMPOS_VEHICULO as $campo) {
                $actualizado[$campo] = array_key_exists($campo, $datos) ? $datos[$campo] : $actual[$campo];
            }
            if ($actualizado["descripcion"] === null) {
                $actualizado["descripcion"] = "";
            }
            $errores = validarVehiculo($actualizado);
            if (count($errores) > 0) {
                responderError(400, "datos_invalidos", "Revisa los datos del vehículo.", $errores);
            }
            guardarYResponder($id, $actualizado, 200);

        case "DELETE":
            requerirAdministrador();
            if ($id === null) {
                responderError(400, "id_requerido", "Indica qué vehículo desactivar: vehiculos.php?id=8");
            }
            if (!obtenerVehiculoAdmin($id)) {
                responderError(404, "no_encontrado", "No existe un vehículo con el id " . $id . ".");
            }

            // no se borra: se desactiva, así no se pierde el historial de solicitudes
            $resultado = desactivarVehiculo($id);
            if (!$resultado["ok"]) {
                // ya desactivado o con solicitudes pendientes: el estado actual no lo permite
                $codigo = (strpos($resultado["mensaje"], "pendientes") !== false) ? "solicitudes_pendientes" : "ya_desactivado";
                responderError(409, $codigo, $resultado["mensaje"]);
            }
            responderExito(200, formatearVehiculo(obtenerVehiculoAdmin($id)), $resultado["mensaje"]);

        default:
            metodoNoPermitido(["GET", "POST", "PUT", "DELETE"]);
    }
} catch (Throwable $e) {
    // sin detalles técnicos hacia afuera
    responderError(500, "error_servidor", "Ocurrió un error en el servidor. Intenta de nuevo.");
}
