<?php
// Iniciar sesión (ESTO DEBE SER LO PRIMERO ANTES DE CUALQUIER OUTPUT)
session_start();

// Si el usuario ya está logueado, redirigir a index.php
if (isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

require_once 'conexion.php';

$error_msg = "";

// Procesar el formulario de login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';
    
    // Validar que no estén vacíos
    if (empty($nombre_usuario) || empty($clave)) {
        $error_msg = "Por favor, completa todos los campos.";
    }
    else {
        // Escapar el nombre de usuario para seguridad
        $nombre_usuario_seguro = mysqli_real_escape_string($conexion, $nombre_usuario);
        
        // Buscar al usuario en la BD
        $sql = "SELECT id_usuario, nombre_usuario, clave, rol, estado FROM usuarios 
            WHERE nombre_usuario = '$nombre_usuario_seguro' AND estado = 'activo'";
        
        $resultado = mysqli_query($conexion, $sql);
        
        if (mysqli_num_rows($resultado) == 1) {
            // El usuario existe, obtener sus datos
            $usuario = mysqli_fetch_assoc($resultado);
            
            // 🔐 VERIFICAR la contraseña con password_verify()
            // Compara la contraseña ingresada con el hash almacenado
            if (password_verify($clave, $usuario['clave'])) {
                
                // ✅ LOGIN EXITOSO
                // Crear sesión con los datos del usuario
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
                $_SESSION['rol'] = $usuario['rol'];
                
                // Redirigir a index.php
                mysqli_close($conexion);
                header("Location: index.php");
                exit();
                
            } else {
                // ❌ Contraseña incorrecta
                $error_msg = "Contraseña incorrecta.";
            }
        } else {
            // ❌ Usuario no encontrado
            $error_msg = "Usuario no encontrado o inactivo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Biblioteca</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-auth {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        .form-auth h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-auth input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-auth button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-auth button:hover {
            background-color: #0056b3;
        }
        .link-registro {
            text-align: center;
            margin-top: 15px;
        }
        .link-registro a {
            color: #28a745;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>📚 Biblioteca - Inicio de Sesión</h1>
    </header>

    <main>
        <div class="form-auth">
            <h2>Inicia Sesión</h2>
            
            <?php if (!empty($error_msg)): ?>
                <p class="alerta" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px;">
                    <?php echo htmlspecialchars($error_msg); ?>
                </p>
            <?php endif; ?>
            
            <form action="login.php" method="POST">
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required 
                       value="<?php echo htmlspecialchars($_POST['nombre_usuario'] ?? ''); ?>">
                
                <label for="contraseña">Contraseña:</label>
                <input type="password" id="contraseña" name="contraseña" required>
                
                <button type="submit">Ingresar</button>
            </form>
            
            <div class="link-registro">
                <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
            </div>
            
            <!-- Para testing: usuario admin / contraseña admin123 -->
            <hr>
            <p style="text-align: center; font-size: 12px; color: #666;">
                📝 Testing: Usuario: <strong>admin</strong> | Contraseña: <strong>admin123</strong>
            </p>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 Proyecto Programación Web 2</p>
    </footer>

    <?php
    if (isset($conexion)) {
        mysqli_close($conexion);
    }
    ?>
</body>
</html>
