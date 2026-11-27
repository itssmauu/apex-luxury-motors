// Agrandar y achicar con el scroll (página "Sobre nosotros").
// Cada elemento con data-zoom recibe la variable CSS --zoom, de 0 a 1:
// sube de 0 a 1 mientras entra en la pantalla, se queda en 1 y baja otra vez al salir por arriba.
// El CSS decide qué hace cada uno con ese número (el video se abre, las cifras crecen...).
// También reproduce cada video solo cuando se ve, con su botón de pausa.
(function () {
    "use strict";

    var elementos = document.querySelectorAll("[data-zoom]");
    var menosMovimiento = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    // ---------- --zoom según la posición en la pantalla ----------
    var pedido = false;

    var calcular = function () {
        pedido = false;
        var alto = window.innerHeight;
        elementos.forEach(function (elemento) {
            var caja = elemento.getBoundingClientRect();
            // 0: el borde de arriba entra por abajo; 1: el borde de abajo sale por arriba
            var p = (alto - caja.top) / (alto + caja.height);
            p = Math.min(1, Math.max(0, p));

            var zoom;
            if (p < 0.4) {
                zoom = p / 0.4;                 // entrando: crece
            } else if (p > 0.75) {
                zoom = 1 - (p - 0.75) / 0.25;   // saliendo: se achica
            } else {
                zoom = 1;
            }
            zoom = zoom * zoom * (3 - 2 * zoom);   // curva suave al empezar y al terminar
            elemento.style.setProperty("--zoom", zoom.toFixed(3));
        });
    };

    var pedir = function () {
        if (!pedido) {
            pedido = true;
            window.requestAnimationFrame(calcular);
        }
    };

    // con menos movimiento todo queda en su tamaño normal (--zoom: 1 en el CSS)
    if (elementos.length > 0 && !menosMovimiento) {
        window.addEventListener("scroll", pedir, { passive: true });
        window.addEventListener("resize", pedir);
        calcular();
    }

    // ---------- videos: cada uno se reproduce solo cuando se ve ----------
    // Cada sección tiene un video (clase video-al-ver) y su botón de pausa (clase video-pausa).
    document.querySelectorAll(".video-al-ver").forEach(function (video) {
        var seccion = video.closest("section");
        var boton = seccion ? seccion.querySelector(".video-pausa") : null;
        var enPausa = menosMovimiento;   // con menos movimiento empieza en pausa
        var visible = false;

        var actualizar = function () {
            if (visible && !enPausa) {
                video.play().catch(function () {});
            } else {
                video.pause();
            }
        };

        if (boton) {
            var dibujarBoton = function () {
                boton.classList.toggle("en-pausa", enPausa);
                boton.setAttribute("aria-label", enPausa
                    ? (boton.dataset.textoReproducir || "Reproducir video")
                    : (boton.dataset.textoPausar || "Pausar video"));
            };
            boton.hidden = false;
            boton.addEventListener("click", function () {
                enPausa = !enPausa;
                dibujarBoton();
                actualizar();
            });
            dibujarBoton();
        }

        if ("IntersectionObserver" in window) {
            new IntersectionObserver(function (entradas) {
                visible = entradas[0].isIntersecting;
                actualizar();
            }, { threshold: 0.15 }).observe(video);
        } else {
            visible = true;
            actualizar();
        }
    });
})();
