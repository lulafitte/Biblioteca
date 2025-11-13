<?php
// Incluye la conexión
require_once 'conexion.php';

$error_msg = "";
$success_msg = "";

// Procesar el formulario de registro
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $email = $_POST['email'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $clave_confirm = $_POST['clave_confirm'] ?? '';
    
    // Validar que no estén vacíos
    if (empty($nombre_usuario) || empty($email) || empty($clave) || empty($clave_confirm)) {
        $error_msg = "Todos los campos son obligatorios.";
    }
    // Validar que las contraseñas coincidan
    elseif ($clave !== $clave_confirm) {
        $error_msg = "Las claves no coinciden.";
    }
    // Validar que la contraseña tenga mínimo 6 caracteres
    elseif (strlen($clave) < 6) {
        $error_msg = "La clave debe tener al menos 6 caracteres.";
    }
    // Validar que el email sea válido
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = "El email no es válido.";
    }
    else {
        // 🔐 HASHEAR la contraseña con bcrypt (lo más seguro)
        // password_hash() crea un hash seguro de la contraseña
        $clave_hasheada = password_hash($clave, PASSWORD_BCRYPT);
        
        // Escapar datos para seguridad
        $nombre_usuario_seguro = mysqli_real_escape_string($conexion, $nombre_usuario);
        $email_seguro = mysqli_real_escape_string($conexion, $email);
        
        // Insertar nuevo usuario (rol por defecto: 'usuario')
        $sql = "INSERT INTO usuarios (nombre_usuario, email, clave, rol, estado) 
            VALUES ('$nombre_usuario_seguro', '$email_seguro', '$clave_hasheada', 'usuario', 'activo')";
        
        if (mysqli_query($conexion, $sql)) {
            $success_msg = "✅ Registro exitoso. Ya puedes iniciar sesión.";
            // Limpiar formulario
            $_POST = array();
        } else {
            // Verificar si el error es por usuario o email duplicado
            $error = mysqli_error($conexion);
            if (strpos($error, 'nombre_usuario') !== false) {
                $error_msg = "El nombre de usuario ya existe. Intenta con otro.";
            } elseif (strpos($error, 'email') !== false) {
                $error_msg = "El email ya está registrado.";
            } else {
                $error_msg = "Error al registrar: " . $error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Biblioteca</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-auth {
            max-width: 400px;
            margin: 50px auto;
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
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-auth button:hover {
            background-color: #218838;
        }
        .link-login {
            text-align: center;
            margin-top: 15px;
        }
        .link-login a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>📚 Biblioteca - Registro de Usuarios</h1>
    </header>

    <main>
        <div class="form-auth">
            <h2>Crear Nueva Cuenta</h2>
            
            <?php if (!empty($error_msg)): ?>
                <p class="alerta" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px;">
                    <?php echo htmlspecialchars($error_msg); ?>
                </p>
            <?php endif; ?>
            
            <?php if (!empty($success_msg)): ?>
                <p class="alerta" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px;">
                    <?php echo htmlspecialchars($success_msg); ?>
                </p>
            <?php endif; ?>
            
            <form action="registro.php" method="POST">
                <label for="nombre_usuario">Nombre de Usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required 
                       value="<?php echo htmlspecialchars($_POST['nombre_usuario'] ?? ''); ?>">
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                
                <label for="clave">Clave:</label>
                <input type="password" id="clave" name="clave" required>
                
                <label for="clave_confirm">Confirmar Clave:</label>
                <input type="password" id="clave_confirm" name="clave_confirm" required>
                
                <button type="submit">Registrarse</button>
            </form>
            
            <div class="link-login">
                <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
            </div>
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
