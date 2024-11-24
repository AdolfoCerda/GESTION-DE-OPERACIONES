<?php
session_start();

// Si el usuario ya ha iniciado sesión, redirigir a la página de inicio
if (isset($_SESSION['usuario'])) {
    header("Location: inicio.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Control de Mantenimiento y Servicios</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #343a40; /* Color de fondo similar al del header */
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .login-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px; /* Ancho máximo del formulario */
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        header {
            background-color: #343a40; /* Fondo del header */
            padding: 15px;
            color: white;
            text-align: center;
            width: 100%;
        }

        .logo {
            max-width: 150px;
        }

        .header-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <img src="imagenes/logo.png" alt="Logo de la Universidad" style="max-width: 150px;">
            <h1>Control de Mantenimiento y Servicios</h1>
        </div>
    </header>
    
    <main>
        <div class="login-container">
            <h2>Iniciar Sesión</h2>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <input type="text" id="usuario" name="usuario" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Ingresar</button>
            </form>

            <?php
            if (isset($_GET['error']) && $_GET['error'] == 1) {
                echo "<p class='text-danger mt-3'>Datos inválidos</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>
