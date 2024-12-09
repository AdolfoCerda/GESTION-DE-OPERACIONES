<?php
include 'config.php'; // Incluir la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_reporte = $_POST['id_reporte'];
    $comentario = $_POST['comentario'];

    // Verificar que el comentario no esté vacío
    if (empty($comentario)) {
        echo json_encode(['success' => false, 'message' => 'Comentario vacío']);
        exit;
    }

    // Actualizar el comentario en la base de datos
    $sql = "UPDATE reportes SET comentarios = ? WHERE id_reporte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $comentario, $id_reporte);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Comentario guardado']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar el comentario']);
    }

    $stmt->close();
}
$conn->close();
?>
