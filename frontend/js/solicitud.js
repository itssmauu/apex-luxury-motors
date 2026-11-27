// Detalle de una solicitud (admin): cuántos caracteres quedan en el comentario.
// Sin JavaScript el campo igual tiene maxlength="255".
(function () {
    "use strict";

    var campo = document.getElementById("comentario");
    var cuenta = document.getElementById("comentario-cuenta");
    if (!campo || !cuenta) {
        return;
    }

    var limite = parseInt(campo.getAttribute("maxlength"), 10) || 255;

    var actualizar = function () {
        var quedan = limite - campo.value.length;
        // textos traducidos que manda PHP (data-texto-*); el español queda de respaldo
        var uno = cuenta.dataset.textoUno || "Queda 1 carácter.";
        var varios = cuenta.dataset.textoVarios || "Quedan %s caracteres.";
        cuenta.textContent = (quedan === 1) ? uno : varios.replace("%s", quedan);
    };

    campo.addEventListener("input", actualizar);
    actualizar();
})();
