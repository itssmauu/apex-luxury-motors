<?php
// Nombres traducidos de los valores fijos que vienen de la base
// (categorías y estados). La base y los enlaces siguen usando el valor original;
// esto solo cambia lo que se lee en pantalla. Un valor que no está en la lista se muestra tal cual.
require_once __DIR__ . "/../includes/idioma.php";

// valor exacto de la base => clave del diccionario
function traducirValor($mapa, $valor)
{
    if (!is_string($valor) || !isset($mapa[$valor])) {
        return (string) $valor;
    }
    return t($mapa[$valor]);
}

// las 4 categorías, escritas igual que en la base
function nombreCategoria($valor)
{
    return traducirValor([
        "Superdeportivo"           => "categoria.superdeportivo",
        "Deportivo alemán"         => "categoria.aleman",
        "Deportivo japonés"        => "categoria.japones",
        "Deportivo estadounidense" => "categoria.estadounidense",
    ], $valor);
}

// estado de una solicitud (pendiente, aprobada, rechazada, cancelada)
function nombreEstadoSolicitud($valor)
{
    return traducirValor([
        "pendiente" => "estado_solicitud.pendiente",
        "aprobada"  => "estado_solicitud.aprobada",
        "rechazada" => "estado_solicitud.rechazada",
        "cancelada" => "estado_solicitud.cancelada",
    ], $valor);
}
