<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Limpiamos los datos
    $nombre      = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $objetivo    = mysqli_real_escape_string($conexion, $_POST['objetivo']);
    // Cambiamos 'nivel' por 'descripcion' porque así se llama en tu tabla
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']); 

    // 2. SQL Ajustado a tu tabla real: id_rutina, nombre_rutina, objetivo, descripcion
    $sql = "INSERT INTO rutinas (nombre_rutina, objetivo, descripcion) 
            VALUES ('$nombre', '$objetivo', '$descripcion')";
    
    if ($conexion->query($sql)) {
        header("Location: admin_rutinas.php?msj=ok");
        exit();
    } else {
        // Esto te avisará si algo más falla
        die("Error en la BD: " . $conexion->error);
    }
}
?>