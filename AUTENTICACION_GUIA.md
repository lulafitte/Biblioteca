# 🔐 SISTEMA DE AUTENTICACIÓN Y ROLES - GUÍA COMPLETA

## 📋 RESUMEN

Has agregado un sistema de:
- **Registro de usuarios** (registro.php)
- **Login/Inicio de sesión** (login.php)
- **Logout/Cierre de sesión** (logout.php)
- **Gestión de sesiones** (sesiones.php)
- **Roles y permisos** (admin vs usuario)

---

## 🗄️ TABLA DE USUARIOS (usuarios_tabla.sql)

```sql
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    contraseña VARCHAR(255) NOT NULL,
    rol ENUM('administrador', 'usuario') DEFAULT 'usuario',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Campos:**
- `id_usuario`: Identificador único
- `nombre_usuario`: Login del usuario (único)
- `email`: Correo electrónico (único)
- `contraseña`: Contraseña hasheada con bcrypt
- `rol`: 'administrador' o 'usuario'
- `estado`: 'activo' o 'inactivo'
- `fecha_registro`: Fecha de creación automática

---

## 📝 1. REGISTRO.PHP - Crear cuenta nueva

### **FLUJO:**
1. Usuario abre registro.php
2. Completa formulario (usuario, email, contraseña)
3. Presiona "Registrarse"
4. Se validan los datos
5. Se hashea la contraseña con bcrypt
6. Se guarda en la BD
7. Puede iniciar sesión

### **VALIDACIONES:**

```php
// ✅ Campos obligatorios
if (empty($nombre_usuario) || empty($email) || empty($contraseña)) {
    $error_msg = "Todos los campos son obligatorios.";
}

// ✅ Las contraseñas coincidan
if ($contraseña !== $contraseña_confirm) {
    $error_msg = "Las contraseñas no coinciden.";
}

// ✅ Contraseña mínimo 6 caracteres
if (strlen($contraseña) < 6) {
    $error_msg = "La contraseña debe tener al menos 6 caracteres.";
}

// ✅ Email válido
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_msg = "El email no es válido.";
}
```

### **SEGURIDAD CLAVE: password_hash()**

```php
// ❌ NUNCA hagas esto (inseguro):
$sql = "INSERT INTO usuarios (nombre_usuario, contraseña) 
        VALUES ('$usuario', '$contraseña')";  // La contraseña en TEXTO PLANO

// ✅ SIEMPRE hashea la contraseña:
$contraseña_hasheada = password_hash($contraseña, PASSWORD_BCRYPT);
// Ahora la contraseña está encriptada y NO se puede desencriptar
```

**¿Por qué bcrypt?**
- Es lenta (toma 0.3 segundos) → Protege contra ataques de fuerza bruta
- Es imposible revertir (no se puede desencriptar)
- Si la BD es hackeada, las contraseñas están protegidas

### **MANEJO DE ERRORES:**

```php
} else {
    $error = mysqli_error($conexion);
    if (strpos($error, 'nombre_usuario') !== false) {
        $error_msg = "El nombre de usuario ya existe.";
    } elseif (strpos($error, 'email') !== false) {
        $error_msg = "El email ya está registrado.";
    }
}
```

---

## 🔑 2. LOGIN.PHP - Iniciar sesión

### **FLUJO:**
1. Usuario abre login.php
2. Escribe usuario y contraseña
3. Se valida que exista el usuario
4. Se verifica la contraseña con password_verify()
5. Si es correcta, se crea una SESIÓN
6. Se redirige a index.php

### **LÍNEA MÁS IMPORTANTE:**

```php
session_start(); // ⭐ DEBE SER LA PRIMERA LÍNEA (antes de cualquier echo/header)
```

### **BÚSQUEDA DEL USUARIO:**

```php
$sql = "SELECT id_usuario, nombre_usuario, contraseña, rol, estado 
        FROM usuarios 
        WHERE nombre_usuario = '$nombre_usuario_seguro' 
        AND estado = 'activo'";
```

- Solo busca usuarios ACTIVOS
- Obtiene la contraseña hasheada

### **VERIFICACIÓN DE CONTRASEÑA:**

```php
// ❌ NUNCA hagas esto:
if ($contraseña_ingresada == $contraseña_de_bd) { }  // INSEGURO

// ✅ SIEMPRE usa password_verify():
if (password_verify($contraseña, $usuario['contraseña'])) {
    // Contraseña correcta
    $_SESSION['id_usuario'] = $usuario['id_usuario'];
    $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
    $_SESSION['rol'] = $usuario['rol'];
    
    header("Location: index.php");
}
```

**¿Por qué password_verify()?**
- Compara la contraseña ingresada con el hash almacenado
- No desencripta el hash
- Si el hash es incorrecto, sigue siendo seguro

### **CREAR SESIÓN:**

```php
// $_SESSION es un array especial que se guarda en el servidor
$_SESSION['id_usuario'] = 5;                // ID del usuario
$_SESSION['nombre_usuario'] = 'juan';       // Nombre
$_SESSION['rol'] = 'administrador';         // Rol

