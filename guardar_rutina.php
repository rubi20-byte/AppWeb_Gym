<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre      = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $objetivo    = mysqli_real_escape_string($conexion, $_POST['objetivo']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']); 

    $sql = "INSERT INTO rutinas (nombre_rutina, objetivo, descripcion) 
            VALUES ('$nombre', '$objetivo', '$descripcion')";
    
    if ($conexion->query($sql)) {
        header("Location: admin_rutinas.php?msj=ok");
        exit();
    } else {
        die("Error en la BD: " . $conexion->error);
    }
}
?>