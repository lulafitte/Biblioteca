<?php
require_once 'conexion.php';

$error_msg = "";
$success_msg = "";
$id_autor = 0; // Inicializamos

// 1. Obtener el ID de la URL (GET) y asegurar que sea un entero (Mejor protección)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php"); 
    exit();
}
// El ID es seguro porque lo validamos como número.
$id_autor = (int)$_GET['id'];
$autor = null; 

// 2. Procesar la actualización (si se envió el formulario por POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id_autor_post = (int)$_POST['id_autor']; // Aseguramos que sea entero
    $nombre = $_POST['nombre'] ?? '';
    $nacionalidad = $_POST['nacionalidad'] ?? '';
    
    if (!empty($nombre)) {
        
        // 🚨 CAMBIO CLAVE: UPDATE con Sentencias Preparadas
        $sql_update = "UPDATE autores SET nombre=?, nacionalidad=? WHERE id_autor=?";
        $stmt = mysqli_prepare($conexion, $sql_update);
        
        if ($stmt) {
            // 'ssi' (dos strings y un integer para el ID)
            mysqli_stmt_bind_param($stmt, 'ssi', $nombre, $nacionalidad, $id_autor_post);
            
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Autor actualizado exitosamente.";
            } else {
                $error_msg = "Error al actualizar el autor: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
             $error_msg = "Error al preparar la consulta de actualización: " . mysqli_error($conexion);
        }
        $id_autor = $id_autor_post; // Mantenemos el ID para la siguiente consulta SELECT
    } else {
        $error_msg = "El campo Nombre es obligatorio.";
    }
}

// 3. Cargar los datos actuales del autor (SELECT con Sentencias Preparadas, opcional pero mejor)
$sql_select = "SELECT nombre, nacionalidad FROM autores WHERE id_autor = ?";
$stmt_select = mysqli_prepare($conexion, $sql_select);

if ($stmt_select) {
    mysqli_stmt_bind_param($stmt_select, 'i', $id_autor);
    mysqli_stmt_execute($stmt_select);
    $resultado_select = mysqli_stmt_get_result($stmt_select);

    if (mysqli_num_rows($resultado_select) == 1) {
        $autor = mysqli_fetch_assoc($resultado_select);
    } else if (empty($error_msg)) { // No redirigir si ya hay un error
        $error_msg = "Autor no encontrado.";
    }
    mysqli_stmt_close($stmt_select);
} else {
    $error_msg = "Error al preparar la consulta de selección: " . mysqli_error($conexion);
}

// ... El HTML y cierre de conexión se mantienen ...
?>