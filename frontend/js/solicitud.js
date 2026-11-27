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
        cuenta.textContent = (quedan === 1) ? "Queda 1 carácter." : "Quedan " + quedan + " caracteres.";
    };

    campo.addEventListener("input", actualizar);
    actualizar();
})();
