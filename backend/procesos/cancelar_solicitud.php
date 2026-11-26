<?php

session_start();

require_once __DIR__ . '/../funciones/solicitudes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/mis_solicitudes.php');
    exit;
}

$resultado = cancelarSolicitud((int) ($_POST['solicitud_id'] ?? 0));

$_SESSION['mensaje'] = [
    'tipo' => $resultado['ok'] ? 'exito' : 'error',
    'texto' => $resultado['mensaje'],
];

header('Location: ../../frontend/mis_solicitudes.php');
