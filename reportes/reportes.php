<?php
// Iniciar sesión
session_start();
include 'config.php'; // Incluir la conexión a la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$nombre_usuario = $_SESSION['usuario'];
$tipo_usuario = $_SESSION['tipo_usuario'];

// Mostrar reportes dependiendo el usuario
if ($tipo_usuario == 'docente') {// A docentes solo muestra los reportes que ha levantado
    $sql_reportes = "SELECT 
        r.*,
        u.nombre_ubicacion,
        us.nombre_usuario
    FROM 
        reportes r
    JOIN 
        Ubicaciones u
    ON 
        r.ubicacion = u.id_ubicacion
    LEFT JOIN 
        Usuarios us
    ON 
        r.id_tecnico = us.id_usuario
    WHERE r.reporta = ?";
    $stmt_reportes = $conn->prepare($sql_reportes);
    $stmt_reportes->bind_param("s", $nombre_usuario);
    $stmt_reportes->execute();
    $result_reportes = $stmt_reportes->get_result();
} elseif ($tipo_usuario == 'tecnico') {// A tecnicos solo muestra reportes donde fue asignado
    $sql_reportes = "SELECT 
        r.*,
        u.nombre_ubicacion,
        us.nombre_usuario
    FROM 
        reportes r
    JOIN 
        Ubicaciones u
    ON 
        r.ubicacion = u.id_ubicacion
    LEFT JOIN 
        Usuarios us
    ON 
        r.id_tecnico = us.id_usuario
    WHERE us.nombre_usuario = ?";
    $stmt_reportes = $conn->prepare($sql_reportes);
    $stmt_reportes->bind_param("s", $nombre_usuario);
    $stmt_reportes->execute();
    $result_reportes = $stmt_reportes->get_result();
} else {// A admin muestra todos los reportes
    $sql_reportes = "SELECT 
        r.*,
        u.nombre_ubicacion,
        us.nombre_usuario
    FROM 
        reportes r
    JOIN 
        Ubicaciones u
    ON 
        r.ubicacion = u.id_ubicacion
    LEFT JOIN 
        Usuarios us
    ON 
        r.id_tecnico = us.id_usuario";
    $result_reportes = $conn->query($sql_reportes);
}

// Variables para mostrar el detalle del reporte
$detalle_reporte = [];
$id_reporte_seleccionado = null;

// Verificar si se ha seleccionado un reporte
if (isset($_GET['id_reporte']) && is_numeric($_GET['id_reporte'])) {
    $id_reporte_seleccionado = (int)$_GET['id_reporte'];

    // Obtener el detalle del reporte seleccionado
    $sql_detalle_reporte = "SELECT 
    r.*,
    u.nombre_ubicacion,
    us.nombre_usuario
FROM 
    reportes r
JOIN 
    Ubicaciones u
ON 
    r.ubicacion = u.id_ubicacion
LEFT JOIN 
    Usuarios us
ON 
    r.id_tecnico = us.id_usuario WHERE id_reporte = ?";
    $stmt_detalle_reporte = $conn->prepare($sql_detalle_reporte);
    $stmt_detalle_reporte->bind_param("i", $id_reporte_seleccionado);
    $stmt_detalle_reporte->execute();
    $result_detalle_reporte = $stmt_detalle_reporte->get_result();

    if ($result_detalle_reporte->num_rows > 0) {
        $detalle_reporte = $result_detalle_reporte->fetch_assoc();
    }
}

$title = "Reportes";
ob_start();
?>

<div class="row">
    <div class="col text-end">
        <h2 style="text-align: center;">Reportes</h2>
        <?php if ($tipo_usuario === 'docente'): ?>
            <a href="anadirreportes.php" class="btn btn-primary">Levantar Reporte</a>
        <?php endif; ?>
    </div>
</div>

<table class="table table-striped mt-3">
    <thead>
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
    </thead>
    <tbody>
        <?php while ($row = $result_reportes->fetch_assoc()): ?>
            <tr>
                <td><a href="reportes.php?id_reporte=<?= $row['id_reporte']; ?>"><?= $row['id_reporte']; ?> <i class="bi bi-pencil-square"></i></a></td>
                <td><?= $row['nombre_ubicacion']; ?></td>
                <td><?= $row['equipo']; ?></td>
                <td><?= $row['descripcion']; ?></td>
                <td><?= $row['fecha']; ?></td>
                <td><?= $row['hora']; ?></td>
                <td><?= $row['nombre_usuario'] ? $row['nombre_usuario'] : 'Pendiente por asignar'; ?></td>
                <td><?= $row['estado']; ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php if ($id_reporte_seleccionado): ?>
    <h3 class="mt-5">Detalle del Reporte #<?= $id_reporte_seleccionado; ?></h3>
    <?php if (!empty($detalle_reporte)): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ubicación</th>
                    <th>Equipo</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Técnico</th>
                    <th>Prioridad</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= $detalle_reporte['nombre_ubicacion']; ?></td>
                    <td><?= $detalle_reporte['equipo']; ?></td>
                    <td><?= $detalle_reporte['descripcion']; ?></td>
                    <td><?= $detalle_reporte['fecha']; ?></td>
                    <td><?= $detalle_reporte['hora']; ?></td>
                    <td>
                        <?php if (empty($detalle_reporte['id_tecnico']) && $tipo_usuario == 'admin'): ?>
                            <form method="POST" action="asignartecnico.php">
                                <input type="hidden" name="id_reporte" value="<?= $id_reporte_seleccionado; ?>">
                                <button type="submit" class="btn btn-primary btn-sm">Asignar Técnico</button>
                            </form>
                        <?php elseif (empty($detalle_reporte['id_tecnico'])): ?>
                            Pendiente por asignar
                        <?php else: ?>
                            <?= $detalle_reporte['nombre_usuario']; ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $detalle_reporte['prioridad']; ?></td>
                    <td><?= $detalle_reporte['estado']; ?></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se encontró el detalle del reporte seleccionado.</p>
    <?php endif; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'layout.php';
$conn->close();
?>
