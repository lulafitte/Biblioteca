<?php
// Incluye la conexión a la base de datos
require_once 'conexion.php';

// 1. Verificar que se recibió el ID del libro por la URL (GET) y que sea un número
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    // Obtener el ID y forzarlo a ser un entero. ¡El valor ya es seguro!
    $id_libro = (int)$_GET['id'];
    
    // 🚨 CAMBIO CRÍTICO: DELETE con Sentencias Preparadas
    $sql_delete = "DELETE FROM libros WHERE id_libro = ?";
    $stmt = mysqli_prepare($conexion, $sql_delete);
    
    if ($stmt) {
        // 2. VINCULAR: 'i' (integer para el ID)
        mysqli_stmt_bind_param($stmt, 'i', $id_libro);
        
        // 3. EJECUTAR
        if (mysqli_stmt_execute($stmt)) {
            // Éxito: Redirigir al listado de libros con un mensaje
            header("Location: libros.php?msg=Libro eliminado exitosamente.");
        } else {
            // Error: Puede ser por problemas de llave foránea (si se aplica)
            $error_detalle = mysqli_stmt_error($stmt);
            header("Location: libros.php?error=Error al eliminar el libro. Código: $error_detalle");
        }
        
        // 4. CERRAR
        mysqli_stmt_close($stmt);
        exit();
    } else {
        // Error de preparación de la consulta
        $error_detalle = mysqli_error($conexion);
        header("Location: libros.php?error=Error interno al preparar la consulta.");
        exit();
    }
} else {
    // Si no se recibe un ID válido, redirigir
    header("Location: libros.php?error=ID de libro no proporcionado o inválido.");
    exit();
}

// Cerrar la conexión (solo se alcanzaría si no se hace el exit(), pero se mantiene)
mysqli_close($conexion);
?>