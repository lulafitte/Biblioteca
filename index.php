<?php
// 🔒 PROTEGER la página - Debe ir PRIMERO antes de cualquier salida
require_once 'sesiones.php';
protegerPagina(); // Si no está logueado, redirige al login

require_once 'conexion.php';

// hacer la consulta para seleccionar los autores
$consulta = "SELECT id_autor, nombre, nacionalidad FROM autores ORDER BY nombre ASC";
$resultado = mysqli_query($conexion, $consulta);

// error de consulta 
if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

// Lógica de mensajes (exito o error)
$success_msg = (isset($_GET['msg']) || isset($_GET['msg_del'])) 
             ? (isset($_GET['msg']) ? $_GET['msg'] : $_GET['msg_del']) 
             : '';
$error_del_msg = isset($_GET['error_del']) ? $_GET['error_del'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca - Gestión de Autores</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <header>
        <h1>📚 Sistema de Gestión de Autores</h1>
        <p>Listado de autores y libros de la Biblioteca.</p>
    </header>

    <main class="main-content">
        <?php mostrarBarraUsuario(); ?>
        <h2>Listado de Autores</h2>
        
        <?php if ($success_msg): ?>
            <p class="alerta success">
                <?php echo htmlspecialchars($success_msg); ?>
            </p>
        <?php elseif ($error_del_msg): ?>
            <p class="alerta error">
                <?php echo htmlspecialchars($error_del_msg); ?>
            </p>
        <?php endif; ?>

        <div class="acciones-main-top"> 
            <a href="autor_crear.php" class="btn-crear primary">
                <i class="fas fa-plus"></i> Agregar Nuevo Autor
            </a> 
            <a href="libros.php" class="btn-crear secondary">
                <i class="fas fa-book"></i> Ir a Gestión de Libros
            </a> 
        </div>
        
        <?php if (mysqli_num_rows($resultado) > 0): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Nacionalidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        while ($fila = mysqli_fetch_assoc($resultado)): 
                    ?>
                    <tr>
                        <td><?php echo $fila['id_autor']; ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($fila['nacionalidad']); ?></td>
                        <td class="acciones">
                            <a href="autor_editar.php?id=<?php echo $fila['id_autor']; ?>" class="btn-accion btn-editar">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                            <a href="autor_eliminar.php?id=<?php echo $fila['id_autor']; ?>" class="btn-accion btn-eliminar" 
                                onclick="return confirm('¿Estás seguro de que quieres eliminar a este autor?');">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alerta info">No hay autores registrados en la base de datos.</p>
        <?php endif; ?>

    </main>
    
    <footer>
        <p>&copy; 2025 Proyecto Programación Web 2</p>
    </footer>

    <?php
        // liberar la memoria del resultado
        mysqli_free_result($resultado);
    ?>
</body>
</html>