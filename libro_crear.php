<?php 
// Reemplaza esto con tu ruta correcta para la conexión
include_once("conexion.php"); 

// Inicializamos variables para evitar errores
$titulo = null;
$anio_publicacion = null;
$id_autor = null;
$error_msg = null;

// Verificamos que el formulario se haya enviado por POST
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // 1. Obtener y validar datos
    // No usamos htmlspecialchars/escape_string aquí, Sentencias Preparadas lo manejan.
    $titulo = $_POST['titulo'] ?? '';
    $anio_publicacion = $_POST['anio_publicacion'] ?? null; 
    $id_autor = $_POST['id_autor'] ?? null;

    // Validación básica
    if (!empty($titulo) && is_numeric($id_autor)) {
        
        // 🚨 Sentencia Preparada: Usamos '?' para proteger los datos
        // Asumiendo que anio_publicacion y id_autor son INTEGER (i) en la DB.
        $sql = "INSERT INTO libros (titulo, anio_publicacion, id_autor) VALUES (?, ?, ?)";
        
        // Asumimos que la conexión es $conexion (si usas $con, cámbialo)
        $stmt = mysqli_prepare($conexion, $sql); 
        
        if ($stmt) {
            // 2. VINCULAR: 'sii' (String, Integer, Integer)
            mysqli_stmt_bind_param($stmt, 'sii', $titulo, $anio_publicacion, $id_autor);

            // 3. EJECUTAR
            if (mysqli_stmt_execute($stmt)) {
                
                mysqli_stmt_close($stmt);
                // 4. REDIRECCIÓN SIMILAR A LA CLASE
                header("Location: libros.php?msg=Libro creado con éxito.");
                exit(); 

            } else {
                $error_msg = "Error al insertar el libro: " . mysqli_stmt_error($stmt);
                mysqli_stmt_close($stmt);
            }
        } else {
            $error_msg = "Error al preparar la consulta: " . mysqli_error($conexion);
        }
    } else {
        $error_msg = "Error: El título y el autor son obligatorios.";
    }
}
// El resto del archivo continuaría con el HTML y el cierre de conexión.
?>