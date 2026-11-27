// Iniciar sesión y registro: videos de fondo que se turnan y botón para ver la contraseña.
// Sin JavaScript la página funciona igual: se ve la imagen fija y la contraseña queda oculta.
(function () {
    "use strict";

    var menosMovimiento = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    // ---------- videos de fondo ----------
    var videos = document.querySelectorAll(".acceso-video");
    var botonPausa = document.querySelector(".acceso-pausa");

    if (videos.length > 0 && botonPausa) {
        var actual = 0;
        // con menos movimiento, los videos empiezan en pausa y la persona decide
        var enPausa = menosMovimiento;

        // si hay un solo video, se repite; si hay varios, se turnan
        if (videos.length === 1) {
            videos[0].loop = true;
        }

        var dibujarBoton = function () {
            botonPausa.classList.toggle("en-pausa", enPausa);
            botonPausa.setAttribute("aria-label", enPausa ? "Reproducir video de fondo" : "Pausar video de fondo");
        };

        var reproducir = function (video) {
            var promesa = video.play();
            if (promesa) {
                promesa.catch(function () {
                    // el navegador no dejó reproducir solo: queda en pausa con el botón
                    enPausa = true;
                    dibujarBoton();
                });
            }
        };

        // cuando un video termina, entra el siguiente con un fundido
        videos.forEach(function (video) {
            video.addEventListener("ended", function () {
                if (videos.length === 1) {
                    return;
                }
                var siguiente = videos[(actual + 1) % videos.length];
                siguiente.currentTime = 0;
                siguiente.classList.add("activo");
                video.classList.remove("activo");
                actual = (actual + 1) % videos.length;
                if (!enPausa) {
                    reproducir(siguiente);
                }
            });
        });

        // mientras se ve el primero, el siguiente se va descargando
        videos[0].addEventListener("playing", function () {
            if (videos.length > 1) {
                videos[1].preload = "auto";
            }
        }, { once: true });

        botonPausa.hidden = false;
        botonPausa.addEventListener("click", function () {
            enPausa = !enPausa;
            if (enPausa) {
                videos[actual].pause();
            } else {
                reproducir(videos[actual]);
            }
            dibujarBoton();
        });

        dibujarBoton();
        if (!enPausa) {
            reproducir(videos[0]);
        }
    }

    // ---------- mostrar u ocultar la contraseña ----------
    document.querySelectorAll(".ver-clave").forEach(function (boton) {
        var campo = document.getElementById(boton.getAttribute("aria-controls"));
        if (!campo) {
            return;
        }
        boton.hidden = false;
        boton.addEventListener("click", function () {
            var mostrar = campo.type === "password";
            campo.type = mostrar ? "text" : "password";
            // el texto del botón dice lo que va a hacer
            boton.textContent = mostrar ? "Ocultar" : "Mostrar";
            campo.focus();
        });
    });
})();
