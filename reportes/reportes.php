<?php
// Iniciar sesión
session_start();
include 'config.php'; // Incluir la conexión a la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

//Por el momento, mostrar todos los reportes para todos los usuarios
$sql_reportes = "SELECT * FROM reportes";

$result_reportes = $conn->query($sql_reportes);

// Preparar el título y el contenido para incluir en el layout
$title = "Reportes";
ob_start();
?>

<div class="row">
    <div class="col text-end">
        <a href="anadirreportes.php" class="btn btn-primary">Levantar Reporte</a>
    </div>
</div>

<table class="table table-striped mt-3">
    <tr>
        <th>ID</th>
        <th>Ubicación</th>
        <th>Equipo</th>
        <th>Descripción</th>
        <th>Fecha</th>
        <th>Hora</th>
        <th>Técnico</th>
        <th>Estado</th>
    </tr>

    <?php while ($row = $result_reportes->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id_reporte'] ?></td>
            <td><?= $row['ubicacion'] ?></td>
            <td><?= $row['equipo'] ?></td>
            <td><?= $row['descripcion'] ?></td>
            <td><?= $row['fecha'] ?></td>
            <td><?= $row['hora'] ?></td>
            <td><?= isset($tecnicos[$row['id_tecnico']]) ? $tecnicos[$row['id_tecnico']] : 'Pendiente por asignar' ?></td>
            <td><?= $row['estado'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<?php
$content = ob_get_clean();
include 'layout.php'; // Incluir el layout después de generar el contenido
$conn->close();
?>