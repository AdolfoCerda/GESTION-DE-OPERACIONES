<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

include 'config.php'; // Incluir la conexión a la base de datos

$tipo_usuario = $_SESSION['tipo_usuario'];

// Obtener todas las ubicaciones de la base de datos, incluyendo id_ubicacion
$sql = "SELECT id_servicio, nombre_servicio, tiempo_estimado, costo_estimado 
        FROM servicios";
$result = $conn->query($sql);

// Preparar el título y el contenido para incluir en el layout
$title = "Servicios";
ob_start();
?>

<div class="row">
    <div class="col text-end">
        <h2 style="text-align: center;">Servicios Disponibles</h2>
        <?php if ($tipo_usuario === 'admin'): ?>
            <a href="anadirservicios.php" class="btn btn-primary">Añadir Servicio</a>
        <?php endif; ?>
    </div>
</div>

<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Tiempo Estimado</th>
            <th>Costo Estimado</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['nombre_servicio'] . "</td>";
                echo "<td>" . $row['tiempo_estimado'] . "</td>";
                echo "<td>" . $row['costo_estimado'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No hay servicios registrados.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
include 'layout.php'; // Incluir el layout después de generar el contenido
$conn->close();
?>
