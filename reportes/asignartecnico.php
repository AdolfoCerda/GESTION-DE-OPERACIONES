<?php
session_start();
include 'config.php'; // Conexión a la base de datos

// Verificar si los datos han sido enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_reporte'], $_POST['tecnico_seleccionado'], $_POST['prioridad'])) {
    $id_reporte = (int)$_POST['id_reporte'];
    $id_tecnico = (int)$_POST['tecnico_seleccionado'];
    $prioridad = $_POST['prioridad'];

    // Validar datos recibidos
    if ($id_reporte > 0 && $id_tecnico > 0 && !empty($prioridad)) {
        $sql = "UPDATE reportes SET id_tecnico = ?, prioridad = ? WHERE id_reporte = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $id_tecnico, $prioridad, $id_reporte);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cambios guardados correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar cambios: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos enviados.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud no válida.']);
}

$conn->close();
?>
