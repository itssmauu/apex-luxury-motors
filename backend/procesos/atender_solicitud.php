<?php

session_start();

require_once __DIR__ . '/../funciones/solicitudes.php';

// Esta acción se envía desde el formulario de decisión del administrador.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/admin/solicitudes.php');
    exit;
}

// Se verifica el rol en el servidor antes de atender la solicitud.
if (($_SESSION['rol'] ?? '') !== 'administrador') {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'No tienes permiso para realizar esta acción.',
    ];
    header('Location: ../../frontend/index.php');
    exit;
}

// La función valida la decisión y actualiza la solicitud.
$resultado = atenderSolicitud(
    (int) ($_POST['solicitud_id'] ?? 0),
    $_POST['decision'] ?? '',
    $_POST['comentario'] ?? ''
);

$_SESSION['mensaje'] = [
    'tipo' => $resultado['ok'] ? 'exito' : 'error',
    'texto' => $resultado['mensaje'],
];

// Regresa a la lista de pendientes con el resultado.
header('Location: ../../frontend/admin/solicitudes.php');
