<?php
include 'config.php';
include 'validar_admin.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Simplemente regresamos el bit a 0 y lo ponemos como inactivo (para que pague)
    $query = "UPDATE socios SET eliminado = 0, estado = 'inactivo' WHERE id_socio = $id";
    
    if ($conexion->query($query)) {
        header("Location: socios.php?msj=reactivado");
    } else {
        echo "Error: " . $conexion->error;
    }
}
?>