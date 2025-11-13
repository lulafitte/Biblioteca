<?php
// Iniciar sesión para poder acceder a $_SESSION
session_start();

// Destruir la sesión (eliminar todas las variables)
session_destroy();

// Redirigir al login
header("Location: login.php");
exit();
