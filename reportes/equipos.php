<?php
// Iniciar sesión
session_start();
include 'config.php'; // Incluir la conexión a la base de datos

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Verificar si se pasó el id_ubicacion desde la URL
if (!isset($_GET['id_ubicacion']) && !isset($_POST['id_ubicacion'])) {
    echo "No se especificó ninguna ubicación.";
    exit();
}

$tipo_usuario = $_SESSION['tipo_usuario'];

// Obtener el id_ubicacion desde GET o POST
$id_ubicacion = isset($_GET['id_ubicacion']) ? $_GET['id_ubicacion'] : $_POST['id_ubicacion'];
// Obtener el nombre_ubicacion consuldando con id_ubicacion
$sql = "SELECT nombre_ubicacion FROM Ubicaciones WHERE id_ubicacion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_ubicacion);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$nombre_ubicacion = $row['nombre_ubicacion'];


// Eliminar equipo si se envía un número de serie para eliminar
if (isset($_POST['eliminar'])) {
    $numero_serie = $_POST['numero_serie'];

    // Eliminar de la tabla Equipos
    $sql_delete_equipo = "DELETE FROM Equipos WHERE numero_serie = ?";
    $stmt_delete = $conn->prepare($sql_delete_equipo);
    $stmt_delete->bind_param("s", $numero_serie);
    $stmt_delete->execute();
    $stmt_delete->close();
}

// Obtener los equipos de la ubicación seleccionada
$sql = "SELECT numero_serie, modelo, tipo_equipo 
        FROM Equipos 
        WHERE id_ubicacion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_ubicacion);
$stmt->execute();
$result = $stmt->get_result();

// Variables para mostrar la configuración
$configuracion = [];
$id_equipo_seleccionado = null;

// Verificar si se ha seleccionado un equipo
if (isset($_GET['id_equipo'])) {
    $numero_serie_seleccionado = $_GET['id_equipo'];

    // Obtener el id_equipo del equipo seleccionado
    $sql_id_equipo = "SELECT id_equipo FROM Equipos WHERE numero_serie = ?";
    $stmt_id_equipo = $conn->prepare($sql_id_equipo);
    $stmt_id_equipo->bind_param("s", $numero_serie_seleccionado);
    $stmt_id_equipo->execute();
    $result_id_equipo = $stmt_id_equipo->get_result();

    // Verificar si se encontró el id_equipo
    if ($result_id_equipo->num_rows > 0) {
        $row_id_equipo = $result_id_equipo->fetch_assoc();
        $id_equipo_seleccionado = $row_id_equipo['id_equipo'];

        // Obtener la configuración del equipo seleccionado
        $sql_configuracion = "SELECT * FROM configuraciones WHERE id_equipo = ?";
        $stmt_configuracion = $conn->prepare($sql_configuracion);
        $stmt_configuracion->bind_param("i", $id_equipo_seleccionado); // Cambiar a "i" si id_equipo es un entero
        $stmt_configuracion->execute();
        $result_configuracion = $stmt_configuracion->get_result();

        // Almacenar la configuración en un array
        if ($result_configuracion->num_rows > 0) {
            while ($row_config = $result_configuracion->fetch_assoc()) {
                $configuracion[] = $row_config;
            }
        }
    }
}

// Configuración para el layout
$title = "Equipos en la Ubicación Seleccionada";
ob_start();
?>

<div class="row mb-3">
    <div class="col text-end">
        <h2 style="text-align: center;">Equipos en <?php echo $nombre_ubicacion; ?></h2>
        <?php if ($tipo_usuario === 'admin'): ?>
            <a href="anadirequipos.php?id_ubicacion=<?php echo $id_ubicacion; ?>" class="btn btn-primary">Añadir Equipo</a>
        <?php endif; ?>
    </div>
</div>

<table class="table table-striped mt-3">
    <thead>
        <tr>
            <th>No. Serie</th>
            <th>Modelo</th>
            <th>Tipo</th>
            <?php if ($tipo_usuario === 'admin'): ?>
                <th>Acciones</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if ($row['tipo_equipo'] == "Computadora"): ?>
                            <a href="equipos.php?id_ubicacion=<?php echo $id_ubicacion; ?>&id_equipo=<?php echo $row['numero_serie']; ?>"><?php echo $row['numero_serie']; ?></a>
                        <?php else: ?>
                            <?php echo $row['numero_serie']; ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $row['modelo']; ?></td>
                    <td><?php echo $row['tipo_equipo']; ?></td>
                    <td>
                        <form method="POST" action="equipos.php" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este equipo?');">
                            <input type="hidden" name="numero_serie" value="<?php echo $row['numero_serie']; ?>">
                            <input type="hidden" name="id_ubicacion" value="<?php echo $id_ubicacion; ?>">
                            <?php if ($tipo_usuario == 'admin'): ?>
                                <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">No hay equipos registrados para esta ubicación.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if ($id_equipo_seleccionado): ?>
    <h2 class="mt-5" style="text-align: center;">Configuración del Equipo <?php echo $numero_serie_seleccionado; ?></h2>
    <?php if (!empty($configuracion)): ?>
        <table class="table table-striped mt-3">
            <thead class="thead-light">
                <tr>
                    <th>Procesador</th>
                    <th>Memoria RAM</th>
                    <th>Almacenamiento</th>
                    <th>Tarjeta Gráfica</th>
                    <th>Sistema Operativo</th>
                    <th>Programas Instalados</th>
                    <th>Encargado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($configuracion as $config): ?>
                <tr>
                <td><?php echo $config['procesador']; ?></td>
                <td><?php echo $config['memoria_ram']; ?></td>
                <td><?php echo $config['almacenamiento']; ?></td>
                <td><?php echo $config['tarjeta_grafica']; ?></td>
                <td><?php echo $config['sistema_operativo']; ?></td>
                <td><?php echo $config['programas']; ?></td>
                <td><?php echo $config['encargado']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No hay configuración registrada para este equipo.</p>
    <?php endif; ?>
<?php endif; ?>

<?php
$content = ob_get_clean();
include 'layout.php';
$conn->close();
?>
