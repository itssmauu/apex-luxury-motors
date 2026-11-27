# API de Apex Luxury Motors

API propia en PHP que trabaja con la entidad principal del sistema: **vehículos**. Intercambia datos en **JSON** y usa las mismas funciones del backend que el sitio (`backend/funciones/vehiculos.php` y `usuarios.php`), así que aplica las mismas reglas.

- Dirección base (XAMPP): `http://localhost/apex/backend/api/`
- Archivos: `backend/api/vehiculos.php`, `backend/api/sesion.php` y `backend/api/respuesta.php` (funciones comunes).
- Pantalla que la consume: **Inventario del administrador** (`frontend/admin/vehiculos.php` con `frontend/js/inventario.js`). "Marcar como vendido / disponible" usa `PUT` y "Desactivar" usa `DELETE`, sin recargar la página.
- Colección para probarla: `backend/apex_api.postman_collection.json` (Postman).

---

## 1. Reglas generales

### Formato de las respuestas

Todas las respuestas son JSON (`Content-Type: application/json; charset=utf-8`).

Éxito:

```json
{
    "ok": true,
    "mensaje": "Vehículo creado.",
    "datos": { "...": "..." }
}
```

Error:

```json
{
    "ok": false,
    "error": {
        "codigo": "datos_invalidos",
        "mensaje": "Revisa los datos del vehículo.",
        "campos": { "precio": "Debe ser un número mayor que 0." }
    }
}
```

- `codigo` no cambia y sirve para programar (por ejemplo, `inventario.js` traduce el aviso según el código).
- `mensaje` es para que lo lea una persona.
- `campos` solo aparece cuando hay datos mal enviados, e indica cuál está mal y por qué.

### Envío de datos

`POST` y `PUT` reciben un objeto JSON en el cuerpo, con la cabecera `Content-Type: application/json`. Si se envía otro tipo de contenido, la API responde `415`. Exigir JSON también impide que otra página use la sesión del administrador para enviar formularios a la API.

### Autenticación y permisos

La API usa la **misma sesión del sitio** (cookie `PHPSESSID`):

- Desde el navegador, quien inició sesión en el sitio ya está autenticado.
- Desde Postman o curl, primero se inicia sesión con `POST /sesion.php` y la cookie se reutiliza en las siguientes solicitudes.

| Operación | Quién puede usarla |
|---|---|
| Consultar vehículos (`GET`) | Cualquiera, sin sesión. Solo ve vehículos **activos**. El administrador ve también los desactivados. |
| Crear, actualizar y desactivar (`POST`, `PUT`, `DELETE`) | Solo el **administrador**. Sin sesión: `401`. Con sesión de cliente: `403`. |

La API **nunca devuelve contraseñas** ni datos personales. La sesión solo responde el id, el nombre y el rol.

### Códigos de respuesta

| Código | Cuándo |
|---|---|
| `200 OK` | Consulta, actualización, desactivación, inicio o cierre de sesión correctos. |
| `201 Created` | Vehículo creado. Incluye la cabecera `Location` con su dirección. |
| `400 Bad Request` | Datos inválidos, JSON mal escrito, id inválido o faltante, campos que no se aceptan. |
| `401 Unauthorized` | Sin sesión, o correo o contraseña incorrectos. |
| `403 Forbidden` | Hay sesión, pero no es de administrador. |
| `404 Not Found` | El vehículo no existe (o está desactivado y quien pregunta no es administrador). |
| `405 Method Not Allowed` | Método que el recurso no acepta. Incluye la cabecera `Allow`. |
| `409 Conflict` | El estado actual no permite la acción: ya estaba desactivado o tiene solicitudes pendientes. |
| `415 Unsupported Media Type` | `POST` o `PUT` sin `Content-Type: application/json`. |
| `500 Internal Server Error` | Error del servidor. No se muestran detalles técnicos. |

### Códigos de error (`error.codigo`)

`datos_invalidos`, `json_invalido`, `id_invalido`, `id_requerido`, `tipo_no_soportado`, `no_autenticado`, `credenciales_invalidas`, `sin_permiso`, `no_encontrado`, `metodo_no_permitido`, `ya_desactivado`, `solicitudes_pendientes`, `error_servidor`.

### Un vehículo en la API

