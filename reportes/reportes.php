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
                    <th>Dirigirse con:</th>
                    <td><?= $detalle_reporte['reporta']; ?></td>
                </tr>
                <tr>
                    <th>Área:</th>
                    <!-- Si prioridad no se ha asignado y es admin, seleccionar -->
                    <?php if ($detalle_reporte['id_area'] === null && $tipo_usuario === 'admin'): ?>
                    <td>
                        <select name="area_seleccionada" id="area_seleccionada" required>
                            <option value="">-- Seleccionar área --</option>
                            <?php
                            $sql_areas = "SELECT id_area, nombre_area FROM areas";
                            $result_areas = $conn->query($sql_areas);
                            while ($area = $result_areas->fetch_assoc()): ?>
                                <option value="<?= $area['id_area']; ?>" 
                                    <?= $detalle_reporte['id_area'] == $area['id_area'] ? 'selected' : ''; ?>>
                                    <?= $area['nombre_area']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </td>
                    <?php endif; ?>
                    <!-- Si tecnico no se ha asignado y no es admin, pendiente -->
                    <?php if ($detalle_reporte['id_area'] === null && $tipo_usuario !== 'admin'): ?>
                        <td>Pendiente por asignar</td>
                    <?php endif; ?>
                    <!-- Si tecnico se asingó, mostrar -->
                    <?php if ($detalle_reporte['id_area'] !== null): ?>
                        <?php
                        //Obtener nombre_area con id_area
                        $sql = "SELECT nombre_area FROM areas WHERE id_area = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $detalle_reporte['id_area']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $nombreArea = $result->fetch_assoc()['nombre_area'];
                        ?>
                        <td><?=$nombreArea?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <th>Prioridad:</th>
                    <!-- Si prioridad no se ha asignado y es admin, seleccionar -->
                    <?php if ($detalle_reporte['prioridad'] === '' && $tipo_usuario === 'admin'): ?>
                    <td>
                        <select name="prioridad" id="prioridad" required>
                            <option value="">-- Seleccionar prioridad --</option>
                            <option value="Baja" <?= $detalle_reporte['prioridad'] === 'Baja' ? 'selected' : ''; ?>>Baja</option>
                            <option value="Media" <?= $detalle_reporte['prioridad'] === 'Media' ? 'selected' : ''; ?>>Media</option>
                            <option value="Alta" <?= $detalle_reporte['prioridad'] === 'Alta' ? 'selected' : ''; ?>>Alta</option>
                        </select>
                    </td>
                    <?php endif; ?>
                    <!-- Si prioridad no se ha asignado y no es admin, pendiente -->
                    <?php if ($detalle_reporte['prioridad'] === '' && $tipo_usuario !== 'admin'): ?>
                        <td>Pendiente por asignar</td>
                    <?php endif; ?>
                    <!-- Si prioridad se asignó, mostrar -->
                    <?php if ($detalle_reporte['prioridad'] !== ''): ?>
                        <td><?= $detalle_reporte['prioridad']; ?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <th>Técnico:</th>
                    <!-- Si tecnico no se ha asignado y es admin, seleccionar -->
                    <?php if ($detalle_reporte['id_tecnico'] === null && $tipo_usuario === 'admin'): ?>
                    <td>
                        <select name="tecnico_seleccionado" id="tecnico_seleccionado" required>
                            <option value="">-- Seleccionar técnico --</option>
                            <?php
                            $sql_tecnicos = "SELECT id_usuario, nombre_usuario FROM Usuarios WHERE tipo_usuario = 'Tecnico'";
                            $result_tecnicos = $conn->query($sql_tecnicos);
                            while ($tecnico = $result_tecnicos->fetch_assoc()): ?>
                                <option value="<?= $tecnico['id_usuario']; ?>" 
                                    <?= $detalle_reporte['id_tecnico'] == $tecnico['id_usuario'] ? 'selected' : ''; ?>>
                                    <?= $tecnico['nombre_usuario']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </td>
                    <?php endif; ?>
                    <!-- Si tecnico no se ha asignado y no es admin, pendiente -->
                    <?php if ($detalle_reporte['id_tecnico'] === null && $tipo_usuario !== 'admin'): ?>
                        <td>Pendiente por asignar</td>
                    <?php endif; ?>
                    <!-- Si tecnico se asingó, mostrar -->
                    <?php if ($detalle_reporte['id_tecnico'] !== null): ?>
                        <?php
                        //Obtener nombre_usuario de usuarios donde id_usuario = id_tecnico
                        $sql = "SELECT nombre_usuario FROM Usuarios WHERE id_usuario = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $detalle_reporte['id_tecnico']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $nombreTecnico = $result->fetch_assoc()['nombre_usuario'];
                        ?>
                        <td><?=$nombreTecnico?></td>
                    <?php endif; ?>
                </tr>
                <tr>
                    <th>Estado:</th>
                    <td><?= $detalle_reporte['estado']; ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Botón para guardar cambios -->
         <!-- Si es admin y area, prioridad y tecnico son nulos-->
          <?php if ($detalle_reporte['id_area'] === null && $detalle_reporte['prioridad'] === '' && $detalle_reporte['id_tecnico'] === null && $tipo_usuario === 'admin'): ?>
        <div style="text-align: center; margin-top: 20px;">
            <input type="hidden" name="id_reporte" value="<?= htmlspecialchars($id_reporte_seleccionado); ?>">
            <button type="button" class="btn btn-success" onclick="guardarCambios()">Guardar Cambios</button>
            <div id="guardarMensaje" style="margin-top: 15px;"></div>
        </div>
        <?php endif; ?>

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
            <th>Folio</th>
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

    .detalle-table th, .detalle-table td {
        text-align: left;
        padding: 10px;
    }
</style>

<script>
    function mostrarModal(idReporte) {
        fetch(`reportes.php?id_reporte=${idReporte}`)
            .then(response => response.text())
            .then(data => {
                document.getElementById('detalleTitulo').textContent = `Detalle del Reporte #${idReporte}`;
                document.getElementById('detalleContenido').innerHTML = data;
                document.getElementById('detalleModal').style.display = 'block';
            })
            .catch(error => console.error('Error:', error));
    }

    function cerrarModal() {
        document.getElementById('detalleModal').style.display = 'none';
    }

    function guardarCambios() {
        const idReporte = document.querySelector('#detalleModal input[name="id_reporte"]').value;
        const areaSeleccionada = document.getElementById('area_seleccionada').value;
        const prioridad = document.getElementById('prioridad').value;
        const tecnicoSeleccionado = document.getElementById('tecnico_seleccionado').value;
        const mensajeDiv = document.getElementById('guardarMensaje');

        if (!areaSeleccionada || !prioridad || !tecnicoSeleccionado) {  
            mensajeDiv.innerHTML = '<p style="color: red;">Por favor asigna el área, incidencia, prioridad y técnico.</p>';
            return;
        }

        fetch('asignartecnico.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_reporte=${encodeURIComponent(idReporte)}&area_seleccionada=${encodeURIComponent(areaSeleccionada)}&prioridad=${encodeURIComponent(prioridad)}&tecnico_seleccionado=${encodeURIComponent(tecnicoSeleccionado)}`,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mensajeDiv.innerHTML = `<p style="color: green;">${data.message}</p>`;
                setTimeout(() => location.reload(), 1500);
            } else {
                mensajeDiv.innerHTML = `<p style="color: red;">${data.message}</p>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mensajeDiv.innerHTML = '<p style="color: red;">Ocurrió un error al guardar los cambios.</p>';
        });
    }
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
$conn->close();
?>
