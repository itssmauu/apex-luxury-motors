<?php

session_start();

require_once __DIR__ . '/../funciones/solicitudes.php';

// La cancelación se recibe mediante el formulario de solicitudes.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../frontend/mis_solicitudes.php');
    exit;
}

// La función comprueba que la solicitud pertenezca al cliente y siga pendiente.
$resultado = cancelarSolicitud((int) ($_POST['solicitud_id'] ?? 0));

$_SESSION['mensaje'] = [
    'tipo' => $resultado['ok'] ? 'exito' : 'error',
    'texto' => $resultado['mensaje'],
];

// Vuelve a la lista para mostrar el resultado de la acción.
header('Location: ../../frontend/mis_solicitudes.php');
