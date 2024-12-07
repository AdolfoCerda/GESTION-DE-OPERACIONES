<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: iniciar-sesion.php");
    exit();
}

include 'config.php'; // Incluir la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener los valores del formulario
    $nombre_servicio = $_POST['nombre_servicio'];
    $tiempo_estimado = $_POST['tiempo_estimado'];
    $costo_estimado = $_POST['costo_estimado'];

    // Insertar los datos en la tabla 'servicios', omitiendo el id_servicio para que se genere automáticamente
    $sql_insert_servicio = "INSERT INTO servicios (nombre_servicio, tiempo_estimado, costo_estimado) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert_servicio);
    $stmt_insert->bind_param("ssi", $nombre_servicio, $tiempo_estimado, $costo_estimado);
    $stmt_insert->execute();

    // Redirigir a la página de equipos después de agregar
        header("Location: servicios.php");
        exit();
}

// Configuración para el layout
$title = "Agregar un Servicio";
ob_start();
?>

<div class="row mb-3">
    <div class="col text-center">
        <h2>Agregar un Servicio</h2>
    </div>
</div>

<form method="POST" action="anadirservicios.php" class="container">
    <div class="form-group">
        <label for="nombre_servicio">Nombre del Servicio:</label>
        <input type="text" name="nombre_servicio" class="form-control" placeholder="Nombre Servicio" required>
    </div>

    <div class="form-group mt-3">
        <label for="tiempo_estimado">Tiempo Estimado:</label>
        <input type="text" name="tiempo_estimado" class="form-control" placeholder="Tiempo Estimado" required>
    </div>

    <div class="form-group mt-3">
        <label for="costo_estimado">Costo Estimado:</label>
        <input type="text" name="costo_estimado" class="form-control" placeholder="Costo Estimado" required>
    </div>

    <button type="submit" class="btn btn-primary mt-4">Agregar Servicio</button>
</form>

<?php
$content = ob_get_clean();
include 'layout.php';
$conn->close();
?>
