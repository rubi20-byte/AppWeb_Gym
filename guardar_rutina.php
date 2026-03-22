<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $objetivo = $_POST['objetivo'];
    $nivel = $_POST['nivel'];

    $sql = "INSERT INTO rutinas (nombre_rutina, objetivo, nivel) VALUES ('$nombre', '$objetivo', '$nivel')";
    
    if ($conexion->query($sql)) {
        header("Location: admin_rutinas.php?msj=ok");
    } else {
        echo "Error: " . $conexion->error;
    }
}
?>