```json
{
    "id": 8,
    "marca": "Ferrari",
    "modelo": "458",
    "precio": 239900,
    "categoria": "Superdeportivo",
    "descripcion": "Motor V8 central de 4.5 L con 570 hp y sonido inconfundible.",
    "estado": "disponible",
    "activo": true,
    "foto": "/apex/img/vehiculos/ferrari-458-lateral.jpg"
}
```

| Campo | Tipo | Reglas al crear o actualizar |
|---|---|---|
| `marca` | texto | Obligatorio, máximo 50 caracteres. |
| `modelo` | texto | Obligatorio, máximo 80 caracteres. |
| `precio` | número | Obligatorio, mayor que 0 (USD). |
| `categoria` | texto | Obligatorio, máximo 50 caracteres. |
| `descripcion` | texto | Opcional. |
| `estado` | texto | `"disponible"` (por defecto) o `"vendido"`. |
| `id`, `activo`, `foto`, `fotos` | — | Solo de lectura. Si se envían, la API responde `400`. |

`foto` (la principal) sale en la lista; `fotos` (todas, en orden) sale al pedir un solo vehículo.

---

## 2. Endpoints de vehículos

### 2.1 Consultar la colección

| | |
|---|---|
| **Ruta y método** | `GET /apex/backend/api/vehiculos.php` |
| **Propósito** | Lista los vehículos del catálogo, con los mismos filtros de la página. |
| **Parámetros** (opcionales, en la dirección) | `marca` (texto exacto), `categoria` (texto exacto), `precio_max` (número mayor que 0) |
| **Autenticación** | No necesita. |

Solicitud:

```
GET /apex/backend/api/vehiculos.php?categoria=Superdeportivo&precio_max=300000
```

Respuesta `200`:

```json
{
    "ok": true,
    "total": 2,
    "datos": [
        { "id": 8, "marca": "Ferrari", "modelo": "458", "precio": 239900, "categoria": "Superdeportivo", "estado": "disponible", "activo": true, "...": "..." },
        { "id": 10, "marca": "McLaren", "modelo": "650S Spider", "precio": 279900, "categoria": "Superdeportivo", "estado": "disponible", "activo": true, "...": "..." }
    ]
}
```

Errores:

- `400 datos_invalidos`: `precio_max` no es un número mayor que 0.

Si ningún vehículo cumple los filtros, la respuesta es `200` con `"total": 0` y `"datos": []`.

### 2.2 Consultar un vehículo por su id

| | |
|---|---|
| **Ruta y método** | `GET /apex/backend/api/vehiculos.php?id={id}` |
| **Propósito** | Devuelve un vehículo con todas sus fotos. |
| **Parámetros** | `id` (entero mayor que 0, en la dirección) |
| **Autenticación** | No necesita. Sin sesión de administrador, solo encuentra vehículos activos. |

Respuesta `200`:

```json
{
    "ok": true,
    "datos": {
        "id": 8,
        "marca": "Ferrari",
        "modelo": "458",
        "precio": 239900,
        "categoria": "Superdeportivo",
        "descripcion": "Motor V8 central de 4.5 L con 570 hp y sonido inconfundible.",
        "estado": "disponible",
        "activo": true,
        "fotos": [
            { "id": 8, "ruta": "/apex/img/Ferrari_458.jpg", "orden": 1 },
            { "id": 30, "ruta": "/apex/img/vehiculos/ferrari-458-lateral.jpg", "orden": 2 }
        ]
    }
}
```

Errores:

- `400 id_invalido`: el id no es un entero mayor que 0 (`?id=abc`).
- `404 no_encontrado`: no existe un vehículo con ese id.

```json
{
    "ok": false,
    "error": { "codigo": "no_encontrado", "mensaje": "No existe un vehículo con el id 999." }
}
```

### 2.3 Crear un vehículo

| | |
|---|---|
| **Ruta y método** | `POST /apex/backend/api/vehiculos.php` |
| **Propósito** | Agrega un vehículo al inventario (queda activo). |
| **Datos requeridos** (JSON) | `marca`, `modelo`, `precio`, `categoria`. Opcionales: `descripcion`, `estado`. |
| **Autenticación** | Sesión de **administrador**. |

Solicitud:

