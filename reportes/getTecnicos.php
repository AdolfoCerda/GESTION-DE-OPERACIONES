<?php
include 'config.php'; // Conexión a la base de datos

if (isset($_POST['id_area'])) {
    $id_area = $_POST['id_area'];

    // Consultar los técnicos en el área seleccionada
    $sql = "SELECT id_usuario, nombre_usuario FROM Usuarios WHERE tipo_usuario = 'Tecnico' AND id_area = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_area);
    $stmt->execute();
    $result = $stmt->get_result();

    $tecnicos = [];
    while ($row = $result->fetch_assoc()) {
        $tecnicos[] = $row;
    }

    // Devolver resultado en formato JSON
    if (empty($tecnicos)) {
        echo json_encode(['mensaje' => 'No hay técnicos disponibles']);
    } else {
        echo json_encode($tecnicos);
    }
}
?>