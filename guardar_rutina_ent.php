<?php
include 'config.php';
include 'validar_entrenador.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre_rutina']);
    $objetivo = mysqli_real_escape_string($conexion, $_POST['objetivo']);

    $sql = "INSERT INTO rutinas (nombre_rutina, objetivo) VALUES ('$nombre', '$objetivo')";

    if ($conexion->query($sql)) {
        $id = $conexion->insert_id;
        header("Location: gestionar_ejercicios.php?id=$id");
    } else {
        header("Location: rutinas_entrenador.php?error=1");
    }
}
?>