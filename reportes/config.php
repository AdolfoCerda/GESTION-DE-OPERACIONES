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
?>
