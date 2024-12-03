<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

include 'config.php'; // Conexión a la base de datos

// Verificar si se recibió el ID del reporte por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reporte'])) {
    $id_reporte = (int)$_POST['id_reporte'];

    // Obtener los técnicos disponibles
    $sql_tecnicos = "SELECT id_usuario, nombre_usuario FROM Usuarios WHERE tipo_usuario = 'Tecnico'";
    $result_tecnicos = $conn->query($sql_tecnicos);

    if (!$result_tecnicos) {
        die("Error al obtener técnicos: " . $conn->error);
    }

    // Verificar si se han enviado el técnico y la prioridad seleccionados
    if (isset($_POST['tecnico_seleccionado']) && isset($_POST['prioridad'])) {
        $id_tecnico = (int)$_POST['tecnico_seleccionado'];
        $prioridad = $_POST['prioridad']; // Baja, Media, Alta

        // Actualizar el reporte con el técnico y la prioridad asignados
        $sql_asignar = "UPDATE reportes SET id_tecnico = ?, prioridad = ? WHERE id_reporte = ?";
        $stmt = $conn->prepare($sql_asignar);
        $stmt->bind_param("isi", $id_tecnico, $prioridad, $id_reporte);

        if ($stmt->execute()) {
            // Redirigir a la lista de reportes
            header("Location: reportes.php");
            exit();
        } else {
            echo "Error al asignar el técnico y la prioridad: " . $stmt->error;
        }
    }
} else {
    die("No se recibió un ID de reporte válido.");
}

// Configuración para el layout
$title = "Asignar Técnico y Prioridad";
ob_start();
?>

<div class="row mb-3">
    <div class="col text-center">
        <h2>Asignar Técnico y Prioridad al Reporte #<?= htmlspecialchars($id_reporte); ?></h2>
    </div>
</div>

<form method="POST" action="asignartecnico.php" class="container">
    <input type="hidden" name="id_reporte" value="<?= htmlspecialchars($id_reporte); ?>">
    
    <!-- Combobox para seleccionar técnico -->
    <label for="tecnico">Seleccionar Técnico:</label><br>
    <select name="tecnico_seleccionado" id="tecnico" required>
        <option value="">-- Seleccionar técnico --</option>
        <?php while ($tecnico = $result_tecnicos->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($tecnico['id_usuario']); ?>">
                <?= htmlspecialchars($tecnico['nombre_usuario']); ?>
            </option>
        <?php endwhile; ?>
    </select><br><br>
    
    <!-- Combobox para seleccionar prioridad -->
    <label for="prioridad">Seleccionar Prioridad:</label><br>
    <select name="prioridad" id="prioridad" required>
        <option value="">-- Seleccionar prioridad --</option>
        <option value="Baja">Baja</option>
        <option value="Media">Media</option>
        <option value="Alta">Alta</option>
    </select><br><br>

    <button type="submit" class="btn btn-primary">Asignar Técnico y Prioridad</button>
</form>

<?php
$content = ob_get_clean();
include 'layout.php';
$conn->close();
?>
