// Botón de encendido: suena el motor del auto (catálogo y página de cada auto).
// Solo suena un motor a la vez; al tocarlo de nuevo se apaga.
// Sin JavaScript los botones quedan escondidos (tienen hidden).
var Motor = (function () {
    "use strict";

    var sonando = null;   // { audio, boton }

    var dibujar = function (boton, encendido) {
        boton.classList.toggle("encendido", encendido);
        boton.setAttribute("aria-pressed", encendido ? "true" : "false");
        var texto = boton.querySelector(".motor-texto");
        if (texto) {
            texto.innerHTML = encendido ? "Engine<br>Stop" : "Engine<br>Start";
        }
    };

    // apaga con un fundido corto, para que no corte de golpe
    var apagar = function () {
        if (!sonando) {
            return;
        }
        var actual = sonando;
        sonando = null;
        dibujar(actual.boton, false);
        var paso = window.setInterval(function () {
            actual.audio.volume = Math.max(0, actual.audio.volume - 0.1);
            if (actual.audio.volume <= 0.01) {
                window.clearInterval(paso);
                actual.audio.pause();
            }
        }, 30);
    };

    var encender = function (boton) {
        var src = boton.dataset.sonido;
        if (!src) {
            return;
        }
        // si este mismo botón ya suena, se apaga
        if (sonando && sonando.boton === boton) {
            apagar();
            return;
        }
        apagar();

        var audio = new Audio(src);
        audio.volume = 0.9;
        sonando = { audio: audio, boton: boton };
        dibujar(boton, true);
        audio.addEventListener("ended", function () {
            if (sonando && sonando.audio === audio) {
                sonando = null;
                dibujar(boton, false);
            }
        });
        audio.play().catch(function () {
            sonando = null;
            dibujar(boton, false);
        });
    };

    // activa un botón: le quita el hidden y le pone el clic
    var preparar = function (boton) {
        boton.hidden = false;
        boton.addEventListener("click", function (evento) {
            evento.preventDefault();
            encender(boton);
        });
    };

    document.querySelectorAll(".motor[data-sonido]").forEach(preparar);

    return { preparar: preparar, apagar: apagar };
})();
