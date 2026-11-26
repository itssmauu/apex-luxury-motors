<?php

session_start();

require_once __DIR__ . '/../funciones/solicitudes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/admin/solicitudes.php');
    exit;
}

if (($_SESSION['rol'] ?? '') !== 'administrador') {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'No tienes permiso para realizar esta acción.',
    ];
    header('Location: ../../frontend/index.php');
    exit;
}

$resultado = atenderSolicitud(
    (int) ($_POST['solicitud_id'] ?? 0),
    $_POST['decision'] ?? '',
    $_POST['comentario'] ?? ''
);

$_SESSION['mensaje'] = [
    'tipo' => $resultado['ok'] ? 'exito' : 'error',
    'texto' => $resultado['mensaje'],
];

header('Location: ../../frontend/admin/solicitudes.php');
