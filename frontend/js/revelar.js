// Aparición suave al hacer scroll, hacia abajo y hacia arriba.
// Lo que tiene la clase "revelar" aparece al entrar en la pantalla
// y se vuelve a ocultar al salir, así se anima otra vez al regresar.
(function () {
    "use strict";

    var elementos = document.querySelectorAll(".revelar");
    if (elementos.length === 0) {
        return;
    }

    // navegador viejo o persona que pidió menos movimiento: todo visible de una vez
    var menosMovimiento = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (!("IntersectionObserver" in window) || menosMovimiento) {
        elementos.forEach(function (elemento) {
            elemento.classList.add("visible");
        });
        return;
    }

    var observador = new IntersectionObserver(function (entradas) {
        entradas.forEach(function (entrada) {
            // true al entrar, false al salir (por arriba o por abajo)
            entrada.target.classList.toggle("visible", entrada.isIntersecting);
        });
    }, {
        threshold: 0.12,                   // con que se vea un 12 % ya cuenta
        rootMargin: "0px 0px -8% 0px"      // y un poco antes del borde de abajo
    });

    elementos.forEach(function (elemento) {
        observador.observe(elemento);
    });
})();