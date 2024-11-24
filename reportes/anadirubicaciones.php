<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: iniciar-sesion.php");
    exit();
}

include 'config.php'; // Incluir la conexión a la base de datos

// Obtener el id_departamento del usuario
$usuario_sesion = $_SESSION['usuario'];
$sql_id_departamento = "SELECT id_departamento FROM usuarios WHERE nombre_usuario = ?";
$stmt = $conn->prepare($sql_id_departamento);
$stmt->bind_param("s", $usuario_sesion);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $id_departamento = $row['id_departamento'];
} else {
    // Manejar el caso en que no se encuentre el usuario
    die("Error: No se pudo obtener el id_departamento del usuario.");
}

// Obtener los edificios existentes
$sql_edificios = "SELECT id_edificio, nombre_edificio FROM Edificios";
$result_edificios = $conn->query($sql_edificios);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_ubicacion = $_POST['nombre_ubicacion'];
    $nombre_edificio = $_POST['nombre_edificio'];

    // Verificar si el edificio seleccionado ya existe o es uno nuevo
    if (!empty($_POST['edificio_existente'])) {
        // Caso 1: El usuario selecciona un edificio existente
        $id_edificio = $_POST['edificio_existente'];
    } else {
        // Caso 2: El usuario escribe un nuevo edificio
        $sql_insert_edificio = "INSERT INTO Edificios (nombre_edificio) VALUES (?)";
        $stmt_edificio = $conn->prepare($sql_insert_edificio);
        $stmt_edificio->bind_param("s", $nombre_edificio);
        $stmt_edificio->execute();

        // Obtener el id del edificio recién insertado
        $id_edificio = $conn->insert_id;
    }

    // Insertar la nueva ubicación
    $sql_insert_ubicacion = "INSERT INTO Ubicaciones (nombre_ubicacion, id_edificio) VALUES (?, ?)";
    $stmt_ubicacion = $conn->prepare($sql_insert_ubicacion);
    $stmt_ubicacion->bind_param("si", $nombre_ubicacion, $id_edificio);
    $stmt_ubicacion->execute();

    // Obtener el id de la ubicación recién insertada
    $id_ubicacion = $conn->insert_id;

    // Insertar en Ubicaciones_Departamentos con el id_ubicacion y el id_departamento del usuario
    $sql_insert_ubic_dept = "INSERT INTO Ubicaciones_Departamentos (id_ubicacion, id_departamento) VALUES (?, ?)";
    $stmt_ubic_dept = $conn->prepare($sql_insert_ubic_dept);
    $stmt_ubic_dept->bind_param("ii", $id_ubicacion, $id_departamento);
    $stmt_ubic_dept->execute();

    // Redirigir a la página de ubicaciones después de agregar
    header("Location: ubicaciones.php");
    exit();
}

// Configuración para el layout
$title = "Añadir Ubicación";
ob_start();
?>

<div class="row mb-3">
    <div class="col text-center">
        <h2>Agregar una Ubicación</h2>
    </div>
</div>

<form method="POST" action="anadirubicaciones.php" class="container">
    <div class="form-group">
        <label for="edificio">Edificio:</label>
        <select name="edificio_existente" class="form-control">
            <option value="">-- Seleccionar edificio existente --</option>
            <?php while ($row = $result_edificios->fetch_assoc()): ?>
                <option value="<?php echo $row['id_edificio']; ?>"><?php echo $row['nombre_edificio']; ?></option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="form-group mt-3">
        <label for="nombre_edificio">O añadir un nuevo edificio:</label>
        <input type="text" name="nombre_edificio" class="form-control" placeholder="Nombre del edificio">
    </div>

    <div class="form-group mt-3">
        <label for="nombre_ubicacion">Nombre de la ubicación:</label>
        <input type="text" name="nombre_ubicacion" class="form-control" placeholder="Nombre de la ubicación" required>
    </div>

    <button type="submit" class="btn btn-primary mt-4">Agregar Ubicación</button>
</form>

<?php
$content = ob_get_clean();
include 'layout.php';
?>