```
POST /apex/backend/api/vehiculos.php
Content-Type: application/json

{
    "marca": "Porsche",
    "modelo": "Taycan Turbo S",
    "precio": 185000,
    "categoria": "Deportivo alemán",
    "descripcion": "Deportivo eléctrico de 750 hp."
}
```

Respuesta `201` (con la cabecera `Location: /apex/backend/api/vehiculos.php?id=15`):

```json
{
    "ok": true,
    "mensaje": "Vehículo creado.",
    "datos": {
        "id": 15,
        "marca": "Porsche",
        "modelo": "Taycan Turbo S",
        "precio": 185000,
        "categoria": "Deportivo alemán",
        "descripcion": "Deportivo eléctrico de 750 hp.",
        "estado": "disponible",
        "activo": true,
        "fotos": []
    }
}
```

Las fotos se suben desde el formulario del sitio (Agregar o editar vehículo).

Errores:

- `400 datos_invalidos`: faltan datos o no cumplen las reglas, o se enviaron campos que no se aceptan.

  ```json
  {
      "ok": false,
      "error": {
          "codigo": "datos_invalidos",
          "mensaje": "Revisa los datos del vehículo.",
          "campos": {
              "marca": "Obligatorio.",
              "precio": "Debe ser un número mayor que 0.",
              "estado": "Debe ser \"disponible\" o \"vendido\"."
          }
      }
  }
  ```

- `400 json_invalido`: el cuerpo no es un objeto JSON.
- `401 no_autenticado`: no hay sesión.
- `403 sin_permiso`: la sesión es de un cliente.
- `415 tipo_no_soportado`: falta `Content-Type: application/json`.

### 2.4 Actualizar un vehículo

| | |
|---|---|
| **Ruta y método** | `PUT /apex/backend/api/vehiculos.php?id={id}` |
| **Propósito** | Cambia uno o varios datos. Lo que no se envía queda igual. |
| **Parámetros** | `id` en la dirección. En el cuerpo (JSON), cualquiera de: `marca`, `modelo`, `precio`, `categoria`, `descripcion`, `estado`. |
| **Autenticación** | Sesión de **administrador**. |

Solicitud (marcar como vendido y bajar el precio):

```
PUT /apex/backend/api/vehiculos.php?id=15
Content-Type: application/json

{ "precio": 179900, "estado": "vendido" }
```

Respuesta `200`:

```json
{
    "ok": true,
    "mensaje": "Vehículo actualizado.",
    "datos": { "id": 15, "precio": 179900, "estado": "vendido", "activo": true, "...": "..." }
}
```

Errores:

- `400 id_requerido`: falta `?id=`.
- `400 datos_invalidos`: cuerpo vacío (`{}`), datos que no cumplen las reglas o campos que no se aceptan.
- `401`, `403`, `415`: igual que al crear.
- `404 no_encontrado`: no existe un vehículo con ese id.

### 2.5 Desactivar un vehículo

| | |
|---|---|
| **Ruta y método** | `DELETE /apex/backend/api/vehiculos.php?id={id}` |
| **Propósito** | Saca el vehículo del catálogo. **No se borra**: se desactiva (`activo = false`) para conservar el historial de solicitudes. |
| **Parámetros** | `id` en la dirección. Sin cuerpo. |
| **Autenticación** | Sesión de **administrador**. |

Respuesta `200`:

```json
{
    "ok": true,
    "mensaje": "Vehículo desactivado.",
    "datos": { "id": 15, "marca": "Porsche", "modelo": "Taycan Turbo S", "activo": false, "...": "..." }
}
```

Errores:

- `400 id_requerido`: falta `?id=`.
- `401 no_autenticado` y `403 sin_permiso`.
- `404 no_encontrado`: no existe un vehículo con ese id.
- `409 solicitudes_pendientes`: tiene solicitudes de compra sin responder. Primero hay que atenderlas.

  ```json
  {
      "ok": false,
      "error": { "codigo": "solicitudes_pendientes", "mensaje": "Tiene solicitudes pendientes. Atiéndelas primero." }
  }
  ```

- `409 ya_desactivado`: ya estaba desactivado.

Otros métodos (por ejemplo `PATCH`): `405 metodo_no_permitido`, con la cabecera `Allow: GET, POST, PUT, DELETE`.

---

## 3. Endpoints de sesión

Sirven para probar la API desde Postman o curl. Usan las mismas funciones que la página de inicio de sesión.