// Estos datos persisten mientras el usuario no cierre sesión
// Se pueden acceder desde CUALQUIER página: <?php echo $_SESSION['nombre_usuario']; ?>
```

---

## 🚪 3. LOGOUT.PHP - Cerrar sesión

### **CÓDIGO SIMPLE:**

```php
session_start();
session_destroy();  // Elimina TODA la sesión
header("Location: login.php");
exit();
```

**¿Qué hace `session_destroy()`?**
- Borra TODAS las variables de $_SESSION
- Elimina el archivo de sesión del servidor
- El usuario vuelve a ser un "anónimo"

---

## 🛡️ 4. SESIONES.PHP - Sistema de control de acceso

### **FUNCIONES DISPONIBLES:**

#### **1. `protegerPagina()`**
```php
require_once 'sesiones.php';
protegerPagina(); // Si no está logueado → redirige a login
```

Úsalo en cualquier página que requiera login:
```php
<?php
require_once 'sesiones.php';
protegerPagina(); // ← Línea de protección

// Resto del código...
```

#### **2. `protegerAdmin()`**
```php
protegerAdmin(); // Si no es admin → redirige a index con error
```

Para páginas solo de administrador:
```php
<?php
require_once 'sesiones.php';
protegerAdmin(); // Solo administrador puede acceder

// Crear usuario, editar roles, etc.
```

#### **3. `estuLogueado()`**
```php
if (estuLogueado()) {
    echo "Usuario autenticado";
} else {
    echo "Por favor, inicia sesión";
}
```

#### **4. `obtenerUsuarioLogueado()`**
```php
$usuario = obtenerUsuarioLogueado();
echo "Hola, $usuario"; // Hola, juan
```

#### **5. `esAdministrador()`**
```php
if (esAdministrador()) {
    echo "Opciones administrativas...";
}
```

#### **6. `mostrarBarraUsuario()`**
```php
mostrarBarraUsuario(); // Muestra: "👤 Logueado como: juan [ADMIN] | Cerrar Sesión"
```

---

## 🔄 FLUJO COMPLETO DEL SISTEMA

```
┌─────────────────────┐
│  Usuario abre app   │
└──────────┬──────────┘
           ↓
     ¿Tiene sesión?
       /        \
      NO        SÍ
      ↓         ↓
   LOGIN    INDEX
    ↓         ↓
 Ingresa    Vé tabla
 usuario/   de autores
 contraseña
    ↓
 ¿Contraseña
 correcta?
   /    \
  NO     SÍ
  ↓      ↓
ERROR  $_SESSION creada
        ↓
      INDEX
      (con datos
      del usuario)
```

---

## 📋 CÓMO PROTEGER TODAS LAS PÁGINAS

### **En index.php:**
```php
<?php
require_once 'sesiones.php';
protegerPagina();
// ... resto del código
```

### **En autor_crear.php:**
```php
<?php
require_once 'sesiones.php';
protegerPagina();
// ... resto del código
```

### **En libro_crear.php:**
```php
<?php
require_once 'sesiones.php';
protegerPagina();
// ... resto del código
```

**Hazlo para TODOS los archivos excepto:**
- `login.php` (necesita ser público)
- `registro.php` (necesita ser público)
- `logout.php` (necesita estar disponible)

---

## 🎯 EJEMPLO: Crear página de solo ADMIN

**archivo_admin.php:**
```php
<?php
require_once 'sesiones.php';
protegerAdmin(); // Solo admin puede entrar

require_once 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Panel Administrativo</title>
</head>
<body>
    <h1>Panel Administrativo</h1>
    <?php mostrarBarraUsuario(); ?>
    
    <h2>Gestión de Usuarios</h2>
    <!-- Aquí puedes:
         - Crear usuarios
         - Cambiar roles
         - Desactivar usuarios
         - Ver logs
    -->
</body>
</html>
```

Si un usuario normal (rol='usuario') intenta entrar:
- Será redirigido a index.php
- Verá un mensaje de error

---

## 🧪 TESTING

### **Usuario de prueba:**
- Usuario: `admin`
- Contraseña: `admin123`
- Rol: Administrador

### **Para crear otro usuario:**
1. Abre `http://localhost/biblioteca/registro.php`
2. Rellena el formulario
3. Se guardará como usuario normal (rol='usuario')

### **Para cambiar un rol (manualmente en BD):**
```sql
UPDATE usuarios SET rol = 'administrador' WHERE nombre_usuario = 'juan';
```

---

## 🔒 SEGURIDAD RESUMIDA

| Elemento | Riesgo | Solución |
|----------|--------|----------|
| Contraseña en texto plano | Hackeo de BD | `password_hash()` |
| Verificación incorrecta de contraseña | Acceso sin autorización | `password_verify()` |
| SQL injection en login | Hackeo de BD | `mysqli_real_escape_string()` |
| Sesiones sin cerrar | Usuario sigue logueado | `session_destroy()` |
| Acceso a páginas protegidas | Usuarios leen datos privados | `protegerPagina()` |
| Falta de cierre de conexión | Conflictos de BD | `mysqli_close()` |

---

## 📝 PASOS PARA IMPLEMENTAR

1. **Ejecutar el SQL:**
   - Abre `usuarios_tabla.sql` en phpMyAdmin
   - Ejecuta para crear la tabla y el usuario admin

2. **Subir los archivos:**
   - `login.php`
   - `registro.php`
   - `logout.php`
   - `sesiones.php`

3. **Modificar los archivos protegidos:**
   - Agregua al inicio: `require_once 'sesiones.php'; protegerPagina();`
   - Archivos: index.php, autor_crear.php, autor_editar.php, autor_eliminar.php, libro_crear.php, libro_editar.php, libro_eliminar.php

4. **Probar:**
   - Abre `http://localhost/biblioteca/login.php`
   - Intenta acceder a `http://localhost/biblioteca/index.php` sin iniciar sesión
   - Deberías ser redirigido a login.php

---

**¡Listo! Ahora tienes un sistema seguro de autenticación y roles. 🔐**
