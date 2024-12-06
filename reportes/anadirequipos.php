<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: iniciar-sesion.php");
    exit();
}

include 'config.php'; // Incluir la conexión a la base de datos

// Obtener el id_ubicacion desde la URL
$id_ubicacion = isset($_GET['id_ubicacion']) ? $_GET['id_ubicacion'] : null;
if (!$id_ubicacion) {
    die("Error: id_ubicacion no especificado.");
}

// Obtener los tipos de equipo existentes
$sql_tipos_equipo = "SELECT id_tipoequipo, tipo_equipo FROM tipoequipo";
$result_tipos_equipo = $conn->query($sql_tipos_equipo);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los valores del formulario
    $numero_serie = $_POST['numero_serie'];
    $marca = $_POST['marca'];
    $modelo = $_POST['modelo'];
    $tipo_equipo = $_POST['tipo_equipo']; // Aquí estás obteniendo el nombre del tipo de equipo

    // Insertar los datos en la tabla 'equipos', omitiendo el id_equipo para que se genere automáticamente
    $sql_insert_equipo = "INSERT INTO equipos (numero_serie, marca, modelo, tipo_equipo, id_ubicacion) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert_equipo);
    $stmt_insert->bind_param("ssssi", $numero_serie, $marca, $modelo, $tipo_equipo, $id_ubicacion);
    $stmt_insert->execute();

    // Obtener el id del equipo recién insertado
    $id_equipo = $conn->insert_id;

    // Redirigir a la página de configuración si el tipo de equipo es "Computadora"
    if ($tipo_equipo === "Computadora") {
        header("Location: anadirconfiguracion.php?id_ubicacion=" . $id_ubicacion . "&id_equipo=" . $id_equipo);
        exit();
    } else {
        // Redirigir a la página de equipos después de agregar
        header("Location: equipos.php?id_ubicacion=" . $id_ubicacion);
        exit();
    }
}

// Configuración para el layout
$title = "Agregar un Equipo";
ob_start();
?>

<div class="row mb-3">
    <div class="col text-center">
        <h2>Agregar un Equipo</h2>
    </div>
</div>

<form method="POST" action="anadirequipos.php?id_ubicacion=<?php echo $id_ubicacion; ?>" class="container">
    <div class="form-group">
        <label for="numero_serie">Número de Serie:</label>
        <input type="text" name="numero_serie" class="form-control" placeholder="Número de Serie" required>
    </div>

    <div class="form-group mt-3">
        <label for="marca">Marca:</label>
        <input type="text" name="marca" class="form-control" placeholder="Marca" required>
    </div>

    <div class="form-group mt-3">
        <label for="modelo">Modelo:</label>
        <input type="text" name="modelo" class="form-control" placeholder="Modelo" required>
    </div>

    <div class="form-group mt-3">
        <label for="tipo_equipo">Tipo de Equipo:</label>
        <select name="tipo_equipo" class="form-control" required>
            <option value="">-- Seleccionar Tipo de Equipo --</option>
            <?php while ($row = $result_tipos_equipo->fetch_assoc()): ?>
                <option value="<?php echo $row['tipo_equipo']; ?>"><?php echo $row['tipo_equipo']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary mt-4">Agregar Equipo</button>
</form>

<?php
$content = ob_get_clean();
include 'layout.php';
$conn->close();
?>
