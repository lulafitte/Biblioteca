<?php
    // Incluye la conexión a la base de datos
    require_once 'conexion.php';

    // 1. Procesar el formulario cuando se envía (método POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Obtener y sanear los datos del formulario
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $nacionalidad = mysqli_real_escape_string($conexion, $_POST['nacionalidad']);
        
        // Validar que los campos no estén vacíos (mínimo, el nombre)
        if (!empty($nombre)) {
            
            // Construir la consulta SQL de inserción (INSERT)
            $sql = "INSERT INTO autores (nombre, nacionalidad) VALUES ('$nombre', '$nacionalidad')";
            
            // Ejecutar la consulta
            if (mysqli_query($conexion, $sql)) {
                // Redirigir al listado principal después de la inserción exitosa
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
    <title>Crear Nuevo Autor</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <header>
        <h1>✍️ Crear Nuevo Autor (ABM - Alta)</h1>
    </header>

    <main>
        <a href="index.php" class="btn-crear" style="background-color: #6c757d;">⬅️ Volver al Listado</a>
        
        <?php if (isset($error_msg)): ?>
            <p class="alerta" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                <?php echo $error_msg; ?>
            </p>
        <?php endif; ?>

        <form action="autor_crear.php" method="POST" class="crud-form">
            <div class="form-group">
                <label for="nombre">Nombre del Autor:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="nacionalidad">Nacionalidad:</label>
                <input type="text" id="nacionalidad" name="nacionalidad">
            </div>
            
            <button type="submit" class="btn-crear">💾 Guardar Autor</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 Proyecto Programación Web 2</p>
    </footer>
    
    <?php mysqli_close($conexion); ?>
</body>
</html>