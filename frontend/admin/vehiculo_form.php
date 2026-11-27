<?php
// Crear o editar vehículo (RF8)

require_once __DIR__ . "/../../backend/funciones/acceso.php";
require_once __DIR__ . "/../../backend/funciones/vehiculos.php";

requerirRol("administrador");

// id = 0 significa "crear"
$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

// valores vacíos por defecto (modo crear)
$vehiculo = [
    "marca"       => "",
    "modelo"      => "",
    "precio"      => "",
    "categoria"   => "",
    "descripcion" => "",
    "estado"      => "disponible"
];

// modo editar: cargar los datos actuales
if ($id > 0) {
    $encontrado = obtenerVehiculoAdmin($id);

    if (!$encontrado) {
        $_SESSION["mensaje"] = ["tipo" => "error", "texto" => "El vehículo no existe."];
        header("Location: /apex/frontend/admin/vehiculos.php");
        exit;
    }

    $vehiculo = $encontrado;
}

$categorias = listarCategorias();

$tituloPagina = ($id > 0) ? "Editar vehículo" : "Agregar vehículo";
$titulo = $tituloPagina;

require_once __DIR__ . "/../includes/encabezado.php";
require_once __DIR__ . "/../includes/menu.php";
?>

<main>
    <h1><?php echo $tituloPagina; ?></h1>

    <form method="post" action="/apex/backend/procesos/vehiculo_guardar.php">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <p>
            <label for="marca">Marca</label><br>
            <input type="text" id="marca" name="marca" required
                   value="<?php echo htmlspecialchars($vehiculo["marca"]); ?>">
        </p>

        <p>
            <label for="modelo">Modelo</label><br>
            <input type="text" id="modelo" name="modelo" required
                   value="<?php echo htmlspecialchars($vehiculo["modelo"]); ?>">
        </p>

        <p>
            <label for="precio">Precio (USD)</label><br>
            <input type="number" id="precio" name="precio" required min="1" step="0.01"
                   value="<?php echo htmlspecialchars((string) $vehiculo["precio"]); ?>">
        </p>

        <p>
            <label for="categoria">Categoría</label><br>
                <select id="categoria" name="categoria" required>
                    <option value="">Selecciona una categoría</option>
                    <?php foreach ($categorias as $c): ?>
                        <?php $nombreCategoria = is_array($c) ? reset($c) : $c; ?>
                        <option value="<?php echo htmlspecialchars($nombreCategoria); ?>"
                            <?php echo ($nombreCategoria === $vehiculo["categoria"]) ? "selected" : ""; ?>>
                            <?php echo htmlspecialchars($nombreCategoria); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
        </p>

        <p>
            <label for="descripcion">Descripción</label><br>
            <textarea id="descripcion" name="descripcion" rows="5" cols="40"><?php
                echo htmlspecialchars($vehiculo["descripcion"]);
            ?></textarea>
        </p>

        <p>
            <label for="estado">Estado</label><br>
            <select id="estado" name="estado" required>
                <option value="disponible" <?php echo ($vehiculo["estado"] === "disponible") ? "selected" : ""; ?>>Disponible</option>
                <option value="vendido" <?php echo ($vehiculo["estado"] === "vendido") ? "selected" : ""; ?>>Vendido</option>
            </select>
        </p>

        <p>
            <button type="submit"><?php echo ($id > 0) ? "Guardar cambios" : "Agregar vehículo"; ?></button>
            <a href="/apex/frontend/admin/vehiculos.php">Cancelar</a>
        </p>
    </form>
</main>

<?php require_once __DIR__ . "/../includes/pie.php"; ?>