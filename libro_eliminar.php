<?php
    // Incluye la conexión a la base de datos
    require_once 'conexion.php';

    // 1. Verificar que se recibió el ID del libro por la URL (GET)
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        
        // Obtener y sanear el ID
        $id_libro = mysqli_real_escape_string($conexion, $_GET['id']);
        
        // 2. Construir la consulta SQL de Eliminación (DELETE)
        $sql_delete = "DELETE FROM libros WHERE id_libro = $id_libro";
        
        // 3. Ejecutar la consulta
        if (mysqli_query($conexion, $sql_delete)) {
            // Éxito: Redirigir al listado de libros con un mensaje
            header("Location: libros.php?msg=Libro eliminado exitosamente.");
            exit();
        } else {
            // Error: Redirigir al listado con un mensaje de error
            header("Location: libros.php?error=Error al eliminar el libro: " . mysqli_error($conexion));
            exit();
        }
    } else {
        // Si no se recibe el ID, redirigir al listado principal
        header("Location: libros.php?error=ID de libro no proporcionado.");
        exit();
    }

    // Cerrar la conexión
    mysqli_close($conexion);
?>