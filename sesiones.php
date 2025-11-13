<?php
/**
 * ARCHIVO: sesiones.php
 * USO: Incluir al inicio de TODOS los archivos protegidos (index.php, autor_crear.php, etc)
 * 
 * Funciones:
 * - Inicia la sesión
 * - Verifica si el usuario está logueado
 * - Redirige al login si no está autenticado
 * - Proporciona funciones de control de roles
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función: Verificar si el usuario está logueado
function estuLogueado() {
    return isset($_SESSION['id_usuario']);
}

// Función: Obtener el usuario logueado
function obtenerUsuarioLogueado() {
    return $_SESSION['nombre_usuario'] ?? 'Desconocido';
}

// Función: Obtener el rol del usuario logueado
function obtenerRolUsuario() {
    return $_SESSION['rol'] ?? null;
}

// Función: Verificar si el usuario es administrador
function esAdministrador() {
    return (obtenerRolUsuario() === 'administrador');
}

// Función: Redirigir al login si NO está logueado
function protegerPagina() {
    if (!estuLogueado()) {
        header("Location: login.php");
        exit();
    }
}

// Función: Redirigir si NO es administrador
function protegerAdmin() {
    if (!estuLogueado()) {
        header("Location: login.php");
        exit();
    }
    if (!esAdministrador()) {
        header("Location: index.php?error=No tienes permiso para acceder a esta página");
        exit();
    }
}

// Función: Mostrar barra de usuario logueado
function mostrarBarraUsuario() {
    if (estuLogueado()) {
        $usuario = obtenerUsuarioLogueado();
        $rol = obtenerRolUsuario();
        echo "<div style='background-color: #e7f3ff; padding: 10px; margin-bottom: 10px; border-radius: 4px;'>";
        echo "👤 Logueado como: <strong>$usuario</strong>";
        if (esAdministrador()) {
            echo " <span style='background-color: #ffc107; padding: 3px 8px; border-radius: 3px; font-size: 12px;'>[ADMIN]</span>";
        }
        echo " | <a href='logout.php' style='color: #dc3545;'>Cerrar Sesión</a>";
        echo "</div>";
    }
}
?>
