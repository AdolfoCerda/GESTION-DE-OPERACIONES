<?php
include 'config.php'; // Incluir la conexión a la base de datos

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['usuario'];
    $contrasena = $_POST['contrasena'];

    // Consultar la base de datos para encontrar el usuario
    $sql = "SELECT * FROM Usuarios WHERE nombre_usuario = ? AND contrasena = ?";
    $stmt = $conn->prepare($sql);

    // Bind de parámetros
    $stmt->bind_param("ss", $usuario, $contrasena);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Usuario y contraseña correctos
        session_start();
        $row = $result->fetch_assoc();
        $_SESSION['usuario'] = $usuario; // Guardar el nombre de usuario en la sesión
        $_SESSION['tipo_usuario'] = $row['tipo_usuario'];
        header("Location: inicio.php");
        exit();
    } else {
        // Usuario o contraseña incorrectos
        header("Location: index.php?error=1");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
