<?php
// Incluir la configuración de conexión
include 'config.php';

// Verificar si se recibe el id del reporte
if (isset($_POST['id_reporte']) && is_numeric($_POST['id_reporte'])) {
    $id_reporte = (int)$_POST['id_reporte'];
    $calificacion = isset($_POST['calificacion']) ? (int)$_POST['calificacion'] : null;
    $comentarios_calificacion = isset($_POST['comentarios_calificacion']) ? $_POST['comentarios_calificacion'] : '';

    // Actualizar el estado del reporte a "Cerrado", agregar calificación y comentarios
    $sql = "UPDATE reportes SET estado = 'Cerrado', calificacion = ?, comentarios_calificacion = ? WHERE id_reporte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $calificacion, $comentarios_calificacion, $id_reporte);

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
