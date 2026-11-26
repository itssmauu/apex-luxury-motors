<?php

require_once __DIR__ . '/../config/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Registra una solicitud pendiente si el cliente y el vehículo son válidos.
function crearSolicitud($vehiculoId)
{
    global $conexion;

    if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'cliente') {
        return ['ok' => false, 'mensaje' => 'Debes iniciar sesión como cliente.'];
    }

    $consulta = $conexion->prepare(
        "SELECT id, estado, activo
         FROM vehiculos
         WHERE id = ?"
    );
    $consulta->execute([(int) $vehiculoId]);
    $vehiculo = $consulta->fetch();

    if (!$vehiculo || (int) $vehiculo['activo'] !== 1) {
        return ['ok' => false, 'mensaje' => 'El vehículo no está disponible.'];
    }

    if ($vehiculo['estado'] !== 'disponible') {
        return ['ok' => false, 'mensaje' => 'El vehículo ya fue vendido.'];
    }

    $consulta = $conexion->prepare(
        "SELECT id FROM solicitudes
         WHERE usuario_id = ? AND vehiculo_id = ? AND estado = 'pendiente'"
    );
    $consulta->execute([
        (int) $_SESSION['usuario_id'],
        (int) $vehiculoId,
    ]);

    if ($consulta->fetch()) {
        return ['ok' => false, 'mensaje' => 'Ya tienes una solicitud pendiente para este vehículo.'];
    }

    $consulta = $conexion->prepare(
        "INSERT INTO solicitudes (usuario_id, vehiculo_id, fecha, estado)
         VALUES (?, ?, CURRENT_DATE, 'pendiente')"
    );
    $consulta->execute([
        (int) $_SESSION['usuario_id'],
        (int) $vehiculoId,
    ]);

    return ['ok' => true, 'mensaje' => 'Tu solicitud fue registrada.'];
}

// Lista únicamente las solicitudes del cliente que inició sesión.
function listarMisSolicitudes()
{
    global $conexion;

    if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'cliente') {
        return [];
    }

    $consulta = $conexion->prepare(
        "SELECT s.id, s.fecha, s.estado, s.comentario,
                v.id AS vehiculo_id, v.marca, v.modelo
         FROM solicitudes s
         INNER JOIN vehiculos v ON v.id = s.vehiculo_id
         WHERE s.usuario_id = ?
         ORDER BY s.fecha DESC, s.id DESC"
    );
    $consulta->execute([(int) $_SESSION['usuario_id']]);
    return $consulta->fetchAll();
}

// Cancela una solicitud propia solo cuando todavía está pendiente.
function cancelarSolicitud($id)
{
    global $conexion;

    if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'cliente') {
        return ['ok' => false, 'mensaje' => 'Debes iniciar sesión como cliente.'];
    }

    $consulta = $conexion->prepare(
        "SELECT id FROM solicitudes
         WHERE id = ? AND usuario_id = ? AND estado = 'pendiente'"
    );
    $consulta->execute([
        (int) $id,
        (int) $_SESSION['usuario_id'],
    ]);
    $solicitud = $consulta->fetch();

    if (!$solicitud) {
        return ['ok' => false, 'mensaje' => 'La solicitud no puede cancelarse.'];
    }

    $consulta = $conexion->prepare(
        "UPDATE solicitudes
         SET estado = 'cancelada'
         WHERE id = ?"
    );
    $consulta->execute([(int) $id]);

    return ['ok' => true, 'mensaje' => 'La solicitud fue cancelada.'];
}

// Lista las solicitudes pendientes para que el administrador las revise.
function listarPendientes()
{
    global $conexion;

    if (($_SESSION['rol'] ?? '') !== 'administrador') {
        return [];
    }

    $consulta = $conexion->query(
        "SELECT s.id, s.fecha, s.estado, s.comentario,
                u.id AS usuario_id, u.nombre, u.correo,
                v.id AS vehiculo_id, v.marca, v.modelo
         FROM solicitudes s
         INNER JOIN usuarios u ON u.id = s.usuario_id
         INNER JOIN vehiculos v ON v.id = s.vehiculo_id
         WHERE s.estado = 'pendiente'
         ORDER BY s.fecha ASC, s.id ASC"
    );
    return $consulta->fetchAll();
}

