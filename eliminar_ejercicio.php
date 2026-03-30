<?php
include 'config.php';
include 'validar_admin.php'; // Asegura que solo el admin pueda acceder 

if (isset($_GET['id'])) {
    $id_relacion = $_GET['id'];

    // Usamos 'id_relacion' que es como se llama en tu tabla rutina_ejercicio
    $sql = "DELETE FROM rutina_ejercicio WHERE id_relacion = '$id_relacion'";
    
    if ($conexion->query($sql)) {
        // Redirigimos de vuelta a tu archivo principal
        header("Location: admin_rutinas.php?msg=eliminado"); 
        exit();
    } else {
        echo "Error al eliminar: " . $conexion->error;
    }
} else {
    echo "No se recibió el ID para eliminar.";
}
?>