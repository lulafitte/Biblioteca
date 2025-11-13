<?php 
// Reemplaza esto con tu ruta correcta para la conexión
require_once("conexion.php"); // Usamos require_once como en el resto del proyecto

// Inicializamos variables para evitar errores
$titulo = null;
$anio_publicacion = null;
$id_autor = null;
$error_msg = null;

// Verificamos que el formulario se haya enviado por POST
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // 1. Obtener y limpiar datos
    $titulo = $_POST['titulo'] ?? '';
    // El año y el ID del autor deben ser tratados como enteros si no son nulos
    $anio_publicacion = $_POST['anio_publicacion'] ?? null; 
    $id_autor = $_POST['id_autor'] ?? null;

    // Validación básica: título no vacío y autor numérico
    if (!empty($titulo) && is_numeric($id_autor)) {
        
        // 🚨 ADAPTACIÓN CLAVE: Sanitizar los datos de texto (String)
        $titulo_seguro = mysqli_real_escape_string($conexion, $titulo);
        
        // El año y el ID ya fueron validados como numéricos o nulos,
        // pero por seguridad extra, nos aseguramos de que sean enteros (casteo a int)
        $anio_seguro = is_numeric($anio_publicacion) ? (int)$anio_publicacion : 'NULL';
        $autor_seguro = (int)$id_autor;


        // CONSTRUIR la consulta SQL concatenando los valores
        // NOTA: Los Strings (título) van entre comillas simples. Los Integer (año, autor) van sin comillas.
        $sql = "INSERT INTO libros (titulo, anio_publicacion, id_autor) 
                VALUES ('$titulo_seguro', $anio_seguro, $autor_seguro)";
        
        
        // EJECUTAR la consulta con mysqli_query()
        if (mysqli_query($conexion, $sql)) {
            
            // Éxito: Cerrar conexión y redirigir
            mysqli_close($conexion);
            header("Location: libros.php?msg=Libro creado con éxito.");
            exit(); 

        } else {
            $error_msg = "Error al insertar el libro: " . mysqli_error($conexion);
        }

    } else {
        $error_msg = "Error: El título y el autor son obligatorios y deben ser válidos.";
    }
}
// El resto del archivo contendría el formulario HTML para ingresar los datos.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Libro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>➕ Crear Nuevo Libro</h1>
    </header>

    <main>
        <a href="libros.php" class="btn-crear" style="background-color: #6c757d;">⬅️ Volver al Listado</a>
        
        <?php if (!empty($error_msg)): ?>
            <p class="alerta" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                <?php echo htmlspecialchars($error_msg); ?>
            </p>
        <?php endif; ?>

        <form action="libro_crear.php" method="POST" class="crud-form">
            <div class="form-group">
                <label for="titulo">Título del Libro: *</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            
            <div class="form-group">
                <label for="anio_publicacion">Año de Publicación:</label>
                <input type="number" id="anio_publicacion" name="anio_publicacion" min="1000" max="<?php echo date('Y'); ?>">
            </div>
            
            <div class="form-group">
                <label for="id_autor">Autor: *</label>
                <select id="id_autor" name="id_autor" required>
                    <option value="">-- Seleccione un autor --</option>
                    <?php
                    // Obtener la lista de autores para el formulario
                    $sql_autores = "SELECT id_autor, nombre FROM autores ORDER BY nombre ASC";
                    $res_autores = mysqli_query($conexion, $sql_autores);
                    if (mysqli_num_rows($res_autores) > 0) {
                        while ($autor = mysqli_fetch_assoc($res_autores)) {
                            echo "<option value='" . $autor['id_autor'] . "'>" . htmlspecialchars($autor['nombre']) . "</option>";
                        }
                    }
                    if (isset($res_autores)) { mysqli_free_result($res_autores); }
                    ?>
                </select>
            </div>
            
            <button type="submit" class="btn-crear" style="background-color: #28a745;">✅ Crear Libro</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2025 Proyecto Programación Web 2</p>
    </footer>

    <?php 
    if (isset($conexion)) {
        mysqli_close($conexion);
    }
    ?>
</body>
</html>