// Obtiene la información de una solicitud para mostrar su detalle.
function obtenerSolicitud($id)
{
    global $conexion;

    if (($_SESSION['rol'] ?? '') !== 'administrador') {
        return null;
    }

    $consulta = $conexion->prepare(
        "SELECT s.id, s.fecha, s.estado, s.comentario,
                u.id AS usuario_id, u.nombre, u.correo, u.telefono,
                v.id AS vehiculo_id, v.marca, v.modelo, v.precio, v.estado AS estado_vehiculo
         FROM solicitudes s
         INNER JOIN usuarios u ON u.id = s.usuario_id
         INNER JOIN vehiculos v ON v.id = s.vehiculo_id
         WHERE s.id = ?"
    );
    $consulta->execute([(int) $id]);
    return $consulta->fetch() ?: null;
}

// Aprueba o rechaza una solicitud pendiente y guarda el comentario.
function atenderSolicitud($id, $decision, $comentario)
{
    global $conexion;

    if (($_SESSION['rol'] ?? '') !== 'administrador') {
        return ['ok' => false, 'mensaje' => 'No tienes permiso para realizar esta acción.'];
    }

    if (!in_array($decision, ['aprobada', 'rechazada'], true)) {
        return ['ok' => false, 'mensaje' => 'La decisión no es válida.'];
    }

    $consulta = $conexion->prepare(
        "SELECT id, vehiculo_id
         FROM solicitudes
         WHERE id = ? AND estado = 'pendiente'"
    );
    $consulta->execute([(int) $id]);
    $solicitud = $consulta->fetch();

    if (!$solicitud) {
        return ['ok' => false, 'mensaje' => 'La solicitud ya no está pendiente.'];
    }

    $comentario = trim($comentario);
    if (strlen($comentario) > 255) {
        return ['ok' => false, 'mensaje' => 'El comentario es demasiado largo.'];
    }
    if ($comentario === '') {
        $comentario = null;
    }

    try {
        // La decisión y el estado del vehículo deben guardarse juntos.
        $conexion->beginTransaction();

        if ($decision === 'aprobada') {
            // el vehículo debe seguir disponible
            $consulta = $conexion->prepare("SELECT estado FROM vehiculos WHERE id = ? FOR UPDATE");
            $consulta->execute([(int) $solicitud['vehiculo_id']]);
            if ($consulta->fetchColumn() !== 'disponible') {
                $conexion->rollBack();
                return ['ok' => false, 'mensaje' => 'El vehículo ya fue vendido.'];
            }

            $consulta = $conexion->prepare(
                "UPDATE solicitudes
                SET estado = 'rechazada', comentario = 'El vehículo fue vendido a otro cliente.'
                WHERE vehiculo_id = ? AND estado = 'pendiente' AND id <> ?"
            );
            $consulta->execute([(int) $solicitud['vehiculo_id'], (int) $id]);
        }

        $consulta = $conexion->prepare(
            "UPDATE solicitudes
             SET estado = ?, comentario = ?
             WHERE id = ?"
        );
        $consulta->execute([
            $decision,
            $comentario,
            (int) $id,
        ]);

        if ($decision === 'aprobada') {
            $consulta = $conexion->prepare(
                "UPDATE vehiculos
                 SET estado = 'vendido'
                 WHERE id = ?"
            );
            $consulta->execute([(int) $solicitud['vehiculo_id']]);
        }

        $conexion->commit();
    } catch (PDOException $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        return ['ok' => false, 'mensaje' => 'No se pudo actualizar la solicitud.'];
    }

    $texto = $decision === 'aprobada'
        ? 'La solicitud fue aprobada.'
        : 'La solicitud fue rechazada.';
    return ['ok' => true, 'mensaje' => $texto];
}
