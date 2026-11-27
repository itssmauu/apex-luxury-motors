// Agregar o editar vehículo: la vista previa cambia mientras se escribe
// y la zona de fotos muestra el nombre del archivo elegido.
(function () {
    "use strict";

    var marca = document.getElementById("marca");
    var modelo = document.getElementById("modelo");
    var categoria = document.getElementById("categoria");
    var precio = document.getElementById("precio");
    var estados = document.querySelectorAll('input[name="estado"]');

    var previaNombre = document.getElementById("previa-nombre");
    var previaCategoria = document.getElementById("previa-categoria");
    var previaPrecio = document.getElementById("previa-precio");
    var previaSello = document.getElementById("previa-sello");

    if (marca && previaNombre) {
        // número con comas, sin decimales: 239900 -> $239,900
        var formato = new Intl.NumberFormat("en-US", { maximumFractionDigits: 0 });

        var actualizar = function () {
            var nombre = (marca.value + " " + modelo.value).trim();
            previaNombre.textContent = nombre !== "" ? nombre : "Marca y modelo";
            previaCategoria.textContent = categoria.value !== "" ? categoria.value : "Categoría";
            var valor = parseFloat(precio.value);
            previaPrecio.textContent = "$" + (isNaN(valor) ? "0" : formato.format(valor));

            var vendido = document.querySelector('input[name="estado"][value="vendido"]').checked;
            previaSello.textContent = vendido ? "Vendido" : "Disponible";
            previaSello.classList.toggle("sello-vendido", vendido);
        };

        [marca, modelo, precio].forEach(function (campo) {
            campo.addEventListener("input", actualizar);
        });
        categoria.addEventListener("change", actualizar);
        estados.forEach(function (opcion) {
            opcion.addEventListener("change", actualizar);
        });
        actualizar();
    }

    // al elegir una foto, la zona muestra su nombre
    var archivo = document.getElementById("foto");
    var nombreFoto = document.getElementById("nombre-foto");
    if (archivo && nombreFoto) {
        archivo.addEventListener("change", function () {
            nombreFoto.textContent = archivo.files.length > 0 ? archivo.files[0].name : "Elegir foto";
            archivo.closest(".fotos-admin-subir").classList.toggle("con-archivo", archivo.files.length > 0);
        });
    }
})();
