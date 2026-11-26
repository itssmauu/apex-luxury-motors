# Backend: contrato para el frontend

Este archivo dice qué ofrece el backend y cómo usarlo desde las páginas del frontend. Si algo cambia, se actualiza aquí.

Las funciones están en `backend/funciones/` y los procesos en `backend/procesos/`.

---

## Sesión

- Cada página y cada proceso que use sesión empieza con `session_start();`. Si incluyen `acceso.php`, ya se abre sola.
- Al iniciar sesión se guardan: `$_SESSION["usuario_id"]`, `$_SESSION["nombre"]` y `$_SESSION["rol"]` (`"cliente"` o `"administrador"`).
- Un **visitante** es quien no tiene sesión iniciada.
- Los mensajes de éxito o error se guardan en `$_SESSION["mensaje"]` así: `["tipo" => "exito" o "error", "texto" => "..."]`. La plantilla común los muestra y los borra.

Cómo incluir una función desde una página de `frontend/`:

```php
require_once __DIR__ . "/../backend/funciones/acceso.php";
```

Desde una página dentro de `frontend/admin/`, la ruta lleva un nivel más: `/../../backend/funciones/acceso.php`.

## Qué devuelven las funciones

- Las que **hacen una acción** devuelven `["ok" => true o false, "mensaje" => "texto"]`.
- Las que **listan** devuelven un arreglo de filas, con los nombres de las columnas de la base (`marca`, `modelo`, `precio`, `estado`, etc.).

---

## Funciones

### `acceso.php`

| Función | Para qué |
|---|---|
| `rolActual()` | Devuelve `"visitante"`, `"cliente"` o `"administrador"` |
| `requerirSesion()` | Si no hay sesión, redirige al inicio de sesión |
| `requerirRol($rol)` | Si el rol no corresponde, muestra «Acceso denegado» |

### `usuarios.php`

| Función | Para qué |
|---|---|
| `registrarCliente($nombre, $correo, $contrasena, $telefono)` | Registro de clientes (RF1) |
| `iniciarSesion($correo, $contrasena)` | Iniciar sesión (RF2) |
| `cerrarSesion()` | Cerrar sesión (RF2) |
| `actualizarPerfil($nombre, $telefono, $actual, $nueva)` | Actualizar perfil (RF3) |

### `vehiculos.php`

| Función | Para qué |
|---|---|
| `listarVehiculos($marca, $categoria, $precioMax)` | Catálogo con filtros (RF4). Solo vehículos activos; cada fila incluye `foto` (la principal) |
| `listarMarcas()` y `listarCategorias()` | Para llenar los filtros |
| `obtenerVehiculo($id)` | Detalle (RF5). Incluye `fotos`, la lista de todas sus fotos |
| `guardarVehiculo($datos, $fotos)` | Crear o editar (RF8) |
| `desactivarVehiculo($id)` | Desactivar (RF8) |

### `solicitudes.php`

| Función | Para qué |
|---|---|
| `crearSolicitud($vehiculoId)` | Solicitar compra (RF6) |
| `listarMisSolicitudes()` | Solicitudes del cliente (RF7) |
| `cancelarSolicitud($id)` | Cancelar una solicitud pendiente (RF7) |
| `listarPendientes()` | Solicitudes pendientes, para el administrador (RF9) |
| `obtenerSolicitud($id)` | Una solicitud con sus datos (RF9) |
| `atenderSolicitud($id, $decision, $comentario)` | Aprobar o rechazar (RF9) |

---

## Procesos y campos de los formularios

Cada formulario usa `method="post"` y envía a `backend/procesos/`. Desde `frontend/` la ruta es `../backend/procesos/archivo.php`, y desde `frontend/admin/` es `../../backend/procesos/archivo.php`.

| Proceso | Campos del formulario |
|---|---|
| `registro.php` | `nombre`, `correo`, `contrasena`, `telefono` |
| `login.php` | `correo`, `contrasena` |
| `logout.php` | (enlace, sin campos) |
| `perfil_guardar.php` | `nombre`, `telefono`, `contrasena_actual`, `contrasena_nueva` |
| `solicitar.php` | `vehiculo_id` (oculto) |
| `cancelar_solicitud.php` | `solicitud_id` (oculto) |
| `vehiculo_guardar.php` | `id` (oculto, solo al editar), `marca`, `modelo`, `precio`, `categoria`, `descripcion`, `estado`, `fotos[]` (archivo) |
| `vehiculo_desactivar.php` | `id` (oculto) |
| `atender_solicitud.php` | `solicitud_id` (oculto), `decision` (`aprobada` o `rechazada`), `comentario` |

Los formularios con archivos (fotos) necesitan `enctype="multipart/form-data"`.

Después de hacer su trabajo, cada proceso guarda un mensaje en `$_SESSION["mensaje"]` y redirige de vuelta a la página que corresponda.

## Filtros del catálogo

El catálogo se filtra por la dirección (método GET): `catalogo.php?marca=...&categoria=...&precio_max=...`.

---

## Estado de las funciones

Mientras el backend las termina, las funciones devuelven **datos de ejemplo** con la forma definitiva. El frontend puede usarlas ya, y cuando la versión real esté lista, las páginas no cambian.

| Archivo | Estado |
|---|---|
| `acceso.php` | Real |
| `usuarios.php` | Esqueleto (datos de ejemplo) |
| `vehiculos.php` | Esqueleto (datos de ejemplo) |
| `solicitudes.php` | Esqueleto (datos de ejemplo) |

Se va actualizando esta tabla a medida que cada archivo pasa a la versión real.