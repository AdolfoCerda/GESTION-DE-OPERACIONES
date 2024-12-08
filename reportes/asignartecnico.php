<?php
session_start();
include 'config.php'; // Conexión a la base de datos

// Verificar si los datos han sido enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reporte'], $_POST['area_seleccionada'], $_POST['prioridad'], $_POST['tecnico_seleccionado'])) {
    $id_reporte = (int)$_POST['id_reporte'];
    $id_area = (int)$_POST['area_seleccionada'];
    $prioridad = $_POST['prioridad'];
    $id_tecnico = (int)$_POST['tecnico_seleccionado'];

    // Validar datos recibidos
    if ($id_reporte > 0 && $id_area > 0 && !empty($prioridad) && $id_tecnico > 0) {
        $sql = "UPDATE reportes SET id_area = ?, prioridad = ?, id_tecnico = ?, estado = 'Asignado' WHERE id_reporte = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isii", $id_area, $prioridad, $id_tecnico, $id_reporte);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Reporte asignado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al asignar reporte: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos enviados.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud no válida.']);
}

$conn->close();
?>
