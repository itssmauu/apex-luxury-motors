<?php

session_start();

require_once __DIR__ . '/../funciones/solicitudes.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/catalogo.php');
    exit;
}

if (($_SESSION['rol'] ?? '') !== 'cliente') {
    $_SESSION['mensaje'] = [
        'tipo' => 'error',
        'texto' => 'Debes iniciar sesión como cliente.',
    ];
    header('Location: ../../frontend/login.php');
    exit;
}

$resultado = crearSolicitud((int) ($_POST['vehiculo_id'] ?? 0));

$_SESSION['mensaje'] = [
    'tipo' => $resultado['ok'] ? 'exito' : 'error',
    'texto' => $resultado['mensaje'],
];

$destino = $resultado['ok']
    ? '../../frontend/mis_solicitudes.php'
    : '../../frontend/vehiculo.php?id=' . (int) ($_POST['vehiculo_id'] ?? 0);
header('Location: ' . $destino);
