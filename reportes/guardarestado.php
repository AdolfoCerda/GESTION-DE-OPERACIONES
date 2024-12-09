<?php
include 'config.php'; // Incluir la conexión a la base de datos

// Verificar si la solicitud es válida
if (isset($_POST['id_reporte']) && isset($_POST['estado'])) {
    $id_reporte = $_POST['id_reporte'];
    $estado = $_POST['estado'];

    // Actualizar el estado en la base de datos
    $sql = "UPDATE reportes SET estado = ? WHERE id_reporte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $estado, $id_reporte);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Estado actualizado con éxito."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al actualizar el estado."]);
    }
    $stmt->close();
}

$conn->close();
?>
