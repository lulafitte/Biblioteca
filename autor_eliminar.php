<?php
    // Incluye la conexión a la base de datos
    require_once 'conexion.php';

    // 1. Verificar que se recibió el ID por la URL (GET)
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        
        // Obtener y sanear el ID
        $id_autor = mysqli_real_escape_string($conexion, $_GET['id']);
        
        // 2. Construir la consulta SQL de Eliminación (DELETE)
        $sql_delete = "DELETE FROM autores WHERE id_autor = $id_autor";
        
        // 3. Ejecutar la consulta
        if (mysqli_query($conexion, $sql_delete)) {
            // Éxito: Redirigir al listado con un mensaje
            header("Location: index.php?msg_del=Autor eliminado exitosamente.");
            exit();
        } else {
            // Error: Redirigir al listado con un mensaje de error
            header("Location: index.php?error_del=Error al eliminar el autor: " . mysqli_error($conexion));
            exit();
        }
    } else {
        // Si no se recibe el ID, redirigir al listado principal
        header("Location: index.php?error_del=ID de autor no proporcionado.");
        exit();
    }

    // Cerrar la conexión
    mysqli_close($conexion);
?>