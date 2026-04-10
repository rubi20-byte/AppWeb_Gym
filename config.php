<?php
$host = 'localhost';     
$user = 'admin';    
$pass = 'c527504a2e76165ad8be016080a33bca80e9e3d0696919ab';   
$db   = 'sistema_gym';  

// Crear la conexión
$conexion = new mysqli($host, $user, $pass, $db);

// Verificar si hay error
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");


?>