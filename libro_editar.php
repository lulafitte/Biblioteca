<?php
require_once 'sesiones.php';
protegerPagina();
require_once 'conexion.php';

// 1. Obtener el ID del libro a editar de la URL y validar
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: libros.php?error=ID de libro no proporcionado o inválido.");
    exit();
}
// El ID es seguro porque lo validamos como número.
$id_libro = (int)$_GET['id'];

// variables
$libro = null;
$error_msg = "";
$success_msg = "";
$res_autores = null; 

// 2. OBTENER AUTORES para el campo SELECT 
$sql_autores = "SELECT id_autor, nombre FROM autores ORDER BY nombre ASC";
$res_autores = mysqli_query($conexion, $sql_autores);

if (!$res_autores) {
    $error_msg = "Error al cargar la lista de autores: " . mysqli_error($conexion);
}

// 3. PROCESAR LA ACTUALIZACIÓN (si se envió el formulario por POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener los datos 
    $id_libro_post = (int)$_POST['id_libro'];
    $titulo = $_POST['titulo'] ?? '';
    $anio_publicacion = $_POST['anio_publicacion'] ?? null;
    $id_autor = $_POST['id_autor'] ?? null; 
    
    if (!empty($titulo) && is_numeric($id_autor) && is_numeric($id_libro_post)) {
        
        // 🚨 ADAPTACIÓN CLAVE: Sanitizar los datos de texto (String)
        $titulo_seguro = mysqli_real_escape_string($conexion, $titulo);
        
        // El año puede ser NULL en la DB. Si es null o vacío en el form, lo mandamos como NULL.
        // Si es numérico, lo casteamos a entero.
        $anio_seguro = (empty($anio_publicacion) || !is_numeric($anio_publicacion)) ? 'NULL' : (int)$anio_publicacion;
        $autor_seguro = (int)$id_autor;
        
        // CONSTRUIR el UPDATE concatenando los valores
        $sql_update = "UPDATE libros SET 
                        titulo = '$titulo_seguro', 
                        anio_publicacion = $anio_seguro, 
                        id_autor = $autor_seguro 
                        WHERE id_libro = $id_libro_post";
        
        
        // EJECUTAR con mysqli_query()
        if (mysqli_query($conexion, $sql_update)) {
            $success_msg = "Libro actualizado exitosamente.";
            // Mantener el ID original para recargar los datos
            $id_libro = $id_libro_post; 
        } else {
            $error_msg = "Error al actualizar el libro: " . mysqli_error($conexion);
        }
    } else {
        $error_msg = "El título y el autor son obligatorios o contienen datos inválidos.";
    }
}

// 4. Cargar los datos actuales del libro (SELECT adaptado al estilo de clase)
// No es necesario escapar $id_libro porque ya lo casteamos a (int)
$sql_select = "SELECT id_libro, titulo, anio_publicacion, id_autor, created_by FROM libros WHERE id_libro = $id_libro";

$resultado_select = mysqli_query($conexion, $sql_select); 

if ($resultado_select) {
    if (mysqli_num_rows($resultado_select) == 1) {
        $libro = mysqli_fetch_assoc($resultado_select);
        // Verificar permisos: solo el creador o administrador puede editar (si created_by existe)
        $owner_id = $libro['created_by'] ?? null;
        if (!esAdministrador() && $owner_id !== null && (int)($_SESSION['id_usuario'] ?? 0) !== (int)$owner_id) {
            mysqli_close($conexion);
            header("Location: libros.php?error=No tienes permiso para editar este libro.");
            exit();
        }
    } else if (empty($success_msg)) { 
        $error_msg = "Libro no encontrado.";
    }
} else {
    $error_msg = "Error al ejecutar la consulta de selección: " . mysqli_error($conexion);
}

// ... El resto del código HTML se mantiene sin cambios ...
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Libro</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <header>
        <h1>✍️ Editar Libro (ABM - Modificación)</h1>
    </header>

    <main>
        <a href="libros.php" class="btn-crear" style="background-color: #6c757d;">⬅️ Volver al Listado de Libros</a>
        
        <?php if (!empty($error_msg)): ?>
            <p class="alerta" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                <?php echo $error_msg; ?>
            </p>
        <?php elseif (!empty($success_msg)): ?>
             <p class="alerta" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb;">
                <?php echo $success_msg; ?>
            </p>
        <?php endif; ?>

        <?php if ($libro): ?>
        <form action="libro_editar.php?id=<?php echo $id_libro; ?>" method="POST" class="crud-form">
            
            <input type="hidden" name="id_libro" value="<?php echo $id_libro; ?>">
            
            <div class="form-group">
                <label for="titulo">Título del Libro:</label>
                <input type="text" id="titulo" name="titulo" 
                        value="<?php echo htmlspecialchars($libro['titulo']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="anio_publicacion">Año de Publicación:</label>
                <input type="number" id="anio_publicacion" name="anio_publicacion" min="1000" max="<?php echo date('Y'); ?>"
                        value="<?php echo htmlspecialchars($libro['anio_publicacion']); ?>">
            </div>

            <div class="form-group">
                <label for="id_autor">Autor:</label>
                <select id="id_autor" name="id_autor" required>
                    <option value="">-- Seleccione un autor --</option>
                    <?php 
                        // Llenar el SELECT con los autores
                        if (mysqli_num_rows($res_autores) > 0) {
                            // Resetear el puntero del resultado si se actualizó el libro
                            mysqli_data_seek($res_autores, 0); 
                            while ($autor = mysqli_fetch_assoc($res_autores)) {
                                $selected = ($autor['id_autor'] == $libro['id_autor']) ? 'selected' : '';
                                echo "<option value='{$autor['id_autor']}' {$selected}>" . htmlspecialchars($autor['nombre']) . "</option>";
                            }
                        }
                    ?>
                </select>
            </div>
            
            <button type="submit" class="btn-crear" style="background-color: #007bff;">✅ Guardar Cambios</button>
        </form>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Proyecto Programación Web 2</p>
    </footer>
    
    <?php 
        // Liberar el resultado de la consulta de autores y cerrar la conexión.
        if (isset($res_autores)) { mysqli_free_result($res_autores); }
        if (isset($resultado_select)) { mysqli_free_result($resultado_select); }
        mysqli_close($conexion); 
    ?>
</body>
</html>