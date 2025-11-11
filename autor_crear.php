<?php
// Incluye la conexión a la base de datos
require_once 'conexion.php';

// 1. Procesar el formulario cuando se envía (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener datos (sin necesidad de mysqli_real_escape_string)
    $nombre = $_POST['nombre'] ?? '';
    $nacionalidad = $_POST['nacionalidad'] ?? '';
    
    // Validar que el campo Nombre sea obligatorio
    if (!empty($nombre)) {
        
        // 🚨 CAMBIO CLAVE: Usamos '?' como marcadores de posición
        $sql = "INSERT INTO autores (nombre, nacionalidad) VALUES (?, ?)";
        
        $stmt = mysqli_prepare($conexion, $sql); 
        
        if ($stmt) {
            // 🚨 VINCULACIÓN: 'ss' indica que ambos parámetros son strings
            mysqli_stmt_bind_param($stmt, 'ss', $nombre, $nacionalidad);

            if (mysqli_stmt_execute($stmt)) {
                // Éxito: Redirigir y salir
                header("Location: index.php?msg=Autor creado exitosamente.");
                mysqli_stmt_close($stmt);
                exit();
            } else {
                // Mostrar error si la consulta falla
                $error_msg = "Error al crear el autor: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error_msg = "Error al preparar la consulta: " . mysqli_error($conexion);
        }
    } else {
        $error_msg = "El campo Nombre es obligatorio.";
    }
}
// El HTML y el cierre de conexión se mantienen sin cambios
?>
<?php if (isset($error_msg)): /* Lógica de mensajes de error */ endif; ?>
<?php mysqli_close($conexion); ?>
</body>
</html>