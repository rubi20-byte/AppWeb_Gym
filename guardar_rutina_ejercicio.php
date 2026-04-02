<?php
include 'config.php';
include 'validar_entrenador.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_rutina = $_POST['id_rutina'];
    $nombre_ejercicio = mysqli_real_escape_string($conexion, $_POST['nombre_ejercicio']);
    $series = $_POST['series'];
    $repeticiones = $_POST['repeticiones'];
    $descanso = mysqli_real_escape_string($conexion, $_POST['descanso']);
    $url_video = mysqli_real_escape_string($conexion, $_POST['url_video']);
    $orden = $_POST['orden'];

    // Insertamos en la tabla de unión (rutina_ejercicio)
    $sql = "INSERT INTO rutina_ejercicio (id_rutina, nombre_ejercicio, series, repeticiones, descanso, url_video, orden) 
            VALUES ('$id_rutina', '$nombre_ejercicio', '$series', '$repeticiones', '$descanso', '$url_video', '$orden')";

    if ($conexion->query($sql)) {
        // Regresamos a la misma página para seguir agregando más
        header("Location: gestionar_ejercicios_ent.php?id=$id_rutina&msj=ok");
    } else {
        echo "Error: " . $conexion->error;
    }
}
?>