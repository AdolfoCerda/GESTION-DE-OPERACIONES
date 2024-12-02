<?php
include 'config.php'; // Conexión a la base de datos

if (isset($_POST['nombre_ubicacion'])) {

    // Obtener id_ubicacion y nombre_ubicacion
    $nombre_ubicacion = $_POST['nombre_ubicacion'];
    $id_ubicacion = $conn->query("SELECT id_ubicacion FROM Ubicaciones WHERE nombre_ubicacion = '$nombre_ubicacion'")->fetch_assoc()['id_ubicacion'];

    // Consultar los equipos en la ubicación seleccionada
    $sql = "SELECT numero_serie FROM Equipos WHERE id_ubicacion = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_ubicacion);
    $stmt->execute();
    $result = $stmt->get_result();

    $equipos = [];
    while ($row = $result->fetch_assoc()) {
        $equipos[] = $row;
    }

    // Devolver resultado en formato JSON
    if (empty($equipos)) {
        echo json_encode(['mensaje' => 'No hay equipos']);
    } else {
        echo json_encode($equipos);
    }
}
?>
