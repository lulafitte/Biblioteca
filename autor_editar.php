<?php
require_once 'conexion.php';

$error_msg = "";
$success_msg = "";
$id_autor = 0; // Inicializamos

// 1. Obtener y validar el ID de la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php"); 
    exit();
}
$id_autor = (int)$_GET['id']; // Aseguramos que sea un número entero
$autor = null; 

// 2. Procesar la actualización (si se envió el formulario por POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_autor_post = (int)$_POST['id_autor']; 
    $nombre = $_POST['nombre'] ?? '';
    $nacionalidad = $_POST['nacionalidad'] ?? '';
    
    if (!empty($nombre)) {
        
        // 🚨 ADAPTACIÓN AL ESTILO DE CLASE: Usamos mysqli_real_escape_string
        $nombre_seguro = mysqli_real_escape_string($conexion, $nombre);
        $nacionalidad_segura = mysqli_real_escape_string($conexion, $nacionalidad);
        
        // CONSTRUIR el UPDATE concatenando variables
        $sql_update = "UPDATE autores SET 
                       nombre = '$nombre_seguro', 
                       nacionalidad = '$nacionalidad_segura' 
                       WHERE id_autor = $id_autor_post";
        
        // EJECUTAR con mysqli_query()
        if (mysqli_query($conexion, $sql_update)) {
            $success_msg = "Autor actualizado exitosamente.";
            // Mantener el ID para la siguiente consulta SELECT
            $id_autor = $id_autor_post; 
        } else {
            $error_msg = "Error al actualizar el autor: " . mysqli_error($conexion);
        }
    } else {
        $error_msg = "El campo Nombre es obligatorio.";
    }
}

// 3. Cargar los datos actuales del autor (SELECT adaptado al estilo de clase)
// No es necesario escapar $id_autor porque ya lo casteamos a (int)
$sql_select = "SELECT nombre, nacionalidad FROM autores WHERE id_autor = $id_autor";

$resultado_select = mysqli_query($conexion, $sql_select);

if ($resultado_select) {
    if (mysqli_num_rows($resultado_select) == 1) {
        $autor = mysqli_fetch_assoc($resultado_select);
    } else if (empty($error_msg)) { 
        $error_msg = "Autor no encontrado.";
    }
} else {
    $error_msg = "Error al ejecutar la consulta de selección: " . mysqli_error($conexion);
}

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
        <h1>✍️ Editar Autor</h1>
    </header>

    <main>
        <a href="index.php" class="btn-crear" style="background-color: #6c757d;">⬅️ Volver al Listado</a>
        
        <?php if (!empty($error_msg)): ?>
            <p class="alerta" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                <?php echo htmlspecialchars($error_msg); ?>
            </p>
        <?php elseif (!empty($success_msg)): ?>
            <p class="alerta" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb;">
                <?php echo htmlspecialchars($success_msg); ?>
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
    
    <?php 
        if (isset($resultado_select)) { mysqli_free_result($resultado_select); }
        mysqli_close($conexion); 
    ?>
</body>
</html>