### 3.1 Iniciar sesión

| | |
|---|---|
| **Ruta y método** | `POST /apex/backend/api/sesion.php` |
| **Datos requeridos** (JSON) | `correo`, `contrasena` |
| **Autenticación** | No necesita. |

```
POST /apex/backend/api/sesion.php
Content-Type: application/json

{ "correo": "admin@apexluxury.com", "contrasena": "Admin123*" }
```

Respuesta `200` (y la cookie `PHPSESSID` de la sesión):

```json
{
    "ok": true,
    "mensaje": "Bienvenido, Administrador Apex.",
    "datos": { "id": 1, "nombre": "Administrador Apex", "rol": "administrador" }
}
```

Errores: `400 datos_invalidos` (falta el correo o la contraseña), `401 credenciales_invalidas` (mismo mensaje si falla el correo o la contraseña) y `415`.

### 3.2 Ver la sesión actual

`GET /apex/backend/api/sesion.php` responde `200` con `{ "id", "nombre", "rol" }`, o `401 no_autenticado` si no hay sesión.

### 3.3 Cerrar sesión

`DELETE /apex/backend/api/sesion.php` responde `200` con `"mensaje": "Sesión cerrada."`, o `401 no_autenticado` si no había sesión.

---

## 4. Cómo probar la API

Cuentas de demostración (de `bases_de_datos/datos_apex_luxury_motors.sql`):

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@apexluxury.com` | `Admin123*` |
| Cliente | `cliente@apexluxury.com` | `Cliente123*` |

### Con Postman

1. En Postman: **Import** → elegir `backend/apex_api.postman_collection.json`.
2. Revisar que la variable `base` de la colección sea `http://localhost/apex/backend/api`.
3. Ejecutar las solicitudes en orden (están numeradas). Postman guarda la cookie de la sesión sola, así que después de "Iniciar sesión (admin)" las operaciones de administrador funcionan.
4. La colección incluye los casos de error: 404, 400, 401, 403, 405, 409 y 415.

### Con curl (PowerShell en Windows: usar `curl.exe`)

```bash
# público
curl.exe -i "http://localhost/apex/backend/api/vehiculos.php?marca=Ferrari"
curl.exe -i "http://localhost/apex/backend/api/vehiculos.php?id=999"

# iniciar sesión y guardar la cookie en sesion.txt
curl.exe -i -c sesion.txt -H "Content-Type: application/json" -d "{\"correo\":\"admin@apexluxury.com\",\"contrasena\":\"Admin123*\"}" http://localhost/apex/backend/api/sesion.php

# crear, actualizar y desactivar con esa sesión
curl.exe -i -b sesion.txt -H "Content-Type: application/json" -d "{\"marca\":\"Porsche\",\"modelo\":\"Taycan Turbo S\",\"precio\":185000,\"categoria\":\"Deportivo alemán\"}" http://localhost/apex/backend/api/vehiculos.php
curl.exe -i -b sesion.txt -X PUT -H "Content-Type: application/json" -d "{\"estado\":\"vendido\"}" "http://localhost/apex/backend/api/vehiculos.php?id=15"
curl.exe -i -b sesion.txt -X DELETE "http://localhost/apex/backend/api/vehiculos.php?id=15"
```

Cambia `15` por el id que devolvió la creación.

### Desde el sitio (consumo de la API)

1. Iniciar sesión como administrador y abrir **Inventario**.
2. **Marcar como vendido / disponible** en cualquier vehículo: la fila, el sello y las cifras cambian sin recargar (`PUT`).
3. **Desactivar** un vehículo sin solicitudes pendientes: la fila pasa a "Fuera del catálogo" (`DELETE`).
4. **Respuesta de error**: desactivar un vehículo que tenga una solicitud pendiente. La API responde `409` y la pantalla muestra el motivo.

En las herramientas del navegador (F12 → Red), filtrando por `api`, se ven la solicitud, el código de respuesta y el JSON.

---

## 5. Limitaciones

- Las fotos se suben desde el formulario del sitio; la API solo las lee.
- Si no hay conexión con la base de datos, `backend/config/conexion.php` responde un texto plano en lugar de JSON.
- Los mensajes de error (`error.mensaje`) están en español; la pantalla del inventario los traduce a partir de `error.codigo`.
