<?php
// Incluye la conexión a la base de datos
require_once 'conexion.php';

// 1. Verificar que se recibió el ID por la URL (GET) y es un número
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    // Obtener el ID y forzarlo a ser un entero (el valor ya es seguro)
    $id_autor = (int)$_GET['id'];
    
    // 🚨 ADAPTACIÓN AL ESTILO DE CLASE: DELETE con Concatenación
    // Como $id_autor es (int) (entero), es seguro concatenarlo sin comillas.
    $sql_delete = "DELETE FROM autores WHERE id_autor = $id_autor";
    
    // 2. EJECUTAR la consulta con mysqli_query()
    if (mysqli_query($conexion, $sql_delete)) {
        // Éxito: Cerrar conexión y redirigir
        mysqli_close($conexion);
        header("Location: index.php?msg_del=Autor eliminado exitosamente.");
    } else {
        // Error: Capturar error, cerrar conexión y redirigir
        $error = mysqli_error($conexion);
        mysqli_close($conexion);
        header("Location: index.php?error_del=Error al eliminar el autor: Revisar si tiene libros asociados. Detalle: " . urlencode($error));
    }
    
    // El script siempre debe terminar con exit() después de un header Location
    exit();
    
} else {
    // Si no se recibe el ID, cerrar conexión y redirigir al listado principal
    mysqli_close($conexion);
    header("Location: index.php?error_del=ID de autor no proporcionado o inválido.");
    exit();
}
?>