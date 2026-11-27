// Selector de idioma del pie (<details class="pie-idioma">).
// Sin JavaScript ya funciona; esto solo agrega dos comodidades:
// cerrar el panel con Esc (el foco vuelve al botón) y al hacer clic fuera.
(function () {
    "use strict";

    var selector = document.querySelector(".pie-idioma");
    if (!selector) {
        return;
    }
    var resumen = selector.querySelector("summary");

    document.addEventListener("keydown", function (evento) {
        if (evento.key === "Escape" && selector.open) {
            selector.open = false;
            resumen.focus();
        }
    });

    document.addEventListener("click", function (evento) {
        if (selector.open && !selector.contains(evento.target)) {
            selector.open = false;
        }
    });
})();
