<?php
// Incluir sesiones y conexión
require_once 'sesiones.php';
protegerPagina();
require_once 'conexion.php';

// 1. Verificar que se recibió el ID del libro por la URL (GET) y que sea un número
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    // Obtener el ID y forzarlo a ser un entero. ¡El valor ya es seguro!
    $id_libro = (int)$_GET['id'];
    
    // Obtener el creador para verificar permisos
    $sql_owner = "SELECT created_by FROM libros WHERE id_libro = $id_libro";
    $res_owner = mysqli_query($conexion, $sql_owner);
    $owner_id = null;
    if ($res_owner && mysqli_num_rows($res_owner) == 1) {
        $row = mysqli_fetch_assoc($res_owner);
        $owner_id = $row['created_by'];
    }

    // Si no es administrador y hay owner definido y no coincide, denegar
    if (!esAdministrador() && $owner_id !== null && (int)($_SESSION['id_usuario'] ?? 0) !== (int)$owner_id) {
        mysqli_close($conexion);
        header("Location: libros.php?error_del=No tienes permiso para eliminar este libro.");
        exit();
    }

    // 🚨 ADAPTACIÓN CLAVE: DELETE con Concatenación (seguro porque $id_libro es (int))
    $sql_delete = "DELETE FROM libros WHERE id_libro = $id_libro";
    
    // 2. EJECUTAR la consulta con mysqli_query()
    if (mysqli_query($conexion, $sql_delete)) {
        // Éxito: Cerrar conexión y redirigir al listado de libros con un mensaje
        mysqli_close($conexion);
        header("Location: libros.php?msg_del=Libro eliminado exitosamente.");
    } else {
        // Error: Capturar error, cerrar conexión y mostrar
        $error_detalle = mysqli_error($conexion);
        mysqli_close($conexion);
        header("Location: libros.php?error_del=Error al eliminar el libro. Detalle: " . urlencode($error_detalle));
    }
    
    // 3. CERRAR Y SALIR
    exit();
    
} else {
    // Si no se recibe un ID válido, cerrar conexión y redirigir
    mysqli_close($conexion);
    header("Location: libros.php?error_del=ID de libro no proporcionado o inválido.");
    exit();
}
?>