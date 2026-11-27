// Catálogo: los autos destacados de arriba cambian cada 6 segundos, con un fundido.
// Hay un botón para pausar. Con menos movimiento se queda fijo el primero.
(function () {
    "use strict";

    var diapositivas = document.querySelectorAll(".gama-diapositiva");
    var boton = document.querySelector(".gama-pausa");
    if (diapositivas.length < 2 || !boton) {
        return;
    }

    var menosMovimiento = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    var actual = 0;
    var enPausa = menosMovimiento;
    var reloj = null;

    var mostrar = function (indice) {
        var anterior = diapositivas[actual];
        var siguiente = diapositivas[indice];

        anterior.classList.remove("activa");
        anterior.setAttribute("aria-hidden", "true");
        anterior.querySelector("a").setAttribute("tabindex", "-1");

        siguiente.classList.add("activa");
        siguiente.removeAttribute("aria-hidden");
        siguiente.querySelector("a").removeAttribute("tabindex");

        // la foto de la siguiente se pide antes de que haga falta
        var despues = diapositivas[(indice + 1) % diapositivas.length].querySelector("img");
        if (despues) {
            despues.loading = "eager";
        }
        actual = indice;
    };

    var arrancar = function () {
        reloj = window.setInterval(function () {
            mostrar((actual + 1) % diapositivas.length);
        }, 6000);
    };

    var dibujarBoton = function () {
        boton.classList.toggle("en-pausa", enPausa);
        // los textos traducidos vienen de PHP (data-texto-*); el español queda de respaldo
        boton.setAttribute("aria-label", enPausa
            ? (boton.dataset.textoReproducir || "Reproducir destacados")
            : (boton.dataset.textoPausar || "Pausar destacados"));
    };

    boton.hidden = false;
    boton.addEventListener("click", function () {
        enPausa = !enPausa;
        if (enPausa) {
            window.clearInterval(reloj);
        } else {
            arrancar();
        }
        dibujarBoton();
    });

    dibujarBoton();
    if (!enPausa) {
        arrancar();
    }
})();
