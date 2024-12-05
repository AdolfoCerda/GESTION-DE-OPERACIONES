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

// Eliminar ubicación si se envía un ID para eliminar
if (isset($_POST['eliminar'])) {
    $id_ubicacion = $_POST['id_ubicacion'];

    // Eliminar primero de la tabla Ubicaciones_Departamentos
    $sql_delete_dept = "DELETE FROM Ubicaciones_Departamentos WHERE id_ubicacion = ?";
    $stmt_dept = $conn->prepare($sql_delete_dept);
    $stmt_dept->bind_param("i", $id_ubicacion);
    $stmt_dept->execute();
    $stmt_dept->close();

    // Eliminar de la tabla Ubicaciones
    $sql_delete_ubicacion = "DELETE FROM Ubicaciones WHERE id_ubicacion = ?";
    $stmt_ubicacion = $conn->prepare($sql_delete_ubicacion);
    $stmt_ubicacion->bind_param("i", $id_ubicacion);
    $stmt_ubicacion->execute();
    $stmt_ubicacion->close();
}

// Obtener todas las ubicaciones de la base de datos, incluyendo id_ubicacion
$sql = "SELECT Ubicaciones.id_ubicacion, Ubicaciones.nombre_ubicacion, Edificios.nombre_edificio, Departamentos.nombre_departamento 
        FROM Ubicaciones
        INNER JOIN Edificios ON Ubicaciones.id_edificio = Edificios.id_edificio
        INNER JOIN Ubicaciones_Departamentos ON Ubicaciones.id_ubicacion = Ubicaciones_Departamentos.id_ubicacion
        INNER JOIN Departamentos ON Ubicaciones_Departamentos.id_departamento = Departamentos.id_departamento";
$result = $conn->query($sql);

// Preparar el título y el contenido para incluir en el layout
$title = "Ubicaciones";
ob_start();
?>

<div class="row">
    <div class="col text-end">
        <h2 style="text-align: center;">Ubicaciones</h2>
        <?php if ($tipo_usuario === 'admin'): ?>
            <a href="anadirubicaciones.php" class="btn btn-primary">Añadir Ubicación</a>
        <?php endif; ?>
    </div>
</div>

<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Edificio</th>
            <th>Departamento</th>
            <?php if ($tipo_usuario === 'admin'): ?>
                <th>Acciones</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo '<td><a href="equipos.php?id_ubicacion=' . $row['id_ubicacion'] . '">' . $row['nombre_ubicacion'] . '</a></td>';
                echo "<td>" . $row['nombre_edificio'] . "</td>";
                echo "<td>" . $row['nombre_departamento'] . "</td>";
                echo '<td>
                        <form method="POST" action="ubicaciones.php" onsubmit="return confirm(\'¿Estás seguro de que deseas eliminar esta ubicación?\');">
                            <input type="hidden" name="id_ubicacion" value="' . $row['id_ubicacion'] . '"> ';
                            if ($tipo_usuario === 'admin') {
                                echo '<button type="submit" name="eliminar" class="btn btn-danger btn-sm">Eliminar</button>';
                            }
                            
                        echo '</form>
                    </td>';
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No hay ubicaciones registradas.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
include 'layout.php'; // Incluir el layout después de generar el contenido
$conn->close();
?>
