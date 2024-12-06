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

// Mostrar reportes dependiendo del usuario
if ($tipo_usuario == 'docente') {
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
} elseif ($tipo_usuario == 'tecnico') {
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
} else {
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

// Manejar solicitudes AJAX para mostrar el detalle del reporte
if (isset($_GET['id_reporte']) && is_numeric($_GET['id_reporte'])) {
    $id_reporte_seleccionado = (int)$_GET['id_reporte'];

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
        r.id_tecnico = us.id_usuario 
    WHERE id_reporte = ?";
    $stmt_detalle_reporte = $conn->prepare($sql_detalle_reporte);
    $stmt_detalle_reporte->bind_param("i", $id_reporte_seleccionado);
    $stmt_detalle_reporte->execute();
    $result_detalle_reporte = $stmt_detalle_reporte->get_result();

    if ($result_detalle_reporte->num_rows > 0) {
        $detalle_reporte = $result_detalle_reporte->fetch_assoc();
        ?>
        <table class="detalle-table">
            <tbody>
                <tr>
                    <th>Ubicación:</th>
                    <td><?= $detalle_reporte['nombre_ubicacion']; ?></td>
                </tr>
                <tr>
                    <th>Equipo:</th>
                    <td><?= $detalle_reporte['equipo']; ?></td>
                </tr>
                <tr>
                    <th>Descripción:</th>
                    <td><?= $detalle_reporte['descripcion']; ?></td>
                </tr>
                <tr>
                    <th>Fecha:</th>
                    <td><?= $detalle_reporte['fecha']; ?></td>
                </tr>
                <tr>
                    <th>Hora:</th>
                    <td><?= $detalle_reporte['hora']; ?></td>
                </tr>
                <tr>
                    <th>Técnico:</th>
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
                </tr>
                <tr>
                    <th>Prioridad:</th>
                    <td><?= $detalle_reporte['prioridad']; ?></td>
                </tr>
                <tr>
                    <th>Estado:</th>
                    <td><?= $detalle_reporte['estado']; ?></td>
                </tr>
            </tbody>
        </table>

        <?php
        exit;
    } else {
        echo "<p>No se encontró el detalle del reporte seleccionado.</p>";
        exit;
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
    <?php if ($result_reportes->num_rows > 0): ?>
        <?php while ($row = $result_reportes->fetch_assoc()): ?>
            <tr>
                <td><a href="javascript:void(0)" onclick="mostrarModal(<?= $row['id_reporte']; ?>)">
                    <?= $row['id_reporte']; ?> <i class="bi bi-pencil-square"></i>
                </a></td>
                <td><?= $row['nombre_ubicacion']; ?></td>
                <td><?= $row['equipo']; ?></td>
                <td><?= $row['descripcion']; ?></td>
                <td><?= $row['fecha']; ?></td>
                <td><?= $row['hora']; ?></td>
                <td><?= $row['nombre_usuario'] ? $row['nombre_usuario'] : 'Pendiente por asignar'; ?></td>
                <td><?= $row['estado']; ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8" class="text-center">No hay reportes levantados.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- Modal -->
<div id="detalleModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModal()">×</span>
        <h3 id="detalleTitulo" style="text-align: center;">Detalle del Reporte</h3>
        <div id="detalleContenido">
            <!-- Aquí se cargará el contenido dinámico -->
        </div>
    </div>
</div>

<style>
    .modal {
        display: none; /* Ocultar por defecto */
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 30%;
        border-radius: 10px;
        position: relative;
        top: 10%; /* Mueve el modal más abajo */
    }

    .close {
        color: red;
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover {
        color: darkred;
    }
    .detalle-table {
    width: 100%;
    border-collapse: collapse; /* Eliminar bordes de la tabla */
    margin: 20px 0;
    background-color: #f9f9f9; /* Fondo gris claro para toda la tabla */
    border-radius: 8px; /* Bordes redondeados */
    overflow: hidden; /* Para asegurar que los bordes redondeados se apliquen */
    }

    .detalle-table th {
        text-align: left;
        font-size: 18px;
        font-weight: bold;
        padding: 10px 15px;
        color: #333; /* Texto gris oscuro para los encabezados */
        background-color: #e0e0e0; /* Fondo gris claro para los encabezados */
        border-bottom: 1px solid #d0d0d0; /* Línea divisoria sutil */
    }

    .detalle-table td {
        font-size: 16px;
        padding: 10px 15px;
        color: #555; /* Texto gris medio para los datos */
        background-color: #f5f5f5; /* Fondo ligeramente más claro para los datos */
    }

    .detalle-table tr:nth-child(even) td {
        background-color: #f0f0f0; /* Alternar fondo para filas pares */
    }

    .detalle-table tr:last-child td {
        border-bottom: none; /* Eliminar la línea inferior de la última fila */
    }

</style>

<script>
    function mostrarModal(idReporte) {
        fetch(`reportes.php?id_reporte=${idReporte}`)
            .then(response => response.text())
            .then(data => {
                // Actualizar el título del modal
                document.getElementById('detalleTitulo').textContent = `Detalle del Reporte #${idReporte}`;
                
                // Insertar el contenido del detalle dentro del modal
                document.getElementById('detalleContenido').innerHTML = data;
                
                // Mostrar el modal
                document.getElementById('detalleModal').style.display = 'block';
            })
            .catch(error => console.error('Error:', error));
    }

    function cerrarModal() {
        document.getElementById('detalleModal').style.display = 'none';
    }
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
$conn->close();
?>
