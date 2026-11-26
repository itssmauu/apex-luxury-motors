<?php

session_start();

require_once __DIR__ . '/../funciones/solicitudes.php';

// Esta acción solo acepta datos enviados desde el formulario.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/catalogo.php');
    exit;
}

// Solo un cliente con sesión puede crear una solicitud.
if (($_SESSION['rol'] ?? '') !== 'cliente') {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'Debes iniciar sesión como cliente.',
    ];
    header('Location: ../../frontend/login.php');
    exit;
}

// Guarda el resultado para mostrarlo al regresar a la página.
$resultado = crearSolicitud((int) ($_POST['vehiculo_id'] ?? 0));

$_SESSION['mensaje'] = [
    'tipo' => $resultado['ok'] ? 'exito' : 'error',
    'texto' => $resultado['mensaje'],
];

$destino = $resultado['ok']
    ? '../../frontend/mis_solicitudes.php'
    : '../../frontend/vehiculo.php?id=' . (int) ($_POST['vehiculo_id'] ?? 0);
// Si falla, regresa al vehículo para que el cliente vea el mensaje.
header('Location: ' . $destino);
