<?php
// muestra el aviso que dejó un proceso del backend y lo borra,
// así no se repite al recargar
if (isset($_SESSION["mensaje"])) {
    $mensaje = $_SESSION["mensaje"];
    unset($_SESSION["mensaje"]);

    // acepta ["tipo" => ..., "texto" => ...] o un texto suelto
    if (is_array($mensaje)) {
        $tipo = $mensaje["tipo"] ?? "error";
        $texto = $mensaje["texto"] ?? "";
    } else {
        $tipo = "error";
        $texto = (string) $mensaje;
    }

    $rolAviso = ($tipo === "exito") ? "status" : "alert";
    echo '<div class="mensaje mensaje-' . htmlspecialchars($tipo) . '" role="' . $rolAviso . '">';
    echo htmlspecialchars($texto);
    echo '</div>';
}