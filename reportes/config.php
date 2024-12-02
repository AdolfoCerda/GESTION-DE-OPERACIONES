<?php
// Datos de conexión
$servername = "localhost";
$username = "root"; // Cambiar por tu usuario de MySQL
$password = ""; // Cambiar por tu contraseña de MySQL
$dbname = "reportes";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión falló
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

//obtener tipo de usuario
$sql = "SELECT tipo_usuario FROM Usuarios WHERE nombre_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['usuario']);
$stmt->execute();
$result = $stmt->get_result();
$tipo_usuario = $result->fetch_assoc()['tipo_usuario'];
$stmt->close();

?>
