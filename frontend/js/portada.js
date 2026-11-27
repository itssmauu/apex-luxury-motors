// Portada (index.php): intro, carrusel de videos y videos de fondo.
// Si este archivo no carga, la portada igual funciona: se ve la primera diapositiva fija.
(function () {
    "use strict";

    // si la persona pidió menos movimiento en su equipo, nada arranca solo
    var menosMovimiento = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    // play() devuelve una promesa que falla si el navegador no deja reproducir;
    // en ese caso se queda la imagen fija (poster) y no pasa nada
    function reproducir(video) {
        if (!video) {
            return;
        }
        video.preload = "auto";
        var promesa = video.play();
        if (promesa) {
            promesa.catch(function () {});
        }
    }

    // cambia el ícono y el texto de un botón de pausa
    function marcarBoton(boton, enPausa, queControla) {
        boton.classList.toggle("en-pausa", enPausa);
        // los textos traducidos los pone PHP en data-texto-reproducir y data-texto-pausar
        var texto = enPausa ? boton.dataset.textoReproducir : boton.dataset.textoPausar;
        boton.setAttribute("aria-label", texto || ((enPausa ? "Reproducir " : "Pausar ") + queControla));
    }

    // ---------- carrusel ----------
    var carrusel = document.querySelector(".hero");
    var iniciarCarrusel = function () {};

    if (carrusel) {
        var diapositivas = carrusel.querySelectorAll(".hero-diapositiva");
        var indicadores = carrusel.querySelectorAll(".hero-indicador");
        var botonPausa = carrusel.querySelector(".hero-pausa");
        var zonaViva = carrusel.querySelector(".hero-diapositivas");
        var actual = 0;
        var pausado = menosMovimiento;

        carrusel.querySelector(".hero-controles").hidden = false;

        var mostrar = function (indice) {
            var anterior = diapositivas[actual];
            anterior.classList.remove("activa");
            anterior.querySelector("video").pause();
            indicadores[actual].classList.remove("activo");
            indicadores[actual].removeAttribute("aria-current");

            // el % hace que después de la última vuelva a la primera
            actual = (indice + diapositivas.length) % diapositivas.length;

            var nueva = diapositivas[actual];
            var video = nueva.querySelector("video");
            nueva.classList.add("activa");
            indicadores[actual].classList.add("activo");
            indicadores[actual].setAttribute("aria-current", "true");
            video.currentTime = 0;
            if (!pausado) {
                reproducir(video);
            }
        };

        var cambiarPausa = function (valor) {
            pausado = valor;
            carrusel.classList.toggle("pausado", pausado);
            marcarBoton(botonPausa, pausado, "el carrusel");
            // en pausa, el lector de pantalla anuncia la diapositiva cuando se cambia a mano
            zonaViva.setAttribute("aria-live", pausado ? "polite" : "off");

            var video = diapositivas[actual].querySelector("video");
            if (pausado) {
                video.pause();
            } else {
                reproducir(video);
            }
        };

        // la barra dorada del indicador es el reloj: cuando termina de llenarse,
        // pasa a la siguiente. Al pausar, el CSS detiene la barra y con ella el cambio
        carrusel.addEventListener("animationend", function (evento) {
            if (evento.target.classList.contains("hero-progreso")) {
                mostrar(actual + 1);
            }
        });

        botonPausa.addEventListener("click", function () {
            cambiarPausa(!pausado);
        });

        indicadores.forEach(function (boton, i) {
            boton.addEventListener("click", function () {
                if (i !== actual) {
                    mostrar(i);
                }
            });
        });

        // flechas izquierda y derecha cuando el foco está en los indicadores
        carrusel.querySelector(".hero-indicadores").addEventListener("keydown", function (evento) {
            if (evento.key !== "ArrowRight" && evento.key !== "ArrowLeft") {
                return;
            }
            evento.preventDefault();
            mostrar(actual + (evento.key === "ArrowRight" ? 1 : -1));
            indicadores[actual].focus();
        });

        // el video de la primera diapositiva arranca ya (se ve al irse la intro);
        // la barra arranca cuando la portada ya está a la vista
        cambiarPausa(pausado);
        iniciarCarrusel = function () {
            indicadores[actual].classList.add("activo");
        };
    }

    // ---------- intro ----------
    var intro = document.querySelector(".intro");

    if (intro && !menosMovimiento) {
        // un clic o cualquier tecla la saltan
        var saltarIntro = function () {
            intro.classList.add("intro-fuera");
        };
        intro.addEventListener("click", saltarIntro);
        document.addEventListener("keydown", saltarIntro, { once: true });

        // cuando termina de desvanecerse se quita de la página y arranca el carrusel
        intro.addEventListener("animationend", function (evento) {
            if (evento.target === intro) {
                intro.remove();
                iniciarCarrusel();
            }
        });
    } else {
        if (intro) {
            intro.remove();
        }
        iniciarCarrusel();
    }

    // ---------- videos de fondo de las bandas ----------
    // se reproducen solo mientras se ven, y cada uno tiene su botón de pausa
    var bandas = document.querySelectorAll(".banda");

    bandas.forEach(function (banda) {
        var video = banda.querySelector(".video-al-ver");
        var boton = banda.querySelector(".video-pausa");
        var pausadoAMano = menosMovimiento;

        boton.hidden = false;
        marcarBoton(boton, pausadoAMano, "el video de fondo");

        boton.addEventListener("click", function () {
            pausadoAMano = !pausadoAMano;
            marcarBoton(boton, pausadoAMano, "el video de fondo");
            if (pausadoAMano) {
                video.pause();
            } else {
                reproducir(video);
            }
        });

        if (!("IntersectionObserver" in window)) {
            return;
        }

        var observador = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (entrada) {
                if (entrada.isIntersecting && !pausadoAMano) {
                    reproducir(video);
                } else {
                    video.pause();
                }
            });
        }, { threshold: 0.25 });

        observador.observe(banda);
    });
})();