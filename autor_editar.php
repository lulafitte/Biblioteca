<?php
    require_once 'conexion.php';

    // 1. Obtener el ID del autor a editar
    // Verifica si se recibió el ID en la URL (GET)
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: index.php"); // Redirige si no hay ID
        exit();
    }
    
    $id_autor = mysqli_real_escape_string($conexion, $_GET['id']);
    $autor = null;
    $error_msg = "";
    $success_msg = "";

    // 2. Procesar la actualización (si se envió el formulario por POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Obtener y sanear los datos del formulario (incluyendo el ID oculto)
        $id_autor_post = mysqli_real_escape_string($conexion, $_POST['id_autor']);
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $nacionalidad = mysqli_real_escape_string($conexion, $_POST['nacionalidad']);
        
        if (!empty($nombre)) {
            
            // Consulta SQL de Actualización (UPDATE)
            $sql_update = "UPDATE autores SET 
                           nombre = '$nombre', 
                           nacionalidad = '$nacionalidad' 
                           WHERE id_autor = $id_autor_post";
            
            if (mysqli_query($conexion, $sql_update)) {
                $success_msg = "Autor actualizado exitosamente.";
                // Opcionalmente, puedes redirigir aquí: header("Location: index.php"); exit();
                // O dejar el mensaje de éxito y recargar los datos (como se hace abajo).
            } else {
                $error_msg = "Error al actualizar el autor: " . mysqli_error($conexion);
            }
        } else {
            $error_msg = "El campo Nombre es obligatorio.";
        }
        
        // Mantener el ID original para la siguiente consulta SELECT
        $id_autor = $id_autor_post; 
    }
    
    // 3. Cargar los datos actuales del autor para mostrarlos en el formulario (SELECT)
    $sql_select = "SELECT nombre, nacionalidad FROM autores WHERE id_autor = $id_autor";
    $resultado_select = mysqli_query($conexion, $sql_select);
    
    if (mysqli_num_rows($resultado_select) == 1) {
        $autor = mysqli_fetch_assoc($resultado_select);
    } else {
        // Manejar el caso de que el ID no exista
        $error_msg = "Autor no encontrado.";
        // Si no se encuentra, es mejor redirigir al listado
        // header("Location: index.php"); exit(); 
    }
    
    mysqli_free_result($resultado_select);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Autor</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <header>
        <h1>✍️ Editar Autor </h1>
    </header>

    <main>
        <a href="index.php" class="btn-crear" style="background-color: #6c757d;">⬅️ Volver al Listado</a>
        
        <?php if (isset($error_msg) && $error_msg != ""): ?>
            <p class="alerta" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                <?php echo $error_msg; ?>
            </p>
        <?php elseif (isset($success_msg) && $success_msg != ""): ?>
             <p class="alerta" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb;">
                <?php echo $success_msg; ?>
            </p>
        <?php endif; ?>

        <?php if ($autor): ?>
        <form action="autor_editar.php?id=<?php echo $id_autor; ?>" method="POST" class="crud-form">
            
            <input type="hidden" name="id_autor" value="<?php echo $id_autor; ?>">
            
            <div class="form-group">
                <label for="nombre">Nombre del Autor:</label>
                <input type="text" id="nombre" name="nombre" 
                       value="<?php echo htmlspecialchars($autor['nombre']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="nacionalidad">Nacionalidad:</label>
                <input type="text" id="nacionalidad" name="nacionalidad" 
                       value="<?php echo htmlspecialchars($autor['nacionalidad']); ?>">
            </div>
            
            <button type="submit" class="btn-crear" style="background-color: #007bff;">✅ Guardar Cambios</button>
        </form>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Proyecto Programación Web 2</p>
    </footer>
    
    <?php mysqli_close($conexion); ?>
</body>
</html>