// Inventario (admin): acciones con la API propia, sin recargar la página.
//   "Marcar como vendido / disponible" → PUT    /apex/backend/api/vehiculos.php?id=8  {"estado": "..."}
//   "Desactivar"                       → DELETE /apex/backend/api/vehiculos.php?id=8
// La respuesta (JSON) actualiza la fila, las cifras de arriba y muestra un aviso.
// Si la API responde un error (por ejemplo 409: tiene solicitudes pendientes), se muestra el motivo.
(function () {
    "use strict";

    var lista = document.querySelector(".inventario-lista[data-api]");
    var aviso = document.getElementById("inventario-aviso");
    if (!lista || !aviso || !window.fetch) {
        return;   // sin fetch, el formulario de "Desactivar" funciona como siempre
    }

    var api = lista.dataset.api;
    var textos = lista.dataset;   // data-texto-* → textos.textoDisponible, textos.textoErrPendientes...

    // la vista elegida arriba: todos, disponibles, vendidos o desactivados
    var enlaceActual = document.querySelector('.inventario-filtros a[aria-current="page"]');
    var vistaActual = enlaceActual ? enlaceActual.getAttribute("href").replace("?vista=", "") : "todos";

    // ---------- llamada a la API ----------
    // devuelve los datos si todo salió bien; si no, lanza el cuerpo del error
    var llamarApi = function (metodo, id, datos) {
        var opciones = {
            method: metodo,
            credentials: "same-origin",            // envía la sesión del administrador
            headers: { "Accept": "application/json" }
        };
        if (datos) {
            opciones.headers["Content-Type"] = "application/json";
            opciones.body = JSON.stringify(datos);
        }
        return fetch(api + "?id=" + encodeURIComponent(id), opciones).then(function (respuesta) {
            return respuesta.json().catch(function () {
                return null;
            }).then(function (cuerpo) {
                if (!respuesta.ok || !cuerpo || !cuerpo.ok) {
                    throw cuerpo;
                }
                return cuerpo.datos;
            });
        });
    };

    // ---------- avisos ----------
    var avisar = function (tipo, texto) {
        aviso.textContent = "";
        var caja = document.createElement("div");
        caja.className = "mensaje mensaje-" + tipo;
        caja.setAttribute("role", tipo === "exito" ? "status" : "alert");
        caja.textContent = texto;
        aviso.appendChild(caja);
    };

    // el texto traducido según el "codigo" que manda la API; si no se conoce, su mensaje
    var textoDeError = function (cuerpo) {
        var codigos = {
            solicitudes_pendientes: textos.textoErrPendientes,
            ya_desactivado: textos.textoErrYaDesactivado,
            no_encontrado: textos.textoErrNoEncontrado,
            no_autenticado: textos.textoErrSesion,
            sin_permiso: textos.textoErrPermiso
        };
        var error = cuerpo && cuerpo.error ? cuerpo.error : null;
        if (error && codigos[error.codigo]) {
            return codigos[error.codigo];
        }
        return (error && error.mensaje) ? error.mensaje : textos.textoError;
    };

    // ---------- pantalla ----------
    var cambiarCifra = function (vista, cambio) {
        var cifra = document.querySelector('.inventario-filtros a[href="?vista=' + vista + '"] .inventario-cifra');
        if (cifra) {
            cifra.textContent = String(parseInt(cifra.textContent, 10) + cambio);
        }
    };

    var vistaDe = function (estado, activo) {
        if (!activo) {
            return "desactivados";
        }
        return (estado === "vendido") ? "vendidos" : "disponibles";
    };

    var sello = function (clase, texto) {
        var s = document.createElement("span");
        s.className = "estado-sello " + clase;
        s.textContent = texto;
        return s;
    };

    // pone la fila como dice el vehículo que devolvió la API
    var actualizarFila = function (fila, vehiculo) {
        var antes = vistaDe(fila.dataset.estado, fila.dataset.activo === "1");
        var despues = vistaDe(vehiculo.estado, vehiculo.activo);
        if (antes !== despues) {
            cambiarCifra(antes, -1);
            cambiarCifra(despues, 1);
        }

        fila.dataset.estado = vehiculo.estado;
        fila.dataset.activo = vehiculo.activo ? "1" : "0";

        var sellos = fila.querySelector(".inventario-sellos");
        sellos.textContent = "";
        sellos.appendChild(vehiculo.estado === "vendido"
            ? sello("estado-sello-vendido", textos.textoVendido)
            : sello("estado-sello-disponible", textos.textoDisponible));

        if (!vehiculo.activo) {
            sellos.appendChild(sello("estado-sello-oculto", textos.textoFuera));
            fila.classList.add("inventario-fila-inactiva");
            // fuera del catálogo: ya no hay página pública, ni estado que cambiar, ni nada que desactivar
            ["a.inventario-ver", ".inventario-estado", ".inventario-desactivar"].forEach(function (selector) {
                var elemento = fila.querySelector(selector);
                if (elemento) {
                    elemento.remove();
                }
            });
        } else {
            prepararBotonEstado(fila);
        }

        // si ya no pertenece a la vista elegida (por ejemplo "Disponibles"), sale de la lista
        if (vistaActual !== "todos" && despues !== vistaActual) {
            fila.classList.add("inventario-fila-saliendo");
            window.setTimeout(function () {
                fila.remove();
            }, 450);
        }
    };

    var ocupado = function (boton, siOno) {
        boton.disabled = siOno;
        if (siOno) {
            boton.dataset.textoAntes = boton.textContent;
            boton.textContent = textos.textoProcesando;
        } else if (boton.dataset.textoAntes) {
            boton.textContent = boton.dataset.textoAntes;
        }
    };

    // ---------- "Marcar como vendido / disponible" (PUT) ----------
    var prepararBotonEstado = function (fila) {
        var boton = fila.querySelector(".inventario-estado");
        if (!boton) {
            return;
        }
        var vendido = fila.dataset.estado === "vendido";
        boton.textContent = vendido ? textos.textoMarcarDisponible : textos.textoMarcarVendido;
        boton.setAttribute("aria-label", (vendido ? textos.textoMarcarDisponibleAria : textos.textoMarcarVendidoAria)
            .replace("%s", fila.dataset.nombre));
        boton.hidden = false;
    };

    lista.querySelectorAll(".inventario-fila").forEach(function (fila) {
        prepararBotonEstado(fila);

        var botonEstado = fila.querySelector(".inventario-estado");
        if (botonEstado) {
            botonEstado.addEventListener("click", function () {
                var nuevo = (fila.dataset.estado === "vendido") ? "disponible" : "vendido";
                ocupado(botonEstado, true);
                llamarApi("PUT", fila.dataset.id, { estado: nuevo }).then(function (vehiculo) {
                    botonEstado.disabled = false;
                    actualizarFila(fila, vehiculo);
                    var texto = (vehiculo.estado === "vendido") ? textos.textoOkVendido : textos.textoOkDisponible;
                    avisar("exito", texto.replace("%s", fila.dataset.nombre));
                }).catch(function (cuerpo) {
                    ocupado(botonEstado, false);
                    avisar("error", textoDeError(cuerpo));
                });
            });
        }

        // ---------- "Desactivar" (DELETE) ----------
        var formulario = fila.querySelector(".inventario-desactivar");
        if (formulario) {
            formulario.addEventListener("submit", function (evento) {
                evento.preventDefault();
                var boton = formulario.querySelector("button");
                ocupado(boton, true);
                llamarApi("DELETE", fila.dataset.id).then(function (vehiculo) {
                    actualizarFila(fila, vehiculo);
                    avisar("exito", textos.textoOkDesactivado.replace("%s", fila.dataset.nombre));
                }).catch(function (cuerpo) {
                    ocupado(boton, false);
                    avisar("error", textoDeError(cuerpo));
                });
            });
        }
    });
})();
