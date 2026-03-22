<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

echo "<h3>Estado de la Sesión:</h3>";
echo "Rol: " . ($_SESSION['rol'] ?? 'No definido') . "<br>";
echo "ID Socio: " . ($_SESSION['id_socio'] ?? 'No definido') . "<br>";

include 'config.php';
if($conexion) {
    echo "<h3 style='color:green'>Conexión a BD: OK</h3>";
} else {
    echo "<h3 style='color:red'>Conexión a BD: FALLÓ</h3>";
}
?>