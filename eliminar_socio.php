<?php
include 'config.php';
include 'validar_admin.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // En lugar de DELETE FROM socios
    // Hacemos un UPDATE para marcar al socio como eliminado (bloqueado)
    $query = "UPDATE socios SET eliminado = 1, estado = 'bloqueado' WHERE id_socio = $id";
    
    if ($conexion->query($query)) {
        // Redirigimos con un mensaje de "bloqueado" o "eliminado"
        header("Location: socios.php?msj=bloqueado");
    } else {
        echo "Error al actualizar: " . $conexion->error;
    }
}
?>