<?php
include 'config.php';

$id = $_GET['id'];

// En lugar de DELETE, usamos UPDATE para mantener historial
$sql = "UPDATE productos SET estado = 'inactivo' WHERE id_producto = $id";

if ($conexion->query($sql)) {
    header("Location: inventario.php?status=deleted");
} else {
    echo "Error: " . $conexion->error;
}
?>