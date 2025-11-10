<?php
    // Incluye la conexión a la base de datos
    require_once 'conexion.php';
    $error_msg = "";

    // 1. OBTENER AUTORES para el campo SELECT
    $sql_autores = "SELECT id_autor, nombre FROM autores ORDER BY nombre ASC";
    $res_autores = mysqli_query($conexion, $sql_autores);

    if (!$res_autores) {
        $error_msg = "Error al cargar los autores: " . mysqli_error($conexion);
    }

    // 2. PROCESAR EL FORMULARIO cuando se envía (método POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error_msg)) {
        
        // Obtener y sanear los datos
        $titulo = mysqli_real_escape_string($conexion, $_POST['titulo']);
        $anio_publicacion = mysqli_real_escape_string($conexion, $_POST['anio_publicacion']);
        $id_autor = mysqli_real_escape_string($conexion, $_POST['id_autor']); // Clave Foránea

        // Validación básica
        if (!empty($titulo) && !empty($id_autor) && is_numeric($id_autor)) {
            
            // Construir la consulta SQL de inserción (INSERT)
            $sql_insert = "INSERT INTO libros (titulo, anio_publicacion, id_autor) 
                           VALUES ('$titulo', '$anio_publicacion', $id_autor)";
            
            // Ejecutar la consulta
            if (mysqli_query($conexion, $sql_insert)) {
                // Redirigir al listado de libros con mensaje de éxito
                header("Location: libros.php?msg=Libro '{$titulo}' creado exitosamente.");
                exit();
            } else {
                $error_msg = "Error al crear el libro: " . mysqli_error($conexion);
            }
        } else {
            $error_msg = "Todos los campos son obligatorios o contienen datos inválidos.";
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Nuevo Libro</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <header>
        <h1>📖 Crear Nuevo Libro</h1>
    </header>

    <main>
        <a href="libros.php" class="btn-crear" style="background-color: #6c757d;">⬅️ Volver al Listado de Libros</a>
        
        <?php if (!empty($error_msg)): ?>
            <p class="alerta" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                <?php echo $error_msg; ?>
            </p>
        <?php endif; ?>

        <form action="libro_crear.php" method="POST" class="crud-form">
            
            <div class="form-group">
                <label for="titulo">Título del Libro:</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            
            <div class="form-group">
                <label for="anio_publicacion">Año de Publicación:</label>
                <input type="number" id="anio_publicacion" name="anio_publicacion" min="1000" max="<?php echo date('Y'); ?>">
            </div>
            
            <div class="form-group">
                <label for="id_autor">Autor:</label>
                <select id="id_autor" name="id_autor" required>
                    <option value="">-- Seleccione un autor --</option>
                    <?php 
                        // Llenar el SELECT con los autores obtenidos de la base de datos
                        if (mysqli_num_rows($res_autores) > 0) {
                            while ($autor = mysqli_fetch_assoc($res_autores)) {
                                echo "<option value='{$autor['id_autor']}'>" . htmlspecialchars($autor['nombre']) . "</option>";
                            }
                        }
                    ?>
                </select>
                <?php if (mysqli_num_rows($res_autores) == 0): ?>
                    <p class="alerta" style="margin-top: 10px;">¡Atención! No hay autores registrados. Debe crear un autor primero.</p>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn-crear">💾 Guardar Libro</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 Proyecto Programación Web 2</p>
    </footer>
    
    <?php 
        // Liberar resultados y cerrar conexión
        if (isset($res_autores)) { mysqli_free_result($res_autores); }
        mysqli_close($conexion); 
    ?>
</body>
</html>