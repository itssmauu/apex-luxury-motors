<?php

require_once __DIR__ . '/../config/conexion.php';

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