<?php
    // Incluye la lógica de conexión a la base de datos
    require_once 'conexion.php';

    // 1. Realizar la consulta para seleccionar todos los autores
    $consulta = "SELECT id_autor, nombre, nacionalidad FROM autores ORDER BY nombre ASC";
    $resultado = mysqli_query($conexion, $consulta);

    // Verificar si la consulta fue exitosa
    if (!$resultado) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca</title>
    
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <header>
        <h1>📚 Sistema de Gestión de Autores</h1>
        <p>Listado de autores almacenados en la base de datos MySQL.</p>
    </header>

    <main>
        <h2>Listado de Autores</h2>
        
        <?php if (isset($_GET['msg']) || isset($_GET['msg_del'])): ?>
            <p class="alerta" style="background-color: #d4edda; color: #155724; border-color: #c3e6cb;">
                <?php echo isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : htmlspecialchars($_GET['msg_del']); ?>
            </p>
        <?php elseif (isset($_GET['error_del'])): ?>
            <p class="alerta" style="background-color: #f8d7da; color: #721c24; border-color: #f5c6cb;">
                <?php echo htmlspecialchars($_GET['error_del']); ?>
            </p>
        <?php endif; ?>
        <a href="autor_crear.php" class="btn-crear">➕ Agregar Nuevo Autor</a> 
        
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
                        // 3. Iterar sobre los resultados y mostrarlos en filas
                        while ($fila = mysqli_fetch_assoc($resultado)): 
                    ?>
                    <tr>
                        <td><?php echo $fila['id_autor']; ?></td>
                        <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($fila['nacionalidad']); ?></td>
                        <td class="acciones">
                            <a href="autor_editar.php?id=<?php echo $fila['id_autor']; ?>" class="btn-editar">Editar</a>
                            <a href="autor_eliminar.php?id=<?php echo $fila['id_autor']; ?>" class="btn-eliminar">Eliminar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alerta">No hay autores registrados en la base de datos.</p>
        <?php endif; ?>
        <a href="libros.php" class="btn-crear" style="background-color: #007bff;">📘 Ir a Gestión de Libros</a> 
        <a href="autor_crear.php" class="btn-crear">➕ Agregar Nuevo Autor</a>

    </main>
    
    <footer>
        <p>&copy; 2025 segundo Parcial Programación Web 2</p>
    </footer>

    <?php
        // 4. Liberar el resultado y cerrar la conexión
        mysqli_free_result($resultado);
        mysqli_close($conexion);
    ?>
</body>
</html>