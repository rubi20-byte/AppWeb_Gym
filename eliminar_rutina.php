<?php
include 'config.php';
include 'validar_admin.php'; // Solo el admin puede borrar rutinas completas

// Verificamos que venga el ID por la URL
if (isset($_GET['id'])) {
    $id_rutina = $_GET['id'];

    // 1. Borramos primero los ejercicios asociados a esa rutina
    // (Esto es para que no haya errores de llaves foráneas)
    $sql_ejercicios = "DELETE FROM rutina_ejercicio WHERE id_rutina = '$id_rutina'";
    $conexion->query($sql_ejercicios);

    // 2. Ahora sí borramos la rutina de la tabla principal
    $sql_rutina = "DELETE FROM rutinas WHERE id_rutina = '$id_rutina'";
    
    if ($conexion->query($sql_rutina)) {
        // Si todo sale bien, regresamos a la lista con un mensaje de éxito
        header("Location: admin_rutinas.php?msj=borrado_ok");
    } else {
        // Si falla, mostramos el error
        echo "Error al eliminar la rutina: " . $conexion->error;
    }
} else {
    // Si no mandaron ID, lo regresamos
    header("Location: admin_rutinas.php");
}
?>