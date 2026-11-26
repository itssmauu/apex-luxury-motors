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
