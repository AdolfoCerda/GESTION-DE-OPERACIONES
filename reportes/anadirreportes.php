<?php
// Iniciar sesión
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: iniciar-sesion.php");
    exit();
}

include 'config.php'; // Incluir la conexión a la base de datos

// Establecer la zona horaria a GMT-7
date_default_timezone_set('America/Chihuahua'); // Puedes ajustar según tu ubicación específica

// Obtener todas las ubicaciones existentes
$sql_ubicaciones = "SELECT id_ubicacion, nombre_ubicacion FROM Ubicaciones";
$result_ubicaciones = $conn->query($sql_ubicaciones);

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_ubicacion = $_POST['ubicacion'];
    $equipo = $_POST['equipo'];
    $descripcion = $_POST['descripcion'];

    // Obtener la fecha y hora actual
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');

    // Insertar el reporte en la tabla reportes
    $sql_insert_reporte = "INSERT INTO reportes (ubicacion, equipo, descripcion, fecha, hora) 
                           VALUES (?, ?, ?, ?, ?)";
    $stmt_reporte = $conn->prepare($sql_insert_reporte);
    $stmt_reporte->bind_param("sssss", $nombre_ubicacion, $equipo, $descripcion, $fecha, $hora);
    $stmt_reporte->execute();

    // Redirigir a la página de reportes después de agregar
    header("Location: reportes.php");
    exit();
}

// Configuración para el layout
$title = "Añadir Reporte";
ob_start();
?>

<div class="row mb-3">
    <div class="col text-center">
        <h2>Levantar un Reporte</h2>
    </div>
</div>

<form method="POST" action="anadirreportes.php" class="container">
    <!-- Selección de ubicación existente -->
    <label for="ubicacion">Ubicación:</label><br>
    <select name="ubicacion" id="ubicacion">
        <option value="">-- Seleccionar ubicación --</option>
        <?php while ($row = $result_ubicaciones->fetch_assoc()): ?>
            <option value="<?php echo $row['id_ubicacion']; ?>"><?php echo $row['nombre_ubicacion']; ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <!-- Selección de equipos existentes en la ubicación -->
    <label for="equipo">Equipo:</label><br>
    <select name="equipo" id="equipo">
        <option value="">-- Seleccionar equipo --</option>
    </select><br><br>

    <label for="descripcion">Descripción:</label><br>
    <textarea name="descripcion" placeholder="Descripción del problema" required></textarea><br><br>

    <button type="submit" class="btn btn-primary mt-4">Agregar Reporte</button>
</form>

<?php
$content = ob_get_clean();
include 'layout.php';
?>

<script>
    document.getElementById('ubicacion').addEventListener('change', function () {
        const ubicacionId = this.value;
        const equipoSelect = document.getElementById('equipo');
        equipoSelect.innerHTML = '<option value="">-- Seleccionar equipo --</option>'; // Limpiar el campo

        if (ubicacionId) {
            fetch('getEquipos.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id_ubicacion=' + ubicacionId
            })
            .then(response => response.json())
            .then(data => {
                if (data.mensaje) {
                    equipoSelect.innerHTML = '<option value="">' + data.mensaje + '</option>';
                } else {
                    data.forEach(equipo => {
                        const option = document.createElement('option');
                        option.value = equipo.numero_serie;
                        option.textContent = equipo.numero_serie;
                        equipoSelect.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
</script>
