<?php

require_once __DIR__ . '/../config/conexion.php';

// Lista los vehículos activos y aplica los filtros que se hayan recibido.
function listarVehiculos($marca = '', $categoria = '', $precioMax = null)
{
    global $conexion;

    $sql = "SELECT v.id, v.marca, v.modelo, v.precio, v.categoria,
                   v.descripcion, v.estado, v.activo,
                   (SELECT f.ruta FROM fotos f
                    WHERE f.vehiculo_id = v.id
                    ORDER BY f.orden ASC, f.id ASC
                    LIMIT 1) AS foto
            FROM vehiculos v
            WHERE v.activo = 1";
    $parametros = [];

    if ($marca !== '') {
        $sql .= " AND v.marca = ?";
        $parametros[] = $marca;
    }

    if ($categoria !== '') {
        $sql .= " AND v.categoria = ?";
        $parametros[] = $categoria;
    }

    if ($precioMax !== null && $precioMax !== '') {
        $sql .= " AND v.precio <= ?";
        $parametros[] = $precioMax;
    }

    $sql .= " ORDER BY v.id ASC";

    $consulta = $conexion->prepare($sql);
    $consulta->execute($parametros);
    return $consulta->fetchAll();
}

// Devuelve las marcas activas para llenar el filtro del catálogo.
function listarMarcas()
{
    global $conexion;

    $consulta = $conexion->query(
        "SELECT DISTINCT marca FROM vehiculos
         WHERE activo = 1
         ORDER BY marca ASC"
    );
    return $consulta->fetchAll();
}

// Devuelve las categorías activas para llenar el filtro del catálogo.
function listarCategorias()
{
    global $conexion;

    $consulta = $conexion->query(
        "SELECT DISTINCT categoria FROM vehiculos
         WHERE activo = 1
         ORDER BY categoria ASC"
    );
    return $consulta->fetchAll();
}

// Busca un vehículo activo y agrega todas sus fotos ordenadas.
function obtenerVehiculo($id)
{
    global $conexion;

    $consulta = $conexion->prepare(
        "SELECT id, marca, modelo, precio, categoria, descripcion, estado, activo
         FROM vehiculos
         WHERE id = ? AND activo = 1"
    );
    $consulta->execute([(int) $id]);
    $vehiculo = $consulta->fetch();

    if (!$vehiculo) {
        return null;
    }

    $consultaFotos = $conexion->prepare(
        "SELECT id, ruta, orden
         FROM fotos
         WHERE vehiculo_id = ?
         ORDER BY orden ASC, id ASC"
    );
    $consultaFotos->execute([(int) $id]);
    $vehiculo['fotos'] = $consultaFotos->fetchAll();

    return $vehiculo;
}

// ---------- RF8: gestión de vehículos (administrador) ----------

function listarInventario()
{
    global $conexion;

    // todos los vehículos, incluso vendidos y desactivados
    $consulta = $conexion->query(
        "SELECT v.id, v.marca, v.modelo, v.precio, v.categoria,
                v.estado, v.activo,
                (SELECT f.ruta FROM fotos f
                 WHERE f.vehiculo_id = v.id
                 ORDER BY f.orden ASC, f.id ASC
                 LIMIT 1) AS foto
         FROM vehiculos v
         ORDER BY v.id ASC"
    );
    return $consulta->fetchAll();
}

function obtenerVehiculoAdmin($id)
{
    global $conexion;

    // igual que obtenerVehiculo, pero sin filtrar por activo
    $consulta = $conexion->prepare(
        "SELECT id, marca, modelo, precio, categoria, descripcion, estado, activo
         FROM vehiculos
         WHERE id = ?"
    );
    $consulta->execute([(int) $id]);
    $vehiculo = $consulta->fetch();

    return $vehiculo ? $vehiculo : null;
}

function guardarVehiculo($id, $marca, $modelo, $precio, $categoria, $descripcion, $estado)
{
    global $conexion;

    $id = (int) $id;
    $marca = trim($marca);
    $modelo = trim($modelo);
    $precio = trim($precio);
    $categoria = trim($categoria);
    $descripcion = trim($descripcion);

    if ($marca === '' || $modelo === '' || $categoria === '') {
        return ['ok' => false, 'mensaje' => 'Marca, modelo y categoría son obligatorios.'];
    }

    if (!is_numeric($precio) || $precio <= 0) {
        return ['ok' => false, 'mensaje' => 'El precio debe ser un número mayor que 0.'];
    }

    if ($estado !== 'disponible' && $estado !== 'vendido') {
        return ['ok' => false, 'mensaje' => 'El estado no es válido.'];
    }

    // sin id = vehículo nuevo
    if ($id === 0) {
        $insertar = $conexion->prepare(
            "INSERT INTO vehiculos (marca, modelo, precio, categoria, descripcion, estado, activo)
             VALUES (?, ?, ?, ?, ?, ?, 1)"
        );
        $insertar->execute([$marca, $modelo, $precio, $categoria, $descripcion, $estado]);

        return [
            'ok' => true,
            'mensaje' => 'Vehículo creado.',
            'id' => (int) $conexion->lastInsertId()
        ];
    }

    // con id = editar; primero ver que exista
    $existe = $conexion->prepare("SELECT id FROM vehiculos WHERE id = ?");
    $existe->execute([$id]);
    if (!$existe->fetch()) {
        return ['ok' => false, 'mensaje' => 'El vehículo no existe.'];
    }

    $actualizar = $conexion->prepare(
        "UPDATE vehiculos
         SET marca = ?, modelo = ?, precio = ?, categoria = ?, descripcion = ?, estado = ?
         WHERE id = ?"
    );
    $actualizar->execute([$marca, $modelo, $precio, $categoria, $descripcion, $estado, $id]);

    return ['ok' => true, 'mensaje' => 'Vehículo actualizado.', 'id' => $id];
}

