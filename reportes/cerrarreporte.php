<?php
// Incluir la configuración de conexión
include 'config.php';

// Verificar si se recibe el id del reporte
if (isset($_POST['id_reporte']) && is_numeric($_POST['id_reporte'])) {
    $id_reporte = (int)$_POST['id_reporte'];

    // Actualizar el estado del reporte a "Cerrado"
    $sql = "UPDATE reportes SET estado = 'Cerrado' WHERE id_reporte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_reporte);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reporte cerrado']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al cerrar reporte']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID de reporte no válido']);
}

$conn->close();
?>
