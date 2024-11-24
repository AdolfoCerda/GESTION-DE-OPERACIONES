<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

include 'config.php'; // Incluir la conexión a la base de datos

// Obtener el id_equipo y id_ubicacion desde la URL
$id_equipo = isset($_GET['id_equipo']) ? $_GET['id_equipo'] : null;
$id_ubicacion = isset($_GET['id_ubicacion']) ? $_GET['id_ubicacion'] : null;

// Verificar que ambos IDs estén presentes
if (!$id_equipo || !$id_ubicacion) {
    die("Error: id_equipo o id_ubicacion no especificados.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los valores del formulario
    $procesador = $_POST['procesador'];
    $memoria_ram = $_POST['memoria_ram'];
    $almacenamiento = $_POST['almacenamiento'];
    $tarjeta_grafica = $_POST['tarjeta_grafica'];
    $sistema_operativo = $_POST['sistema_operativo'];
    $encargado = $_POST['encargado'];

    // Insertar los datos en la tabla 'configuraciones'
    $sql_insert_configuracion = "INSERT INTO configuraciones (id_equipo, procesador, memoria_ram, almacenamiento, tarjeta_grafica, sistema_operativo, encargado) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert_configuracion);
    $stmt_insert->bind_param("issssss", $id_equipo, $procesador, $memoria_ram, $almacenamiento, $tarjeta_grafica, $sistema_operativo, $encargado);
    $stmt_insert->execute();

    // Redirigir a la página de equipos
    header("Location: equipos.php?id_ubicacion=" . $id_ubicacion);
    exit();
}

// Configuración para el layout
$title = "Añadir Configuración";
ob_start();
?>

<div class="row mb-3">
    <div class="col text-center">
        <h2>Añadir Configuración al Equipo</h2>
    </div>
</div>

<form method="POST" action="anadirconfiguracion.php?id_equipo=<?php echo $id_equipo; ?>&id_ubicacion=<?php echo $id_ubicacion; ?>" class="container">
    <div class="form-group">
        <label for="procesador">Procesador:</label>
        <input type="text" name="procesador" class="form-control" required>
    </div>

    <div class="form-group mt-3">
        <label for="memoria_ram">Memoria RAM:</label>
        <input type="text" name="memoria_ram" class="form-control" required>
    </div>

    <div class="form-group mt-3">
        <label for="almacenamiento">Almacenamiento:</label>
        <input type="text" name="almacenamiento" class="form-control" required>
    </div>

    <div class="form-group mt-3">
        <label for="tarjeta_grafica">Tarjeta Gráfica:</label>
        <input type="text" name="tarjeta_grafica" class="form-control" required>
    </div>

    <div class="form-group mt-3">
        <label for="sistema_operativo">Sistema Operativo:</label>
        <input type="text" name="sistema_operativo" class="form-control" required>
    </div>

    <div class="form-group mt-3">
        <label for="encargado">Encargado:</label>
        <input type="text" name="encargado" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary mt-4">Agregar Configuración</button>
</form>

<?php
$content = ob_get_clean();
include 'layout.php';
$conn->close();
?>