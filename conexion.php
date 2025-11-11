<?php

// 1. Definir las credenciales de la base de datos
$servidor = "localhost"; // Generalmente es 'localhost' si usas XAMPP/WAMP
$usuario = "root";       // Usuario por defecto de MySQL en XAMPP/WAMP
$password = "";          // Contraseña por defecto de MySQL en XAMPP/WAMP (a menudo vacía)
$base_de_datos = "biblioteca_db"; // El nombre que le dimos a la base de datos
$puerto = 3306;


// 2. Establecer la conexión
// Usamos mysqli_connect(servidor, usuario, password, base_de_datos)
$conexion = mysqli_connect($servidor, $usuario, $password, $base_de_datos);

// 3. Verificar la conexión
if (!$conexion) {
    // Si la conexión falla, muestra un error y detiene la ejecución
    die("Error al conectar con la base de datos: " . mysqli_connect_error());
}

// evitar problemas de acentos y eñes
mysqli_set_charset($conexion, "utf8");



?>