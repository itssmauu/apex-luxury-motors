// Detalle del vehículo: galería de vistas y videos.
// Sin JavaScript la página funciona igual: cada miniatura abre su foto y los videos no se reproducen solos.
(function () {
    "use strict";

    var menosMovimiento = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    // ---------- galería ----------
    var foto = document.getElementById("visor-foto");
    var miniaturas = document.querySelectorAll(".visor-miniatura");

    if (foto && miniaturas.length > 1) {
        var nombre = document.getElementById("visor-nombre");
        var numero = document.getElementById("visor-numero");
        var anterior = document.querySelector(".visor-anterior");
        var siguiente = document.querySelector(".visor-siguiente");
        var actual = 0;

        // muestra la vista número "indice" (si se pasa del final, vuelve al principio)
        var mostrar = function (indice) {
            indice = (indice + miniaturas.length) % miniaturas.length;
            if (indice === actual) {
                return;
            }
            var elegida = miniaturas[indice];

            // la foto se desvanece, cambia y vuelve a aparecer
            foto.classList.add("cambiando");
            window.setTimeout(function () {
                foto.src = elegida.getAttribute("href");
                foto.alt = elegida.dataset.alt;
                // la foto nueva puede tener otras medidas: que el navegador las calcule
                foto.removeAttribute("width");
                foto.removeAttribute("height");
            }, menosMovimiento ? 0 : 180);

            miniaturas[actual].removeAttribute("aria-current");
            elegida.setAttribute("aria-current", "true");
            nombre.textContent = elegida.dataset.vista;
            numero.textContent = indice + 1;
            actual = indice;
        };

        // cuando termina de cargar la foto nueva, vuelve a aparecer
        // (y si no carga, también, para que no quede invisible)
        foto.addEventListener("load", function () {
            foto.classList.remove("cambiando");
        });
        foto.addEventListener("error", function () {
            foto.classList.remove("cambiando");
        });

        miniaturas.forEach(function (miniatura, indice) {
            miniatura.addEventListener("click", function (evento) {
                evento.preventDefault();          // no abrir la foto: cambiar la grande
                mostrar(indice);
            });
        });

        anterior.hidden = false;
        siguiente.hidden = false;
        anterior.addEventListener("click", function () { mostrar(actual - 1); });
        siguiente.addEventListener("click", function () { mostrar(actual + 1); });

        // flechas del teclado cuando el foco está en la galería
        document.querySelector(".vistas").addEventListener("keydown", function (evento) {
            if (evento.key === "ArrowLeft") {
                mostrar(actual - 1);
            } else if (evento.key === "ArrowRight") {
                mostrar(actual + 1);
            }
        });

        // en el celular también se puede deslizar el dedo sobre la foto
        var inicioX = null;
        foto.addEventListener("touchstart", function (evento) {
            inicioX = evento.touches[0].clientX;
        }, { passive: true });
        foto.addEventListener("touchend", function (evento) {
            if (inicioX === null) {
                return;
            }
            var distancia = evento.changedTouches[0].clientX - inicioX;
            if (Math.abs(distancia) > 40) {
                mostrar(distancia < 0 ? actual + 1 : actual - 1);
            }
            inicioX = null;
        });
    }

    // ---------- videos ----------
    // se reproducen solo mientras se ven; el botón redondo los pausa
    document.querySelectorAll(".modelo-video").forEach(function (seccion) {
        var video = seccion.querySelector("video");
        var boton = seccion.querySelector(".video-pausa");
        // con menos movimiento, el video empieza en pausa y la persona decide
        var pausadoPorPersona = menosMovimiento;

        var dibujarBoton = function () {
            var enPausa = video.paused;
            boton.classList.toggle("en-pausa", enPausa);
            boton.setAttribute("aria-label", enPausa ? "Reproducir video" : "Pausar video");
        };

        boton.hidden = false;
        boton.addEventListener("click", function () {
            if (video.paused) {
                pausadoPorPersona = false;
                video.play();
            } else {
                pausadoPorPersona = true;
                video.pause();
            }
        });
        video.addEventListener("play", dibujarBoton);
        video.addEventListener("pause", dibujarBoton);
        dibujarBoton();

        if (!("IntersectionObserver" in window)) {
            return;
        }
        new IntersectionObserver(function (entradas) {
            entradas.forEach(function (entrada) {
                if (entrada.isIntersecting && !pausadoPorPersona) {
                    video.play().catch(function () {
                        dibujarBoton();           // el navegador no dejó reproducir: queda el botón
                    });
                } else if (!entrada.isIntersecting) {
                    video.pause();
                }
            });
        }, { threshold: 0.25 }).observe(seccion);
    });
})();
