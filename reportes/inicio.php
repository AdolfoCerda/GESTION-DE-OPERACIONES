<?php
// Iniciar sesión
session_start();



// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$title = "Inicio";
ob_start(); // Comienza a capturar el contenido de la página
?>

<div class="row">
    <div class="col text-center">
        <h2>Bienvenido al Sistema de Control de Mantenimiento</h2>
        <p>Desde aquí puedes gestionar reportes y equipos.</p>
    </div>
</div>

<?php
$content = ob_get_clean(); // Obtiene el contenido capturado y lo almacena en $content
include 'layout.php'; // Incluye el layout principal
?>