function desactivarVehiculo($id)
{
    global $conexion;

    $id = (int) $id;

    $consulta = $conexion->prepare("SELECT activo FROM vehiculos WHERE id = ?");
    $consulta->execute([$id]);
    $vehiculo = $consulta->fetch();

    if (!$vehiculo) {
        return ['ok' => false, 'mensaje' => 'El vehículo no existe.'];
    }

    if ((int) $vehiculo['activo'] === 0) {
        return ['ok' => false, 'mensaje' => 'Ese vehículo ya está desactivado.'];
    }

    // no se desactiva si hay clientes esperando respuesta
    $pendientes = $conexion->prepare(
        "SELECT COUNT(*) FROM solicitudes WHERE vehiculo_id = ? AND estado = 'pendiente'"
    );
    $pendientes->execute([$id]);
    if ($pendientes->fetchColumn() > 0) {
        return ['ok' => false, 'mensaje' => 'Tiene solicitudes pendientes. Atiéndelas primero.'];
    }

    // no se borra: se marca como inactivo para conservar el historial
    $actualizar = $conexion->prepare("UPDATE vehiculos SET activo = 0 WHERE id = ?");
    $actualizar->execute([$id]);

    return ['ok' => true, 'mensaje' => 'Vehículo desactivado.'];
}

// Fotos de un vehículo, en orden (para mostrarlas en el formulario del admin)
function listarFotosVehiculo($vehiculoId) {
    global $conexion;

    $consulta = $conexion->prepare(
        "SELECT id, ruta, orden FROM fotos WHERE vehiculo_id = ? ORDER BY orden ASC, id ASC"
    );
    $consulta->execute([(int) $vehiculoId]);
    return $consulta->fetchAll();
}

// Sube una foto y la asocia a un vehículo (solo administrador)
function subirFotoVehiculo($vehiculoId, $archivo) {
    global $conexion;

    // el rol se verifica en el servidor
    if (($_SESSION["rol"] ?? "") !== "administrador") {
        return ["ok" => false, "mensaje" => "Acceso denegado."];
    }

    // el vehículo debe existir
    $consulta = $conexion->prepare("SELECT id FROM vehiculos WHERE id = ?");
    $consulta->execute([(int) $vehiculoId]);
    if (!$consulta->fetch()) {
        return ["ok" => false, "mensaje" => "El vehículo no existe."];
    }

    // el navegador debe haber enviado un archivo
    if (!is_array($archivo) || ($archivo["error"] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ["ok" => false, "mensaje" => "Selecciona una foto."];
    }
    if ($archivo["error"] !== UPLOAD_ERR_OK) {
        return ["ok" => false, "mensaje" => "No se pudo subir la foto. Intenta de nuevo."];
    }

    // tamaño máximo: 5 MB
    if ($archivo["size"] > 5 * 1024 * 1024) {
        return ["ok" => false, "mensaje" => "La foto no puede pesar más de 5 MB."];
    }

    // el tipo se lee del contenido real, no del nombre del archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipo = finfo_file($finfo, $archivo["tmp_name"]);
    finfo_close($finfo);

    $extensiones = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp"
    ];

    if (!isset($extensiones[$tipo]) || getimagesize($archivo["tmp_name"]) === false) {
        return ["ok" => false, "mensaje" => "Solo se permiten fotos JPG, PNG o WEBP."];
    }

    // nombre propio y único: nunca se usa el nombre que envía el usuario
    $carpeta = __DIR__ . "/../../img/subidas/";
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $nombre = "vehiculo_" . (int) $vehiculoId . "_" . bin2hex(random_bytes(6)) . "." . $extensiones[$tipo];

    if (!move_uploaded_file($archivo["tmp_name"], $carpeta . $nombre)) {
        return ["ok" => false, "mensaje" => "No se pudo guardar la foto."];
    }

    // siguiente posición dentro de las fotos de ese vehículo
    $consulta = $conexion->prepare("SELECT COALESCE(MAX(orden), 0) + 1 FROM fotos WHERE vehiculo_id = ?");
    $consulta->execute([(int) $vehiculoId]);
    $orden = (int) $consulta->fetchColumn();

    $consulta = $conexion->prepare("INSERT INTO fotos (vehiculo_id, ruta, orden) VALUES (?, ?, ?)");
    $consulta->execute([(int) $vehiculoId, "img/subidas/" . $nombre, $orden]);

    return ["ok" => true, "mensaje" => "Foto subida."];
}