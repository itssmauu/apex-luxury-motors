// Pinta la carrocería del modelo 3D del mismo color que las fotos del auto.
// Los datos vienen de datos/modelos3d.php ($pinturas3d) en el atributo data-pintura:
// {"materiales": [...], "color": "#rrggbb", "metalico": 0.1, "quitarTextura": true,
//  "vidrio": {"materiales": [...], "color": "#rrggbb"}}

(function () {
    "use strict";

    // "#rrggbb" -> [r, g, b] en el formato que usan los modelos 3D (lineal, de 0 a 1)
    function colorLineal(hex) {
        var partes = [1, 3, 5].map(function (i) {
            var c = parseInt(hex.substr(i, 2), 16) / 255;
            return c <= 0.04045 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
        });
        return partes;
    }

    // cambia el color de los materiales nombrados y deja igual su transparencia
    function cambiarColor(visor, capa, alCambiar) {
        if (!capa || !capa.materiales || !capa.color) {
            return;
        }
        var rgb = colorLineal(capa.color);
        visor.model.materials.forEach(function (material) {
            if (capa.materiales.indexOf(material.name) === -1) {
                return;
            }
            var pbr = material.pbrMetallicRoughness;
            var alfa = pbr.baseColorFactor[3];
            pbr.setBaseColorFactor([rgb[0], rgb[1], rgb[2], alfa]);
            if (alCambiar) {
                alCambiar(pbr);
            }
        });
    }

    function pintar(visor) {
        var pintura;
        try {
            pintura = JSON.parse(visor.dataset.pintura);
        } catch (e) {
            return;
        }
        if (!visor.model || !pintura) {
            return;
        }

        cambiarColor(visor, pintura, function (pbr) {
            // algunas carrocerías traen franjas o calcomanías en una imagen;
            // sin quitarla, el color nuevo se mezclaría con ella
            if (pintura.quitarTextura && pbr.baseColorTexture) {
                pbr.baseColorTexture.setTexture(null);
            }
            if (typeof pintura.metalico === "number") {
                pbr.setMetallicFactor(pintura.metalico);
            }
        });
        cambiarColor(visor, pintura.vidrio, null);
    }

    document.querySelectorAll("model-viewer[data-pintura]").forEach(function (visor) {
        // "load" se dispara cuando el modelo terminó de cargar
        visor.addEventListener("load", function () {
            pintar(visor);
        });
        if (visor.loaded) {
            pintar(visor);
        }
    });
})();
