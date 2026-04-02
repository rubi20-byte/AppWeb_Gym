<?php

include 'config.php';
session_start();

// Verificar que recibimos el ID
if (isset($_GET['id'])) {
    $id_comida = mysqli_real_escape_string($conexion, $_GET['id']);
    // Eliminar la comida de la base de datos
    $sql = "DELETE FROM sugerencias_comidas WHERE id_sugerencia = '$id_comida'";

    if ($conexion->query($sql)) {
        // Éxito: regresamos a la tabla de nutrición
        header("Location: admin_nutricion.php?msj=eliminado");
        exit();
    } else {
        // Si falla la base de datos, nos dirá por qué
        die("Error al eliminar en la base de datos: " . $conexion->error);
    }
} else {
    // Si entran al archivo sin ID, los mandamos de regreso
    header("Location: admin_nutricion.php");
    exit();
}
?>