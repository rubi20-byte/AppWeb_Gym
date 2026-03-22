<?php
include 'config.php';

// Esto nos ayudará a ver el error real si algo falla
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibimos los datos del formulario
    $id_rutina = $_POST['id_rutina'];
    $nombre = $conexion->real_escape_string($_POST['nombre_ejercicio']);
    $series = (int)$_POST['series'];
    $reps = $conexion->real_escape_string($_POST['repeticiones']);
    $video = $conexion->real_escape_string($_POST['url_video']);
    $orden = (int)$_POST['orden'];

    // SQL ajustado a tus nuevas columnas
    // Asegúrate que los nombres coincidan con los que pusimos en el SQL anterior
    $sql = "INSERT INTO rutina_ejercicio (id_rutina, nombre_ejercicio, series, repeticiones, url_video, orden) 
            VALUES ('$id_rutina', '$nombre', '$series', '$reps', '$video', '$orden')";
    
    if ($conexion->query($sql)) {
        // Si todo sale bien, regresamos a la gestión
        header("Location: gestionar_ejercicios.php?id=$id_rutina&status=success");
    } else {
        // Si hay error de SQL, aquí nos lo dirá en lugar de dar error 500
        echo "Error en la base de datos: " . $conexion->error;
    }
} else {
    echo "No se recibieron datos.";
}
?>