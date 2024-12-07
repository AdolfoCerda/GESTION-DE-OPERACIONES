<header>
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <img src="imagenes/logo.png" alt="Logo de la Universidad" class="logo" style="max-width: 150px; margin-right:-100px;">
            <h1 class="ms-3 text-center" style="flex-grow: 1;">Control de Mantenimiento y Servicios</h1>
        </div>
        <div style="text-align: right;">
            <?php
                // Verificar si existe un usuario en la sesión
                if (isset($_SESSION['usuario'])) {
                    echo '<p class="mb-0 ms-3">Hola, ' . htmlspecialchars($_SESSION['usuario']) . '!</p>';
                }
            ?>
        </div>
        <nav>
            <ul class="nav justify-content-center mt-3">
                <li class="nav-item"><a href="inicio.php" class="nav-link">Inicio</a></li>
                <li class="nav-item"><a href="ubicaciones.php" class="nav-link">Ubicaciones y Equipos</a></li>
                <li class="nav-item"><a href="reportes.php" class="nav-link">Reportes</a></li>
                <li class="nav-item"><a href="servicios.php" class="nav-link">Catálogo de Servicios</a></li>
                <li class="nav-item"><a href="problemas.php" class="nav-link">Tabla de Conocimientos</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</header>
