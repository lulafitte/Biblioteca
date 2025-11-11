<?php
// Incluye la lógica de conexión a la base de datos
require_once 'conexion.php';

// Manejo de mensajes de éxito/error (usando clases 'success' y 'error')
$success_msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$error_msg = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// 1. Consulta con JOIN para obtener el título del libro y el nombre del autor
$consulta = "SELECT 
                    l.id_libro, 
                    l.titulo, 
                    l.anio_publicacion, 
                    a.nombre AS nombre_autor 
                 FROM libros l
                 JOIN autores a ON l.id_autor = a.id_autor
                 ORDER BY l.titulo ASC";
                 
$resultado = mysqli_query($conexion, $consulta);

if (!$resultado) {
    die("Error en la consulta de libros: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Libros - Biblioteca</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <h1>📚 Sistema de Gestión de Libros </h1>
        <p>Listado de libros y autores.</p>
    </header>

    <main class="main-content">
        <a href="index.php" class="btn-crear secondary">
            <i class="fas fa-arrow-left"></i> Volver a Autores
        </a>
        
        <h2>Listado de Libros</h2>
        
        <?php if ($success_msg): ?>
            <p class="alerta success">
                <?php echo $success_msg; ?>
            </p>
        <?php elseif ($error_msg): ?>
            <p class="alerta error">
                <?php echo $error_msg; ?>
            </p>
        <?php endif; ?>

        <a href="libro_crear.php" class="btn-crear primary">
             <i class="fas fa-plus"></i> Agregar Nuevo Libro
        </a> 
        
        <?php if (mysqli_num_rows($resultado) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Año</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        while ($fila = mysqli_fetch_assoc($resultado)): 
                    ?>
                    <tr>
                        <td><?php echo $fila['id_libro']; ?></td>
                        <td><?php echo htmlspecialchars($fila['titulo']); ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre_autor']); ?></td>
                        <td><?php echo $fila['anio_publicacion']; ?></td>
                        <td class="acciones">
                            <a href="libro_editar.php?id=<?php echo $fila['id_libro']; ?>" class="btn-accion btn-editar">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="libro_eliminar.php?id=<?php echo $fila['id_libro']; ?>" class="btn-accion btn-eliminar" 
                              onclick="return confirm('¿Estás seguro de que quieres eliminar este libro?');">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alerta info">No hay libros registrados en la base de datos. ¡Añade uno!</p>
        <?php endif; ?>

    </main>
    
    <footer>
        <p>&copy; 2025 Proyecto Programación Web 2</p>
    </footer>

    <?php
        mysqli_free_result($resultado);
        mysqli_close($conexion);
    ?>
</body>
</html>