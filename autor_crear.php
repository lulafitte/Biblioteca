<?php
// Incluye la conexión a la base de datos
require_once 'conexion.php';

// 1. Procesar el formulario cuando se envía (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener y limpiar datos
    $nombre = $_POST['nombre'] ?? '';
    $nacionalidad = $_POST['nacionalidad'] ?? '';
    
    // 2. Validar que el campo Nombre sea obligatorio
    if (!empty($nombre)) {
        
        // 🚨 PASO DE SEGURIDAD (IMPORTANTE): Escapar los datos
        // Usa mysqli_real_escape_string para prevenir inyección SQL
        $nombre_seguro = mysqli_real_escape_string($conexion, $nombre);
        $nacionalidad_segura = mysqli_real_escape_string($conexion, $nacionalidad);
        
        // 3. CONSTRUIR la consulta SQL con los datos concatenados
        $sql = "INSERT INTO autores (nombre, nacionalidad) 
                VALUES ('$nombre_seguro', '$nacionalidad_segura')";
        
        // 4. EJECUTAR la consulta con mysqli_query()
        if (mysqli_query($conexion, $sql)) {
            // Éxito: Cerrar conexión, redirigir y salir
            mysqli_close($conexion);
            header("Location: index.php?msg=Autor creado exitosamente.");
            exit();
        } else {
            // Mostrar error si la consulta falla
            $error_msg = "Error al crear el autor: " . mysqli_error($conexion);
        }

    } else {
        $error_msg = "El campo Nombre es obligatorio.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Autor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>➕ Crear Nuevo Autor</h1>
    </header>

    <main>
        <a href="index.php" class="btn-crear" style="background-color: #6c757d;">⬅️ Volver al Listado</a>
        
        <?php if (!empty($error_msg)): ?>
            <p class="alerta" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                <?php echo htmlspecialchars($error_msg); ?>
            </p>
        <?php endif; ?>

        <form action="autor_crear.php" method="POST" class="crud-form">
            <div class="form-group">
                <label for="nombre">Nombre del Autor: *</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="nacionalidad">Nacionalidad:</label>
                <input type="text" id="nacionalidad" name="nacionalidad">
            </div>
            
            <button type="submit" class="btn-crear" style="background-color: #28a745;">✅ Crear Autor</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 Proyecto Programación Web 2</p>
    </footer>

    <?php
    // Cerrar la conexión al final del script
    if (isset($conexion)) {
        mysqli_close($conexion);
    }
    ?>
</body>
</html>