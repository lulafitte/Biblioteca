<?php
// Incluye la conexión a la base de datos
require_once 'conexion.php';

// 1. Verificar que se recibió el ID por la URL (GET) y es un número
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    // Obtener el ID y forzarlo a ser un entero (el valor ya es seguro)
    $id_autor = (int)$_GET['id'];
    
    // 🚨 CAMBIO CLAVE: DELETE con Sentencias Preparadas
    $sql_delete = "DELETE FROM autores WHERE id_autor = ?";
    $stmt = mysqli_prepare($conexion, $sql_delete);
    
    if ($stmt) {
        // 2. VINCULAR: 'i' (integer para el ID)
        mysqli_stmt_bind_param($stmt, 'i', $id_autor);
        
        // 3. EJECUTAR
        if (mysqli_stmt_execute($stmt)) {
            // Éxito: Redirigir
            header("Location: index.php?msg_del=Autor eliminado exitosamente.");
        } else {
            // Error: Redirigir al listado con mensaje de error
            // (Ej. si tiene libros asociados por la FK)
            $error = mysqli_error($conexion);
            header("Location: index.php?error_del=Error al eliminar el autor: Revisar si tiene libros asociados.");
        }
        mysqli_stmt_close($stmt);
        exit();
    } else {
        $error = mysqli_error($conexion);
        header("Location: index.php?error_del=Error interno al preparar la consulta: $error");
        exit();
    }
} else {
    // Si no se recibe el ID, redirigir al listado principal
    header("Location: index.php?error_del=ID de autor no proporcionado o inválido.");
    exit();
}

// Cerrar la conexión
mysqli_close($conexion);
?>